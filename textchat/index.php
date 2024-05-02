<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging Service</title>
    <!-- <link rel="stylesheet" href="stylesheets/messaging-styles-colour.css"> -->
    <link rel="stylesheet" href="stylesheets/messaging-styles.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-item">ANALYTICS</div>
        <div class="nav-item">CHAT</div>
        <div class="nav-circle"></div>
    </header>
    <main>
        <div class="groups-sidebar">
            <div class="groups-sidebar-item">1-1</div>
            <div class="groups-sidebar-item">Group</div>
            <a href="settings.html" class="groups-sidebar-item">Settings</a>
        </div>

        <div class="message-list-sidebar">
            <div class="message-list-sidebar-content">
                <p id="message-list-title">Messages</p>
                <?php
                // Include database connection
                include_once(__DIR__ . '/../src/db_connection.php');

                // Hardcoded chat_id and user_id
                $chat_id = 1;
                $user_id = 1;

                // Fetch messages from the database
                $sql = "SELECT * FROM chat_log WHERE chat_id = '$chat_id' ORDER BY timestamp DESC";
                $result = mysqli_query($conn, $sql);

                if ($result && mysqli_num_rows($result) > 0) {
                    // Loop through each message and display
                    while ($row = mysqli_fetch_assoc($result)) {
                        $sender_id = $row['sender_id'];
                        $message = $row['message'];
                        $message_class = ($sender_id == $user_id) ? 'outgoing' : 'incoming';
                        echo "<div class='chat-preview $message_class'>";
                        echo "<p class='chat-name'>$sender_id</p>";
                        echo "<p class='chat-preview-text'>$message</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No messages found</p>";
                }

                // Close database connection
                mysqli_close($conn);
                ?>
            </div>
        </div>

        <div class="main-section">
            <div>
                <div class="topbar-section">
                    <p id="current-conversation-name">John Doe</p>
                    <div id="close-chat-button">X</div>
                </div>
                <hr class="divider">
            </div>
            
            <div class="chat-section">
                <!-- Messages will be dynamically added here -->
            </div>
            
            <div class="send-bar-section">
                <form id="send-message-form" action="send-message2.php" method="post" onsubmit="sendMessage(event)">
                    <input type="hidden" name="chat_id" id="chat_id" value="1">
                    <input type="text" name="message" id="message" placeholder="Type your message...">
                    <button type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </main>
    <script>

        function sendMessage(event) {
            event.preventDefault(); // Prevent the default form submission

            var chatId = document.getElementById("chat_id").value;
            var message = document.getElementById("message").value;

            // Basic validation
            if (!message.trim()) {
                console.log("Message is empty.");
                return;
            }

            // Construct the POST data
            var formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('message', message);

            // Create and send an AJAX request to encrypt-message.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "encrypt-message.php", true); // Change URL to encrypt-message.php
            xhr.onload = function () {
                if (this.status === 200) {
                    console.log("Encryption successful.");
                    // Once encryption is done, send the encrypted message to send-message.php
                    var encryptedMessage = this.responseText;
                    console.log("Encrypted message: ", encryptedMessage);
                    sendEncryptedMessage(chatId, encryptedMessage);
                } else {
                    console.error('An error occurred during the AJAX request to encrypt-message.php');
                }
            };
            xhr.onerror = function () {
                console.error('An error occurred during the AJAX request to encrypt-message.php');
            };
            xhr.send(formData);

            // Clear the message input
            document.getElementById("message").value = '';
        }

        // Function to send the encrypted message to send-message.php
        function sendEncryptedMessage(chatId, encryptedMessage) {
            console.log("Sending encrypted message to send-message.php...");
            // Construct the POST data
            var formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('message', encryptedMessage);

            // Create and send an AJAX request to send-message.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "send-message.php", true); // URL remains send-message.php
            xhr.onload = function () {
                if (this.status === 200) {
                    console.log("Message sent successfully: ", this.responseText);
                    // You may want to call scrollToBottom() to scroll the chat into view.
                } else {
                    console.error('An error occurred during the AJAX request to send-message.php');
                }
            };
            xhr.onerror = function () {
                console.error('An error occurred during the AJAX request to send-message.php');
            };
            xhr.send(formData);
        }


        // Ensures that the chat section is scrolled to the bottom
        // when the page is loaded, making the latest messages visible.
        document.addEventListener("DOMContentLoaded", function () {
            var chatSection = document.querySelector(".chat-section");
            chatSection.scrollTop = chatSection.scrollHeight;
        });


        // Scrolls the chat section to the bottom, ensuring visibility
        // of the most recent messages. Call this function when a new
        // message is sent or received, or when a new chat is loaded.
        function scrollToBottom() {
            var chatSection = document.querySelector(".chat-section");
            chatSection.scrollTop = chatSection.scrollHeight;
        }
        
    </script>
</body>
</html>