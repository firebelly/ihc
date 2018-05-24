<?php
/**
 * Division taxonomy
 */

namespace Firebelly\PostTypes\Division;

/**
 * Add capabilities to control permissions of Taxonomy via roles
 */
function add_capabilities() {
  $role_admin = get_role('administrator');
  $role_admin->add_cap('manage_divisions');
  $role_admin->add_cap('edit_divisions');
  $role_admin->add_cap('delete_divisions');
  $role_admin->add_cap('assign_divisions');
}
add_action('switch_theme', __NAMESPACE__ . '\add_capabilities');

// Custom taxonomy Divisions
register_taxonomy( 'division',
  array('program', 'post', 'thought', 'event'),
  array('hierarchical' => true, // if this is true, it acts like categories
    'labels' => array(
      'name' => 'Divisions',
      'singular_name' => 'Division',
      'search_items' =>  'Search Divisions',
      'all_items' => 'All Divisions',
      'parent_item' => 'Parent Division',
      'parent_item_colon' => 'Parent Division:',
      'edit_item' => 'Edit Division',
      'update_item' => 'Update Division',
      'add_new_item' => 'Add New Division',
      'new_item_name' => 'New Division',
    ),
    'show_admin_column' => true,
    'show_ui' => true,
    'query_var' => true,
    'capabilities' => array(
        'manage_terms' => 'manage_divisions',
        'edit_terms' => 'edit_divisions',
        'delete_terms' => 'delete_divisions',
        'assign_terms' => 'assign_divisions'
    ),
    'rewrite' => array(
      'slug' => 'division',
      'with_front' => false
    ),
  )
);
