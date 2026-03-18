<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load ENV
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Global error handling (dev only)
if ($_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

// Common headers for API
header("X-Powered-By: Student Admission Portal");

