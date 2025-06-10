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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
:root {
    --primary: #4361ee;
    --primary-dark: #3a56d4;
    --secondary: #3f37c9;
    --accent: #4895ef;
    --light: #f8f9fa;
    --dark: #212529;
    --gray: #6c757d;
    --light-gray: #e9ecef;
    --success: #4cc9f0;
    --warning: #f72585;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f7ff;
    color: var(--dark);
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

/* Header Styles */
header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 2rem 1rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

header h1 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

header p {
    font-size: 1.1rem;
    opacity: 0.9;
    max-width: 800px;
    margin: 0 auto;
}

/* Main Content */
.main-content {
    flex: 1;
    padding: 2rem 1rem;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

.section-title {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background-color: var(--accent);
}

/* Card Grid */
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    border: none;
    display: flex;
    flex-direction: column;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.card-icon {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--dark);
}

.card-text {
    color: var(--gray);
    margin-bottom: 1.5rem;
    flex: 1;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    width: fit-content;
}

.btn:hover {
    background-color: var(--primary-dark);
    color: white;
    transform: translateY(-2px);
}

.btn i {
    margin-right: 0.5rem;
}

.btn-outline {
    background-color: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn-outline:hover {
    background-color: var(--primary);
    color: white;
}

/* Footer */
footer {
    background-color: var(--dark);
    color: white;
    text-align: center;
    padding: 1.5rem;
    margin-top: auto;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.social-links a {
    color: white;
    font-size: 1.25rem;
    transition: var(--transition);
}

.social-links a:hover {
    color: var(--accent);
}

.copyright {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Chatbot Styles */
.chatbot-container {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.chatbot-toggle {
    background-color: var(--primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 1.5rem;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.chatbot-toggle:hover {
    background-color: var(--primary-dark);
    transform: scale(1.1);
}

.chatbot-window {
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    display: none;
    flex-direction: column;
    height: 500px;
    width: 380px;
    border: 1px solid var(--light-gray);
}

.chatbot-header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 1rem;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.1rem;
}

.chatbot-header-controls {
    display: flex;
    gap: 0.5rem;
}

.chatbot-close,
.chatbot-clear {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 1rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.chatbot-close:hover,
.chatbot-clear:hover {
    background: rgba(255, 255, 255, 0.3);
}

.chatbot-messages {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    background-color: white;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.chatbot-input-area {
    padding: 0.75rem;
    background-color: white;
    border-top: 1px solid var(--light-gray);
    display: flex;
    gap: 0.5rem;
}

.chatbot-input {
    flex: 1;
    padding: 0.75rem 1rem;
    border: 1px solid var(--light-gray);
    border-radius: 25px;
    font-size: 0.95rem;
    outline: none;
    transition: var(--transition);
}

.chatbot-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
}

.chatbot-send-btn {
    background-color: var(--primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 45px;
    height: 45px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
}

.chatbot-send-btn:hover {
    background-color: var(--primary-dark);
}

.message {
    max-width: 80%;
    padding: 0.75rem 1rem;
    border-radius: 18px;
    line-height: 1.4;
    word-wrap: break-word;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.user-message {
    background-color: var(--primary);
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

.bot-message {
    background-color: var(--light-gray);
    color: var(--dark);
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}

.chatbot-menu {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.chatbot-menu-btn {
    background-color: white;
    color: var(--primary);
    border: 1px solid var(--light-gray);
    border-radius: 18px;
    padding: 0.75rem 1rem;
    cursor: pointer;
    text-align: left;
    transition: var(--transition);
    font-size: 0.95rem;
}

.chatbot-menu-btn:hover {
    background-color: var(--primary);
    color: white;
    border-color: var(--primary);
}

.input-container {
    margin-top: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.chatbot-input-field {
    padding: 0.75rem 1rem;
    border: 1px solid var(--light-gray);
    border-radius: 18px;
    font-size: 0.95rem;
    outline: none;
    transition: var(--transition);
    width: 100%;
}

.chatbot-input-field:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
}

.chatbot-submit-btn {
    background-color: var(--primary);
    color: white;
    border: none;
    border-radius: 18px;
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    transition: var(--transition);
    align-self: flex-end;
    font-size: 0.95rem;
}

.chatbot-submit-btn:hover {
    background-color: var(--primary-dark);
}

.loading-spinner {
    border: 3px solid rgba(67, 97, 238, 0.1);
    border-radius: 50%;
    border-top: 3px solid var(--primary);
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
    margin: 0.5rem auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.api-response {
    background-color: white;
    border: 1px solid var(--light-gray);
    border-radius: 12px;
    padding: 1rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    box-shadow: var(--card-shadow);
}

.api-response h4 {
    color: var(--primary);
    margin-bottom: 0.5rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.api-response ul {
    padding-left: 1.25rem;
}

.api-response li {
    margin-bottom: 0.5rem;
    position: relative;
    padding-left: 1rem;
}

.api-response li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: var(--primary);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    header h1 {
        font-size: 2rem;
    }
    
    .card-grid {
        grid-template-columns: 1fr;
    }
    
    .chatbot-container {
        bottom: 1rem;
        right: 1rem;
    }
    
    .chatbot-window {
        width: calc(100vw - 2rem);
        height: 70vh;
    }
}

@media (max-width: 480px) {
    header h1 {
        font-size: 1.75rem;
    }
    
    header p {
        font-size: 1rem;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .card-text {
        font-size: 0.9rem;
    }
    
    .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <header>
        <h1>Bus Management System</h1>
        <p>Efficiently manage and track bus routes, schedules, and fares with our comprehensive system</p>
    </header>

    <main class="main-content">
        <section>
            <h2 class="section-title">Quick Actions</h2>
            <div class="card-grid">
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3 class="card-title">View Routes</h3>
                        <p class="card-text">Explore all available bus routes with detailed information about stops and schedules.</p>
                        <a href="ViewRoutes.php" class="btn">
                            <i class="fas fa-eye"></i> View Routes
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-ticket"></i>
                        </div>
                        <h3 class="card-title">Tickets</h3>
                        <p class="card-text">Purchase tickets and view the purchased tickets.</p>
                        <a href="TicketsHome.php" class="btn">
                            <i class="fas fa-ticket"></i> Tickets
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="card-title">Find Routes</h3>
                        <p class="card-text">Discover the best routes between your starting point and destination.</p>
                        <a href="FindRoutes.php" class="btn">
                            <i class="fas fa-search"></i> Find Routes
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3 class="card-title">Calculate Fare</h3>
                        <p class="card-text">Estimate your travel costs based on route, distance, and passenger count.</p>
                        <a href="CalculateFare.php" class="btn">
                            <i class="fas fa-calculator"></i> Calculate
                        </a>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="card-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3 class="card-title">Fare Chart</h3>
                        <p class="card-text">View comprehensive fare information for all routes and bus types.</p>
                        <a href="FareChart.php" class="btn">
                            <i class="fas fa-table"></i> View Chart
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="chatbot-container">
        <button class="chatbot-toggle" id="chatbotToggle">
            <i class="fas fa-robot"></i>
        </button>
        <div class="chatbot-window" id="chatbotWindow">
            <div class="chatbot-header">
                <span>Bus System Assistant</span>
                <div class="chatbot-header-controls">
                    <button class="chatbot-clear" id="chatbotClear" title="Clear chat">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="chatbot-close" id="chatbotClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                </div>
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type your message...">
                <button class="chatbot-send-btn" id="chatbotSend">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <p class="copyright">&copy; <?php echo date("Y"); ?> Bus Management System. All rights reserved.</p>
        </div>
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
                
                // If all inputs are collected, execute the action
                if (conversationState.currentFieldIndex < conversationState.inputFields.length) {
                    const nextField = conversationState.inputFields[conversationState.currentFieldIndex];
                    let prompt = "";
                    let type = "text";
                    let placeholder = "";
                    
                    switch(nextField) {
                        case 'fromStage':
                            prompt = "Please enter your starting point:";
                            placeholder = "Enter starting stage";
                            break;
                        case 'toStage':
                            prompt = "Please enter your destination:";
                            placeholder = "Enter destination stage";
                            break;
                        case 'passengerCount':
                            prompt = "How many passengers are traveling?";
                            type = "number";
                            placeholder = "Enter number of passengers";
                            break;
                        default:
                            prompt = "Please provide the " + nextField + ":";
                            placeholder = "Enter " + nextField;
                    }
                    addBotMessage(prompt);
                    createInputField(nextField, type, placeholder);
                } else {
                    // All inputs collected, execute the action
                    conversationState.waitingForInput = false;
                    executeAction(conversationState.currentAction, conversationState.collectedInputs);
                }
            }
            
            function addUserMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message user-message';
                messageDiv.textContent = message;
                chatbotMessages.appendChild(messageDiv);
                scrollToBottom();
            }
            
            function addBotMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot-message';
                messageDiv.textContent = message;
                chatbotMessages.appendChild(messageDiv);
                scrollToBottom();
            }
            
            function addBotHtmlResponse(htmlContent) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot-message api-response';
                messageDiv.innerHTML = htmlContent;
                chatbotMessages.appendChild(messageDiv);
                scrollToBottom();
            }
            
            function scrollToBottom() {
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }
            
            async function executeAction(action, inputs) {
                addBotMessage("Processing your request...");
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'loading-spinner';
                chatbotMessages.appendChild(loadingDiv);
                scrollToBottom();

                try {
                    let url;
                    let data = {};
                    let response;
                    let result;

                    switch (action) {
                        case 'getRouteByCode':
                            url = API_ENDPOINTS.getRouteByCode + inputs.routeCode;
                            response = await fetch(url);
                            result = await response.json();
                            displayRouteDetails(result);
                            break;
                        case 'findRoutesBetweenStages':
                            url = API_ENDPOINTS.findRoutesBetweenStages + `${inputs.fromStage}/${inputs.toStage}`;
                            response = await fetch(url);
                            result = await response.json();
                            displayFoundRoutes(result);
                            break;
                        case 'calculateFare':
                            url = API_ENDPOINTS.calculateFare + 
                                `${inputs.routeCode}/${inputs.busType}/${inputs.fromStage}/${inputs.toStage}/${inputs.passengerCount}`;
                            response = await fetch(url);
                            result = await response.json();
                            displayCalculatedFare(result);
                            break;
                        default:
                            addBotMessage("I don't know how to handle that request.");
                    }
                } catch (error) {
                    console.error('API Error:', error);
                    addBotMessage("I apologize, but there was an error processing your request. Please try again later.");
                } finally {
                    loadingDiv.remove();
                    showMenuOptions(); // Always show menu options after an action
                }
            }

            function displayRouteDetails(data) {
                if (data && data.routeCode) {
                    let html = `<h4><i class="fas fa-route"></i> Route Details for ${data.routeCode} - ${data.routeName}</h4>`;
                    if (data.stages && data.stages.length > 0) {
                        html += `<h5>Stages:</h5><ul>`;
                        data.stages.forEach(stage => {
                            html += `<li>${stage.stageName} (Order: ${stage.stageOrder})</li>`;
                        });
                        html += `</ul>`;
                    } else {
                        html += `<p>No stages found for this route.</p>`;
                    }
                    addBotHtmlResponse(html);
                } else {
                    addBotMessage("Route not found or invalid route code.");
                }
            }

            function displayFoundRoutes(data) {
                if (data && data.length > 0) {
                    let html = `<h4><i class="fas fa-search"></i> Routes found from ${conversationState.collectedInputs.fromStage} to ${conversationState.collectedInputs.toStage}:</h4><ul>`;
                    data.forEach(route => {
                        html += `<li><strong>${route.routeCode}</strong> - ${route.routeName}</li>`;
                    });
                    html += `</ul>`;
                    addBotHtmlResponse(html);
                } else {
                    addBotMessage(`No direct routes found from ${conversationState.collectedInputs.fromStage} to ${conversationState.collectedInputs.toStage}.`);
                }
            }

            function displayCalculatedFare(data) {
                if (data && data.fare !== undefined) {
                    let html = `<h4><i class="fas fa-calculator"></i> Fare Calculation Result</h4>`;
                    html += `<p><strong>Route:</strong> ${conversationState.collectedInputs.routeCode}</p>`;
                    html += `<p><strong>Bus Type:</strong> ${conversationState.collectedInputs.busType}</p>`;
                    html += `<p><strong>From:</strong> ${conversationState.collectedInputs.fromStage}</p>`;
                    html += `<p><strong>To:</strong> ${conversationState.collectedInputs.toStage}</p>`;
                    html += `<p><strong>Passengers:</strong> ${conversationState.collectedInputs.passengerCount}</p>`;
                    html += `<p><strong>Total Fare:</strong> <span style="font-size: 1.2rem; font-weight: bold; color: var(--success);">$${data.fare.toFixed(2)}</span></p>`;
                    addBotHtmlResponse(html);
                } else {
                    addBotMessage("Could not calculate fare. Please check the provided details and try again.");
                }
            }
        });
    </script>
</body>
</html>