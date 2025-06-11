<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Form</title>
    <script src="assets/jQuery/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="assets/bootstrap-5.3.5/css/bootstrap.css" />

    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="manifest" href="assets/favicon/site.webmanifest">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">Reset Form</div>
                <div class="card-body">
                    <form id="resetForm">
                        <label class="form-label">Email for Reset</label>
                        <input type="email" name="email" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-warning w-100">Reset password</button>
                    </form>
                    <hr>
                    <a href="index.php">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#resetForm').submit(function(e) {
        e.preventDefault();
        $.post('reset_request.php', $(this).serialize(), function(res) {
            alert(res === 'sent' ? '✔ Reset-Email is sent' : '❗ Email not found');
        });
    });
</script>

</body>
</html>
