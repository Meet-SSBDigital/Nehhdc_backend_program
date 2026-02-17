<?php


$rfslNumbers = [
    "RFSL/EE/2026/TOX/0897",
    "RFSL/EE/2026/TOX/0888",
    "RFSL/EE/2026/TOX/0843",
    "RFSL/EE/2026/TOX/0834",
    "RFSL/EE/2026/TOX/0835",
    "RFSL/EE/2026/TOX/0833",
    "RFSL/EE/2026/TOX/0875",
    "RFSL/EE/2026/TOX/0874",
    "RFSL/EE/2026/TOX/1045",
    "RFSL/EE/2026/TOX/0980",
    "RFSL/EE/2026/TOX/0964",
    "RFSL/EE/2026/TOX/0864",
    "RFSL/EE/2026/TOX/1012",
    "RFSL/EE/2026/TOX/0960",
    "RFSL/EE/2026/TOX/0907",
    "RFSL/EE/2026/TOX/0921",
    "RFSL/EE/2026/TOX/0867",
    "RFSL/EE/2026/TOX/0925",
    "RFSL/EE/2026/TOX/0972",
    "RFSL/EE/2026/TOX/0852",
    "RFSL/EE/2026/TOX/0893",
    "RFSL/EE/2026/TOX/0899",
    "RFSL/EE/2026/TOX/0896",
    "RFSL/EE/2026/TOX/0869",
    "RFSL/EE/2026/TOX/0850",
    "RFSL/EE/2026/TOX/0923",
    "RFSL/EE/2026/TOX/0963",
    "RFSL/EE/2026/TOX/0791",
    "RFSL/EE/2026/TOX/0924",
    "RFSL/EE/2026/TOX/0929",
    "RFSL/EE/2026/TOX/0922",
    "RFSL/EE/2026/TOX/0908",
    "RFSL/EE/2026/TOX/0926",
    "RFSL/EE/2026/TOX/0721",
    "RFSL/EE/2026/TOX/0685",
    "RFSL/EE/2026/TOX/1064",
    "RFSL/EE/2026/TOX/1127",
    "RFSL/EE/2026/TOX/1033",
    "RFSL/EE/2026/TOX/0879",
    "RFSL/EE/2026/TOX/0880",
    "RFSL/EE/2026/TOX/0884",
    "RFSL/EE/2026/TOX/0862",
    "RFSL/EE/2026/TOX/0857",
    "RFSL/EE/2026/TOX/0842",
    "RFSL/EE/2026/TOX/1106",
    "RFSL/EE/2026/TOX/0913",
    "RFSL/EE/2026/TOX/0927",
    "RFSL/EE/2026/TOX/0910",
    "RFSL/EE/2026/TOX/0971",
    "RFSL/EE/2026/TOX/0912",
    "RFSL/EE/2026/TOX/0928"
];



$filePath = "casemasterdata.txt"; // your txt file path
$jsonData = file_get_contents($filePath);

// Step 3: Decode JSON
$dataArray = json_decode($jsonData, true);

if (!$dataArray) {
    die("Invalid JSON data");
}

// Step 4: Match and Create New Array
$output = [];

foreach ($dataArray as $item) {

    if (isset($item['case_master/case_id']) &&
        in_array($item['case_master/case_id'], $rfslNumbers)) {

        $output[] = [
            "_id"     => $item["_id"],
            "case_id" => $item["case_master/case_id"]
        ];
    }
}

// Print exactly like your required format
foreach ($output as $index => $row) {
    echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    // Add comma except last item
    if ($index < count($output) - 1) {
        echo ",";
    }

    echo "\n";
}
?>
