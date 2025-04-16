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

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Handle logged-in user
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);

    // Fetch user profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    // Show greeting and logout option
    echo "
    <!DOCTYPE html>
    <html lang='en'>
        <head>
            <title>Login with Google</title>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap' rel='stylesheet'>
            <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'>
            <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='index.css'>
        </head>
        <body class='img js-fullheight' style='background-image: url(images/bg.jpg);'>
            <section class='ftco-section'>
                <div class='container'>
                    <div class='row justify-content-center'>
                        <div class='col-md-6 col-lg-4 white-box'>
                            <h2 class='welcome-message'>Hello, <span class='user-name'>$name</span>!</h2>
                            <p>Welcome to Pandora Company Limited</p>
                            <p class='user-email'>Your email is: <span class='email'>$email</span></p>
                            <div class='form-group text-center'>
                            <a href='?logout=true' class='btn btn-danger logout-btn'>Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
            <script src='https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js'></script>
            <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        </body>
    </html>";
    exit;
} else {
    // Redirect to login if not authenticated
    header('Location: login.php');
    exit;
}
?>
