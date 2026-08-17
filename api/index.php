<?php

// Copy SQLite file to writable runtime temp directory
if (!file_exists('/tmp/database.sqlite') && file_exists(__DIR__ . '/../database/database.sqlite')) {
    copy(__DIR__ . '/../database/database.sqlite', '/tmp/database.sqlite');
}

require __DIR__ . '/../public/index.php';