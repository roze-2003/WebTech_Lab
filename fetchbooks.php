<?php
header('Content-Type: application/json');

$conn = new mysqli('localhost', 'root', '', 'library');

// Check the connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$sql = "SELECT name FROM books";
$result = $conn->query($sql);

$books = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = ['name' => $row['name']];
    }
}

// Return book data as JSON
echo json_encode($books);

$conn->close();
?>