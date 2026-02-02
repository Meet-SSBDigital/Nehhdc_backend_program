<?php

// FILE PATHS
$txtFile = "nehhdcdata.txt";
$csvFile = "Devicedata.csv";

// ==============================
// STEP 1: READ TXT (JSON FILE)
// ==============================
$data = json_decode(file_get_contents($txtFile), true);

// Store LAST used state per device
$deviceLast = [];

foreach ($data as $row) {

    if (
        empty($row['productdetailsmaster/devicecode']) ||
        empty($row['productdetailsmaster/state'])
    ) {
        continue;
    }

    $device = strtoupper(trim($row['productdetailsmaster/devicecode']));
    $state  = trim($row['productdetailsmaster/state']);

    // ❌ Ignore invalid devicecodes
    if (
        $device === 'ALL' ||
        str_contains($device, '/') ||
        str_starts_with($device, 'HTTP')
    ) {
        continue;
    }

    $time = $row['productdetailsmaster/updateddate']
        ?? $row['productdetailsmaster/createddate']
        ?? 0;

    // Keep only latest record
    if (
        !isset($deviceLast[$device]) ||
        $time > $deviceLast[$device]['time']
    ) {
        $deviceLast[$device] = [
            'state' => $state,
            'time'  => $time
        ];
    }
}

// Collect FINAL Meghalaya devices from TXT
$txtMeghalayaDevices = [];

foreach ($deviceLast as $device => $info) {
    if (strcasecmp($info['state'], 'Meghalaya') === 0) {
        $txtMeghalayaDevices[$device] = true;
    }
}

// ==============================
// STEP 2: READ CSV FILE
// ==============================
$csvDevices = [];

if (($handle = fopen($csvFile, "r")) !== false) {

    $header = fgetcsv($handle); // skip header

    while (($row = fgetcsv($handle)) !== false) {
        // CSV format: No, State, District, Mac id
        if (isset($row[1], $row[3]) && trim($row[1]) === 'Meghalaya') {

            $device = strtoupper(trim($row[3]));
            if ($device !== '') {
                $csvDevices[$device] = true;
            }
        }
    }
    fclose($handle);
}

// ==============================
// STEP 3: FIND MISSING DEVICES
// ==============================
$missingDevices = array_diff_key($csvDevices, $txtMeghalayaDevices);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Missing Meghalaya Devices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            max-width: 800px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #dc3545;
            color: #fff;
            padding: 10px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .count {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>🚨 Missing Devicecodes – Meghalaya</h2>

    <table>
        <thead>
            <tr>
                <th>Missing Devicecode (Mac ID)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($missingDevices) === 0): ?>
                <tr><td>No missing devicecodes 🎉</td></tr>
            <?php else: ?>
                <?php foreach ($missingDevices as $device => $_): ?>
                    <tr>
                        <td><?= htmlspecialchars($device) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="count">
        Total Missing: <?= count($missingDevices) ?>
    </div>
</div>

</body>
</html>
