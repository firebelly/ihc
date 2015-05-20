<?php 

class ThoughtCSVImporter {
    var $defaults = array(
        'quote'       => null,
        'author'     => null,
        'focus_area' => null,
    );

    var $log = array();
    var $ajax = false;
    var $focus_areas;

    /**
     * Handle POST submission
     *
     * @return void
     */
    function handle_post($ajax=false) {
      global $wpdb;
      $this->ajax = $ajax;
      
      // Rejigger HTML5 multiple file upload array format
      $files = array();
      $fdata = $_FILES['csv_import'];
      if ($fdata) {
        if ( is_array($fdata['name']) ) {
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

      // Nothing to process!
      if ( count($files) == 0 ) {
        $this->log['error'][] = 'No file uploaded, aborting.';
        return $this->log;
      }

      // Check permissions
      if (!current_user_can('create_thoughts')) {
        $this->log['error'][] = 'You don\'t have the permissions to CSV import. Please contact the site administrator.';
        return $this->log;
      }

      // http://code.google.com/p/php-csv-parser/
      require_once( 'csv-datasource.php' );

      $time_start = microtime(true);
      $i = $num_skipped = $num_updated = $num_imported = 0;

      $this->focus_areas = get_terms('focus_area', array('hide_empty' => 0));

      // temp disable autocommit
      $wpdb->query( 'SET autocommit = 0;' );

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
            "SELECT ID FROM `wp_posts` WHERE post_content = '%s' AND post_type = 'thought' AND post_status = 'publish'", 
            convert_chars($csv_data['quote'])
          ));
          if ($existing_post_id) {
            // wp_delete_post($existing_post_id, true);
            update_post_meta($existing_post_id, '_cmb2_author', $csv_data['author']); 
            wp_update_post(['ID' => $existing_post_id, 'post_title' => 'Quote from '.$csv_data['author']]); 
            $this->set_focus_area($existing_post_id, $csv_data['focus_area']);
            $num_updated++;
          } else {
            if ($post_id = $this->create_post($csv_data)) {
                $num_imported++;
            } else {
                $num_skipped++;
            }
          }
        }

        // remove temp upload file
        if (file_exists($file)) {
          @unlink($file);
        }
        $i++;
      }

      // Commit query queue
      $wpdb->query( 'COMMIT;' );
      $wpdb->query( 'SET autocommit = 1;' );

      $exec_time = microtime(true) - $time_start;
      if ($num_skipped) {
          $this->log['notice'][] = "skipped {$num_skipped} entries";
      }
      if ($num_updated) {
          $this->log['notice'][] = "updated {$num_updated} entries";
      }
      $this->log['notice'][] = sprintf("imported %s entries", $num_imported, $exec_time);
      $this->log['stats']['entries'] = $num_imported;
      $this->log['stats']['exec_time'] = sprintf("%.2f", $exec_time);
      return $this->log;
    }

    function create_post($data) {
      $data = array_merge($this->defaults, $data);
      $new_post = array(
        'post_title'   => 'Quote from ' . convert_chars($data['author']),
        'post_content' => convert_chars($data['quote']),
        'post_status'  => 'publish',
        'post_type'    => 'thought',
      );
      $id = wp_insert_post($new_post);

      $this->set_focus_area($id, $data['focus_area']);
      update_post_meta($id, '_cmb2_author', $data['author']); 

      return $id;
    }
    
    // Add focus area
    function set_focus_area($id,$focus_area) {
      $cat_ids = array();
      foreach($this->focus_areas as $focus_area_obj) {
        if ($focus_area_obj->name == esc_html($focus_area))
          $cat_ids[] = (int)$focus_area_obj->term_id;
      } 
      wp_set_object_terms($id, $cat_ids, 'focus_area');
    }

}