<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: 600;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Data Management System</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">OFAC Database</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Manage OFAC (Office of Foreign Assets Control) data records.</p>
                        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#ofacModal">
                            View/Search OFAC
                        </button>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadOfacModal">
                            Upload Excel
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">IIBS Database</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Manage IIBS (Integrated Intelligence-Based Screening) data records.</p>
                        <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#iibsModal">
                            View/Search IIBS
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadIibsModal">
                            Upload Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OFAC Modal -->
    <div class="modal fade" id="ofacModal" tabindex="-1" aria-labelledby="ofacModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ofacModalLabel">OFAC Database</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="ofacSearch" placeholder="Search OFAC records...">
                    </div>
                    <div id="ofacResults">
                        <!-- OFAC search results will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- IIBS Modal -->
    <div class="modal fade" id="iibsModal" tabindex="-1" aria-labelledby="iibsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="iibsModalLabel">IIBS Database</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="iibsSearch" placeholder="Search IIBS records...">
                    </div>
                    <div id="iibsResults">
                        <!-- IIBS search results will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload OFAC Modal -->
    <div class="modal fade" id="uploadOfacModal" tabindex="-1" aria-labelledby="uploadOfacModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadOfacModalLabel">Upload OFAC Excel File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadOfacForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ofacFile" class="form-label">Select Excel File</label>
                            <input type="file" class="form-control" id="ofacFile" name="excel_file" accept=".xlsx,.xls" required>
                        </div>
                        <div class="alert alert-info">
                            <small>Excel file should contain columns: SOURCE MEDIA, SOURCE MEDIA DATE, RESOLUTION, INDIVIDUAL/CORPORATION INVOLVED, FIRST NAME, MIDDLE NAME, LAST NAME, NAME EXT, CORPORATION NAME/FULLNAME, ALTERNATE NAME/ALIAS</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload IIBS Modal -->
    <div class="modal fade" id="uploadIibsModal" tabindex="-1" aria-labelledby="uploadIibsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadIibsModalLabel">Upload IIBS Excel File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadIibsForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="iibsFile" class="form-label">Select Excel File</label>
                            <input type="file" class="form-control" id="iibsFile" name="excel_file" accept=".xlsx,.xls" required>
                        </div>
                        <div class="alert alert-info">
                            <small>Excel file should contain columns: SOURCE MEDIA, SOURCE MEDIA DATE, RESOLUTION, INDIVIDUAL/CORPORATION INVOLVED, FIRST NAME, MIDDLE NAME, LAST NAME, NAME EXT, CORPORATION NAME/FULLNAME, ALTERNATE NAME/ALIAS</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load OFAC data when modal opens
            $('#ofacModal').on('shown.bs.modal', function() {
                loadOfacData();
            });

            // Load IIBS data when modal opens
            $('#iibsModal').on('shown.bs.modal', function() {
                loadIibsData();
            });

            // OFAC search functionality
            $('#ofacSearch').on('keyup', function() {
                var searchTerm = $(this).val();
                searchOfacData(searchTerm);
            });

            // IIBS search functionality
            $('#iibsSearch').on('keyup', function() {
                var searchTerm = $(this).val();
                searchIibsData(searchTerm);
            });

            // Upload OFAC form
            $('#uploadOfacForm').on('submit', function(e) {
                e.preventDefault();
                uploadExcel('ofac', this);
            });

            // Upload IIBS form
            $('#uploadIibsForm').on('submit', function(e) {
                e.preventDefault();
                uploadExcel('iibs', this);
            });
        });

        function loadOfacData() {
            $.ajax({
                url: 'api/search.php',
                method: 'GET',
                data: { type: 'ofac' },
                success: function(response) {
                    $('#ofacResults').html(response);
                }
            });
        }

        function loadIibsData() {
            $.ajax({
                url: 'api/search.php',
                method: 'GET',
                data: { type: 'iibs' },
                success: function(response) {
                    $('#iibsResults').html(response);
                }
            });
        }

        function searchOfacData(searchTerm) {
            $.ajax({
                url: 'api/search.php',
                method: 'GET',
                data: { type: 'ofac', search: searchTerm },
                success: function(response) {
                    $('#ofacResults').html(response);
                }
            });
        }

        function searchIibsData(searchTerm) {
            $.ajax({
                url: 'api/search.php',
                method: 'GET',
                data: { type: 'iibs', search: searchTerm },
                success: function(response) {
                    $('#iibsResults').html(response);
                }
            });
        }

        function uploadExcel(type, form) {
            var formData = new FormData(form);
            formData.append('type', type);

            $.ajax({
                url: 'api/upload.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert('File uploaded successfully!');
                        $('#upload' + type.charAt(0).toUpperCase() + type.slice(1) + 'Modal').modal('hide');
                        form.reset();
                    } else {
                        alert('Upload failed: ' + result.message);
                    }
                },
                error: function() {
                    alert('Upload failed. Please try again.');
                }
            });
        }
    </script>
</body>
</html>
