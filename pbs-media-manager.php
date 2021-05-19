<?php

/*
Plugin Name: PBS Media Manager 
Description: Support functions of the PBS Media Manager API.
Author: William Tam / WNET
Version: 0.3 
*/

register_activation_hook(__FILE__ , 'pbs_media_manager_install');
register_deactivation_hook(__FILE__ , 'pbs_media_manager_uninstall');


# go get everything non-admin
require_once("classes/class-pbs-media-manager.php");

# instantiate the main class
$pluginobj =  new PBS_Media_Manager(__FILE__);


# Setup the settings page but only if admin
if (is_admin()) {
  require_once('classes/class-pbs-media-manager-settings.php');
  $settingsobj = new PBS_Media_Manager_Settings(__FILE__);
}

function pbs_media_manager_admin_notice__error() {
  if (!get_option('timezone_string')){
    $class = 'notice notice-error';
    $message = __( 'The PBS Media Manager plugin requires the timezone name to be set.  Visit the <a href=options-general.php>options page</a> and set it to something like "New York"', 'sample-text-domain' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
  }
}
add_action( 'admin_notices', 'pbs_media_manager_admin_notice__error' );

# decide whether to display windowed passport videos

# on install/activate
function pbs_media_manager_install() {
  // nothing for now
}


# on uninstall/deactivate
function pbs_media_manager_uninstall() {
  // nothing for now
}



