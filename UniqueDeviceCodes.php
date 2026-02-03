<?php
// ------------------ CALL .NET WEBMETHOD API ------------------
$apiUrl = "http://localhost:63407/dashboard.aspx/GetIotActiveDeviceDataforphp";   // ← Change this to your real API URL

$payload = json_encode(new stdClass());

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json; charset=utf-8"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
curl_close($ch);

// Decode ASP.NET wrapper
$layer1 = json_decode($response, true);
if (isset($layer1['d'])) {
    $res = json_decode($layer1['d'], true);
} else {
    $res = $layer1;
}

if (!$res || !isset($res['statuscode']) || $res['statuscode'] != '200') {
    die("API Error: " . $response);
}

$data = $res['data'];

// ------------------ PROCESS DATA ------------------

// 1. Count how many times each device appears per state
$deviceCountsPerState = []; // device => [state => count]

foreach ($data as $item) {
    $state = trim($item['state']);
    $device = trim($item['devicecode']);
    if ($device == "" || $device == null) continue;

    if (!isset($deviceCountsPerState[$device])) {
        $deviceCountsPerState[$device] = [];
    }
    if (!isset($deviceCountsPerState[$device][$state])) {
        $deviceCountsPerState[$device][$state] = 0;
    }
    $deviceCountsPerState[$device][$state]++;
}

// 2. Assign each device to the state with maximum count
$stateDeviceMap = [];      // state => unique devices assigned
$multiStateDevices = [];   // device => count per state for devices appearing in multiple states

foreach ($deviceCountsPerState as $device => $counts) {

    // Track devices appearing in multiple states
    if (count($counts) > 1) {
        $multiStateDevices[$device] = $counts;
    }

    // Find state with max count
    $maxCount = max($counts);
    $candidateStates = [];
    foreach ($counts as $state => $c) {
        if ($c == $maxCount) $candidateStates[] = $state;
    }

    // Tie-breaker: first alphabetically
    sort($candidateStates, SORT_STRING);
    $assignedState = $candidateStates[0];

    // Assign device to that state
    if (!isset($stateDeviceMap[$assignedState])) $stateDeviceMap[$assignedState] = [];
    $stateDeviceMap[$assignedState][] = $device;
}

// Count unique devices per state
$stateCounts = [];
foreach ($stateDeviceMap as $state => $devices) {
    $stateCounts[$state] = count($devices);
}

// Sort states alphabetically
ksort($stateCounts);

// Chart & JSON
$chartLabels = json_encode(array_keys($stateCounts));
$chartValues = json_encode(array_values($stateCounts));
$multiStateJson = json_encode($multiStateDevices, JSON_PRETTY_PRINT);
?>

<!DOCTYPE html>
<html>
<head>
    <title>IOT Device Unique Count Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        body { font-family: Arial; background: #f3f3f3; padding: 20px; }
        .box { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 0 10px #ccc; margin-bottom: 30px; width: 900px; margin:auto; }
        h2 { text-align:center; }
        pre { background: #eee; padding: 15px; border-radius: 5px; overflow-x:auto; }
    </style>
</head>
<body>

<div class="box">
    <h2>State-wise Unique Device Count (Majority Assignment)</h2>
    <canvas id="stateChart" height="150"></canvas>
</div>

<div class="box">
    <h2>Devices Used in Multiple States</h2>
    <pre><?php echo $multiStateJson; ?></pre>
</div>

<script>
Chart.register(ChartDataLabels);

const labels = <?php echo $chartLabels ?>;
const values = <?php echo $chartValues ?>;

new Chart(document.getElementById('stateChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Unique Devices',
            data: values,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            legend: { display: false },
            datalabels: {
                anchor: 'end',
                align: 'top',
                font: { weight: 'bold', size: 14 }
            }
        },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>