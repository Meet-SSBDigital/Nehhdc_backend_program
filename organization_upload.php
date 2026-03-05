<?php
$servername = "192.168.1.245:3307";
$username   = "qrhandloom";
$password   = "qrhandloom";
$database   = "qrhandloom";

// Connect to database
$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Check if file is uploaded
if (isset($_FILES['logfile']) && $_FILES['logfile']['error'] === UPLOAD_ERR_OK) {
    $filename = $_FILES['logfile']['tmp_name'];
    $destinationDir = __DIR__ . "/logs/";

    // Create logs folder if not exist
    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    $destination = $destinationDir . basename($_FILES['logfile']['name']);

    if (move_uploaded_file($filename, $destination)) {
        echo "<h3>✅ File uploaded successfully!</h3>";

        $jsonContent = file_get_contents($destination);
        $dataArray = json_decode($jsonContent, true);

        if ($dataArray === null) {
            die("❌ Invalid JSON file!");
        }

        $successCount = 0;
        $errorLogs = [];
        $line_no = 0;

        foreach ($dataArray as $jsonData) {
            $line_no++;

            // Ensure this is an organization entry
            if (!isset($jsonData['organization/organizationid'])) {
                continue;
            }

            // Convert timestamps to MySQL datetime
             $createdate = !empty($jsonData['organization/createdate']) ? date('Y-m-d H:i:s', $jsonData['organization/createdate'] / 1000) : null;
            $updatedate = !empty($jsonData['organization/updatedate']) ? date('Y-m-d H:i:s', $jsonData['organization/updatedate'] / 1000) : null;
           
            $sql = "INSERT INTO organization
                (organizationid, state, district, department, city, type, organization_name, isactive, createdate, updatedate)
                VALUES (
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/organizationid'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/state'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/district'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/department'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/city'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/type'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['organization/organization_name'] ?? '') . "',
                    " . (int)($jsonData['organization/isactive'] ?? 0) . ",
                    " . ($createdate ? "'$createdate'" : "NULL") . ",
                    " . ($updatedate ? "'$updatedate'" : "NULL") . "
                )";

            if (mysqli_query($conn, $sql)) {
                $successCount++;
            } else {
                $errorLogs[] = "❌ Line $line_no insert failed: " . mysqli_error($conn);
            }
        }

        echo "<p>Inserted <b>$successCount</b> organization entries successfully.</p>";

        if (!empty($errorLogs)) {
            echo "<h4>Errors:</h4><ul>";
            foreach ($errorLogs as $err) {
                echo "<li>$err</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "❌ File upload failed!";
    }
} else {
    echo "❌ No file uploaded or file upload error!";
}
?>
