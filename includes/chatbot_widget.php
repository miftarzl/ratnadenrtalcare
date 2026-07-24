<div class="chatbot-button" onclick="toggleChatbot()">
    💬
</div>

<div class="chatbot-popup" id="chatbotPopup">
    <div class="chat-header">
        <div>
            <h3>Chatbot Klinik Gigi</h3>
            <p>Informasi & Konsultasi Awal</p>
        </div>
        <button onclick="toggleChatbot()" class="close-chat">×</button>
    </div>

    <div class="chat-disclaimer">
        Chatbot ini hanya memberikan informasi umum dan konsultasi awal, bukan pengganti diagnosis dokter gigi.
    </div>

    <div class="chat-box" id="chatBox">
        <div class="message bot-message">
            Halo! Saya chatbot Klinik Gigi. Ada yang bisa saya bantu?
        </div>
    </div>

    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Tulis pertanyaan Anda..." autocomplete="off">
        <button onclick="sendMessage()">Kirim</button>
    </div>
</div>