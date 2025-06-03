<?php
/**
 * Google Safe Browsing API Test File
 * 
 * This file tests the Google Safe Browsing API integration by checking URLs against
 * Google's database of known malicious sites.
 * 
 * Usage:
 * 1. Ensure ENP_GOOGLE_SAFE_BROWSING_API_KEY is defined in wp-config.php
 * 2. Move this file to your theme root and access this file through your browser:
 *    http://your-wordpress-site.com/wp-content/themes/engage-2-x/test-safe-browsing.php
 * 
 * Test URLs:
 * - http://malware.testing.google.test/testing/malware/ (should trigger MALWARE)
 * - http://phishing.testing.google.test/testing/phishing/ (should trigger SOCIAL_ENGINEERING)
 * - http://unwanted.testing.google.test/testing/unwanted/ (should trigger UNWANTED_SOFTWARE)
 * 
 * To add your own test URLs, modify the $test_urls array below.
 * 
 * API Response Format:
 * - Empty response: URL is safe
 * - Non-empty response: URL is unsafe, contains threat details:
 *   - threatType: Type of threat (MALWARE, SOCIAL_ENGINEERING, etc.)
 *   - platformType: Platform affected (ANY_PLATFORM)
 *   - threatEntryType: Type of threat entry (URL)
 *   - cacheDuration: How long the threat information is cached
 * 
 * @package Enp_quiz
 * @since 1.0.0
 */

// Load WordPress core
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Check if API key is defined
if (!defined('ENP_GOOGLE_SAFE_BROWSING_API_KEY')) {
    die('Error: ENP_GOOGLE_SAFE_BROWSING_API_KEY is not defined in wp-config.php');
}

// Test URLs (known malicious URLs for testing)
$test_urls = array(
    'http://malware.testing.google.test/testing/malware/',
    'http://phishing.testing.google.test/testing/phishing/',
    'http://unwanted.testing.google.test/testing/unwanted/',
	'http://eaous.com/community/how-much-do-you-really-know-about-christmas-2-791098'
);

// Function to check URL against Google Safe Browsing API
function check_url_safety($url) {
    $api_key = ENP_GOOGLE_SAFE_BROWSING_API_KEY;
    $api_url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . $api_key;
    
    $data = array(
        'client' => array(
            'clientId' => 'wordpress',
            'clientVersion' => '1.0.0'
        ),
        'threatInfo' => array(
            'threatTypes' => array('MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION'),
            'platformTypes' => array('ANY_PLATFORM'),
            'threatEntryTypes' => array('URL'),
            'threatEntries' => array(
                array('url' => $url)
            )
        )
    );

    // Initialize cURL
    $ch = curl_init($api_url);
    
    // Get the current URL for the referrer
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $referrer = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Referer: ' . $referrer
    ));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    
    // Execute the request
    $result = curl_exec($ch);
    
    // Check for errors
    if ($result === false) {
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        return array(
            'error' => 'cURL Error: ' . $error . ' (Error number: ' . $errno . ')',
            'request_data' => $data,
            'api_url' => $api_url,
            'referrer' => $referrer
        );
    }
    
    // Get HTTP status code
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check HTTP status code
    if ($http_code !== 200) {
        return array(
            'error' => 'HTTP Error: ' . $http_code,
            'response' => $result,
            'request_data' => $data,
            'api_url' => $api_url,
            'referrer' => $referrer
        );
    }

    return json_decode($result, true);
}

// Test each URL
echo "<h1>Google Safe Browsing API Test Results</h1>";
echo "<p>Using API Key: " . substr(ENP_GOOGLE_SAFE_BROWSING_API_KEY, 0, 8) . "...</p>";

foreach ($test_urls as $url) {
    echo "<h2>Testing URL: " . htmlspecialchars($url) . "</h2>";
    $result = check_url_safety($url);
    
    if (isset($result['error'])) {
        echo "<p style='color: red;'>Error: " . htmlspecialchars($result['error']) . "</p>";
        if (isset($result['request_data'])) {
            echo "<h3>Request Data:</h3>";
            echo "<pre>" . htmlspecialchars(json_encode($result['request_data'], JSON_PRETTY_PRINT)) . "</pre>";
        }
        if (isset($result['response'])) {
            echo "<h3>Response:</h3>";
            echo "<pre>" . htmlspecialchars($result['response']) . "</pre>";
        }
        if (isset($result['referrer'])) {
            echo "<h3>Referrer:</h3>";
            echo "<pre>" . htmlspecialchars($result['referrer']) . "</pre>";
        }
    } else if (empty($result)) {
        echo "<p style='color: green;'>URL is safe</p>";
    } else {
        echo "<p style='color: red;'>URL is unsafe!</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
} 