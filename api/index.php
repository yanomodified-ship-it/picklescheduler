<?php

// Direct view compilation and framework storage to Vercel's writable /tmp directory
$storagePath = '/tmp/storage';

if (!is_dir($storagePath)) {
    mkdir($storagePath . '/framework/views', 0755, true);
    mkdir($storagePath . '/framework/sessions', 0755, true);
    mkdir($storagePath . '/framework/cache', 0755, true);
}

// Ensure SQLite file exists in writable /tmp if using SQLite
if (!file_exists('/tmp/database.sqlite') && file_exists(__DIR__ . '/../database/database.sqlite')) {
    copy(__DIR__ . '/../database/database.sqlite', '/tmp/database.sqlite');
}

require __DIR__ . '/../public/index.php';