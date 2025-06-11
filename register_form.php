<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
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
                <div class="card-header text-center">Sign Up</div>
                <div class="card-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control mb-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control mb-2" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control mb-2" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <hr>
                    <p>Already have an account? <a href="index.php">Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        $.post('register.php', $(this).serialize(), function(res) {
            if (res === 'success') {
                alert('✅ Registered successfully!');
                location.href = 'index_recorder.php';
            } else {
                alert('⚠ User already exists!');
            }
        });
    });
</script>

<style>
    label::after {
        content: " *";
        color: #ff0000;
    }
</style>

</body>
</html>
