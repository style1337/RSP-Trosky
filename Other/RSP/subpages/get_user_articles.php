
<?php
session_start();
require("connect.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit;
}

$user_id = mysqli_real_escape_string($spojeni, $_GET['id']);
$query = "SELECT COUNT(*) as count FROM troskopis_articles WHERE author_id = '$user_id'";
$result = mysqli_query($spojeni, $query);
$count = mysqli_fetch_assoc($result)['count'];

header('Content-Type: application/json');
echo json_encode(['articleCount' => $count]);
?>