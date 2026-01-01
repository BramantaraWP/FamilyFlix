<?php
$TOKEN = "2i1y7FrfXdnz7KipvnZ7hLMN7SX_6gRPgqaygwDY7sm2Xy9zC";

if ($_POST['token'] !== $TOKEN) {
    http_response_code(403);
    exit("Unauthorized");
}

$title = $_POST['title'] ?? '';
$link  = $_POST['link'] ?? '';

if (!$title || !$link) exit("Invalid data");

$data = json_decode(file_get_contents("data.json"), true);
$data[] = [
    "title" => htmlspecialchars($title),
    "link"  => htmlspecialchars($link)
];

file_put_contents("data.json", json_encode($data, JSON_PRETTY_PRINT));
echo "OK";
