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
                
                <div class="chat-preview selected-chat">
                    <p class="chat-name">John Doe</p>
                    <p class="chat-preview-text">Agreed. I'll prioritize the design revisions and coordinate with the design team to ensure we stay on track. Looking forward to a productive week!</p>
                </div>
        
                <div class="chat-preview">
                    <p class="chat-name">Alice Smith</p>
                    <p class="chat-preview-text">Hey! How's it going with the project timeline?</p>
                </div>
        
                <div class="chat-preview">
                    <p class="chat-name">Bob Johnson</p>
                    <p class="chat-preview-text">Sure thing! Let's catch up tomorrow at 2 PM.</p>
                </div>        
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
            
            <div id="chat-section" class="chat-section">
  
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

        // Call fetchMessages function when the page loads
        fetchMessages();

        function sendMessage(event) {
            event.preventDefault(); // Prevent the default form submission

            var chatId = document.getElementById("chat_id").value;
            var message = document.getElementById("message").value;

            // Basic validation
            if (!message.trim()) {
                console.log("Message is empty.");
                return;
            }

            addMessageToChat(message, 'outgoing');
            scrollToBottom();

            // Construct the POST data
            var formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('message', message);

            // Create and send an AJAX request to send-message.php
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "send-message2.php", true);
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

            // Clear the message input
            document.getElementById("message").value = '';
        }


        // function sendMessage(event) {
        //     event.preventDefault(); // Prevent the default form submission

        //     var chatId = document.getElementById("chat_id").value;
        //     var message = document.getElementById("message").value;

        //     // Basic validation
        //     if (!message.trim()) {
        //         console.log("Message is empty.");
        //         return;
        //     }

        //     addMessageToChat(message, 'outgoing');
        //     scrollToBottom();
    
        //     // Construct the POST data
        //     var formData = new FormData();
        //     formData.append('chat_id', chatId);
        //     formData.append('message', message);

        //     // Create and send an AJAX request to encrypt-message.php
        //     var xhr = new XMLHttpRequest();
        //     xhr.open("POST", "encrypt-message.php", true); // Change URL to encrypt-message.php
        //     xhr.onload = function () {
        //         if (this.status === 200) {
        //             console.log("Encryption successful.");
        //             // Once encryption is done, send the encrypted message to send-message.php
        //             var encryptedMessage = this.responseText;
        //             console.log("Encrypted message: ", encryptedMessage);
        //             sendEncryptedMessage(chatId, encryptedMessage);
        //         } else {
        //             console.error('An error occurred during the AJAX request to encrypt-message.php');
        //         }
        //     };
        //     xhr.onerror = function () {
        //         console.error('An error occurred during the AJAX request to encrypt-message.php');
        //     };
        //     xhr.send(formData);

        //     // Clear the message input
        //     document.getElementById("message").value = '';
        // }

        // // Function to send the encrypted message to send-message.php
        // function sendEncryptedMessage(chatId, encryptedMessage) {
        //     // Construct the POST data
        //     var formData = new FormData();
        //     formData.append('chat_id', chatId);
        //     formData.append('message', encryptedMessage);

        //     // Create and send an AJAX request to send-message.php
        //     var xhr = new XMLHttpRequest();
        //     xhr.open("POST", "send-message2.php", true); // URL remains send-message.php
        //     xhr.onload = function () {
        //         if (this.status === 200) {
        //             console.log("Message sent successfully: ", this.responseText);
        //             // You may want to call scrollToBottom() to scroll the chat into view.
        //         } else {
        //             console.error('An error occurred during the AJAX request to send-message.php');
        //         }
        //     };
        //     xhr.onerror = function () {
        //         console.error('An error occurred during the AJAX request to send-message.php');
        //     };
        //     xhr.send(formData);
        // }



        function addMessageToChat(message, type) {
            var chatSection = document.querySelector(".chat-section");
            var messageDiv = document.createElement("div");
            messageDiv.classList.add("message-container", type);
            messageDiv.innerHTML = `<div class="${type}-message">${message}</div>`;
            chatSection.appendChild(messageDiv);
        }


        function fetchMessages() {
            var chatContainer = document.getElementById('chat-section');
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch-messages.php?chat_id=1', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateChatUI(response.messages);
                        } else {
                            console.error('Error fetching messages:', response.message);
                        }
                    } else {
                        console.error('Error fetching messages:', xhr.statusText);
                    }
                }
            };
            xhr.send();
        }

        function updateChatUI(messages) {
            var chatSection = document.getElementById("chat-section");
            chatSection.innerHTML = ''; // Clear existing messages

            messages.forEach(function(message) {
                var messageDiv = document.createElement("div");
                var messageType = message.user_id == 1 ? "outgoing" : "incoming";
                messageDiv.classList.add("message-container", messageType);
                var messageContent = messageDiv.appendChild(document.createElement("div"));
                messageContent.classList.add(messageType + "-message");
                messageContent.textContent = message.message; // assuming message field contains the message content
                chatSection.appendChild(messageDiv);
            });

            scrollToBottom(); // Ensure the newest messages are visible
        }

        // Ensures that the chat section is scrolled to the bottom
        // when the page is loaded, making the latest messages visible.
        document.addEventListener("DOMContentLoaded", function () {
            var chatSection = document.getElementById("chat-section");
            chatSection.scrollTop = chatSection.scrollHeight;
        });

        // Scrolls the chat section to the bottom, ensuring visibility
        // of the most recent messages. Call this function when a new
        // message is sent or received, or when a new chat is loaded.
        function scrollToBottom() {
            var chatSection = document.getElementById("chat-section");
            chatSection.scrollTop = chatSection.scrollHeight;
        }

        
    </script>
</body>
</html>
