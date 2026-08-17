<?php

// Copy SQLite database to writable /tmp directory in serverless environment
if (!file_exists('/tmp/database.sqlite') && file_exists(__DIR__ . '/../database/database.sqlite')) {
    copy(__DIR__ . '/../database/database.sqlite', '/tmp/database.sqlite');
}

require __DIR__ . '/../public/index.php';