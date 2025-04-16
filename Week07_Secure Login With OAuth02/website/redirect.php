<?php
session_start();
require_once 'vendor/autoload.php'; // Include Composer autoload file

// Load configuration
$config = require 'config.php';

if (!$config) {
    die('Config file could not be loaded.');
}

// Initialize Google OAuth configuration
$clientID = $config['client_id'] ?? null;
$clientSecret = $config['client_secret'] ?? null;
$redirectUri = $config['redirect_uri'] ?? null;

if (!$clientID || !$clientSecret || !$redirectUri) {
    die('Google OAuth credentials are missing in config.php.');
}

// Create Google Client
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Check if the code exists from the OAuth process
if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        if (isset($token['error'])) {
            throw new Exception('Error fetching token: ' . $token['error_description']);
        }

        // Save the token to the session
        $_SESSION['access_token'] = $token;

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;

    } catch (Exception $e) {
        echo "Failed to log in: " . $e->getMessage();
        exit;
    }
} else {
    // Redirect to login if the code is not present (failure)
    header('Location: login.php');
    exit;
}
?>
