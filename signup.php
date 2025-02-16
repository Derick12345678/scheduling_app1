<?php include('header1.php');
      include('config.php');
      include('signupcheck.php'); //Do i really need this? the location of the post is the same too...
?>
<body>
<div id="navbar">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </nav>
</div>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">Create Account</div>
                <div class="card-body">
                    <form method="POST" action="signupcheck.php">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="repeatpassword">Repeat password:</label>
                            <input type="password" name="repeatpassword" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block">Sign Up</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>