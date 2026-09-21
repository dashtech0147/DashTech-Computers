<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
requireAdmin();
require_once __DIR__ . '/../php/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { http_response_code(404); exit('File not found.'); }
$stmt = $pdo->prepare('SELECT attachment FROM bookings WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$attachment = (string) $stmt->fetchColumn();
if (strpos($attachment, 'uploads/') !== 0) { http_response_code(404); exit('File not found.'); }
$uploadDirectory = realpath(__DIR__ . '/../uploads');
$file = realpath(__DIR__ . '/../' . $attachment);
if (!$uploadDirectory || !$file || strpos($file, $uploadDirectory . DIRECTORY_SEPARATOR) !== 0 || !is_file($file)) { http_response_code(404); exit('File not found.'); }

header('Content-Type: application/octet-stream');
header('Content-Length: ' . filesize($file));
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('X-Content-Type-Options: nosniff');
readfile($file);
exit;
