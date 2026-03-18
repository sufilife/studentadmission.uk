<?php
require_once __DIR__ . '/../../bootstrap.php';

use Google\Client;

$google = require __DIR__ . '/../../config/google.php';

$client = new Client();
$client->setClientId($google['client_id']);
$client->setClientSecret($google['client_secret']);
$client->setRedirectUri($google['redirect_uri']);
$client->addScope('email');
$client->addScope('profile');

header('Location: ' . $client->createAuthUrl());
exit;
