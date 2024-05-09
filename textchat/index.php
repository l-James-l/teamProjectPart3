<?php
session_start();
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
    <?php
    $currentPage = "chat";
    include "../src/header.php"; 
  
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Define $user_id
    ?>
    <main>
        <div class="groups-sidebar">
            <div class="groups-sidebar-item" >1-1</div>
            <div class="groups-sidebar-item" >Groups</div>
            <!-- <a href="settings.html" class="groups-sidebar-item">Settings</a> -->
        </div>
        <div class="message-list-sidebar-container">
            <div class="message-list-topbar">
                <p id="message-list-title">Messages</p>
                <p>+</p>
            </div>
            
            <div class="message-list-sidebar">
                    
                    <div class="message-list-sidebar-content">
                        

                    </div>
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
            
            <form id="send-message-form" action="send-message.php" method="post" onsubmit="sendMessage(event)">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="chat_id" id="chat_id" value="">
                <textarea name="message" id="message" placeholder="Type your message..." class="send-message-field-textarea"></textarea>

                <button type="submit" id="send-message-button">
                    <svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 115.4 122.88">
                        <title>up-arrow</title>
                        <path d="M24.94,67.88A14.66,14.66,0,0,1,4.38,47L47.83,4.21a14.66,14.66,0,0,1,20.56,0L111,46.15A14.66,14.66,0,0,1,90.46,67.06l-18-17.69-.29,59.17c-.1,19.28-29.42,19-29.33-.25L43.14,50,24.94,67.88Z"/>
                    </svg>
                </button>
            </form>

            <div id="editMessageModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form id="editMessageForm">
                        <input type="hidden" id="editMessageId">
                        <label for="editMessageText">Edit Message:</label>
                        <input type="text" id="editMessageText" name="editMessageText">
                        <button type="submit">Save</button>
                    </form>
                </div>
            </div>

        </div>
    </main>
    <script>

        // Call fetchChats function when the page loads
        <?php if(isset($user_id)): ?>
        fetchChats(<?php echo $user_id; ?>, false); // Fetch one-to-one chats by default
        fetchMessages();
        <?php endif; ?>

        document.addEventListener("DOMContentLoaded", function () {
            var oneToOneButton = document.querySelector('.groups-sidebar-item:nth-child(1)');
            var groupButton = document.querySelector('.groups-sidebar-item:nth-child(2)');

            // Attach event listener to the one-to-one button
            oneToOneButton.addEventListener('click', function () {
                if (<?php echo isset($user_id) ? 'true' : 'false'; ?>) {
                    fetchChats(<?php echo $user_id; ?>, false);  // Pass false to indicate 1-1 chats
                }
            });

            // Attach event listener to the group button
            groupButton.addEventListener('click', function () {
                if (<?php echo isset($user_id) ? 'true' : 'false'; ?>) {
                    fetchChats(<?php echo $user_id; ?>, true);  // Pass true to indicate group chats
                }
            });

            // Call fetchChats function with 1-1 chats as default
            if (<?php echo isset($user_id) ? 'true' : 'false'; ?>) {
                fetchChats(<?php echo $user_id; ?>, false);  // Fetch 1-1 chats by default
            }

            var chatSection = document.getElementById("chat-section");
            chatSection.scrollTop = chatSection.scrollHeight;

            var selectedChatId = localStorage.getItem('selectedChatId');
            if (selectedChatId) {
                loadChatMessages(selectedChatId);
                highlightSelectedChat(selectedChatId);
                fetchMessages();
            }

            var messageTextarea = document.getElementById("message");
            messageTextarea.addEventListener("keydown", function(event) {
                if (event.key === "Enter" && !event.shiftKey) {
                    event.preventDefault(); // Prevent default action (typically a new line)
                    sendMessage(event); // Call your sendMessage function
                }
            });
        });



        function sendMessage(event) {
            event.preventDefault(); // Prevent the default form submission

            var chatId = document.querySelector('.chat-preview.selected-chat').dataset.chatId;
            var message = document.getElementById("message").value;
            var userId = document.getElementById("user_id").value;

            // Basic validation
            if (!message.trim() || !userId || !chatId) {
                console.log("Message, user ID, or chat ID is empty.");
                return;
            }

            addMessageToChat(message, 'outgoing');
            scrollToBottom();

            // Construct the POST data
            var formData = new FormData();
            formData.append('chat_id', chatId);
            formData.append('message', message);
            formData.append('user_id', userId); // Add user ID to the form data

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

            fetchMessages();
            updateChatUI();
            // Get the selected chat ID from local storage
            var selectedChatId = localStorage.getItem('selectedChatId');
        
            // If a chat is selected, reload its messages to ensure it's up to date
            if (selectedChatId) {
                loadChatMessages(selectedChatId);
            }

        }

        function addMessageToChat(message, type) {
            var chatSection = document.querySelector(".chat-section");
            var messageDiv = document.createElement("div");
            messageDiv.classList.add("message-container", type);
            messageDiv.innerHTML = `<div class="${type}-message">${message}</div>`;
            chatSection.appendChild(messageDiv);

            // Add edit and delete buttons for outgoing messages
            if (type === 'outgoing') {
                var deleteBtnSVG = `<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0,0,256,256">
                <g fill="#ff0000" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                <g transform="scale(8.53333,8.53333)">
                <path d="M14.98438,2.48633c-0.55152,0.00862 -0.99193,0.46214 -0.98437,1.01367v0.5h-5.5c-0.26757,-0.00363 -0.52543,0.10012 -0.71593,0.28805c-0.1905,0.18793 -0.29774,0.44436 -0.29774,0.71195h-1.48633c-0.36064,-0.0051 -0.69608,0.18438 -0.87789,0.49587c-0.18181,0.3115 -0.18181,0.69676 0,1.00825c0.18181,0.3115 0.51725,0.50097 0.87789,0.49587h18c0.36064,0.0051 0.69608,-0.18438 0.87789,-0.49587c0.18181,-0.3115 0.18181,-0.69676 0,-1.00825c-0.18181,-0.3115 -0.51725,-0.50097 -0.87789,-0.49587h-1.48633c0,-0.26759 -0.10724,-0.52403 -0.29774,-0.71195c-0.1905,-0.18793 -0.44836,-0.29168 -0.71593,-0.28805h-5.5v-0.5c0.0037,-0.2703 -0.10218,-0.53059 -0.29351,-0.72155c-0.19133,-0.19097 -0.45182,-0.29634 -0.72212,-0.29212zM6,9l1.79297,15.23438c0.118,1.007 0.97037,1.76563 1.98438,1.76563h10.44531c1.014,0 1.86538,-0.75862 1.98438,-1.76562l1.79297,-15.23437z"></path>
                </g>
                </g>
                </svg>`;
                
                var deleteBtn = document.createElement("div");
                deleteBtn.innerHTML = deleteBtnSVG;
                deleteBtn.classList.add("delete-button");
                deleteBtn.onclick = function() { deleteMessage(messageDiv); };
                messageDiv.appendChild(deleteBtn);
                
                var editBtnSVG = '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 50 50"><path d="M 43.125 2 C 41.878906 2 40.636719 2.488281 39.6875 3.4375 L 38.875 4.25 L 45.75 11.125 C 45.746094 11.128906 46.5625 10.3125 46.5625 10.3125 C 48.464844 8.410156 48.460938 5.335938 46.5625 3.4375 C 45.609375 2.488281 44.371094 2 43.125 2 Z M 37.34375 6.03125 C 37.117188 6.0625 36.90625 6.175781 36.75 6.34375 L 4.3125 38.8125 C 4.183594 38.929688 4.085938 39.082031 4.03125 39.25 L 2.03125 46.75 C 1.941406 47.09375 2.042969 47.457031 2.292969 47.707031 C 2.542969 47.957031 2.90625 48.058594 3.25 47.96875 L 10.75 45.96875 C 10.917969 45.914063 11.070313 45.816406 11.1875 45.6875 L 43.65625 13.25 C 44.054688 12.863281 44.058594 12.226563 43.671875 11.828125 C 43.285156 11.429688 42.648438 11.425781 42.25 11.8125 L 9.96875 44.09375 L 5.90625 40.03125 L 38.1875 7.75 C 38.488281 7.460938 38.578125 7.011719 38.410156 6.628906 C 38.242188 6.246094 37.855469 6.007813 37.4375 6.03125 C 37.40625 6.03125 37.375 6.03125 37.34375 6.03125 Z"></path></svg>'
                var editBtn = document.createElement("div");
                editBtn.innerHTML = editBtnSVG;
                editBtn.classList.add("edit-button");
                editBtn.onclick = function() { editMessage(message.message_id, messageContent); };
                messageDiv.appendChild(editBtn);
            }
        }

        function fetchMessages() {
            var chatContainer = document.getElementById('chat-section');
            var xhr = new XMLHttpRequest();
            var lastMessageId = getLastMessageId(); // Implement a function to get the ID of the last displayed message
            xhr.open('GET', 'fetch-chats.php?user_id=<?php echo $user_id; ?>', true);

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateChatUI(response.messages, <?php echo $user_id; ?>);

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

        function updateChatUI(messages, userId) {
            var chatSection = document.getElementById("chat-section");
            chatSection.innerHTML = ''; // Clear existing messages

             // Debugging: Log the messages to the console
            console.log("Messages received:", messages);

            if (!Array.isArray(messages)) {
                console.error("Expected 'messages' to be an array, but received:", messages);
                return; 
            }

            messages.forEach(function(message) {
                var messageDiv = document.createElement("div");
                var messageType = message.user_id == userId ? "outgoing" : "incoming";
                messageDiv.classList.add("message-container", messageType);
                messageDiv.dataset.messageId = message.message_id;

                var messageTimestamp = document.createElement("div");
                messageTimestamp.classList.add("message-timestamp");
                messageTimestamp.textContent = formatTimestamp(message.timestamp); // Assuming 'timestamp' is the field name in the message object

                
                
                var messageContent = messageDiv.appendChild(document.createElement("div"));
                messageContent.classList.add(messageType + "-message");
                messageContent.textContent = message.message; // assuming message field contains the message content

                
                messageDiv.appendChild(messageContent);

                if (message.user_id == userId) {
                    var deleteBtnSVG = `<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0,0,256,256">
                    <g fill="#ff0000" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                    <g transform="scale(8.53333,8.53333)">
                    <path d="M14.98438,2.48633c-0.55152,0.00862 -0.99193,0.46214 -0.98437,1.01367v0.5h-5.5c-0.26757,-0.00363 -0.52543,0.10012 -0.71593,0.28805c-0.1905,0.18793 -0.29774,0.44436 -0.29774,0.71195h-1.48633c-0.36064,-0.0051 -0.69608,0.18438 -0.87789,0.49587c-0.18181,0.3115 -0.18181,0.69676 0,1.00825c0.18181,0.3115 0.51725,0.50097 0.87789,0.49587h18c0.36064,0.0051 0.69608,-0.18438 0.87789,-0.49587c0.18181,-0.3115 0.18181,-0.69676 0,-1.00825c-0.18181,-0.3115 -0.51725,-0.50097 -0.87789,-0.49587h-1.48633c0,-0.26759 -0.10724,-0.52403 -0.29774,-0.71195c-0.1905,-0.18793 -0.44836,-0.29168 -0.71593,-0.28805h-5.5v-0.5c0.0037,-0.2703 -0.10218,-0.53059 -0.29351,-0.72155c-0.19133,-0.19097 -0.45182,-0.29634 -0.72212,-0.29212zM6,9l1.79297,15.23438c0.118,1.007 0.97037,1.76563 1.98438,1.76563h10.44531c1.014,0 1.86538,-0.75862 1.98438,-1.76562l1.79297,-15.23437z"></path>
                    </g>
                    </g>
                    </svg>`;
                    
                    var deleteBtn = document.createElement("div");
                    deleteBtn.innerHTML = deleteBtnSVG;
                    deleteBtn.classList.add("delete-button");
                    deleteBtn.onclick = function() { deleteMessage(message.message_id); };
                    messageDiv.appendChild(deleteBtn);

                    var editBtnSVG = '<svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 50 50"><path d="M 43.125 2 C 41.878906 2 40.636719 2.488281 39.6875 3.4375 L 38.875 4.25 L 45.75 11.125 C 45.746094 11.128906 46.5625 10.3125 46.5625 10.3125 C 48.464844 8.410156 48.460938 5.335938 46.5625 3.4375 C 45.609375 2.488281 44.371094 2 43.125 2 Z M 37.34375 6.03125 C 37.117188 6.0625 36.90625 6.175781 36.75 6.34375 L 4.3125 38.8125 C 4.183594 38.929688 4.085938 39.082031 4.03125 39.25 L 2.03125 46.75 C 1.941406 47.09375 2.042969 47.457031 2.292969 47.707031 C 2.542969 47.957031 2.90625 48.058594 3.25 47.96875 L 10.75 45.96875 C 10.917969 45.914063 11.070313 45.816406 11.1875 45.6875 L 43.65625 13.25 C 44.054688 12.863281 44.058594 12.226563 43.671875 11.828125 C 43.285156 11.429688 42.648438 11.425781 42.25 11.8125 L 9.96875 44.09375 L 5.90625 40.03125 L 38.1875 7.75 C 38.488281 7.460938 38.578125 7.011719 38.410156 6.628906 C 38.242188 6.246094 37.855469 6.007813 37.4375 6.03125 C 37.40625 6.03125 37.375 6.03125 37.34375 6.03125 Z"></path></svg>'

                    var editBtn = document.createElement("div");
                    editBtn.innerHTML = editBtnSVG;
                    editBtn.classList.add("edit-button");
                    editBtn.onclick = function() { editMessage(message.message_id, messageContent); };
                    messageDiv.appendChild(editBtn);

                }
                chatSection.appendChild(messageTimestamp);
                chatSection.appendChild(messageDiv);

            });

            scrollToBottom(); // Ensure the newest messages are visible
        }

        // Function to format timestamp for display
        function formatTimestamp(timestamp) {
            var date = new Date(timestamp);
            var day = date.getDate();
            var month = date.getMonth() + 1; // Months are zero indexed
            var year = date.getFullYear();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // Handle midnight
            minutes = minutes < 10 ? '0' + minutes : minutes;
            var formattedTimestamp = month + '/' + day + '/' + year + ' ' + hours + ':' + minutes + ' ' + ampm;
            return formattedTimestamp;
        }


        function fetchChats(userId, isGroup) {
            var chatListContainer = document.querySelector('.message-list-sidebar-content'); // Adjust selector based on your HTML structure
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch-chats.php?user_id=' + userId + '&is_group=' + (isGroup ? '1' : '0'), true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateMessageListUI(response.chats, chatListContainer); // Update UI with fetched chats
                            // messageListSidebar.style.display = 'block';

                            // Fetch new messages for the first chat
                            if (response.chats.length > 0) {
                                fetchMessages(response.chats[0].chat_id); // Fetch messages for the first chat
                            }
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

        function getLastMessageId() {
            // Assuming your messages have a unique ID assigned to them, you can retrieve the ID of the last displayed message
            var lastMessageElement = document.querySelector(".chat-section .message-container:last-child");
            if (lastMessageElement) {
                return lastMessageElement.dataset.messageId; // Assuming the message ID is stored in a 'data-message-id' attribute
            } else {
                return null; // If no messages are displayed, return null
            }
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
                    
                    // Get the selected chat ID from local storage
                    var selectedChatId = localStorage.getItem('selectedChatId');
                    
                    // If a chat is selected, reload its messages to ensure it's up to date
                    if (selectedChatId) {
                        loadChatMessages(selectedChatId);
                    }
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


        function editMessage(messageId, messageDiv) {
            var messageText = messageDiv.textContent; 

            document.getElementById('editMessageText').value = messageText;
            document.getElementById('editMessageId').value = messageId;
            var modal = document.getElementById('editMessageModal');
            modal.style.display = "block";
            var span = document.getElementsByClassName("close")[0];

            span.onclick = function() {
                modal.style.display = "none";
            }
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
     
        document.getElementById('editMessageForm').onsubmit = function(event) {
            event.preventDefault(); // Prevent the default form submission

            var messageId = document.getElementById('editMessageId').value;
            var editedText = document.getElementById('editMessageText').value;

            if (!editedText.trim()) {
                console.log("Edited message is empty.");
                return;
            }

            var formData = new FormData();
            formData.append('message_id', messageId);
            formData.append('edited_message', editedText);

            // AJAX request to the server to update the message
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "edit-message.php", true);
            xhr.onload = function () {
                if (this.status === 200) {
                    console.log("Message edited successfully", this.responseText);
                    
                    // Update the UI with the new message text
                    var originalMessageDiv = document.querySelector(`[data-message-id="${messageId}"] .message-text`);
                    if (originalMessageDiv) {
                        originalMessageDiv.textContent = editedText; // Update text directly
                    }

                    // Close the modal
                    document.getElementById('editMessageModal').style.display = "none";
                    fetchMessages(); // Refresh messages to reflect deletion
                    
                    // Get the selected chat ID from local storage
                    var selectedChatId = localStorage.getItem('selectedChatId');
                    
                    // If a chat is selected, reload its messages to ensure it's up to date
                    if (selectedChatId) {
                        loadChatMessages(selectedChatId);
                    }
                } else {
                    console.error('Failed to edit message:', this.status, this.responseText);
                }
            };
            xhr.onerror = function () {
                console.error('Error during the AJAX request to edit the message.');
            };
            xhr.send(formData);
        };

        // Ensures that the chat section is scrolled to the bottom
        // when the page is loaded, making the latest messages visible.
        document.addEventListener("DOMContentLoaded", function () {
            var oneToOneButton = document.getElementById('1-1');
            var messageListSidebar = document.querySelector('.message-list-sidebar');

            // Attach event listener to the one-to-one button
            oneToOneButton.addEventListener('click', function() {
                if (<?php echo isset($user_id) ? 'true' : 'false'; ?>) {
                    fetchChats(<?php echo $user_id; ?>, false);  // Pass false to indicate 1-1 chats
                }
            });

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
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Response:', xhr.responseText); // Log the response
                        var response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            updateChatUI(response.messages, <?php echo $user_id; ?>);
                            document.getElementById("current-conversation-name").textContent = response.chat_name; // Update the chat name

                            // Remove the 'selected-chat' class from all chat previews
                            var chatPreviews = document.querySelectorAll('.chat-preview');
                            chatPreviews.forEach(function (chatPreview) {
                                chatPreview.classList.remove('selected-chat');
                            });

                            // Add the 'selected-chat' class to the currently selected chat preview
                            var selectedChatPreview = document.querySelector('.chat-preview[data-chat-id="' + chatId + '"]');
                            if (selectedChatPreview) {
                                selectedChatPreview.classList.add('selected-chat');
                            }

                            // Store the selected chat ID in local storage
                            localStorage.setItem('selectedChatId', chatId);
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

        // Function to highlight the selected chat preview
        function highlightSelectedChat(chatId) {
            var selectedChatPreview = document.querySelector('.chat-preview[data-chat-id="' + chatId + '"]');
            if (selectedChatPreview) {
                // Remove highlighting from all chat previews
                var chatPreviews = document.querySelectorAll('.chat-preview');
                chatPreviews.forEach(function (chatPreview) {
                    chatPreview.classList.remove('selected-chat');
                });

                // Add highlighting to the selected chat preview
                selectedChatPreview.classList.add('selected-chat');
            }
        }
    </script>
</body>
</html>
