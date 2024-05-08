<?php

session_start();
if(isset($_SESSION['user_id'])) {
    include 'fetch-messages.php'; 
}

$_SESSION['user_id'] = 4; // Assuming $user_id contains the user's ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Define $user_id




?>
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
            <div class="groups-sidebar-item" >1-1</div>
            <div class="groups-sidebar-item" >Group</div>
            <a href="settings.html" class="groups-sidebar-item">Settings</a>
        </div>

        <div class="message-list-sidebar">
            
            <div class="message-list-sidebar-content">
                <p id="message-list-title">Messages</p>

            </div>
        </div>
        
        <div class="main-section">
            <div>
                <div class="topbar-section">
                    <p id="current-conversation-name"></p>
                    <div id="close-chat-button">X</div>
                </div>
                <hr class="divider">
            </div>
            
            <div id="chat-section" class="chat-section">
  
            </div>
            
            <div class="send-bar-section">
                <form id="send-message-form" action="send-message.php" method="post" onsubmit="sendMessage(event)">
                    <input type="hidden" name="chat_id" id="chat_id" value="1">
                    <input type="text" name="message" id="message" placeholder="Type your message...">
                    <button type="submit" id="send-message-button">Send message</button>
                </form>  
            </div>
        </div>
    </main>
    <script>

        // Call fetchChats function when the page loads
        <?php if(isset($user_id)): ?>
        fetchChats(<?php echo $user_id; ?>);
        <?php endif; ?>


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
            xhr.open("POST", "send-message.php", true);
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
            xhr.open('GET', 'fetch-chats.php?user_id=<?php echo $user_id; ?>', true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateChatUI(response.essages, <?php echo $user_id; ?>);

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

        function updateChatUI(response, userId) {
            var chatSection = document.getElementById("chat-section");
            chatSection.innerHTML = ''; // Clear existing messages

            if (response && response.messages && Array.isArray(response.messages)) {
                response.messages.forEach(function(message) {
                    var messageDiv = document.createElement("div");
                    var messageType = message.user_id == userId ? "outgoing" : "incoming";
                    messageDiv.classList.add("message-container", messageType);
                    var messageContent = messageDiv.appendChild(document.createElement("div"));
                    messageContent.classList.add(messageType + "-message");
                    messageContent.textContent = message.message; // assuming message field contains the message content

                    if (message.user_id == userId) {
                        var deleteBtn = document.createElement("button");
                        deleteBtn.textContent = "Delete";
                        deleteBtn.onclick = function() { deleteMessage(message.message_id); };
                        messageDiv.appendChild(deleteBtn);
                    }
                    chatSection.appendChild(messageDiv);
                });

                scrollToBottom(); // Ensure the newest messages are visible
            } else {
                console.error('Error: Messages array is undefined or not an array in the response.');
            }
        }



        // Function to fetch chats from the server
        function fetchChats(userId) {
            var chatListContainer = document.querySelector('.message-list-sidebar-content'); // Adjust selector based on your HTML structure
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch-chats.php?user_id=' + userId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateMessageListUI(response.chats, chatListContainer); // Update UI with fetched chats
                        } else {
                            console.error('Error fetching chats:', response.message);
                        }
                    } else {
                        console.error('Error fetching chats:', xhr.statusText);
                    }
                }
            };
            xhr.send();
        }


        // Function to update the message list UI with fetched chats
        function updateMessageListUI(chats, container) {
            container.innerHTML = ''; // Clear existing chat list

            // Iterate over each chat
            chats.forEach(function(chat) {
                var chatPreview = document.createElement('div');
                chatPreview.classList.add('chat-preview');
                
                // Set the data attribute to store the chat id
                chatPreview.dataset.chatId = chat.chat_id;

                var chatName = document.createElement('p');
                chatName.classList.add('chat-name');
                chatName.textContent = chat.chat_name;

                // Append chat name to the chat preview
                chatPreview.appendChild(chatName);
                
                // Add an event listener to load the chat messages when clicked
                chatPreview.addEventListener('click', function() {
                    loadChatMessages(chat.chat_id); // Call loadChatMessages function with chat id
                });

                // Append the chat preview to the container
                container.appendChild(chatPreview);
            });
        }


        function deleteMessage(messageId) {
            var formData = new FormData();
            formData.append('message_id', messageId);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete-message.php", true);
            xhr.onload = function () {
                if (this.status === 200) {
                    // Message deleted successfully
                    console.log("Message deleted");
                    fetchMessages(); // Refresh messages to reflect deletion
                } else {
                    // Handle errors, such as message not found or server error
                    console.error('Failed to delete message:', this.status);
                }
            };
            xhr.onerror = function () {
                console.error('Error during the AJAX request to delete the message.');
            };
            xhr.send(formData);
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

        // Function to load messages for a specific chat
        function loadChatMessages(chatId) {
            var chatSection = document.getElementById("chat-section");
            chatSection.innerHTML = ''; // Clear existing messages

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch-messages.php?chat_id=' + chatId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateChatUI(response.essages, <?php echo $user_id; ?>);
                            document.getElementById("current-conversation-name").textContent = response.chat_name; // Update the chat name

                            // Remove the 'selected-chat' class from all chat previews
                            var chatPreviews = document.querySelectorAll('.chat-preview');
                            chatPreviews.forEach(function(chatPreview) {
                                chatPreview.classList.remove('selected-chat');
                            });

                            // Add the 'selected-chat' class to the currently selected chat preview
                            var selectedChatPreview = document.querySelector('.chat-preview[data-chat-id="' + chatId + '"]');
                            if (selectedChatPreview) {
                                selectedChatPreview.classList.add('selected-chat');
                            }
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
        //     xhr.open("POST", "send-message.php", true); // URL remains send-message.php
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
    </script>
</body>
</html>
