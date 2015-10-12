<?
/*
*   Create all the new tables for the database
*   For a plugin, we should probably prefix these with the prefix from wp-config.php
*
*/
global $wpdb;
$sql_enp_quiz = "CREATE TABLE `enp_quiz` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `quiz_type` varchar(25) NOT NULL,
  `question` varchar(255) NOT NULL,
  `create_datetime` datetime NOT NULL,
  `last_modified_datetime` datetime NOT NULL,
  `last_modified_user_id` bigint(20) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `locked` tinyint(4) NOT NULL,
  PRIMARY KEY (`ID`)
);";

$sql_enp_quiz_next = "CREATE TABLE `enp_quiz_next` (
  `enp_quiz_next` bigint(20) NOT NULL AUTO_INCREMENT,
  `curr_quiz_id` bigint(20) NOT NULL,
  `next_quiz_id` bigint(20) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`enp_quiz_next`)
);"; // ||KVB

$sql_enp_quiz_options = "CREATE TABLE `enp_quiz_options` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) NOT NULL,
  `field` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  `create_datetime` datetime NOT NULL,
  `display_order` int(10) NOT NULL,
  PRIMARY KEY (`ID`)
);";



$sql_enp_quiz_responses = "CREATE TABLE `enp_quiz_responses` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `quiz_id` bigint(20) NOT NULL,
  `quiz_option_id` bigint(20) NOT NULL,
  `quiz_option_value` varchar(255) NOT NULL,
  `correct_option_id` bigint(20) NOT NULL,
  `correct_option_value` varchar(255) NOT NULL,
  `is_correct` tinyint(4) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `response_datetime` datetime NOT NULL,
  `preview_response` tinyint(4) NOT NULL,
  PRIMARY KEY (`ID`)
);";

// require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// dbDelta( $sql_enp_quiz );
// require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// dbDelta( $sql_enp_quiz_options );
// require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// dbDelta( $sql_enp_quiz_responses );

?>
