<!-- Floating Chat Widget -->
<div id="chat-widget" class="chat-widget shadow-lg">
  <div class="chat-header bg-success text-white p-3 d-flex justify-content-between align-items-center">
    <strong><i class="fas fa-seedling me-2"></i> FarmSmart AI</strong>
    <button class="btn btn-sm btn-light text-success fw-bold" id="close-chat">×</button>
  </div>

  <div class="chat-body p-3" id="chat-body">
    <div class="text-muted small text-center mb-2">Ask me anything about farming 🌾</div>
  </div>

  <div class="chat-input p-2 border-top bg-light d-flex">
    <input type="text" id="chat-input" class="form-control me-2" placeholder="Type your message...">
    <button id="chat-send" class="btn btn-success">Send</button>
  </div>
</div>

<!-- Floating Button -->
<button id="open-chat" class="open-chat-btn btn btn-success shadow-lg">
  <i class="fas fa-comments"></i>
</button>

<script>
  const openBtn = document.getElementById('open-chat');
  const closeBtn = document.getElementById('close-chat');
  const widget = document.getElementById('chat-widget');
  const chatBody = document.getElementById('chat-body');
  const sendBtn = document.getElementById('chat-send');
  const input = document.getElementById('chat-input');

  openBtn.onclick = () => widget.style.display = 'flex';
  closeBtn.onclick = () => widget.style.display = 'none';

  function appendMsg(sender, text) {
    const msg = document.createElement('div');
    msg.classList.add('my-2', 'd-flex', sender === 'user' ? 'justify-content-end' : 'justify-content-start');
    msg.innerHTML = `<div class="p-2 rounded-3 ${sender === 'user' ? 'bg-success text-white' : 'bg-light border'}">${text}</div>`;
    chatBody.appendChild(msg);
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  async function sendMsg() {
    const text = input.value.trim();
    if (!text) return;
    appendMsg('user', text);
    input.value = '';

    appendMsg('ai', '⏳ Typing...');
    try {
      const res = await fetch('{{ route("ai.chat") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: text })
      });
      const data = await res.json();
      chatBody.lastChild.remove();
      appendMsg('ai', data.reply || '⚠️ No response.');
    } catch (err) {
      chatBody.lastChild.remove();
      appendMsg('ai', '⚠️ Error: ' + err.message);
    }
  }

  sendBtn.onclick = sendMsg;
  input.addEventListener('keypress', e => {
    if (e.key === 'Enter') sendMsg();
  });
</script>

<style>
  .chat-widget {
    display: none;
    flex-direction: column;
    position: fixed;
    bottom: 90px;
    right: 30px;
    width: 360px;
    height: 480px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    z-index: 2000;
    border: 1px solid #dcdcdc;
  }

  .chat-body {
    flex: 1;
    overflow-y: auto;
    background-color: #f8f9fa;
  }

  .open-chat-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    font-size: 24px;
    z-index: 1000;
  }

  .chat-body::-webkit-scrollbar {
    width: 6px;
  }
  .chat-body::-webkit-scrollbar-thumb {
    background-color: #ccc;
    border-radius: 10px;
  }

  @media (max-width: 600px) {
    .chat-widget {
      width: 95%;
      right: 2.5%;
      bottom: 80px;
      height: 70%;
    }
  }
</style>
