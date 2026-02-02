<?php

$filePath = "nehhdcdata.txt"; // your txt file path



$data = json_decode(file_get_contents($filePath), true);

// STEP 1: Track last occurrence of each device
$deviceLastState = [];

foreach ($data as $row) {

    if (
        empty($row['productdetailsmaster/devicecode']) ||
        empty($row['productdetailsmaster/state'])
    ) {
        continue;
    }

    $device = trim($row['productdetailsmaster/devicecode']);
    $state  = trim($row['productdetailsmaster/state']);

    // ❌ Ignore ALL
    if (strtoupper($device) === 'ALL') {
        continue;
    }

    // ❌ Ignore URLs / paths
    if (
        str_starts_with($device, 'http') ||
        str_contains($device, '/') ||
        str_contains($device, '\\')
    ) {
        continue;
    }

    // Timestamp priority
    $time = $row['productdetailsmaster/updateddate']
        ?? $row['productdetailsmaster/createddate']
        ?? 0;

    // Keep only the LATEST entry per device
    if (
        !isset($deviceLastState[$device]) ||
        $time > $deviceLastState[$device]['time']
    ) {
        $deviceLastState[$device] = [
            'state' => $state,
            'time'  => $time
        ];
    }
}

// STEP 2: Count device per final state
$stateCount = [];
$grandTotal = 0;

foreach ($deviceLastState as $info) {
    $stateCount[$info['state']] =
        ($stateCount[$info['state']] ?? 0) + 1;

    $grandTotal++;
}

// Sort by highest count
arsort($stateCount);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Final Device Usage Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f7;
            padding: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            max-width: 700px;
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
            background: #0d6efd;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 9px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f3f6ff;
        }
        .count {
            background: #198754;
            color: #fff;
            padding: 4px 10px;
            border-radius: 14px;
            font-size: 13px;
        }
        .total {
            background: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>📍 Device Final-State Report</h2>

    <table>
        <thead>
            <tr>
                <th>State</th>
                <th>Unique Devices (Last Used)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stateCount as $state => $count): ?>
                <tr>
                    <td><?= htmlspecialchars($state) ?></td>
                    <td><span class="count"><?= $count ?></span></td>
                </tr>
            <?php endforeach; ?>

            <tr class="total">
                <td>Grand Total</td>
                <td><?= $grandTotal ?></td>
            </tr>
        </tbody>
    </table>
</div>

</body>
</html>
