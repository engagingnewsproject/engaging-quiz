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
    plugin_dir_path( __FILE__ ) .'init/',
    // plugin_dir_path( __FILE__ ) .'admin/settings/',
    // plugin_dir_path( __FILE__ ) .'front-end/functions/',
);

//TODO: Action Hook for plugin activate and include the pages create
// and database table create
if(1 === 2) {
  $classesDir[] = plugin_dir_path( __FILE__ ) .'activate/';
}

foreach ($classesDir as $directory) {
    foreach (glob($directory."*.php") as $filename){
        include $filename;
    }
}


?>
