<?php

$file = "bldata.txt";


if (!file_exists($file)) {
    die("File not found.");
}

$jsonData = file_get_contents($file);

// If your file is not wrapped in [] array, uncomment this:
// $jsonData = "[" . rtrim($jsonData, ",") . "]";

$data = json_decode($jsonData, true);

if ($data === null) {
    die("Invalid JSON format.");
}

$totalBL = 0;
$statusCounts = [];

foreach ($data as $record) {

    // Check div_code
    if (isset($record["evidence_acceptancedetails/div_code"]) &&
        $record["evidence_acceptancedetails/div_code"] == "BL") {

        $totalBL++;

        // Count status also
        if (isset($record["evidence_acceptancedetails/status"])) {

            $status = $record["evidence_acceptancedetails/status"];

            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }

            $statusCounts[$status]++;
        }
    }
}

echo "<h3>Total Records for BL Division: $totalBL</h3>";

echo "<h3>Status Wise Count (BL Division):</h3>";

foreach ($statusCounts as $status => $count) {
    echo $status . " : " . $count . "<br>";
}




?>
