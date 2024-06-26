<?php
// session_start();

// if (!isset($_SESSION["user_id"])) {
//     header("location: login.php");
// }

$currentPage = "textchat"; 
include "../src/header.php"; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messaging Service</title>
    <!-- <link rel="stylesheet" href="stylesheets/messaging-styles-colour.css"> -->
    <link rel="stylesheet" href="stylesheets/messaging-styles-colour.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/async_handlers.js"></script>
</head>
<body>
    <main>

        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="?lf=projects" class="nav-link <?php echo $currentPage == "projects" ? "active" : "link-dark" ?>" aria-current="page">
                    <i class="bi bi-folder-fill"></i>
                    Projects
                </a>
            </li>
            <li>
                <a href="?lf=users" class="nav-link <?php echo $currentPage == "users" ? "active" : "link-dark" ?>">
                    <i class="bi bi-people-fill"></i>
                    Users
                </a>
            </li>
        </ul>
        <hr>
        </div>

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
        
                <div class="chat-preview">
                    <p class="chat-name">Emily White</p>
                    <p class="chat-preview-text">I've shared the latest report with you. Take a look when you have time.</p>
                </div>
                <div class="chat-preview">
                    <p class="chat-name">Sarah Brown</p>
                    <p class="chat-preview-text">Just wanted to update you on the client meeting scheduled for next week. Everything is set, and we're ready to go!</p>
                </div>
        
                <div class="chat-preview">
                    <p class="chat-name">Michael Johnson</p>
                    <p class="chat-preview-text">Any updates on the budget proposal? Let's discuss it in our next team meeting.</p>
                </div>
                <div class="chat-preview">
                    <p class="chat-name">Alexandra Davis</p>
                    <p class="chat-preview-text">Just received feedback from the client on the latest design mockups. They're impressed with the changes and have a few minor suggestions. Will share the details in our design review meeting.</p>
                </div>
                
                <div class="chat-preview">
                    <p class="chat-name">Christopher White</p>
                    <p class="chat-preview-text">Quick reminder: team training session tomorrow at 10 AM. Please review the materials shared earlier to make the most of the session.</p>
                </div>
                
                <div class="chat-preview">
                    <p class="chat-name">Emily Rodriguez</p>
                    <p class="chat-preview-text">Important: The deadline for submitting project milestones is approaching. Make sure all team members are on track to meet their targets.</p>
                </div>
                
                <div class="chat-preview">
                    <p class="chat-name">Daniel Smith</p>
                    <p class="chat-preview-text">Great news! Our social media campaign is gaining traction, and engagement has increased by 20% this week. Let's discuss strategies to maintain this momentum in our marketing meeting.</p>
                </div>
                
                <div class="chat-preview">
                    <p class="chat-name">Olivia Taylor</p>
                    <p class="chat-preview-text">Reminder: HR has scheduled individual performance reviews for next week. Please prepare any self-assessment or achievements you'd like to discuss during the meeting.</p>
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
            
            <div class="chat-section">
                <div class="message-container incoming">
                    <div class="incoming-message">Good morning! Have you had a chance to review the project proposal?</div>
                </div>
                <div class="message-container outgoing">
                    <div class="outgoing-message">Yes, I went through it yesterday. Overall, it looks solid. Do you have any specific points you'd like to discuss?</div>
                </div>
                <div class="message-container incoming">
                    <div class="incoming-message">I'm glad to hear that! I wanted to get your input on the budget allocation for marketing. Do you think we should allocate more funds there?</div>
                </div>
                <div class="message-container outgoing">
                    <div class="outgoing-message">I think a slight increase in the marketing budget could be beneficial, considering our target audience. Let's discuss it further in the upcoming meeting.</div>
                </div>
                <div class="message-container incoming">
                    <div class="incoming-message">Sounds good! Speaking of meetings, do you have a preferred time for our weekly team catch-up?</div>
                </div>
                <div class="message-container outgoing">
                    <div class="outgoing-message">How about Tuesday mornings at 10 AM? Does that work for you?</div>
                </div>
                <div class="message-container incoming">
                    <div class="incoming-message">Tuesday at 10 AM works for me. Let's schedule it and make it a recurring meeting. Also, I'll send you the updated project timeline later today.</div>
                </div>
                <div class="message-container outgoing">
                    <div class="outgoing-message">Perfect! Looking forward to the updated timeline. If you need any help, feel free to reach out.</div>
                </div>
                <div class="message-container incoming">
                    <div class="incoming-message">Great! By the way, I noticed some feedback from the client regarding the design mockups. Have you had a chance to go through those comments?</div>
                </div>
                <div class="message-container outgoing">
                    <div class="outgoing-message">Yes, I've reviewed the client's feedback. I'll address the specific points and make the necessary revisions today. We want to ensure we meet their expectations.</div>
                </div>
                <div class="message-container incoming">
                    <div class="incoming-message">Thanks for taking care of that! Let's aim to have the updated mockups ready for presentation by the end of the week. It will be crucial for our progress meeting on Friday.</div>
                </div>
                <div class="message-container outgoing">
                    <div class="outgoing-message">Agreed. I'll prioritize the design revisions and coordinate with the design team to ensure we stay on track. Looking forward to a productive week!</div>
                </div>
                
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
    event.preventDefault(); 

    var chatId = document.getElementById("chat_id").value;
    var message = document.getElementById("message").value;

    
    if (!message.trim()) {
        console.log("Message is empty.");
        return;
    }

    // Construct the POST data
    var formData = new FormData();
    formData.append('chat_id', chatId);
    formData.append('message', message);

    // Create and send an AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "send-message2.php", true);
    xhr.onload = function () {
        if (this.status === 200) {
            console.log(this.responseText);
            
        } else {
            console.error('An error occurred during the AJAX request');
        }
    };
    xhr.onerror = function () {
        console.error('An error occurred during the AJAX request');
    };
    xhr.send(formData);

    // Clear the message input
    document.getElementById("message").value = '';
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