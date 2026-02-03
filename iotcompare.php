<?php
// Read JSON files
$json1 = file_get_contents('02withoutstate.json');
$json2 = file_get_contents('02withstate.json');

$data1 = json_decode($json1, true);
$data2 = json_decode($json2, true);

$combined = array_merge($data1, $data2);

// Function to validate devicecode
function isValidDeviceCode($code) {
    $code = trim($code);
    if ($code === '' || $code === null) return false;
    if (filter_var($code, FILTER_VALIDATE_URL)) return false;
    return true;
}

// STEP 1: Count device usage per state
$deviceStateCount = [];

foreach ($combined as $item) {
    if (!isset($item['state'], $item['devicecode'])) continue;
    if (!isValidDeviceCode($item['devicecode'])) continue;

    $device = strtoupper(trim($item['devicecode']));
    $state = trim($item['state']);

    if (!isset($deviceStateCount[$device])) {
        $deviceStateCount[$device] = [];
    }
    if (!isset($deviceStateCount[$device][$state])) {
        $deviceStateCount[$device][$state] = 0;
    }
    $deviceStateCount[$device][$state]++;
}

// STEP 2: Lock each device to the state with max usage
$deviceLockedState = [];
foreach ($deviceStateCount as $device => $states) {
    // Find state with max usage
    arsort($states); // descending
    $maxState = key($states); // first state = max usage
    $deviceLockedState[$device] = $maxState;
}

// STEP 3: Count unique devices per locked state
$stateDeviceCount = [];
foreach ($deviceLockedState as $device => $state) {
    if (!isset($stateDeviceCount[$state])) {
        $stateDeviceCount[$state] = 0;
    }
    $stateDeviceCount[$state]++;
}

// Optional: sort descending
arsort($stateDeviceCount);

// ====================
// OUTPUT: Unique device count per locked state
// ====================
echo "<h3>✅ Unique Device Count Per State (Locked by Max Usage)</h3>";
echo "<ul>";
foreach ($stateDeviceCount as $state => $count) {
    echo "<li><strong>$state</strong> : $count devices</li>";
}
echo "</ul>";

// ====================
// OPTIONAL: Show original per-device usage for reference
// ====================
echo "<h3>📌 Device Usage Per State (Original Data)</h3>";
echo "<ul>";
foreach ($deviceStateCount as $device => $states) {
    $total = array_sum($states);
    $perState = [];
    foreach ($states as $state => $count) {
        $perState[] = "$state: $count";
    }
    echo "<li>$device : $total time(s) → " . implode(', ', $perState) . " (Locked to: {$deviceLockedState[$device]})</li>";
}
echo "</ul>";
?>
