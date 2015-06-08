<?php
/**
 * Event CSV Importer - IL Humanities - Firebelly 2015
 */

class EventCSVImporter {
  var $defaults = array(
      'Ev_Type'                        => null, // focus area
      'Ev_AtrCat_3_01_Description'     => null, // sub-focus area (not currently used)
      'Ev_Group'                       => null, // program
      'Ev_Start_Date'                  => null,
      'Ev_End_Date'                    => null,
      'Ev_Start_Time'                  => null,
      'Ev_End_Time'                    => null,
      'Ev_Note_1_01_Actual_Notes'      => null, // event registration URL
      'Ev_Note_1_02_Actual_Notes'      => null, // body
      'Ev_AtrCat_4_01_Description'     => null, // RSVP required (Yes or no/blank)
      'Ev_AtrCat_4_01_Comments'        => null, // RSVP text (required/recommended)
      'Ev_AtrCat_2_01_Description'     => null, // cost
      'Ev_Note_1_04_Actual_Notes'      => null, // title
      'Ev_Prt_2_01_Name'               => null, // sponsor
      'Ev_Prt_1_01_CnBio_Name'         => null, // location
      'Ev_Prt_1_01_CnAdrPrf_Addrline1' => null,
      'Ev_Prt_1_01_CnAdrPrf_Addrline2' => null,
      'Ev_Prt_1_01_CnAdrPrf_City'      => null,
      'Ev_Prt_1_01_CnAdrPrf_State'     => null,
      'Ev_Prt_1_01_CnAdrPrf_ZIP'       => null,
      'Ev_Prt_1_01_CnAdrPrf_County'    => null,
  );

  var $log = array();
  var $focus_areas;
  var $program_cache = array();
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
        $event_start = strtotime($csv_data['Ev_Start_Date'] . ' ' . $csv_data['Ev_Start_Time']);
        $existing_post_id = $wpdb->get_var($wpdb->prepare("
          SELECT ID FROM `wp_posts` p
          LEFT JOIN wp_postmeta pm ON (p.ID=pm.post_id AND pm.meta_key='_cmb2_event_start')
          WHERE p.post_title = %s 
          AND p.post_type = 'event' 
          AND pm.meta_value = %d
          AND (p.post_status = 'publish' OR p.post_status = 'draft')
          ", $csv_data['Ev_Note_1_03_Actual_Notes'], $event_start
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
    $wpdb->query('COMMIT;');
    $wpdb->query('SET autocommit = 1;');

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
  function create_post($csv_data) {
    $csv_data = array_merge($this->defaults, $csv_data);
    $new_post = array(
      'post_title'   => $csv_data['Ev_Note_1_04_Actual_Notes'],
      'post_content' => $csv_data['Ev_Note_1_02_Actual_Notes'],
      'post_status'  => 'draft',
      'post_type'    => 'event',
    );
    $post_id = wp_insert_post($new_post);

    if ($post_id) {
      // Only add price info if "free" isn't in description
      // if ($csv_data['Ev_Note_1_02_Actual_Notes'] && !preg_match('/free/i',$csv_data['Ev_Note_1_02_Actual_Notes'])) {
      if ($csv_data['Ev_AtrCat_2_01_Description']) {
        update_post_meta($post_id, $this->prefix.'cost', $csv_data['Ev_AtrCat_2_01_Description']);
      }

      // Keep county data for giggles
      if ($csv_data['Ev_Prt_1_01_CnAdrPrf_County'])
        update_post_meta($post_id, $this->prefix.'county', $csv_data['Ev_Prt_1_01_CnAdrPrf_County']);

      // Set times
      $event_start = strtotime($csv_data['Ev_Start_Date'] . ' ' . $csv_data['Ev_Start_Time']);
      if (!$csv_data['Ev_End_Date'] && !$csv_data['Ev_End_Time']) {
        $event_end = $event_start;
      } else if ($csv_data['Ev_End_Time']) {
        $event_end = strtotime($csv_data['Ev_Start_Date'] . ' ' . $csv_data['Ev_End_Time']);
      } else {
        $event_end = strtotime($csv_data['Ev_End_Date'] . ' ' . $csv_data['Ev_End_Time']);
      }
      update_post_meta($post_id, $this->prefix.'event_start', $event_start);
      update_post_meta($post_id, $this->prefix.'event_end', $event_end);

      // Venue and address
      update_post_meta($post_id, $this->prefix.'venue', $csv_data['Ev_Prt_1_01_CnBio_Name']);
      $address = [
        'address-1' => $csv_data['Ev_Prt_1_01_CnAdrPrf_Addrline1'],
        'address-2' => $csv_data['Ev_Prt_1_01_CnAdrPrf_Addrline2'],
        'city' => $csv_data['Ev_Prt_1_01_CnAdrPrf_City'],
        'state' => $csv_data['Ev_Prt_1_01_CnAdrPrf_State'],
        'zip' => $csv_data['Ev_Prt_1_01_CnAdrPrf_ZIP'],
      ];
      update_post_meta($post_id, $this->prefix.'address', $address);

      // Find/set related program
      if ($csv_data['Ev_Group'])
        $this->set_program($post_id, $csv_data['Ev_Group']);

      // Set Registration URL
      if ($csv_data['Ev_Note_1_01_Actual_Notes'])
        update_post_meta($post_id, $this->prefix.'registration_url', $csv_data['Ev_Note_1_01_Actual_Notes']);

      // RSVP text
      if ($csv_data['Ev_AtrCat_4_01_Description'] && preg_match('/yes/i',$csv_data['Ev_AtrCat_4_01_Description'])) {
        if ($csv_data['Ev_AtrCat_4_01_Comments']) {
          if (preg_match('/required/i',$csv_data['Ev_AtrCat_4_01_Comments'])) {
            update_post_meta($post_id, $this->prefix.'rsvp_text', 'required');
          } else if (preg_match('/recommended/i',$csv_data['Ev_AtrCat_4_01_Comments'])) {
            update_post_meta($post_id, $this->prefix.'rsvp_text', 'recommended');
          }
        }
      }

      // Get sponsor/partner/funder fields
      $sponsoring_orgs = $partners = $funders = [];
      for ($i=1; $i <= 4; $i++) {
        if ($csv_data['Ev_Prt_2_0'.$i.'_Name']) {
          $sponsoring_orgs[] = $csv_data['Ev_Prt_2_0'.$i.'_Name'];
        }
        if ($csv_data['Ev_Prt_3_0'.$i.'_Name']) {
          $partners[] = $csv_data['Ev_Prt_3_0'.$i.'_Name'];
        }
        if ($csv_data['Ev_Prt_4_0'.$i.'_Name']) {
          $funders[] = $csv_data['Ev_Prt_4_0'.$i.'_Name'];
        }
      }

      // Set sponsor/partner/funder fields
      if (count($sponsoring_orgs)>0) {
        update_post_meta($post_id, $this->prefix.'sponsor', implode('<br>', $sponsoring_orgs));
      }
      if (count($partners)>0) {
        update_post_meta($post_id, $this->prefix.'partner', implode('<br>', $partners));
      }
      if (count($funders)>0) {
        update_post_meta($post_id, $this->prefix.'funder', implode('<br>', $funders));
      }

      // Set focus area
      if ($csv_data['Ev_Type'])
        $this->set_focus_area($post_id, $csv_data['Ev_Type']);

      // if ($csv_data['Ev_AtrCat_3_01_Description'])
      // todo: sub focus area handling

      // Trigger geolocation routines
      \Firebelly\PostTypes\Event\geocode_address($post_id);

      return $post_id;
    } else {
      return false;
    }
  }

  // Assign Focus Area
  function set_focus_area($post_id, $focus_area) {
    $cat_ids = array();
    foreach($this->focus_areas as $focus_area_obj) {
      if ($focus_area_obj->name == esc_html($focus_area))
        $cat_ids[] = (int)$focus_area_obj->term_id;
    }
    wp_set_object_terms($post_id, $cat_ids, 'focus_area');
  }

  // Find/set related program
  function set_program($post_id, $program_title) {
    if (array_key_exists($program_title, $this->program_cache)) {
      update_post_meta($post_id, $this->prefix.'related_program', $this->program_cache[$program_title]);
    } else {
      if ($program = get_page_by_title($program_title, OBJECT, 'program')) {
        $this->program_cache[$program_title] = $program->ID;
        update_post_meta($post_id, $this->prefix.'related_program', $program->ID);
      }
    }
  }

}
