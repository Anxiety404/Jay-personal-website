<?php
session_start();

if (isset($_POST['toggle_sound'])) {
    $_SESSION['sound_on'] = !($_SESSION['sound_on'] ?? false);
}

$timeout_duration = 10;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    $_SESSION['timeout'] = true;
    $_SESSION['authenticated'] = false;
} else {
    $_SESSION['LAST_ACTIVITY'] = time();
}

$password = "yuiop";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['password']) && strtolower($_POST['password']) === strtolower($password)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['timeout'] = false;
        $_SESSION['LAST_ACTIVITY'] = time();
        $message = "Access granted!";
    } else {
        $message = "Invalid password!";
    }
}

$images = glob("../assets/images/*.{jpg,png,gif,webp}", GLOB_BRACE);
$randomImage = !empty($images) ? $images[array_rand($images)] : ".../assets/images/placeholder.webp";

$mp3Files = glob("../assets/mp3/*.mp3");
$randomSong = !empty($mp3Files) ? $mp3Files[array_rand($mp3Files)] : "";

$showLoginForm = !($_SESSION['authenticated'] ?? false);
$showTimeoutMessage = ($_SESSION['timeout'] ?? false) && !$showLoginForm;

if ($_SESSION['authenticated'] ?? false) {
    header("Location: ../html/index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <img src="<?php echo htmlspecialchars($randomImage); ?>" alt="Random Image" class="img-fluid mb-3">

                <?php if ($showTimeoutMessage): ?>
                    <div class="alert alert-warning">Session timed out. Please log in again.</div>
                <?php endif; ?>

                <?php if ($showLoginForm): ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="password" class="form-label">password:</label>
                            <input type="password" class="form-control password" id="password" name="password" required maxlength="5">
                        </div>
                    </form>
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-success">You are logged in!</div>
                <?php endif; ?>

                <form method="POST" action="">
                    <button type="submit" name="toggle_sound" class="emoji-btn">
                        <?php echo ($_SESSION['sound_on'] ?? false) ? '🔊' : '🔇'; ?>
                    </button>
                </form>

                <?php if ($_SESSION['sound_on'] ?? false && $randomSong): ?>
                    <audio id="audioPlayer" controls autoplay>
                        <source src="<?php echo htmlspecialchars($randomSong); ?>" type="audio/mp3">
                    </audio>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>