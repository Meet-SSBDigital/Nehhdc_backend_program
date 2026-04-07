<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weaver Data Search</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        
        .form-container {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        input[type="text"], select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: #4facfe;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79, 172, 254, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4facfe;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .results {
            margin-top: 30px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid #4facfe;
        }
        
        .results h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .results pre {
            background: white;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 14px;
            border: 1px solid #e1e5e9;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #c33;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 25px;
            }
            
            .header h1 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧵 Weaver Data Search</h1>
            <p>Search for weaver information by organization and location</p>
        </div>
        
        <div class="form-container">
            <form method="POST" id="weaverForm">
                <div class="form-group">
                    <label for="state">State *</label>
                    <input type="text" id="state" name="state" value="Assam" required>
                </div>
                
                <div class="form-group">
                    <label for="district">District *</label>
                    <input type="text" id="district" name="district" value="Kamrup" required>
                </div>
                
                <div class="form-group">
                    <label for="department">Department *</label>
                    <input type="text" id="department" name="department" value="Department of Business Development" required>
                </div>
                
                <div class="form-group">
                    <label for="type">Type *</label>
                    <select id="type" name="type" required>
                        <option value="Organization">Organization</option>
                        <option value="Individual">Individual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city" value="Sualkuchi" required>
                </div>
                
                <div class="form-group">
                    <label for="organization">Organization *</label>
                    <input type="text" id="organization" name="organization" value="Marvella" required>
                </div>
                
                <div class="form-group">
                    <label for="weaverid">Weaver ID</label>
                    <input type="text" id="weaverid" name="weaverid" value="AS-53279">
                </div>
                
                <button type="submit" class="btn">🔍 Search Weaver Data</button>
            </form>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Fetching weaver data...</p>
            </div>
            
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="results">
                    <h3>📊 Search Results</h3>
                    <?php
                    if (isset($_POST['state']) && !empty($_POST['state'])) {
                        $curl = curl_init();
                        
                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://nec-mdoner.in/nehhdc/weavermaster.aspx/GetweaverData',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => http_build_query([
                                'state' => $_POST['state'],
                                'district' => $_POST['district'],
                                'department' => $_POST['department'],
                                'type' => $_POST['type'],
                                'city' => $_POST['city'],
                                'organization' => $_POST['organization'],
                                'weaverid' => $_POST['weaverid'] ?? ''
                            ]),
                            CURLOPT_HTTPHEADER => array(
                                'Content-Type: application/x-www-form-urlencoded'
                            ),
                        ));
                        
                        $response = curl_exec($curl);
                        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                        $error = curl_error($curl);
                        
                        curl_close($curl);
                        
                        if ($error) {
                            echo '<div class="error">';
                            echo '<strong>❌ Request Error:</strong><br>' . htmlspecialchars($error);
                            echo '</div>';
                        } elseif ($httpCode !== 200) {
                            echo '<div class="error">';
                            echo '<strong>❌ HTTP Error:</strong> ' . $httpCode;
                            echo '<br>Response: ' . htmlspecialchars(substr($response, 0, 500)) . '...';
                            echo '</div>';
                        } else {
                            echo '<pre>' . htmlspecialchars($response) . '</pre>';
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('weaverForm').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
        });
    </script>
</body>
</html>