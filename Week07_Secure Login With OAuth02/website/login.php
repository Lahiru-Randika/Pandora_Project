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

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    header("Location: dashboard.php");
    exit;
}

// If not logged in, show the login page
?>
<!doctype html>
<html lang="en">
<head>
    <title>Login with Google</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
</head>
<body class="img js-fullheight" style="background-image: url(images/bg.jpg);">
    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4 white-box">
                    <h2 class="heading-section">Welcome to Pandora</h2>
                    <h3 class="mb-4 text-center">Sign In</h3>
                    <form action="#" class="signin-form">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <input id="password-field" type="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="form-control btn btn-primary submit px-3">Sign In</button>
                        </div>
                    </form>
                    <p class="text-center">&mdash; Or Sign In With &mdash;</p>
                    <div class="form-group text-center">
                        <a href="<?php echo $client->createAuthUrl(); ?>" class="form-control google-btn btn btn-primary submit px-3">
                            <img src="images/google_PNG19635.png"/>Sign in with Google
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
