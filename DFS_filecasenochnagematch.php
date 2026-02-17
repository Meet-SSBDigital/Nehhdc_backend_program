<?php



$rfslNumbers = [
    "RFSL/EE/2026/TOX/0885",
    "RFSL/EE/2026/TOX/0866",
    "RFSL/EE/2026/TOX/0870",
    "RFSL/EE/2026/TOX/0811",
    "RFSL/EE/2026/TOX/0868",
    "RFSL/EE/2026/TOX/0881",
    "RFSL/EE/2026/TOX/0916",
    "RFSL/EE/2026/TOX/0901",
    "RFSL/EE/2026/TOX/0860",
    "RFSL/EE/2026/TOX/0882",
    "RFSL/EE/2026/TOX/0905",
    "RFSL/EE/2026/TOX/1104",
    "RFSL/EE/2026/TOX/0902",
    "RFSL/EE/2026/TOX/0757",
    "RFSL/EE/2026/TOX/0900",
    "RFSL/EE/2026/TOX/0795",
    "RFSL/EE/2026/TOX/0796",
    "RFSL/EE/2026/TOX/0771",
    "RFSL/EE/2026/TOX/0784",
    "RFSL/EE/2026/TOX/0788",
    "RFSL/EE/2026/TOX/0799",
    "RFSL/EE/2026/TOX/0798",
    "RFSL/EE/2026/TOX/0797",
    "RFSL/EE/2026/TOX/1031",
    "RFSL/EE/2026/TOX/1041",
    "RFSL/EE/2026/TOX/1004",
    "RFSL/EE/2026/TOX/1072",
    "RFSL/EE/2026/TOX/1102",
    "RFSL/EE/2026/TOX/1026",
    "RFSL/EE/2026/TOX/1030",
    "RFSL/EE/2026/TOX/0786",
    "RFSL/EE/2026/TOX/1122",
    "RFSL/EE/2026/TOX/1060",
    "RFSL/EE/2026/TOX/0877",
    "RFSL/EE/2026/TOX/0914",
    "RFSL/EE/2026/TOX/0803",
    "RFSL/EE/2026/TOX/0814",
    "RFSL/EE/2026/TOX/0808",
    "RFSL/EE/2026/TOX/0844",
    "RFSL/EE/2026/TOX/0904",
    "RFSL/EE/2026/TOX/0909",
    "RFSL/EE/2026/TOX/0855",
    "RFSL/EE/2026/TOX/0800",
    "RFSL/EE/2026/TOX/0895",
    "RFSL/EE/2026/TOX/0863",
    "RFSL/EE/2026/TOX/0805",
    "RFSL/EE/2026/TOX/0809",
    "RFSL/EE/2026/TOX/0810",
    "RFSL/EE/2026/TOX/0806",
    "RFSL/EE/2026/TOX/0812",
    "RFSL/EE/2026/TOX/0804",
    "RFSL/EE/2026/TOX/0859",
    "RFSL/EE/2026/TOX/0854",
    "RFSL/EE/2026/TOX/0801",
    "RFSL/EE/2026/TOX/0802",
    "RFSL/EE/2026/TOX/1339",
    "RFSL/EE/2026/TOX/1330",
    "RFSL/EE/2026/TOX/1337",
    "RFSL/EE/2026/TOX/1265",
    "RFSL/EE/2026/TOX/1268",
    "RFSL/EE/2026/TOX/1237",
    "RFSL/EE/2026/TOX/1267"
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
