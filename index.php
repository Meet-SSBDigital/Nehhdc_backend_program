<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log Reader Uploads</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: Arial, sans-serif;
    }
    .upload-card {
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .upload-card:hover {
      transform: translateY(-5px);
    }
    .card-header {
      font-weight: bold;
      font-size: 1.2rem;
    }
    .upload-icon {
      font-size: 40px;
      color: #0d6efd;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h1 class="text-center mb-4">📤 Upload Center</h1>
    <div class="row g-4">

      <!-- Log File Upload -->
      <div class="col-md-4">
        <div class="card upload-card">
          <div class="card-header text-center bg-primary text-white">
            Upload Log File
          </div>
          <div class="card-body text-center">
            <div class="upload-icon">📝</div>
            <p class="text-muted">Upload a <strong>.txt</strong> log file for processing.</p>
            <form action="uploadlogs.php" method="post" enctype="multipart/form-data">
              <input type="file" class="form-control mb-3" name="logfile" accept=".txt" required>
              <button type="submit" class="btn btn-primary w-100">Upload & Insert</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Product Details Upload -->
      <div class="col-md-4">
        <div class="card upload-card">
          <div class="card-header text-center bg-success text-white">
            Upload Product Details
          </div>
          <div class="card-body text-center">
            <div class="upload-icon">📦</div>
            <p class="text-muted">Upload a <strong>.txt</strong> file for product details master.</p>
            <form action="productdetailsmaster.php" method="post" enctype="multipart/form-data">
              <input type="file" class="form-control mb-3" name="logfile" accept=".txt" required>
              <button type="submit" class="btn btn-success w-100">Upload & Insert</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Organization Upload -->
      <div class="col-md-4">
        <div class="card upload-card">
          <div class="card-header text-center bg-warning text-dark">
            Upload Organization Data
          </div>
          <div class="card-body text-center">
            <div class="upload-icon">🏢</div>
            <p class="text-muted">Upload a <strong>.txt</strong> file for organization  records.</p>
            <form action="organization_upload.php" method="post" enctype="multipart/form-data">
              <input type="file" class="form-control mb-3" name="logfile" accept=".txt" required>
              <button type="submit" class="btn btn-warning w-100">Upload Org</button>
            </form>
          </div>
        </div>
      </div>

    </div>

    <!-- Bottom Navigation -->
   
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
