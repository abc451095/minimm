<?php
session_start();

// Initialize user session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'user_'.uniqid(); // Generate unique user ID
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ads Cash Pro</title>
    <!-- Add Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>
    <script src="firebase-config.js"></script>
    <style>
        /* Previous CSS styles remain the same */
    </style>
</head>
<body>
    <div class="notification" id="notification">+10 Points Added!</div>

    <div class="profile">
        <div class="profile-img">
            <img src="https://ui-avatars.com/api/?name=Hasan&background=random" alt="Profile">
        </div>
        <h2>Name: <span id="user-name">Hasan</span></h2>
    </div>

    <table class="balance-table">
        <tr>
            <th>Total Balance</th>
            <th>Available Balance</th>
        </tr>
        <tr>
            <td id="total-balance">0</td>
            <td id="available-balance">0</td>
        </tr>
    </table>

    <button id="show-ads-btn" class="btn">Show Ads (+10 Points)</button>
    <a href="auto.php" class="btn btn-auto">Auto Ads</a>
    <button id="visit-ads-btn" class="btn btn-visit">Visit Ads (+10 Points)</button>

    <div class="withdrawal-form">
        <h3>Withdrawal</h3>
        <form id="withdrawal-form">
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" id="amount" name="amount" min="1" required>
            </div>
            <div class="form-group">
                <label for="method">Bkash or Nagad</label>
                <select id="method" name="method" required>
                    <option value="">Select Method</option>
                    <option value="Bkash">Bkash</option>
                    <option value="Nagad">Nagad</option>
                </select>
            </div>
            <div class="form-group">
                <label for="number">Enter Your Number</label>
                <input type="text" id="number" name="number" required>
            </div>
            <button type="submit" class="btn btn-withdraw">Withdraw</button>
        </form>
    </div>

    <div class="history">
        <h3>Withdrawal History</h3>
        <div id="withdrawal-history"></div>
    </div>

    <script>
        // Get current user ID
        const userId = '<?php echo $_SESSION['user_id']; ?>';
        
        // Reference to user data in Firebase
        const userRef = firebase.database().ref('users/' + userId);
        
        // Load user data
        userRef.on('value', (snapshot) => {
            const userData = snapshot.val() || {
                name: "Hasan",
                total_balance: 500,
                available_balance: 500,
                withdrawal_history: {}
            };
            
            // Update UI
            document.getElementById('user-name').textContent = userData.name;
            document.getElementById('total-balance').textContent = userData.total_balance;
            document.getElementById('available-balance').textContent = userData.available_balance;
            document.getElementById('amount').max = userData.available_balance;
            
            // Update withdrawal history
            const historyContainer = document.getElementById('withdrawal-history');
            historyContainer.innerHTML = '';
            
            if (!userData.withdrawal_history || Object.keys(userData.withdrawal_history).length === 0) {
                historyContainer.innerHTML = '<p>No withdrawal history yet.</p>';
            } else {
                const historyArray = Object.values(userData.withdrawal_history).reverse();
                historyArray.forEach(item => {
                    const historyItem = document.createElement('div');
                    historyItem.className = 'history-item';
                    historyItem.innerHTML = `
                        <p><strong>Date:</strong> ${item.date}</p>
                        <p><strong>Amount:</strong> ${item.amount}</p>
                        <p><strong>Method:</strong> ${item.method}</p>
                        <p><strong>Number:</strong> ${item.number}</p>
                        <p><strong>Status:</strong> ${item.status}</p>
                    `;
                    historyContainer.appendChild(historyItem);
                });
            }
        });
        
        // Show Ads button
        document.getElementById('show-ads-btn').addEventListener('click', function() {
            this.disabled = true;
            this.textContent = 'Processing...';
            
            userRef.transaction(userData => {
                if (userData) {
                    userData.total_balance = (userData.total_balance || 0) + 10;
                    userData.available_balance = (userData.available_balance || 0) + 10;
                }
                return userData;
            }).then(() => {
                showNotification('+10 Points Added!');
                this.disabled = false;
                this.textContent = 'Show Ads (+10 Points)';
            });
        });
        
        // Visit Ads button
        document.getElementById('visit-ads-btn').addEventListener('click', function() {
            this.disabled = true;
            this.textContent = 'Redirecting...';
            
            userRef.transaction(userData => {
                if (userData) {
                    userData.total_balance = (userData.total_balance || 0) + 10;
                    userData.available_balance = (userData.available_balance || 0) + 10;
                }
                return userData;
            }).then(() => {
                window.location.href = 'https://example.com/ads';
            });
        });
        
        // Withdrawal form
        document.getElementById('withdrawal-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const amount = parseInt(document.getElementById('amount').value);
            const method = document.getElementById('method').value;
            const number = document.getElementById('number').value;
            
            userRef.transaction(userData => {
                if (userData) {
                    if (amount > 0 && amount <= userData.available_balance) {
                        userData.available_balance -= amount;
                        
                        // Generate unique ID for withdrawal
                        const withdrawalId = 'wd_' + Date.now();
                        
                        userData.withdrawal_history = userData.withdrawal_history || {};
                        userData.withdrawal_history[withdrawalId] = {
                            date: new Date().toISOString(),
                            amount: amount,
                            method: method,
                            number: number,
                            status: 'Pending'
                        };
                    }
                }
                return userData;
            }).then(() => {
                showNotification('Withdrawal request submitted!');
                this.reset();
            });
        });
        
        function showNotification(message) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
