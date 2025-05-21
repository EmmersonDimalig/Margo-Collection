<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body class="signup-page">
    <div class="signup-card">
        <div class="signup-back-button">
            <a href="login.php">←</a>
        </div>
        <h1 class="signup-title">Create an account</h1>
        <p class="signup-subtitle">Your journey starts today!</p>
        
        <form action="signup_validate.php" method="post">
            <div class="signup-form-group">
                <label for="firstname">Name</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            
            <div class="signup-form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="signup-form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="signup-form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" required>
            </div>

            <div class="signup-form-group">
                <label for="phone">Contact Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="signup-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="signup-form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="g-recaptcha" data-sitekey="6LdZRD8rAAAAADx8Rt8koCu-JNv7TthZTjE5gl_V" style="margin: 15px auto; display: block; width: 304px;"></div>

            <button type="submit" class="signup-button">Sign up</button>
            
            <p class="signup-login-link">Already have an account? <a href="login.php">Log in</a></p>
        </form>
    </div>
</body>
</html>
