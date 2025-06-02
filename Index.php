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
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar placeholder styling */
        nav {
            background-color: #0b2e6a;
            padding: 12px 24px;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: white;
        }

        header {
            background-color: #145da0;
            padding: 40px 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            color: white;
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

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255 255 255 / 0.15);
            border: 2px solid rgba(255 255 255 / 0.3);
            color: white;
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
            background: rgba(255 255 255 / 0.35);
            border-color: white;
            box-shadow: 0 12px 20px rgba(255 255 255, 0.4);
            transform: translateY(-4px);
            outline: none;
        }

        .btn:active {
            transform: translateY(-1px);
            box-shadow: 0 8px 10px rgba(255 255 255, 0.3);
        }

        footer {
            margin-top: auto;
            padding: 20px 10px;
            color: rgba(255 255 255 / 0.6);
            font-size: 0.9rem;
            text-align: center;
            background-color: #0b2e6a;
            box-shadow: inset 0 1px 0 rgba(255 255 255 / 0.1);
            user-select: none;
        }

        /* Chatbot styles */
        .chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 380px;
            z-index: 1000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .chatbot-toggle {
            background-color: #145da0;
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
            background-color: #0b2e6a;
        }

        .chatbot-window {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            overflow: hidden;
            display: none;
            flex-direction: column;
            height: 500px;
            border: 1px solid #e0e0e0;
        }

        .chatbot-header {
            background: linear-gradient(135deg, #145da0, #0b2e6a);
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
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chatbot-input-area {
            padding: 12px;
            background-color: #f0f0f0;
            border-top: 1px solid #e0e0e0;
            display: flex;
            gap: 8px;
        }

        .chatbot-input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .chatbot-input:focus {
            border-color: #145da0;
        }

        .chatbot-send-btn {
            background-color: #145da0;
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
            background-color: #0b2e6a;
        }

        .message {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 18px;
            line-height: 1.4;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .user-message {
            background-color: #145da0;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .bot-message {
            background-color: white;
            color: #333;
            align-self: flex-start;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 4px;
        }

        .chatbot-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 8px;
        }

        .chatbot-menu-btn {
            background-color: #f0f0f0;
            color: #333;
            border: none;
            border-radius: 18px;
            padding: 10px 16px;
            cursor: pointer;
            text-align: left;
            transition: all 0.2s;
            font-size: 0.95rem;
            border: 1px solid #e0e0e0;
        }

        .chatbot-menu-btn:hover {
            background-color: #e0e0e0;
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
            border: 1px solid #ddd;
            border-radius: 18px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
            width: 100%;
        }

        .chatbot-input-field:focus {
            border-color: #145da0;
        }

        .chatbot-submit-btn {
            background-color: #145da0;
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
            background-color: #0b2e6a;
        }

        .loading-spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 3px solid #145da0;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 8px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .api-response {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 12px;
            margin-top: 8px;
            font-size: 0.9rem;
        }

        .api-response h4 {
            color: #145da0;
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
        }
    </style>
</head>
<body>
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