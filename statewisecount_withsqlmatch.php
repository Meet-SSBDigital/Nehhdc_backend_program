<?php

$jsonFile = "csvforstate/Sikkim.json";
$csvFile  = "csvforstate/Sikkim.csv";

// =======================
// DATABASE CONNECTION
// =======================
$conn = new mysqli("localhost", "root", "", "qrhandloom_dumb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =======================
// CHECK FILES
// =======================
if (!file_exists($jsonFile)) {
    die("JSON file not found");
}

if (!file_exists($csvFile)) {
    die("CSV file not found");
}

// =======================
// READ JSON
// =======================
$jsonContent = file_get_contents($jsonFile);
$jsonData = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Invalid JSON");
}

// =======================
// STORE JSON MAC IDs
// =======================
$jsonDevices = [];
$jsonFullData = [];

foreach ($jsonData as $row) {

    if (isset($row['productdetailsmaster/devicecode'])) {

        $mac = strtoupper(trim($row['productdetailsmaster/devicecode']));
        $mac = str_replace(" ", "", $mac);

        $state    = $row['productdetailsmaster/state'] ?? '';
        $district = $row['productdetailsmaster/district'] ?? '';

        $jsonDevices[$mac] = true;

        $jsonFullData[$mac] = [
            'State'    => $state,
            'District' => $district,
            'Mac id'   => $mac
        ];
    }
}

// =======================
// READ CSV
// =======================
$csvDevices = [];
$csvFullData = [];

if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip header
    fgetcsv($handle);

    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {

        $mac = strtoupper(trim($row[3]));
        $mac = str_replace(" ", "", $mac);

        $csvDevices[$mac] = true;

        $csvFullData[$mac] = [
            'No'       => $row[0],
            'State'    => $row[1],
            'District' => $row[2],
            'Mac id'   => $mac
        ];
    }

    fclose($handle);
}

// =======================
// FIND REPORTS
// =======================
$notInJson = [];
$notInCsv = [];
$matched = [];


// CSV -> JSON compare
foreach ($csvDevices as $mac => $v) {

    if (!isset($jsonDevices[$mac])) {

        $notInJson[] = $csvFullData[$mac];

    } else {

        $matched[] = $csvFullData[$mac];
    }
}


// JSON -> CSV compare
foreach ($jsonDevices as $mac => $v) {

    if (!isset($csvDevices[$mac])) {

        // =========================
        // CHECK IN DATABASE
        // =========================
        $checkQuery = "
            SELECT *
            FROM devicemaster
            WHERE UPPER(REPLACE(macid,' ','')) = '$mac'
        ";

        $result = $conn->query($checkQuery);

        $dbStatus = "Not Found In DB";

        if ($result && $result->num_rows > 0) {
            $dbStatus = "Found In DB";
        }

        $rowData = $jsonFullData[$mac];
        $rowData['DB Status'] = $dbStatus;

        $notInCsv[] = $rowData;
    }
}

// =======================
// SUMMARY REPORT
// =======================
echo "<h2>MAC Comparison Report</h2>";

echo "
<table border='1' cellpadding='8' cellspacing='0'>
<tr>
    <th>Total CSV MACs</th>
    <th>Total JSON MACs</th>
    <th>Matched</th>
    <th>Not In JSON</th>
    <th>Not In CSV</th>
</tr>

<tr>
    <td>" . count($csvDevices) . "</td>
    <td>" . count($jsonDevices) . "</td>
    <td>" . count($matched) . "</td>
    <td>" . count($notInJson) . "</td>
    <td>" . count($notInCsv) . "</td>
</tr>
</table>";


// =======================
// NOT IN JSON
// =======================
echo "<br><h3>Devices Present In CSV But NOT In JSON</h3>";

if (count($notInJson) > 0) {

    echo "
    <table border='1' cellpadding='8' cellspacing='0'>
    <tr>
        <th>No.</th>
        <th>State</th>
        <th>District</th>
        <th>Mac ID</th>
    </tr>";

    foreach ($notInJson as $row) {

        echo "
        <tr>
            <td>{$row['No']}</td>
            <td>{$row['State']}</td>
            <td>{$row['District']}</td>
            <td>{$row['Mac id']}</td>
        </tr>";
    }

    echo "</table>";

} else {

    echo "No mismatch found.";
}


// =======================
// NOT IN CSV + DB CHECK
// =======================
echo "<br><h3>Devices Present In JSON But NOT In CSV</h3>";

if (count($notInCsv) > 0) {

    echo "
    <table border='1' cellpadding='8' cellspacing='0'>
    <tr>
        <th>State</th>
        <th>District</th>
        <th>Mac ID</th>
        <th>Database Status</th>
    </tr>";

    foreach ($notInCsv as $row) {

        echo "
        <tr>
            <td>{$row['State']}</td>
            <td>{$row['District']}</td>
            <td>{$row['Mac id']}</td>
            <td>{$row['DB Status']}</td>
        </tr>";
    }

    echo "</table>";

} else {

    echo "No extra JSON devices found.";
}


// =======================
// MATCHED
// =======================
echo "<br><h3>Matched Devices</h3>";

if (count($matched) > 0) {

    echo "
    <table border='1' cellpadding='8' cellspacing='0'>
    <tr>
        <th>No.</th>
        <th>State</th>
        <th>District</th>
        <th>Mac ID</th>
    </tr>";

    foreach ($matched as $row) {

        echo "
        <tr>
            <td>{$row['No']}</td>
            <td>{$row['State']}</td>
            <td>{$row['District']}</td>
            <td>{$row['Mac id']}</td>
        </tr>";
    }

    echo "</table>";

} else {

    echo "No matched devices.";
}

$conn->close();

?>