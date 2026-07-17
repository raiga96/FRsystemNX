<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
<!-- <button id="open-chat" style="position:fixed;bottom:100px;right:24px;z-index:9999;padding:12px 18px;background-color:#2684DA;color:white;border:none;border-radius:24px;font-weight:bold;box-shadow:0 4px 8px rgba(0,0,0,0.2);cursor:pointer;">
  💬 Chat with Commander
</button> -->
<script type="module">
  import {
    createChat
  } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

  createChat({
    // webhookUrl: 'https://darmizi.app.n8n.cloud/webhook/499666c3-d807-4bb7-8195-43932f64a91f/chat',
    webhookUrl: 'https://sassoku.app.n8n.cloud/webhook/499666c3-d807-4bb7-8195-43932f64a91f/chat',

    initialMessages: ["Hi there! 👋", "My name is Commander. How can I assist you today?"],
    i18n: {
      en: {
        title: "Hi there! 👋",
        subtitle: "Chat with Commander. We're here to help you 24/7.",
        footer: "",
        getStarted: "New Conversation",
        inputPlaceholder: "Type your question..",
        closeButtonTooltip: "Close chat"
      }
    },
  });
</script>
<footer class="footer py-4  ">
  <div class="container-fluid">
    <div class="row align-items-center justify-content-lg-between">
      <div class="col-lg-4 mb-lg-0 mb-4">
        <div class="copyright text-center text-sm text-muted text-lg-start">
          © <script>
            document.write(new Date().getFullYear())
          </script>,
          all right reserve by
          <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">HQ Survey</a>

        </div>
      </div>

      <div class="col-lg-3">
        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
          <li class="nav-item">
            <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>
          </li>
          <li class="nav-item">
            <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a>
          </li>
          <li class="nav-item">
            <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>
          </li>
          <li class="nav-item">
            <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</footer>