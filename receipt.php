<?php
session_start();


if (!isset($_SESSION["name"])) {
    echo "No data available. Please fill up the form.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Borrow Receipt</title>
    <link rel="stylesheet" href="receipt_style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
</head>
<body>
    <div class="receipt" id="receipt">
        <h2>Book Borrowing Receipt</h2>
        <canvas id="qr-code"></canvas><br> 
        <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION["name"]); ?></p>
        <p><strong>Student ID:</strong> <?php echo htmlspecialchars($_SESSION["idnum"]); ?></p>
        <p><strong>Student Email:</strong> <?php echo htmlspecialchars($_SESSION["email"]); ?></p>
        <p><strong>Book Title:</strong> <?php echo htmlspecialchars($_SESSION["book"]); ?></p>
        <p><strong>Borrow Date:</strong> <?php echo htmlspecialchars($_SESSION["borrow_date"]); ?></p>
        <p><strong>Return Date:</strong> <?php echo htmlspecialchars($_SESSION["return_date"]); ?></p>
        <p><strong>Token Number:</strong> <?php echo htmlspecialchars($_SESSION["token"]); ?></p>
        <button onclick="downloadReceipt()">Download Receipt</button>
    </div>
    

    <script>
        function generateQRCode() {
            const receiptData = `
                Name: <?php echo htmlspecialchars($_SESSION["name"]); ?>
                Student ID: <?php echo htmlspecialchars($_SESSION["idnum"]); ?>
                Student Email: <?php echo htmlspecialchars($_SESSION["email"]); ?>
                Book Title: <?php echo htmlspecialchars($_SESSION["book"]); ?>
                Borrow Date: <?php echo htmlspecialchars($_SESSION["borrow_date"]); ?>
                Return Date: <?php echo htmlspecialchars($_SESSION["return_date"]); ?>
                Token Number: <?php echo htmlspecialchars($_SESSION["token"]); ?>
            `;
            const qr = new QRious({
                element: document.getElementById('qr-code'),
                value: receiptData,
                size: 150
            });
        }
        generateQRCode();

        function downloadReceipt() {
            const receipt = document.getElementById("receipt");
            html2canvas(receipt).then(canvas => {
                const link = document.createElement("a");
                link.href = canvas.toDataURL("image/png");
                link.download = "receipt.png";
                link.click();
            });
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</body>
</html>