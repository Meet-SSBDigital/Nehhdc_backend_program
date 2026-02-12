<?php


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

            // Ensure this is a usermaster entry
            if (!isset($jsonData['usermaster/userid'])) {
                continue;
            }

            // Convert timestamps to MySQL datetime
            $dob = !empty($jsonData['usermaster/dob']) ? date('Y-m-d', $jsonData['usermaster/dob'] / 1000) : null;
            $createdate = !empty($jsonData['usermaster/createdate']) ? date('Y-m-d H:i:s', $jsonData['usermaster/createdate'] / 1000) : null;
            $updatedate = !empty($jsonData['usermaster/updatedate']) ? date('Y-m-d H:i:s', $jsonData['usermaster/updatedate'] / 1000) : null;
            $approved_time = !empty($jsonData['usermaster/approved_time']) ? date('Y-m-d H:i:s', $jsonData['usermaster/approved_time'] / 1000) : null;
            $isdeleted = isset($jsonData['usermaster/isdeleted']) && $jsonData['usermaster/isdeleted'] === 'true' ? 1 : 0;

            $sql = "INSERT INTO usermaster
                (userid, state, district, department, city, role, weaverid, firstname, lastname, gender, dob, contactno, type, organization, password, userstatus, kyc_type, first_login, isdeleted, kyc_id, createdate, updatedate, approved_time, approvedby, email)
                VALUES (
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/userid'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/state'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/district'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/department'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/city'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/role'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/weaverid'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/firstname'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/lastname'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/gender'] ?? '') . "',
                    " . ($dob ? "'$dob'" : "NULL") . ",
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/contactno'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/type'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/organization'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/password'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/userstatus'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/kyc_type'] ?? '') . "',
                    " . (int)($jsonData['usermaster/first_login'] ?? 0) . ",
                    $isdeleted,
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/kyc_id'] ?? '') . "',
                    " . ($createdate ? "'$createdate'" : "NULL") . ",
                    " . ($updatedate ? "'$updatedate'" : "NULL") . ",
                    " . ($approved_time ? "'$approved_time'" : "NULL") . ",
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/approvedby'] ?? '') . "',
                    '" . mysqli_real_escape_string($conn, $jsonData['usermaster/email'] ?? '') . "'
                )";

            if (mysqli_query($conn, $sql)) {
                $successCount++;
            } else {
                $errorLogs[] = "❌ Line $line_no insert failed: " . mysqli_error($conn);
            }
        }

        echo "<p>Inserted <b>$successCount</b> usermaster entries successfully.</p>";

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
