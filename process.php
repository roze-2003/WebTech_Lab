<?php
file_put_contents('debug_log.txt', "Form Data: " . print_r($_POST, true) . "\n", FILE_APPEND);


session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["student-name"]);
    $id = trim($_POST["student-id"]);
    $email = trim($_POST["mail-id"]);
    $book = trim($_POST["book"]);
    $borrow_date = $_POST["borrow-date"];
    $return_date = $_POST["return-date"];
    $token = trim($_POST["user-token"]);

    $errors = [];

    // Validate inputs
    if (empty($name) || !preg_match("/^[a-zA-Z. ]+$/", $name)) {
        $errors[] = "Name can only contain letters and dots.";
    }
    file_put_contents('debug_log.txt', "Name\n", FILE_APPEND);


    if (empty($id) || !preg_match("/^\\d{2}-\\d{5}-\\d$/", $id)) {
        $errors[] = "ID must follow the format XX-XXXXX-X.";
    }
    file_put_contents('debug_log.txt', "ID\n", FILE_APPEND);


    if (empty($email) || !preg_match("/^{$id}@student\\.aiub\\.edu$/", $email)) {
        $errors[] = "Email must follow the format Student ID@student.aiub.edu.";
    }
    file_put_contents('debug_log.txt', "mail\n", FILE_APPEND);


    if (empty($book) || $book === "select") {
        $errors[] = "You must select a book title.";
    }
    file_put_contents('debug_log.txt', "books\n", FILE_APPEND);


    // Check if the book is already borrowed (using cookies or tokens)
    $book_cookie = preg_replace('/[^a-zA-Z0-9_]/', '', strtolower($book)); 
    file_put_contents('debug_log.txt', "Book cookie\n", FILE_APPEND);


    // If the book has already been borrowed by someone else (via cookies)
    if (isset($_COOKIE[$book_cookie])) {
        $errors[] = "The book '$book' is currently borrowed by " . htmlspecialchars($_COOKIE[$book_cookie]) . ". Please wait until it is returned.";
    }
    file_put_contents('debug_log.txt', "Book Not availble\n", FILE_APPEND);


    if (empty($borrow_date) || empty($return_date)) {
        $errors[] = "Borrow and Return dates are required.";
    } else {
        $borrow_time = strtotime($borrow_date);
        $return_time = strtotime($return_date);
        $difference_in_days = ($return_time - $borrow_time) / (60 * 60 * 24);

        if ($difference_in_days <= 0) {
            $errors[] = "Return date must be later than borrow date.";
        }
        if ($difference_in_days > 10) {
            
            if (empty($token)) {
                $errors[] = "For borrowing more than 10 days, a valid token is required.";
            } else {
              
                $json = file_get_contents('token.json');
                $data = json_decode($json, true);

                $token_valid = false;
                $book_available = true;

                // Check if the token is valid and not used
                foreach ($data['tokens'] as &$token_data) {
                    if ($token_data['token'] === $token) {
                        if ($token_data['used']) {
                            $errors[] = "The token '$token' has already been used. Please use another token.";
                        } else {
                            // Check if the book is already borrowed by another user
                            if (!empty($token_data['book']) && $token_data['book'] === $book && $token_data['used']) {
                                $book_available = false;
                                $errors[] = "The book '$book' is already borrowed and cannot be taken.";
                            } else {
                                // Valid token: Do not mark it as used yet, we will do that after the validation
                                $token_valid = true;
                            }
                        }
                        break;
                    }
                }

                if (!$token_valid) {
                    $errors[] = "Invalid token provided. Please enter a valid token.";
                }

                if (!$book_available) {
                    $errors[] = "This book is already borrowed. Please wait until it's returned.";
                }

            }
        }
    }
    file_put_contents('debug_log.txt', "Borrow Time calc\n", FILE_APPEND);


    if (!empty($errors)) {
        // Return the user to the form with error messages
        $error_string = implode('|', $errors);
        file_put_contents('debug_log.txt', "Errors detected: " . print_r($errors, true) . "\n", FILE_APPEND);
        header("Location: index.php?errors=$error_string");
        exit;
    }
    file_put_contents('debug_log.txt', "No error\n", FILE_APPEND);

    $json = file_get_contents('token.json');
    $data = json_decode($json, true);

    foreach ($data['tokens'] as &$token_data) {
        if ($token_data['token'] === $token) {
            $token_data['used'] = true;
            $token_data['book'] = $book;
            $token_data['borrowed_by'] = $name; 
            break;
        }
    }

    file_put_contents('token.json', json_encode($data, JSON_PRETTY_PRINT));

    file_put_contents('debug_log.txt', "Update Token\n", FILE_APPEND);

    $_SESSION["name"] = $name;
    $_SESSION["idnum"] = $id;
    $_SESSION["email"] = $email;
    $_SESSION["book"] = $book;
    $_SESSION["borrow_date"] = $borrow_date;
    $_SESSION["return_date"] = $return_date;
    $_SESSION["token"] = $token;

    
    file_put_contents('debug_log.txt', "After session\n", FILE_APPEND);
  
    setcookie($book_cookie, $name, time() + (10 * 24 * 60 * 60), '/');

    file_put_contents('debug_log.txt', "after setting cookie\n", FILE_APPEND);

    header("Location: receipt.php");
    exit;

}