<?php
$data = json_decode(file_get_contents("data.json"), true);
$id = $_GET['id'] ?? null;

if (!isset($data[$id])) {
    die("Video tidak ditemukan");
}

$video = $data[$id];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $video['title'] ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white min-h-screen p-6">

<a href="index.php" class="text-gray-400 hover:text-white">⬅ Kembali</a>

<h1 class="text-2xl font-bold my-4"><?= $video['title'] ?></h1>

<div class="max-w-4xl mx-auto">

  <video id="player"
         class="w-full rounded-lg bg-black"
         controls>
    <source src="<?= $video['link'] ?>" type="video/mp4">

    <!-- Subtitle (contoh VTT) -->
    <track
      label="English"
      kind="subtitles"
      srclang="en"
      src="https://raw.githubusercontent.com/PolyglotProgrammer/sample-vtt/main/sample.vtt"
      default>
  </video>

  <!-- Custom Settings -->
  <div class="flex gap-4 mt-4">
    <button onclick="changeSpeed(0.75)" class="btn">0.75x</button>
    <button onclick="changeSpeed(1)" class="btn">1x</button>
    <button onclick="changeSpeed(1.25)" class="btn">1.25x</button>
    <button onclick="changeSpeed(1.5)" class="btn">1.5x</button>
  </div>

</div>

<script>
const video = document.getElementById("player")

function changeSpeed(rate) {
  video.playbackRate = rate
}
</script>

<style>
.btn {
  background:#1f2937;
  padding:8px 14px;
  border-radius:8px;
}
.btn:hover {
  background:#374151;
}
</style>

</body>
</html>
