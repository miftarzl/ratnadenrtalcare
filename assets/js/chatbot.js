const chatBox = document.getElementById("chatBox");
const userInput = document.getElementById("userInput");

function saveChatHistory() {
    sessionStorage.setItem("chatHistory", chatBox.innerHTML);
}

function loadChatHistory() {
    const history = sessionStorage.getItem("chatHistory");

    if (history) {
        chatBox.innerHTML = history;
        chatBox.scrollTop = chatBox.scrollHeight;
    }
}

function clearChatHistory() {
    sessionStorage.removeItem("chatHistory");
    localStorage.removeItem("chatHistory");
}

function toggleChatbot() {
    const chatbotPopup = document.getElementById("chatbotPopup");
    chatbotPopup.classList.toggle("active");
}

function escapeHTML(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

function linkify(text) {
    const escapedText = escapeHTML(text);
    const urlRegex = /(https?:\/\/[^\s]+)/g;

    return escapedText.replace(urlRegex, function(url) {
        return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
    });
}

function addMessage(message, sender) {
    const messageDiv = document.createElement("div");
    messageDiv.classList.add("message");

    if (sender === "user") {
        messageDiv.classList.add("user-message");
        messageDiv.innerText = message;
    } else {
        messageDiv.classList.add("bot-message");
        messageDiv.innerHTML = linkify(message);
    }

    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    saveChatHistory();
}

function addTyping() {
    const typingDiv = document.createElement("div");
    typingDiv.classList.add("message", "bot-message", "typing");
    typingDiv.id = "typing";
    typingDiv.innerText = "Chatbot sedang mengetik...";
    chatBox.appendChild(typingDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function removeTyping() {
    const typingDiv = document.getElementById("typing");
    if (typingDiv) {
        typingDiv.remove();
    }
}

function sendMessage() {
    const message = userInput.value.trim();

    if (message === "") return;

    addMessage(message, "user");
    userInput.value = "";
    addTyping();

    const basePath = window.location.pathname.includes('/klinikdoktergigi/') ? '/klinikdoktergigi/' : '/';
    const chatbotUrl = `${basePath}api_chatbot.php`;

    fetch(chatbotUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        removeTyping();

        if (data.response) {
            addMessage(data.response, "bot");
        } else {
            addMessage("Maaf, respons chatbot tidak tersedia.", "bot");
        }
    })
    .catch(error => {
        removeTyping();
        addMessage("Maaf, server chatbot belum aktif. Pastikan Flask API sedang berjalan.", "bot");
        console.error(error);
    });
}

if (userInput) {
    userInput.addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get("logout") === "1") {
        clearChatHistory();
        params.delete("logout");
        const cleanUrl = window.location.pathname + (params.toString() ? `?${params.toString()}` : "") + window.location.hash;
        window.history.replaceState({}, "", cleanUrl);
    }

    loadChatHistory();
});
