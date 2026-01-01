<?php
$data = json_decode(file_get_contents("data.json"), true);
$search = $_GET['search'] ?? "";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Dashboard</h2>

<form method="get">
    <input name="search" placeholder="Cari title..." value="<?= htmlspecialchars($search) ?>">
    <button>Search</button>
</form>

<ul>
<?php foreach ($data as $item): ?>
    <?php if (stripos($item['title'], $search) !== false): ?>
        <li>
            <a href="<?= $item['link'] ?>" target="_blank">
                <?= $item['title'] ?>
            </a>
        </li>
    <?php endif; ?>
<?php endforeach; ?>
</ul>

</body>
</html>
