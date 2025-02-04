<?php
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

if (isset($_GET['query'])) {
    $query = $conn->real_escape_string($_GET['query']);
    $sql = "SELECT name FROM books WHERE name LIKE '%$query%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $books = [];
        while ($row = $result->fetch_assoc()) {
            $books[] = $row['name'];
        }
        echo json_encode(['found' => true, 'books' => $books]);
    } else {
        echo json_encode(['found' => false]);
    }
} else {
    echo json_encode(['found' => false]);
}

$conn->close();
?>
