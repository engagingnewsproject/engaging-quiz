<?php
   /*
   Plugin Name: Engaging Quiz Tool
   Description: A plugin for allowing users to create and share quizzes.
   Version: 1.8
   Author: Engaging News Project
   Author URI: http://engagingnewsproject.org
   License: ASK US
   */

// Disallows this file to be accessed via a web browser
if ( ! defined( 'WPINC' ) ) {
    die;
}

// for registering, enqueuing relative to root plugin dir
function get_root_plugin_path() {
  $path = plugin_dir_path( __FILE__ );
  return $path;
}

// for registering, enqueuing relative to root plugin url
function get_root_plugin_url() {
  $url = plugin_dir_url( __FILE__ );
  return $url;
}


//Automatically Load all the PHP files we need
$classesDir = array (
    plugin_dir_path( __FILE__ ) .'install/',
    plugin_dir_path( __FILE__ ) .'init/',
    // plugin_dir_path( __FILE__ ) .'admin/settings/',
    // plugin_dir_path( __FILE__ ) .'front-end/functions/',
);

foreach ($classesDir as $directory) {
    foreach (glob($directory."*.php") as $filename){
        include $filename;
    }
}

// from install/create-tables.php
register_activation_hook(__FILE__, 'create_enp_quiz_tables');


?>
