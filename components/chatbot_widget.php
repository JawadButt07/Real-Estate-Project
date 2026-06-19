<!-- simple inline styles for AI chatbot widget -->
<style>
   .ai-chatbot-toggle{
      position:fixed;
      right:20px;
      bottom:20px;
      z-index:9999;
      background:#eb2f06;
      color:#fff;
      border-radius:50%;
      width:55px;
      height:55px;
      display:flex;
      align-items:center;
      justify-content:center;
      cursor:pointer;
      box-shadow:0 4px 12px rgba(0,0,0,.25);
   }
   .ai-chatbot-toggle i{
      font-size:22px;
   }
   .ai-chatbot-panel{
      position:fixed;
      right:20px;
      bottom:90px;
      width:320px;
      max-height:480px;
      background:#fff;
      border-radius:12px;
      box-shadow:0 8px 24px rgba(0,0,0,.25);
      display:none;
      flex-direction:column;
      overflow:hidden;
      z-index:9999;
   }
   .ai-chatbot-header{
      padding:12px 14px;
      background:#eb2f06;
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:space-between;
   }
   .ai-chatbot-header h3{
      margin:0;
      font-size:16px;
   }
   .ai-chatbot-close{
      cursor:pointer;
      font-size:16px;
   }
   .ai-chatbot-messages{
      padding:10px;
      background:#f7f7f7;
      overflow-y:auto;
      flex:1;
      font-size:13px;
   }
   .ai-msg{
      margin-bottom:8px;
      clear:both;
   }
   .ai-msg-user{
      text-align:right;
   }
   .ai-msg-user span{
      display:inline-block;
      background:#eb2f06;
      color:#fff;
      padding:6px 9px;
      border-radius:10px 10px 0 10px;
   }
   .ai-msg-bot{
      text-align:left;
   }
   .ai-msg-bot span{
      display:inline-block;
      background:#fff;
      border:1px solid #ddd;
      padding:6px 9px;
      border-radius:10px 10px 10px 0;
   }
   .ai-chatbot-input{
      border-top:1px solid #ddd;
      padding:8px;
      background:#fff;
      display:flex;
      gap:6px;
   }
   .ai-chatbot-input textarea{
      flex:1;
      resize:none;
      border:1px solid #ddd;
      border-radius:8px;
      padding:6px 8px;
      font-size:13px;
      height:40px;
   }
   .ai-chatbot-input button{
      background:#eb2f06;
      color:#fff;
      border:none;
      border-radius:8px;
      padding:0 12px;
      cursor:pointer;
      font-size:13px;
      min-width:70px;
   }
   .ai-chatbot-input button:disabled{
      opacity:.6;
      cursor:default;
   }
   @media (max-width:480px){
      .ai-chatbot-panel{
         right:10px;
         left:10px;
         width:auto;
      }
   }
</style>

<!-- AI Chatbot widget -->
<div class="ai-chatbot-toggle" id="aiChatbotToggle" title="Ask our AI assistant">
   <i class="fas fa-comments"></i>
</div>
<div class="ai-chatbot-panel" id="aiChatbotPanel">
   <div class="ai-chatbot-header">
      <h3>AI Property Assistant</h3>
      <span class="ai-chatbot-close" id="aiChatbotClose">&times;</span>
   </div>
   <div class="ai-chatbot-messages" id="aiChatbotMessages">
      <div class="ai-msg ai-msg-bot">
         <span>Assalamualaikum! Main aapko property search, budget aur listings ke baare mein madad kar sakta hoon. Aap kya dekhna chahte hain?</span>
      </div>
   </div>
   <div class="ai-chatbot-input">
      <textarea id="aiChatbotInput" placeholder="Apna sawal yahan likhein..."></textarea>
      <button id="aiChatbotSend">Send</button>
   </div>
</div>

<script>
   // Simple AI chatbot front-end logic
   const aiToggle = document.getElementById('aiChatbotToggle');
   const aiPanel = document.getElementById('aiChatbotPanel');
   const aiClose = document.getElementById('aiChatbotClose');
   const aiMessages = document.getElementById('aiChatbotMessages');
   const aiInput = document.getElementById('aiChatbotInput');
   const aiSend = document.getElementById('aiChatbotSend');

   function aiAddMessage(text, from){
      const msg = document.createElement('div');
      msg.className = 'ai-msg ' + (from === 'user' ? 'ai-msg-user' : 'ai-msg-bot');
      const span = document.createElement('span');
      span.textContent = text;
      msg.appendChild(span);
      aiMessages.appendChild(msg);
      aiMessages.scrollTop = aiMessages.scrollHeight;
   }

   function aiSetLoading(isLoading){
      aiSend.disabled = isLoading;
      if(isLoading){
         aiSend.textContent = '...';
      }else{
         aiSend.textContent = 'Send';
      }
   }

   async function aiSendMessage(){
      const text = aiInput.value.trim();
      if(!text) return;
      aiAddMessage(text, 'user');
      aiInput.value = '';
      aiSetLoading(true);

      try{
         const formData = new FormData();
         formData.append('message', text);

         const res = await fetch('chatbot.php', {
            method: 'POST',
            body: formData
         });

         const raw = await res.text();
         let data = null;

         try{
            data = JSON.parse(raw);
         }catch(parseError){
            aiAddMessage('Server ne invalid response diya. Please developer se console/server logs check karwain.', 'bot');
            return;
         }

         if(!res.ok){
            aiAddMessage(data.reply || ('Server error (' + res.status + ').'), 'bot');
            return;
         }

         if(data && data.reply){
            aiAddMessage(data.reply, 'bot');
         }else{
            aiAddMessage('Maaf kijiyega, abhi jawab nahi aa saka. Thori der baad dobara koshish karein.', 'bot');
         }
      }catch(e){
         aiAddMessage('Request fail hui. Internet ya server URL check karein, phir dobara try karein.', 'bot');
      }finally{
         aiSetLoading(false);
      }
   }

   if(aiToggle && aiPanel){
      aiToggle.addEventListener('click', () => {
         aiPanel.style.display = (aiPanel.style.display === 'flex') ? 'none' : 'flex';
      });
   }

   if(aiClose){
      aiClose.addEventListener('click', () => {
         aiPanel.style.display = 'none';
      });
   }

   if(aiSend){
      aiSend.addEventListener('click', aiSendMessage);
   }

   if(aiInput){
      aiInput.addEventListener('keydown', (e) => {
         if(e.key === 'Enter' && !e.shiftKey){
            e.preventDefault();
            aiSendMessage();
         }
      });
   }
</script>
