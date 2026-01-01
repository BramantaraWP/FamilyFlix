<?php
$data = json_decode(file_get_contents("data.json"), true);
$search = $_GET['search'] ?? "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FamilyFlix</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen p-6">

<h1 class="text-3xl font-bold mb-6">🎬 FamilyFlix</h1>

<form method="get" class="mb-6">
  <input
    name="search"
    placeholder="Cari film..."
    value="<?= htmlspecialchars($search) ?>"
    class="w-full p-3 rounded bg-gray-800 outline-none"
  >
</form>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
<?php foreach ($data as $i => $item): ?>
  <?php if (stripos($item['title'], $search) !== false): ?>
    <a href="watch.php?id=<?= $i ?>"
       class="bg-gray-800 p-4 rounded hover:bg-gray-700 transition">
       <p class="font-semibold"><?= $item['title'] ?></p>
    </a>
  <?php endif; ?>
<?php endforeach; ?>
</div>

</body>
</html>
