<?php
global $wpdb;

if(isset($_POST['input-id'])) {
  $quiz_id = $_POST['input-id'];
} else {
  $quiz_id = false;
}


if( $quiz_id ) {
  $guid = $_POST['input-guid'];
  $date = date('Y-m-d H:i:s');

  processQuizReport($quiz_id, $date, $wpdb);

  //NTH Check for update errors in DB and show gracefully to the user
  header("Location: " . get_site_url() . "/quiz-report?guid=" . $guid . "&quiz_report_updated=1" );
}

function processQuizReport($quiz_id, $date, $wpdb) {
  $wpdb->delete( 'enp_quiz_options', array( 'quiz_id' => $quiz_id, 'field' => 'report_ignored_ip_addresses'), array( '%d', '%s' ) );

  (isset($_POST['input-report-ip-addresses']) ? $report_ignored_ip_addresses = $_POST['input-report-ip-addresses'] : $report_ignored_ip_addresses = false);

  // Add new options
  $wpdb->insert( 'enp_quiz_options', array( 'quiz_id' => $quiz_id, 'field' => 'report_ignored_ip_addresses',
    'value' => $report_ignored_ip_addresses, 'create_datetime' => $date, 'display_order' => 0),
      array(
          '%d',
          '%s',
          '%s',
          '%s',
          '%d'));

}
?>
