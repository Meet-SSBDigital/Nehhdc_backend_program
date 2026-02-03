<?php

$filePath = "meghalay02.json"; // Your JSON file path

if (!file_exists($filePath)) {
    die("File not found");
}

// Read JSON
$jsonContent = file_get_contents($filePath);
$data = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid JSON");
}

// Track unique devicecodes per state
$stateDevices = [];

foreach ($data as $row) {
    if (isset($row['state'], $row['devicecode'])) {
        $state = trim($row['state']);
        $device = trim($row['devicecode']);

        // Initialize state array if not exists
        if (!isset($stateDevices[$state])) {
            $stateDevices[$state] = [];
        }

        // Use devicecode as key to make it UNIQUE
        $stateDevices[$state][$device] = true;
    }
}

// Count unique devices per state
$finalCount = [];
foreach ($stateDevices as $state => $devices) {
    $finalCount[$state] = count($devices);
}

// Output
echo "<pre>";
foreach ($finalCount as $state => $count) {
    echo $state . " => " . $count . PHP_EOL;
}
echo "</pre>";
