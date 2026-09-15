<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'recruiter') {
    header('Location: ../view/login.php');
    exit();
}

if (!isset($_GET['resume']) || trim($_GET['resume']) === '') {
    http_response_code(400);
    exit('Resume file not specified.');
}

$resume_path = trim($_GET['resume']);
$base_dir = realpath(__DIR__ . '/..');

if ($base_dir === false) {
    http_response_code(500);
    exit('Unable to resolve project path.');
}

$normalized_resume = str_replace('\\', '/', $resume_path);
$sanitized_resume = preg_replace('#^/?(?:\.\./)+#', '', $normalized_resume);
$full_path = $base_dir . '/' . $sanitized_resume;
$resolved_full_path = realpath($full_path);

if ($resolved_full_path === false || strpos($resolved_full_path, $base_dir) !== 0) {
    http_response_code(404);
    exit('Resume file not found.');
}

if (!is_file($resolved_full_path)) {
    http_response_code(404);
    exit('Resume file not found.');
}

$mime_type = mime_content_type($resolved_full_path);
if ($mime_type === false) {
    $mime_type = 'application/octet-stream';
}

$file_name = basename($resolved_full_path);
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . filesize($resolved_full_path));

readfile($resolved_full_path);
exit();
