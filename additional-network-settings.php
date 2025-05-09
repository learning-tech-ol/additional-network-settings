<?php
/**
 * Plugin Name:       Additional Network Settings
 * Description:       Adds additional network settings such as hiding certain menu items from non-superadmins
 * Version:           1.0.1
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Steph
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       additional-network-settings
 */

 defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function dashboard_custom_scripts_and_styles() {                           
   wp_enqueue_style( 'network-admin-styles-css', plugin_dir_url( __FILE__) . 'css/style.css');
   wp_enqueue_style('dashicons');
}
add_action('admin_enqueue_scripts', 'dashboard_custom_scripts_and_styles');

//---------------------------------------------------------------------
// Adding MIME types
//---------------------------------------------------------------------

// Add mime types
function add_my_mime_types($mimes) {

  // mimes that only super admins can upload
  if (is_multisite() && is_super_admin()) {
    $mimes['json'] = 'application/json';
  }
  // other mimes can go here

  return $mimes;
}
add_filter('upload_mimes', 'add_my_mime_types');


// Just in case the above doesn't work with our wordpress setup
function my_file_types_fix($data, $file, $filename, $mimes) {
  
  // mimes that only super admins can upload
  if (is_multisite() && is_super_admin()) {
    
    // Check for JSON files
    if (substr($filename, -5) === '.json') {
        $data['ext'] = 'json';
        $data['type'] = 'application/json';
    }
  }
  // other mimes can go here
  
  return $data;
}
add_filter('wp_check_filetype_and_ext', 'my_file_types_fix', 10, 4);


//---------------------------------------------------------------------
// Quick tips admin page for super admin to reference special features
//---------------------------------------------------------------------

// Can add it to tools page if you want instead with add_management_page( $page_title, $menu_title, $capability, $menu_slug, $function)

// Add admin menu page for super admin
function quick_tips_admin_menu() {
  add_menu_page(
    'Quick Tips for Super Admin', // Page title
    'Quick Tips', // Menu title
    'manage_options', // Capability
    'super-admin-quick-tips', // Menu slug
    'quick_tips_admin_page', // Function to display the page content
    'dashicons-info-outline', // Icon
    100 // Position
  );
}
add_action('admin_menu', 'quick_tips_admin_menu');

// Display the admin page content
function quick_tips_admin_page() {
?>
  <div class="wrap main-container">
    <h1>Quick Tips for Superadmin</h1>
    <p>Quick documentation on multisite features. This page is only visible to superadmin users.<p>

    <details>
      <summary><h2>How to Update the Dashboard Announcements Block</h2></summary>
      <div class="details-inner">
        <p>You can add an announcement by adding a new blog post on site one and categorizing it as an "announcement". If you want it to have a high importance highlight, tag it as "alert".</p>
        <p>To update the "Getting Started" section, go to site one, and update the "Getting Started Documentation" side bar item on the dashboard there.</p>
      </div>
    </details>

    <details>
      <summary><h2>Special User Roles</h2></summary>
      <div class="details-inner">
        <p>Some plugins have their dashbord access revoked for all users except superadmin so that you can set up something for a user on their site without worrying. 
          To reenable that access to an admin level user, turn on the "members" plugin, create one the following user roles for whatever plugin you are trying to give 
          them access to, then assign that role to the user in addition to their current role. </p>
          <p>Will provide more detailed steps later</p>

          <table class="widefat">
          <thead>
            <tr >
              <th style="max-width: 100px">Role</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>ACF User</td>
              <td>Allows admin level user to see Advanced Custom Fields plugin on sidebar.</td>
            </tr>
            <!-- Add more roles here -->
          </tbody>
        </table>
      </div>
    </details>

    <details>
      <summary><h2>Helper Shortcodes</h2></summary>
      <div class="details-inner">
        <p>These shortcodes have been added to our WordPress install. You can use them on any page.</p>
        <table class="widefat">
          <thead>
            <tr >
              <th style="max-width: 100px">Shortcode</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td></td>
              <td></td>
            </tr>
            <!-- Add more shortcodes here -->
          </tbody>
        </table>
      </div>
    </details>
  </div>
<?php
}

//---------------------------------------------------------------------
// Remove certain plugin menu items from dashboard for non superadmins
//---------------------------------------------------------------------
function wpdocs_remove_menus(){

    
  if (is_super_admin()) {
      return;
  }

  // remove some plugins from dashboard if you aren't super admin.

  remove_menu_page( 'litespeed' );
  remove_menu_page( 'members' ) ;
  remove_menu_page( 'super-admin-quick-tips' );

  // allow custom members roles to see some plugins

  $user = wp_get_current_user();

  if ( !in_array( 'acf_user', (array) $user->roles ) ) {
    remove_menu_page( 'edit.php?post_type=acf-field-group' );
  }

  //one for toolset

  //one for gravity forms?

}
add_action( 'admin_menu', 'wpdocs_remove_menus', 999 );


