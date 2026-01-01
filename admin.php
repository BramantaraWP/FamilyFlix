<?php
session_start();

$admin_user = "admin";
$admin_pass = "12345";

if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Login gagal bro 😭";
    }
}

if (isset($_POST['upload']) && isset($_SESSION['admin'])) {
    $title = htmlspecialchars($_POST['title']);
    $link  = htmlspecialchars($_POST['link']);

    $data = json_decode(file_get_contents("data.json"), true);
    $data[] = ["title" => $title, "link" => $link];
    file_put_contents("data.json", json_encode($data, JSON_PRETTY_PRINT));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body>

<?php if (!isset($_SESSION['admin'])): ?>
    <h2>Login Admin</h2>
    <form method="post">
        <input name="username" placeholder="Username"><br><br>
        <input name="password" type="password" placeholder="Password"><br><br>
        <button name="login">Login</button>
    </form>
    <?= isset($error) ? $error : "" ?>
<?php else: ?>
    <h2>Upload Link</h2>
    <form method="post">
        <input name="title" placeholder="Judul" required><br><br>
        <input name="link" placeholder="https://example.com" required><br><br>
        <button name="upload">Upload</button>
    </form>
    <br>
    <a href="logout.php">Logout</a>
<?php endif; ?>

</body>
</html>
