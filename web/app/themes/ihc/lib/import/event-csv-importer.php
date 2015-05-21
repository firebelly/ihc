<?php
/**
 * Event CSV Importer - IL Humanities - Firebelly 2015
 */

class EventCSVImporter {
  var $defaults = array(
      'Ev_Start_Date'                  => null,
      'Ev_End_Date'                    => null,
      'Ev_Start_Time'                  => null,
      'Ev_End_Time'                    => null,
      'Ev_Note_1_01_Actual_Notes'      => null, // body
      'Ev_Note_1_02_Actual_Notes'      => null, // cost
      'Ev_Note_1_03_Actual_Notes'      => null, // title
      'Ev_Prt_1_01_CnBio_Name'         => null, // sponsor
      'Ev_Prt_1_01_CnBio_Import_ID'    => null, // sponsor
      'Ev_Prt_1_02_CnBio_Name'         => null, // location
      'Ev_Prt_1_02_CnAdrPrf_Addrline1' => null,
      'Ev_Prt_1_02_CnAdrPrf_Addrline2' => null,
      'Ev_Prt_1_02_CnAdrPrf_City'      => null,
      'Ev_Prt_1_02_CnAdrPrf_State'     => null,
      'Ev_Prt_1_02_CnAdrPrf_ZIP'       => null,
      'Ev_Prt_1_02_CnAdrPrf_County'    => null,
  );

  var $log = array();
  var $focus_areas;
  var $prefix = '_cmb2_';

  /**
   * Handle POST submission
   */
  function handle_post() {
    global $wpdb;

    $files = array();
    $fdata = $_FILES['csv_import'];
    if ($fdata) {
      if (is_array($fdata['name'])) {
        // Rejigger HTML5 multiple file upload array format
        for ($i = 0; $i<count($fdata['name']); ++$i) {
          $files[] = array(
            'name'     => $fdata['name'][$i],
            'type'     => $fdata['type'][$i],
            'tmp_name' => $fdata['tmp_name'][$i],
            'error'    => $fdata['error'][$i],
            'size'     => $fdata['size'][$i]
          );
        }
      } else $files[] = $fdata;
    }

    // Nothing to process?
    if (count($files) == 0) {
      $this->log['error'][] = 'No file uploaded, aborting.';
      return $this->log;
    }

    // Check permissions
    if (!current_user_can('create_thoughts')) {
      $this->log['error'][] = 'You don\'t have the permissions to CSV import. Please contact the site administrator.';
      return $this->log;
    }

    // http://code.google.com/p/php-csv-parser/
    require_once('csv-datasource.php');

    $time_start = microtime(true);
    $i = $num_skipped = $num_updated = $num_imported = 0;

    // Get focus areas to match for import
    $this->focus_areas = get_terms('focus_area', ['hide_empty' => 0]);

    // Temp disable autocommit
    $wpdb->query('SET autocommit = 0;');

    foreach($files as $file_upload) {

      $csv = new File_CSV_DataSource;
      $file = $file_upload['tmp_name'];

      if (!$csv->load($file)) {
        $this->log['error'][] = 'Failed to load file, aborting.';
        return $this->log;
      }

      // pad shorter rows with empty values
      $csv->symmetrize();

      foreach ($csv->connect() as $csv_data) {
        // Check if post already exists in db
        $existing_post_id = $wpdb->get_var($wpdb->prepare(
          "SELECT ID FROM `wp_posts` WHERE post_content LIKE %s AND post_type = 'event' AND post_status = 'publish'", convert_chars($csv_data['Ev_Note_1_01_Actual_Notes'])
        ));
        if ($existing_post_id)
          wp_delete_post($existing_post_id, true);

        if ($this->create_post($csv_data)) {
            $num_imported++;
        } else {
            $num_skipped++;
        }
      }

      // Remove temp upload file
      if (file_exists($file)) {
        @unlink($file);
      }
      $i++;
    }

    // Commit query queue
    $wpdb->query( 'COMMIT;' );
    $wpdb->query( 'SET autocommit = 1;' );

    // Build response notices
    if ($num_skipped)
      $this->log['notice'][] = sprintf("skipped %s entries", $num_skipped);

    if ($num_updated)
      $this->log['notice'][] = sprintf("updated %s entries", $num_updated);

    if ($num_imported)
      $this->log['notice'][] = sprintf("imported %s entries", $num_imported);

    $exec_time = microtime(true) - $time_start;
    $this->log['stats']['exec_time'] = sprintf("%.2f", $exec_time);

    return $this->log;
  }

  // Create a new post with CSV data
  function create_post($data) {
    $data = array_merge($this->defaults, $data);
    $new_post = array(
      'post_title'   => $data['Ev_Note_1_03_Actual_Notes'],
      'post_content' => $data['Ev_Note_1_01_Actual_Notes'],
      'post_status'  => 'draft',
      'post_type'    => 'event',
    );
    $post_id = wp_insert_post($new_post);

    if ($post_id) {
      // Add Sponsor
      if ($data['Ev_Prt_1_01_CnBio_Name']) {
        update_post_meta($post_id, $this->prefix.'sponsor', $data['Ev_Prt_1_01_CnBio_Name']);
      }
      
      // Only add price info if "free" isn't in description
      if ($data['Ev_Note_1_02_Actual_Notes'] && !preg_match('/free/i',$data['Ev_Note_1_02_Actual_Notes'])) {
        update_post_meta($post_id, $this->prefix.'cost', $data['Ev_Note_1_02_Actual_Notes']);
      }

      // Keep county data for giggles
      if ($data['Ev_Prt_1_02_CnAdrPrf_County'])
        update_post_meta($post_id, $this->prefix.'county', $data['Ev_Prt_1_02_CnAdrPrf_County']);

      // Set times
      $event_start = strtotime($data['Ev_Start_Date'] . ' ' . $data['Ev_Start_Time']);
      if (!$data['Ev_End_Date']) {
        $event_end = $event_start;
      } else {
        $event_end = strtotime($data['Ev_End_Date'] . ' ' . $data['Ev_End_Time']);
      }
      update_post_meta($post_id, $this->prefix.'event_start', $event_start);
      update_post_meta($post_id, $this->prefix.'event_end', $event_end);

      // Venue and address
      update_post_meta($post_id, $this->prefix.'venue', $data['Ev_Prt_1_02_CnBio_Name']);
      $address = [
        'address-1' => $data['Ev_Prt_1_02_CnAdrPrf_Addrline1'],
        'address-2' => $data['Ev_Prt_1_02_CnAdrPrf_Addrline2'],
        'city' => $data['Ev_Prt_1_02_CnAdrPrf_City'],
        'state' => $data['Ev_Prt_1_02_CnAdrPrf_State'],
        'zip' => $data['Ev_Prt_1_02_CnAdrPrf_ZIP'],
      ];
      update_post_meta($post_id, $this->prefix.'address', $address);

      // Todo: find how to build reg_url from CSV import
      // if ($registration_url)
      //   update_post_meta($post_id, $this->prefix.'registration_url', $registration_url);

      // Set focus area
      // $this->set_focus_area($post_id, $data['focus_area']);

      // Trigger geolocation routines
      \Firebelly\PostTypes\Event\geocode_address($post_id);

      return $post_id;
    } else {
      return false;
    }
  }

  // Assign Focus Area to Thought
  function set_focus_area($post_id,$focus_area) {
    $cat_ids = array();
    foreach($this->focus_areas as $focus_area_obj) {
      if ($focus_area_obj->name == esc_html($focus_area))
        $cat_ids[] = (int)$focus_area_obj->term_id;
    }
    wp_set_object_terms($post_id, $cat_ids, 'focus_area');
  }

}
