<?php
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$new_book = $_POST['new_book'];

$sql = "INSERT INTO `library`.`books` (`name`) VALUES ('$new_book')";

if ($conn->query($sql) === TRUE) {
    echo "Book added successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>