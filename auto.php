<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Ads - Ads Cash Pro</title>
    <!-- Add Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
    <script src="firebase-config.js"></script>
    <style>
        /* Previous CSS styles remain the same */
    </style>
</head>
<body>
    <div class="container">
        <h1>Auto Ads</h1>
        <p>You're viewing auto ads. This page will refresh automatically.</p>
        
        <div class="points-added" id="points-added">
            Loading...
        </div>
        
        <p>Current Balance: <span id="current-balance">0</span> points</p>
        
        <div class="countdown" id="countdown">
            Next ad in: 18 seconds
        </div>
        
        <a href="index.php" class="back-btn">Back to Dashboard</a>
    </div>

    <script>
        const userId = '<?php echo $_SESSION['user_id']; ?>';
        const userRef = firebase.database().ref('users/' + userId);
        
        // Add points on page load
        userRef.transaction(userData => {
            if (userData) {
                userData.total_balance = (userData.total_balance || 0) + 10;
                userData.available_balance = (userData.available_balance || 0) + 10;
            }
            return userData;
        }).then(() => {
            document.getElementById('points-added').textContent = '+10 Points Added to Your Balance!';
        });
        
        // Update current balance display
        userRef.on('value', (snapshot) => {
            const userData = snapshot.val();
            if (userData) {
                document.getElementById('current-balance').textContent = userData.available_balance;
            }
        });
        
        // Countdown timer
        let seconds = 18;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownElement.textContent = `Next ad in: ${seconds} seconds`;
            
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
