<?php
    $config = include('config.php');
    $apiBaseUrl = $config['api_base_url'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bus Management System - Home</title>
    <style>
        /* Reset some default */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    /* RCB inspired gradient: Dark Red to Black */
    background: linear-gradient(135deg, #56042C, #000000); /* Deep maroon to black */
    margin: 0;
    padding: 0;
    color: #f0f0f0; /* Light grey for text on dark background */
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* RCB Celebration Banner - Keep as is, it's already RCB themed */
.rcb-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(90deg, #000, #56042C, #000);
    color: white;
    padding: 15px;
    text-align: center;
    font-weight: bold;
    font-size: 1.2rem;
    z-index: 2000;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    animation: bannerSlideDown 0.8s ease-out;
}

.rcb-banner img {
    height: 40px;
    width: auto;
}

.rcb-banner .close-btn {
    position: absolute;
    right: 15px;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    cursor: pointer;
    font-weight: bold;
}

.rcb-banner .close-btn:hover {
    background: rgba(255,255,255,0.3);
}

@keyframes bannerSlideDown {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

/* Navbar styling - RCB Black */
nav {
    background-color: #000000; /* RCB Black */
    padding: 12px 24px;
    font-weight: 600;
    letter-spacing: 0.05em;
    color: #FFD700; /* RCB Gold for text */
    margin-top: 70px;
}

/* Header styling - RCB Red */
header {
    background-color: #E21B4C; /* RCB Red */
    padding: 40px 20px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white; /* White text for contrast */
}

header h1 {
    margin: 0;
    font-size: 2.8rem;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
}

.container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 24px;
    max-width: 900px;
    width: 90%;
    margin: 40px auto 60px;
    padding: 0 10px;
}

/* Buttons styling - Transparent with Gold accents */
.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 215, 0, 0.1); /* Slightly transparent gold */
    border: 2px solid rgba(255, 215, 0, 0.3); /* Transparent gold border */
    color: #FFD700; /* RCB Gold for text */
    font-weight: 700;
    font-size: 1.1rem;
    padding: 18px 24px;
    border-radius: 12px;
    text-decoration: none;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    transition:
        background-color 0.3s ease,
        border-color 0.3s ease,
        box-shadow 0.3s ease,
        transform 0.2s ease;
    cursor: pointer;
    user-select: none;
    gap: 8px;
}

.btn:hover,
.btn:focus {
    background: rgba(255, 215, 0, 0.3); /* More opaque gold on hover */
    border-color: #FFD700; /* Solid gold border on hover */
    box-shadow: 0 12px 20px rgba(255, 215, 0, 0.4); /* Gold shadow */
    transform: translateY(-4px);
    outline: none;
}

.btn:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 10px rgba(255, 215, 0, 0.3);
}

/* Footer styling - RCB Black */
footer {
    margin-top: auto;
    padding: 20px 10px;
    color: rgba(255, 215, 0, 0.6); /* Transparent gold text */
    font-size: 0.9rem;
    text-align: center;
    background-color: #000000; /* RCB Black */
    box-shadow: inset 0 1px 0 rgba(255, 215, 0, 0.1); /* Transparent gold shadow */
    user-select: none;
}

/* Chatbot styles - RCB themed */
.chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 380px;
    z-index: 1000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Chatbot toggle button - RCB Red */
.chatbot-toggle {
    background-color: #E21B4C; /* RCB Red */
    color: white;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: auto;
    transition: all 0.3s ease;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    background-color: #56042C; /* Darker maroon on hover */
}

.chatbot-window {
    background-color: #1a1a1a; /* Dark background for chat window */
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    overflow: hidden;
    display: none;
    flex-direction: column;
    height: 500px;
    border: 1px solid #333; /* Darker border */
}

/* Chatbot header - RCB Red to Black gradient */
.chatbot-header {
    background: linear-gradient(135deg, #E21B4C, #000000); /* RCB Red to Black */
    color: white;
    padding: 16px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.1rem;
}

.chatbot-header-controls {
    display: flex;
    gap: 8px;
}

.chatbot-close,
.chatbot-clear {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.chatbot-close:hover,
.chatbot-clear:hover {
    background-color: rgba(255,255,255,0.2);
}

.chatbot-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    background-color: #1a1a1a; /* Dark background */
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chatbot-input-area {
    padding: 12px;
    background-color: #000000; /* RCB Black */
    border-top: 1px solid #333; /* Darker border */
    display: flex;
    gap: 8px;
}

.chatbot-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #333; /* Darker border */
    border-radius: 20px;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.3s;
    background-color: #2a2a2a; /* Slightly lighter black for input */
    color: #f0f0f0; /* Light text color */
}

.chatbot-input:focus {
    border-color: #E21B4C; /* RCB Red on focus */
}

/* Chatbot send button - RCB Red */
.chatbot-send-btn {
    background-color: #E21B4C; /* RCB Red */
    color: white;
    border: none;
    border-radius: 20px;
    padding: 0 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.3s;
}

.chatbot-send-btn:hover {
    background-color: #56042C; /* Darker maroon on hover */
}

.message {
    max-width: 80%;
    padding: 10px 14px;
    border-radius: 18px;
    line-height: 1.4;
    word-wrap: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* User message - RCB Red */
.user-message {
    background-color: #E21B4C; /* RCB Red */
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

/* Bot message - Dark background with gold text */
.bot-message {
    background-color: #000000; /* RCB Black */
    color: #FFD700; /* RCB Gold for text */
    align-self: flex-start;
    border: 1px solid #56042C; /* Maroon border */
    border-bottom-left-radius: 4px;
}

.chatbot-menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 8px;
}

/* Chatbot menu button - Dark background with gold text */
.chatbot-menu-btn {
    background-color: #2a2a2a; /* Dark grey */
    color: #FFD700; /* RCB Gold */
    border: none;
    border-radius: 18px;
    padding: 10px 16px;
    cursor: pointer;
    text-align: left;
    transition: all 0.2s;
    font-size: 0.95rem;
    border: 1px solid #56042C; /* Maroon border */
}

.chatbot-menu-btn:hover {
    background-color: #3a3a3a; /* Lighter dark grey on hover */
    transform: translateX(4px);
}

.chatbot-menu-btn:active {
    transform: translateX(2px);
}

.input-container {
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.chatbot-input-field {
    padding: 10px 14px;
    border: 1px solid #333; /* Darker border */
    border-radius: 18px;
    font-size: 0.95rem;
    outline: none;
    transition: border-color 0.3s;
    background-color: #2a2a2a; /* Dark background */
    color: #f0f0f0; /* Light text */
}

.chatbot-input-field:focus {
    border-color: #E21B4C; /* RCB Red on focus */
}

/* Chatbot submit button - RCB Red */
.chatbot-submit-btn {
    background-color: #E21B4C; /* RCB Red */
    color: white;
    border: none;
    border-radius: 18px;
    padding: 10px 16px;
    cursor: pointer;
    transition: background-color 0.3s;
    align-self: flex-end;
    font-size: 0.95rem;
}

.chatbot-submit-btn:hover {
    background-color: #56042C; /* Darker maroon on hover */
}

/* Loading spinner - RCB Red */
.loading-spinner {
    border: 3px solid rgba(255, 215, 0, 0.1); /* Transparent gold */
    border-radius: 50%;
    border-top: 3px solid #E21B4C; /* RCB Red */
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
    margin: 8px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* API response display - Dark background with gold text */
.api-response {
    background-color: #000000; /* RCB Black */
    border: 1px solid #56042C; /* Maroon border */
    border-radius: 12px;
    padding: 12px;
    margin-top: 8px;
    font-size: 0.9rem;
    color: #f0f0f0; /* Light text */
}

.api-response h4 {
    color: #FFD700; /* RCB Gold */
    margin-bottom: 8px;
    font-size: 1rem;
}

.api-response ul {
    padding-left: 20px;
}

.api-response li {
    margin-bottom: 6px;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    header h1 {
        font-size: 2rem;
    }
    .btn {
        font-size: 1rem;
        padding: 14px 20px;
    }
    .chatbot-container {
        width: 90%;
        right: 5%;
        bottom: 10px;
        max-width: 100%;
    }
    .chatbot-window {
        height: 70vh;
    }
    .rcb-banner {
        flex-direction: column;
        padding: 10px;
        font-size: 1rem;
    }
    .rcb-banner img {
        height: 30px;
    }
    nav {
        margin-top: 120px;
    }
}

    </style>
</head>
<body>
    <!-- <div class="rcb-banner" id="rcbBanner">
    <img src="data:image/png;base64,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" alt="RCB Logo">
    <span>🏆 Congratulations to Royal Challengers Bengaluru - IPL 2025 Champions! 🏆</span>
    <span>🏆 Ee Sala Cup Namdu 🏆</span>
    <button class="close-btn" id="closeBanner">×</button>
    </div> -->

<?php include 'navbar.php'; ?>

<header>
    <h1>Welcome to Bus Management System</h1>
</header>

<div class="container">
    <a class="btn" href="ViewRoutes.php">📋 View Routes</a>
    <a class="btn" href="FindRoutes.php">🔍 Find Routes</a>
    <a class="btn" href="CalculateFare.php">🔢 Calculate Fare</a>
    <a class="btn" href="FareChart.php">💰 Fare Chart</a>
</div>

<!-- Chatbot Container -->
<div class="chatbot-container">
    <button class="chatbot-toggle" id="chatbotToggle">🤖</button>
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <span>Bus System Assistant</span>
            <div class="chatbot-header-controls">
                <button class="chatbot-clear" id="chatbotClear" title="Clear chat">🗑️</button>
                <button class="chatbot-close" id="chatbotClose">×</button>
            </div>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <!-- Messages will appear here -->
        </div>
        <div class="chatbot-input-area">
            <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type your message...">
            <button class="chatbot-send-btn" id="chatbotSend">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </div>
    </div>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Bus Management System
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // const closeBanner = document.getElementById('closeBanner');
        // const rcbBanner = document.getElementById('rcbBanner');
        
        // closeBanner.addEventListener('click', function() {
        //     rcbBanner.style.display = 'none';
        //     document.querySelector('nav').style.marginTop = '0';
        // });

        const chatbotToggle = document.getElementById('chatbotToggle');
        const chatbotWindow = document.getElementById('chatbotWindow');
        const chatbotClose = document.getElementById('chatbotClose');
        const chatbotClear = document.getElementById('chatbotClear');
        const chatbotMessages = document.getElementById('chatbotMessages');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSend = document.getElementById('chatbotSend');

        const API_BASE_URL = "<?php echo $apiBaseUrl; ?>";
        
        // API endpoints
        const API_ENDPOINTS = {
            getRouteByCode: `${API_BASE_URL}GetRouteByCode/`,
            findRoutesBetweenStages: `${API_BASE_URL}FindRoutesBetweenStages/`,
            getRouteStagesByCode: `${API_BASE_URL}GetRouteStagesByCode/`,
            calculateFare: `${API_BASE_URL}CalculateFare/`
        };
        
        // Current state of the conversation
        let conversationState = {
            waitingForInput: false,
            currentAction: null,
            inputFields: [],
            collectedInputs: {},
            currentFieldIndex: 0,
            routeStages: [] // For storing stages when calculating fare
        };
        
        // Toggle chatbot window
        chatbotToggle.addEventListener('click', function() {
            chatbotWindow.style.display = 'flex';
            if (chatbotMessages.children.length === 0) {
                addBotMessage("Hello! I'm your Bus System Assistant. How can I help you today?");
                showMenuOptions();
            }
        });
        
        // Close chatbot window
        chatbotClose.addEventListener('click', function() {
            chatbotWindow.style.display = 'none';
        });
        
        // Clear chat history
        chatbotClear.addEventListener('click', function() {
            chatbotMessages.innerHTML = '';
            addBotMessage("Hello! I'm your Bus System Assistant. How can I help you today?");
            showMenuOptions();
            
            // Reset conversation state
            conversationState = {
                waitingForInput: false,
                currentAction: null,
                inputFields: [],
                collectedInputs: {},
                currentFieldIndex: 0,
                routeStages: []
            };
        });
        
        // Send message when button is clicked
        chatbotSend.addEventListener('click', sendMessage);
        
        // Send message when Enter key is pressed
        chatbotInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
        
        function sendMessage() {
            const message = chatbotInput.value.trim();
            if (message) {
                addUserMessage(message);
                chatbotInput.value = '';
                
                if (conversationState.waitingForInput) {
                    handleUserInput(message);
                } else {
                    // Simple bot response logic for free text
                    setTimeout(() => {
                        if (message.toLowerCase().includes('route') && message.toLowerCase().includes('detail')) {
                            initiateGetRouteDetails();
                        } else if (message.toLowerCase().includes('find') && message.toLowerCase().includes('route')) {
                            initiateFindRoutesBetweenStages();
                        } else if (message.toLowerCase().includes('fare') || message.toLowerCase().includes('calculate')) {
                            initiateCalculateFare();
                        } else if (message.toLowerCase().includes('hello') || message.toLowerCase().includes('hi')) {
                            addBotMessage("Hello! How can I assist you with the bus system today?");
                        } else {
                            addBotMessage("I'm not sure I understand. Please select one of the menu options for assistance:");
                        }
                        showMenuOptions();
                    }, 500);
                }
            }
        }
        
        function showMenuOptions() {
            const menuDiv = document.createElement('div');
            menuDiv.className = 'chatbot-menu';
            
            const option1 = document.createElement('button');
            option1.className = 'chatbot-menu-btn';
            option1.innerHTML = '<span style="font-weight:bold">1. Get Route Details</span><br><small>View stops and details for a specific route</small>';
            option1.addEventListener('click', function() {
                addUserMessage("Get Route Details");
                initiateGetRouteDetails();
            });
            
            const option2 = document.createElement('button');
            option2.className = 'chatbot-menu-btn';
            option2.innerHTML = '<span style="font-weight:bold">2. Find Routes between Stages</span><br><small>Discover routes connecting two locations</small>';
            option2.addEventListener('click', function() {
                addUserMessage("Find Routes between Stages");
                initiateFindRoutesBetweenStages();
            });
            
            const option3 = document.createElement('button');
            option3.className = 'chatbot-menu-btn';
            option3.innerHTML = '<span style="font-weight:bold">3. Calculate Fare</span><br><small>Estimate travel cost between two points</small>';
            option3.addEventListener('click', function() {
                addUserMessage("Calculate Fare");
                initiateCalculateFare();
            });
            
            menuDiv.appendChild(option1);
            menuDiv.appendChild(option2);
            menuDiv.appendChild(option3);
            
            chatbotMessages.appendChild(menuDiv);
            scrollToBottom();
        }
        
        function initiateGetRouteDetails() {
            conversationState = {
                waitingForInput: true,
                currentAction: 'getRouteByCode',
                inputFields: ['routeCode'],
                collectedInputs: {},
                currentFieldIndex: 0
            };
            
            addBotMessage("Please enter the Route Code you're interested in (e.g., S88A):");
            createInputField('routeCode', 'text', 'Enter route code');
        }
        
        function initiateFindRoutesBetweenStages() {
            conversationState = {
                waitingForInput: true,
                currentAction: 'findRoutesBetweenStages',
                inputFields: ['fromStage', 'toStage'],
                collectedInputs: {},
                currentFieldIndex: 0
            };
            
            addBotMessage("Let's find routes between stages. First, please enter your starting point:");
            createInputField('fromStage', 'text', 'Enter starting stage');
        }
        
        function initiateCalculateFare() {
            conversationState = {
                waitingForInput: true,
                currentAction: 'calculateFare',
                inputFields: ['routeCode', 'busType', 'fromStage', 'toStage', 'passengerCount'],
                collectedInputs: {},
                currentFieldIndex: 0,
                routeStages: []
            };
            
            addBotMessage("Let's calculate your fare. First, please enter the Route Code:");
            createInputField('routeCode', 'text', 'Enter route code');
        }
        
        function createInputField(name, type, placeholder) {
            const inputContainer = document.createElement('div');
            inputContainer.className = 'input-container';
            
            // For busType, create a dropdown instead of input
            if (name === 'busType') {
                const select = document.createElement('select');
                select.className = 'chatbot-input-field';
                select.id = 'chatbot-input-' + name;
                
                const options = ['AC', 'Deluxe', 'Express', 'Night', 'Ordinary'];
                options.forEach(option => {
                    const optElement = document.createElement('option');
                    optElement.value = option;
                    optElement.textContent = option;
                    select.appendChild(optElement);
                });
                
                inputContainer.appendChild(select);
            } else if (name === 'fromStage' || name === 'toStage') {
                // For stages, create a dropdown if we have the stages
                if (conversationState.routeStages.length > 0) {
                    const select = document.createElement('select');
                    select.className = 'chatbot-input-field';
                    select.id = 'chatbot-input-' + name;
                    
                    conversationState.routeStages.forEach(stage => {
                        const optElement = document.createElement('option');
                        optElement.value = stage.stageName;
                        optElement.textContent = stage.stageName;
                        select.appendChild(optElement);
                    });
                    
                    inputContainer.appendChild(select);
                } else {
                    // If we don't have stages yet, use normal input
                    const input = document.createElement('input');
                    input.type = type;
                    input.className = 'chatbot-input-field';
                    input.placeholder = placeholder;
                    input.id = 'chatbot-input-' + name;
                    inputContainer.appendChild(input);
                }
            } else if (name === 'passengerCount') {
                // For passenger count, create a number input
                const input = document.createElement('input');
                input.type = 'number';
                input.className = 'chatbot-input-field';
                input.placeholder = placeholder || 'Enter number of passengers';
                input.id = 'chatbot-input-' + name;
                input.min = '1';
                input.max = '10';
                input.value = '1';
                inputContainer.appendChild(input);
            } else {
                // Normal input field
                const input = document.createElement('input');
                input.type = type;
                input.className = 'chatbot-input-field';
                input.placeholder = placeholder;
                input.id = 'chatbot-input-' + name;
                inputContainer.appendChild(input);
            }
            
            const submitBtn = document.createElement('button');
            submitBtn.className = 'chatbot-submit-btn';
            submitBtn.textContent = 'Submit';
            submitBtn.addEventListener('click', function() {
                let value;
                const inputElement = document.getElementById('chatbot-input-' + name);
                if (inputElement.tagName === 'SELECT') {
                    value = inputElement.value;
                } else {
                    value = inputElement.value.trim();
                }
                
                if (value) {
                    // Show the user's input in the chat
                    addUserMessage(value);
                    handleUserInput(value);
                }
            });
            
            inputContainer.appendChild(submitBtn);
            chatbotMessages.appendChild(inputContainer);
            scrollToBottom();
            
            // Focus the input/select
            const inputElement = document.getElementById('chatbot-input-' + name);
            if (inputElement) inputElement.focus();
        }
        
        async function handleUserInput(input) {
            // Remove the input field
            const inputContainers = document.querySelectorAll('.input-container');
            inputContainers.forEach(container => container.remove());
            
            // Store the input
            const currentField = conversationState.inputFields[conversationState.currentFieldIndex];
            conversationState.collectedInputs[currentField] = input;
            
            // Move to next field if there are more
            conversationState.currentFieldIndex++;
            
            // For Calculate Fare, we need to get stages after getting routeCode
            if (conversationState.currentAction === 'calculateFare' && 
                currentField === 'routeCode' && 
                conversationState.currentFieldIndex < conversationState.inputFields.length) {
                
                // First get the stages for this route
                try {
                    const loadingDiv = document.createElement('div');
                    loadingDiv.className = 'loading-spinner';
                    chatbotMessages.appendChild(loadingDiv);
                    scrollToBottom();
                    
                    const response = await fetch(API_ENDPOINTS.getRouteStagesByCode + input);
                    const data = await response.json();
                    
                    loadingDiv.remove();
                    
                    if (data && data.stages && data.stages.length > 0) {
                        conversationState.routeStages = data.stages;
                        addBotMessage("Great! Now please select the bus type:");
                        createInputField('busType', 'select', '');
                    } else {
                        addBotMessage("Sorry, I couldn't find any stages for that route. Please try again.");
                        conversationState = {
                            waitingForInput: false,
                            currentAction: null,
                            inputFields: []
                        };
                        showMenuOptions();
                    }
                } catch (error) {
                    console.error('Error fetching route stages:', error);
                    addBotMessage("Sorry, there was an error fetching route information. Please try again.");
                    conversationState = {
                        waitingForInput: false,
                        currentAction: null,
                        inputFields: []
                    };
                    showMenuOptions();
                }
                return;
            }
            
            // For Calculate Fare, after busType, ask for fromStage
            if (conversationState.currentAction === 'calculateFare' && 
                currentField === 'busType' && 
                conversationState.currentFieldIndex < conversationState.inputFields.length) {
                
                addBotMessage("Now please select your boarding point:");
                createInputField('fromStage', 'select', '');
                return;
            }
            
            // For Calculate Fare, after fromStage, ask for toStage
            if (conversationState.currentAction === 'calculateFare' && 
                currentField === 'fromStage' && 
                conversationState.currentFieldIndex < conversationState.inputFields.length) {
                
                addBotMessage("Now please select your destination:");
                createInputField('toStage', 'select', '');
                return;
            }
            
            // For Calculate Fare, after toStage, ask for passengerCount
            if (conversationState.currentAction === 'calculateFare' && 
                currentField === 'toStage' && 
                conversationState.currentFieldIndex < conversationState.inputFields.length) {
                
                addBotMessage("How many passengers are traveling?");
                createInputField('passengerCount', 'number', 'Enter number (1-10)');
                return;
            }
            
            // For Find Routes Between Stages, after fromStage, ask for toStage
            if (conversationState.currentAction === 'findRoutesBetweenStages' && 
                currentField === 'fromStage' && 
                conversationState.currentFieldIndex < conversationState.inputFields.length) {
                
                addBotMessage("Now please enter your destination:");
                createInputField('toStage', 'text', 'Enter destination stage');
                return;
            }
            
            // All inputs collected, call the appropriate API
            callApiWithCollectedInputs();
        }
        
        async function callApiWithCollectedInputs() {
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'loading-spinner';
            chatbotMessages.appendChild(loadingDiv);
            scrollToBottom();
            
            try {
                let response, data;
                
                switch (conversationState.currentAction) {
                    case 'getRouteByCode':
                        response = await fetch(API_ENDPOINTS.getRouteByCode + conversationState.collectedInputs.routeCode);
                        data = await response.json();
                        displayRouteDetails(data);
                        break;
                        
                    case 'findRoutesBetweenStages':
                        const fromStage = encodeURIComponent(conversationState.collectedInputs.fromStage);
                        const toStage = encodeURIComponent(conversationState.collectedInputs.toStage);
                        response = await fetch(API_ENDPOINTS.findRoutesBetweenStages + fromStage + '/' + toStage);
                        data = await response.json();
                        displayRoutesBetweenStages(data, conversationState.collectedInputs.fromStage, conversationState.collectedInputs.toStage);
                        break;
                        
                    case 'calculateFare':
                        const routeCode = encodeURIComponent(conversationState.collectedInputs.routeCode);
                        const busType = encodeURIComponent(conversationState.collectedInputs.busType);
                        const fromStage1 = encodeURIComponent(conversationState.collectedInputs.fromStage);
                        const toStage1 = encodeURIComponent(conversationState.collectedInputs.toStage);
                        response = await fetch(API_ENDPOINTS.calculateFare + routeCode + '/' + busType + '/' + fromStage1 + '/' + toStage1);
                        data = await response.json();
                        displayFareCalculation(data, conversationState.collectedInputs);
                        break;
                }
            } catch (error) {
                console.error('API call failed:', error);
                addBotMessage("Sorry, there was an error processing your request. Please try again.");
            } finally {
                loadingDiv.remove();
                
                // Reset conversation state
                conversationState = {
                    waitingForInput: false,
                    currentAction: null,
                    inputFields: []
                };
                
                // Show menu options again
                setTimeout(() => {
                    addBotMessage("What else can I help you with?");
                    showMenuOptions();
                }, 500);
            }
        }
        
        function displayRouteDetails(routeData) {
            if (!routeData || !routeData.code) {
                addBotMessage("Sorry, I couldn't find details for that route. Please check the route code and try again.");
                return;
            }
            
            const responseDiv = document.createElement('div');
            responseDiv.className = 'api-response';
            
            // Create stops list with distance
            const stopsList = routeData.stages.map(stage => 
                `<li>
                    <strong>${stage.stageName}</strong> 
                    ${stage.distanceFromStart > 0 ? `(Distance: ${stage.distanceFromStart} km)` : ''}
                </li>`
            ).join('');
            
            responseDiv.innerHTML = `
                <h4>🚌 Route ${routeData.code}</h4>
                <p><strong>From:</strong> ${routeData.from}</p>
                <p><strong>To:</strong> ${routeData.to}</p>
                <p><strong>🛑 Stops:</strong></p>
                <ol>${stopsList}</ol>
            `;
            
            chatbotMessages.appendChild(responseDiv);
            scrollToBottom();
        }
        
        function displayRoutesBetweenStages(routesData, fromStage, toStage) {
            const responseDiv = document.createElement('div');
            responseDiv.className = 'api-response';
            
            if (!routesData || routesData.length === 0) {
                responseDiv.innerHTML = `
                    <p>No direct routes found between ${fromStage} and ${toStage}.</p>
                    <p>You might need to consider transfers or alternative transportation.</p>
                `;
                chatbotMessages.appendChild(responseDiv);
                scrollToBottom();
                return;
            }
            
            let routesHtml = '';
            
            routesData.forEach(route => {
                // Find the order of from and to stages
                let fromOrder, toOrder;
                route.stages.forEach(stage => {
                    if (stage.stageName === fromStage) fromOrder = stage.stageOrder;
                    if (stage.stageName === toStage) toOrder = stage.stageOrder;
                });
                
                // Calculate stops between
                const stopsBetween = Math.abs(fromOrder - toOrder) - 1;
                
                routesHtml += `
                    <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; margin-bottom: 10px;">
                        <div style="font-weight: bold; color: #145da0;">${route.code} - ${route.from} to ${route.to}</div>
                        <div style="display: flex; justify-content: space-between; margin-top: 6px;">
                            <span>🚏 ${stopsBetween} stops between</span>
                        </div>
                        <div style="margin-top: 8px;">
                            <strong>Direction:</strong> ${fromOrder < toOrder ? route.from + ' → ' + route.to : route.to + ' → ' + route.from}
                        </div>
                    </div>
                `;
            });
            
            responseDiv.innerHTML = `
                <h4>🔍 Routes between ${fromStage} and ${toStage}</h4>
                <div style="margin-top: 12px;">
                    ${routesHtml}
                </div>
            `;
            
            chatbotMessages.appendChild(responseDiv);
            scrollToBottom();
        }
        
        function displayFareCalculation(fareData, inputs) {
            if (!fareData || fareData.fare === undefined) {
                addBotMessage("Sorry, I couldn't calculate the fare for that route. Please check your inputs and try again.");
                return;
            }
            
            const passengerCount = parseInt(inputs.passengerCount) || 1;
            const totalFare = fareData.fare * passengerCount;
            
            const responseDiv = document.createElement('div');
            responseDiv.className = 'api-response';
            
            responseDiv.innerHTML = `
                <h4>💰 Fare Calculation</h4>
                <p><strong>Route:</strong> ${inputs.routeCode} (${inputs.busType})</p>
                <p><strong>From:</strong> ${inputs.fromStage}</p>
                <p><strong>To:</strong> ${inputs.toStage}</p>
                <p><strong>Passengers:</strong> ${passengerCount}</p>
                <div style="margin-top: 12px; background: #f0f7ff; padding: 12px; border-radius: 8px;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #145da0; text-align: center;">
                        ₹${fareData.fare} <small style="font-size: 1rem;">per passenger</small>
                    </div>
                    <div style="font-size: 1.5rem; font-weight: bold; color: #0b2e6a; text-align: center; margin-top: 8px;">
                        ₹${totalFare} <small style="font-size: 1rem;">total</small>
                    </div>
                    <div style="text-align: center; margin-top: 4px;">Estimated Fare</div>
                </div>
            `;
            
            chatbotMessages.appendChild(responseDiv);
            scrollToBottom();
        }
        
        function addUserMessage(text) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message user-message';
            messageDiv.textContent = text;
            chatbotMessages.appendChild(messageDiv);
            scrollToBottom();
        }
        
        function addBotMessage(text) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message bot-message';
            messageDiv.textContent = text;
            chatbotMessages.appendChild(messageDiv);
            scrollToBottom();
        }
        
        function scrollToBottom() {
            // Small delay to ensure DOM is updated
            setTimeout(() => {
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }, 50);
        }
    });
</script>

</body>
</html>