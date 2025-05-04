<?php
require_once 'db_connect.php';
include 'header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>Dropdown Test Page</h1>
            <p>This page is for testing the dropdown functionality.</p>
            
            <div class="alert alert-info">
                <p><strong>Instructions:</strong></p>
                <ol>
                    <li>Check if the user dropdown in the header works when clicked</li>
                    <li>If it doesn't work, try clearing your browser cache and refreshing</li>
                    <li>Check the browser console for any JavaScript errors</li>
                </ol>
            </div>
            
            <h3>Test Bootstrap Dropdown</h3>
            <div class="dropdown mt-3">
                <button class="btn btn-primary dropdown-toggle" type="button" id="testDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Test Dropdown
                </button>
                <ul class="dropdown-menu" aria-labelledby="testDropdown">
                    <li><a class="dropdown-item" href="#">Action</a></li>
                    <li><a class="dropdown-item" href="#">Another action</a></li>
                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
