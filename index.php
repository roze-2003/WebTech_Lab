<?php
session_start();

$conn = new mysqli("localhost", "root", "", "projectwebtechlab"); 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch books from the database
$book_query = "SELECT title FROM books WHERE available = 1"; // Only fetch available books
$book_result = $conn->query($book_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrow Management</title>
    <link rel="stylesheet" href="style.css">
</head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#search_button").click(function() {
            var query = $("#search_query").val().trim();

            if (query === "") {
                $("#search_results").html("<p>Please enter a book title.</p>");
                return;
            }

            $.ajax({
                url: "search_books.php",
                type: "GET",
                data: { query: query },
                dataType: "json",
                success: function(response) {
                    if (response.found) {
                        $("#search_results").html("<p>Book found </p>");
                    } else {
                        $("#search_results").html("<p>Book not found.</p>");
                    }
                },
                error: function() {
                    $("#search_results").html("<p>Error fetching data.</p>");
                }
            });
        });
    });
</script>

<body>
    <section class="container">
        <div class="leftcolumn">
            <h2>Used Tokens</h2>
            <div id="used-tokens" style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; background-color: #f9f9f9;">
            <?php
        // Read the token.json file
        $json = file_get_contents('token.json');
        $data = json_decode($json, true);

        // Check if tokens are present
        if (isset($data['tokens']) && is_array($data['tokens'])) {
            // Filter the used tokens
            $used_tokens = array_filter($data['tokens'], function($token) {
                return $token['used']; // Only include tokens marked as used
            });

            // Display the used tokens
            if (count($used_tokens) > 0) {
                foreach ($used_tokens as $token_data) {
                    echo htmlspecialchars($token_data['token']) . "<br>";
                }
            } else {
                echo "<p>No tokens have been used yet.</p>";
            }
        } else {
            echo "<p>No token data available.</p>";
        }
        ?>
            </div>
        </div>

        <div class="middle">
            <div class="top">
                <div class="box1">
                <img src="ID.png" alt="No ID" class="center">
                </div>
            </div>

            <div class="middlethree">
                <div class="box2">
                <div id="borrowed_books">
                    <script>function fetchBorrowedBooks() {
                    fetch('borrowed_books.php')
                        .then(response => response.json())
                        .then(data => {
                            let output = "<h3>Currently Borrowed Books:</h3>";
                            if (data.length > 0) {
                                data.forEach(book => {
                                    output += `<p><strong>Book:</strong> ${book.book} - <strong>Borrowed By:</strong> ${book.borrowed_by}</p>`;
                                });
                            } else {
                                output += "<p>No books are currently borrowed.</p>";
                            }
                            document.getElementById("borrowed_books").innerHTML = output;
                        });
                }
                    fetchBorrowedBooks();

                    setInterval(fetchBorrowedBooks, 10000);
                     </script>
                </div>

                </div>
                <div class="box2">
                    <h3>Search Books</h3>
                    <input type="text" id="search_query" placeholder="Enter book title" class="sbar">
                    <button id="search_button" class="sbutton">Search</button>
                    <div id="search_results"></div>
                    
                </div>
                <div class="box2">
                    <h3>ADD BOOKS</h3>
                    <form action="add_books.php" method="post">
                        
                        <input type="text" id="new_book" name="new_book" placeholder="Enter Book Name" required>
                        <input type="submit" value="Add Book">
                    </form>
                    </div>
            </div>

            <div class="bottom">
                <div class="box-container">
                    <!-- Form Box -->
                    <div class="box3type1">
                        <form id="borrow-form" action="process.php" method="POST">
                            <div class="form-group">
                                <label for="student-name">Enter Name:</label>
                                <input type="text" name="student-name" id="student-name" placeholder="Enter Name" required>
                            </div>

                            <div class="form-group">
                                <label for="student-id">Enter ID:</label>
                                <input type="text" name="student-id" id="student-id" placeholder="Enter ID" required>
                            </div>

                            <div class="form-group">
                                <label for="mail-id">Enter Email:</label>
                                <input type="email" name="mail-id" id="mail-id" placeholder="Enter Email" required>
                            </div>

                            <div class="form-group">
                            <label for="booklist">Choose Book You Want:</label>
                            <?php
                            $servername = "localhost"; // or your server name
                            $username = "root"; // your database username
                            $password = ""; // your database password
                            $dbname = "library"; // your database name

                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                 die("Connection failed: " . $conn->connect_error);
                             }

                            // Fetch books from the database
                            $sql = "SELECT id, name FROM books";
                            $result = $conn->query($sql);
                            echo '<select name="book" id="book" required>';
                            echo '<option value="select">Select</option>';

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                                }
                            } else {
                                echo '<option value="">No books available</option>';
                            }

                            echo '</select><br><br>';

                            // Close the connection
                            $conn->close();
                            ?>
                            </div>

                            <div class="form-group">
                                <label for="borrow-date">Borrow Date:</label>
                                <input type="date" name="borrow-date" id="borrow-date" required>
                            </div>

                            <div class="form-group">
                                <label for="return-date">Return Date:</label>
                                <input type="date" name="return-date" id="return-date" required>
                            </div>

                            <div class="form-group">
                                <label for="user-token">Enter Token:</label>
                                <input type="text" name="user-token" id="user-token" placeholder="Paste your token here" required>
                            </div>

                            <div class="form-group">
                                <input type="submit" form="borrow-form" value="Submit">
                            </div>
                        </form>
                    </div>

                    <div class="box3type2">
                        <div class="token-box">
                            <label for="token">Available Tokens:</label>
                            <?php
        
                            $json = file_get_contents('token.json');
                            $data = json_decode($json, true);


                            if (isset($data['tokens']) && is_array($data['tokens'])) {
                                $unused_tokens = array_filter($data['tokens'], function($token) {
                                    return !$token['used']; 
                                });

                                if (count($unused_tokens) > 0) {
                                    foreach ($unused_tokens as $token_data) {
                                        echo "<p>" . htmlspecialchars($token_data['token']) . "</p>";
                                    }
                                } else {
                                    echo "<p>No tokens available</p>";
                                }
                            } else {
                                echo "<p>No tokens available</p>";
                            }
                            ?>
                           
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="rightcolumn"></div>
    </section>
</body>
</html>
