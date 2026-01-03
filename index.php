<?php
// Ambil raw query string
$q = $_SERVER['QUERY_STRING'] ?? '';

// Kalau kosong → BLANK
if ($q === '' || $q === '=') {
    http_response_code(204);
    exit;
}

// Kalau format ?=link → buang "="
if ($q[0] === '=') {
    $link = substr($q, 1);
} else {
    $link = $q;
}

// basic validasi
if (!preg_match('#^https?://#i', $link)) {
    http_response_code(400);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Iframe Loader</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
html, body {
    margin: 0;
    width: 100%;
    height: 100%;
    background: #000;
    overflow: hidden;
}
iframe {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    border: none;
}
</style>
</head>
<body>

<iframe src="<?= htmlspecialchars($link, ENT_QUOTES) ?>"></iframe>

</body>
</html>
