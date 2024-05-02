<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging Service</title>
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
                <!-- Your chat previews here -->
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
            
            <div class="chat-section" id="chat-section">
                <!-- Messages will be dynamically inserted here -->
            </div>

            <div class="send-bar-section">
                <form id="send-message-form" onsubmit="sendMessage(event)">
                    <input type="hidden" name="chat_id" id="chat_id" value="1"> <!-- Assuming chat ID 1 for now -->
                    <input type="text" name="message" id="message" placeholder="Type your message...">
                    <button type="submit">Send Message</button>
                </form>
            </div>
        </div>
    </main>
    <script>
        // Function to send message to server
        function sendMessage(event) {
            event.preventDefault(); // Prevent form submission
            var formData = new FormData(document.getElementById("send-message-form"));

            fetch("send-message2.php", {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    console.log("Message sent successfully");
                    document.getElementById("message").value = ""; // Clear input field
                    fetchMessages(); // Refresh chat to display the sent message
                } else {
                    console.error("Failed to send message");
                }
            })
            .catch(error => console.error("Error sending message:", error));
        }

        // Function to fetch messages from server
        function fetchMessages() {
            fetch("fetch-messages.php?chat_id=1") // Assuming chat ID 1 for now
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error("Failed to fetch messages");
                }
            })
            .then(data => {
                if (data.status === "success") {
                    updateChatUI(data.messages);
                    scrollToBottom();
                } else {
                    console.error("Error:", data.message);
                }
            })
            .catch(error => console.error("Error fetching messages:", error));
        }

        // Function to update chat UI with received messages
        function updateChatUI(messages) {
            var chatSection = document.getElementById("chat-section");
            chatSection.innerHTML = ""; // Clear existing messages
            messages.forEach(message => {
                var messageContainer = document.createElement("div");
                messageContainer.className = "message-container " + message.sender;
                messageContainer.innerHTML = `<div class="${message.sender}-message">${message.message_content}</div>`;
                chatSection.appendChild(messageContainer);
            });
        }

        // Function to scroll to the bottom of chat
        function scrollToBottom() {
            var chatSection = document.getElementById("chat-section");
            chatSection.scrollTop = chatSection.scrollHeight;
        }

        // Fetch messages when page loads
        document.addEventListener("DOMContentLoaded", fetchMessages);

        // Optionally, implement functionality to load messages for selected chat preview
    </script>
</body>
</html>
