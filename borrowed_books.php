<?php
header('Content-Type: application/json');

// Debugging: Log all cookies to check if they exist
file_put_contents('debug_log.txt', "Cookies received:\n" . print_r($_COOKIE, true), FILE_APPEND);

$borrowed_books = [];

foreach ($_COOKIE as $key => $value) {
    if (strpos($key, 'book_cookies') === 0) { 
        $borrowed_books[] = [
            'book' => ucfirst(str_replace('book_cookies', '', $key)),
            'borrowed_by' => htmlspecialchars($value) // Sanitize the borrower's name
        ];
    }
}

// Debugging: Log retrieved books
file_put_contents('debug_log.txt', "Borrowed books data:\n" . print_r($borrowed_books, true), FILE_APPEND);

echo json_encode($borrowed_books);
?>
