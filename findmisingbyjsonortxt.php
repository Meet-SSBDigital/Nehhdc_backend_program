<?php

/**
 * -----------------------------------------
 * CONFIG
 * -----------------------------------------
 */
$jsonFile = __DIR__ . '/nhdc342data.txt';
$csvFile  = __DIR__ . '/NHDC.csv';

/**
 * -----------------------------------------
 * READ JSON (.txt)
 * -----------------------------------------
 */
if (!file_exists($jsonFile)) {
    die("JSON file not found");
}

$jsonData = json_decode(file_get_contents($jsonFile), true);
if (!is_array($jsonData)) {
    die("Invalid JSON data");
}

$jsonQrValues = [];

foreach ($jsonData as $item) {
    if (!empty($item['productdetailsmaster/qrcodevalue'])) {
        $jsonQrValues[] = strtoupper(trim($item['productdetailsmaster/qrcodevalue']));
    }
}

/**
 * -----------------------------------------
 * READ CSV
 * -----------------------------------------
 */
if (!file_exists($csvFile)) {
    die("CSV file not found");
}

$csvQrValues = [];

$handle = fopen($csvFile, 'r');
if ($handle === false) {
    die("Unable to open CSV file");
}

// Read header
$header = fgetcsv($handle);

// Find qrvalue column index
$qrIndex = array_search('qrvalue', $header);
if ($qrIndex === false) {
    die("qrvalue column not found in CSV");
}

// Read rows
while (($row = fgetcsv($handle)) !== false) {
    if (!empty($row[$qrIndex])) {
        $csvQrValues[] = strtoupper(trim($row[$qrIndex]));
    }
}

fclose($handle);

/**
 * -----------------------------------------
 * COMPARE
 * -----------------------------------------
 */
$missingQrValues = array_values(array_diff($csvQrValues, $jsonQrValues));

/**
 * -----------------------------------------
 * OUTPUT
 * -----------------------------------------
 */
echo "<h2>Missing QR Values (CSV → Not in JSON)</h2>";

if (empty($missingQrValues)) {
    echo "<p style='color:green;'>No missing QR values found</p>";
} else {
    echo "<table border='1' cellpadding='6'>
            <tr>
                <th>#</th>
                <th>QR VALUE</th>
            </tr>";

    foreach ($missingQrValues as $i => $qr) {
        echo "<tr>
                <td>" . ($i + 1) . "</td>
                <td>{$qr}</td>
              </tr>";
    }
    echo "</table>";
}
