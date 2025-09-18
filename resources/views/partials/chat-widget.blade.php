<div id="floatingChat" class="floating-chat">
  <button type="button" class="chat-fab btn btn-success rounded-circle shadow">
    <i class="bi bi-chat-dots"></i>
  </button>

  <div class="chat-panel card shadow-lg">
    <div class="card-header d-flex align-items-center justify-content-between py-2">
      <strong>Messages</strong>
      <div class="d-flex align-items-center gap-1">
        {{-- Show chat list only (toggles the iframe to list-only mode) --}}
        <button type="button"
                class="btn btn-light btn-sm action-listonly"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Show chat list only"
                aria-pressed="false">
          <i class="bi bi-box-arrow-in-right"></i>
        </button>

        {{-- Minimize (closes the panel, leaves the FAB) --}}
        <button type="button"
                class="btn btn-light btn-sm action-minimize"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom"
                title="Minimize">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>
    </div>

    <iframe
      src="{{ route('chat') }}?embed=1"
      class="chat-iframe"
      title="Chat"
      allow="clipboard-read; clipboard-write"></iframe>
  </div>
</div>

<style>
  .floating-chat{position:fixed;right:16px;bottom:16px;z-index:1200}
  .floating-chat .chat-fab{
    width:56px;height:56px;display:flex;align-items:center;justify-content:center
  }

  .floating-chat .chat-panel{
    --header-h: 44px;
    --chat-w: clamp(420px, 40vw, 680px);
    --chat-h: min(74vh, 760px);

    position:absolute; right:0; bottom:calc(16px + env(safe-area-inset-bottom, 0px));
    width:var(--chat-w); height:var(--chat-h);
    max-height:calc(100vh - 96px);

    border-radius:16px;
    background:#fff;
    box-shadow:0 10px 30px rgba(0,0,0,.15);
    overflow:hidden;       /* ← prevent double scrollbars */
    display:none;
  }
  .floating-chat.open .chat-panel{display:block}
  .floating-chat.open .chat-fab{display:none} /* hide FAB while open */

  .floating-chat .card-header{height:var(--header-h)}
  .floating-chat .chat-iframe{
    border:0;display:block;
    width:100%;height:calc(100% - var(--header-h));
  }

  /* ========= Mobile (≤ 576px): turn panel into full-screen sheet ========= */
  @media (max-width: 575.98px){
    .floating-chat{right:12px;bottom:12px}
    .floating-chat .chat-panel{
      position:fixed; inset:0;
      width:100%; height:100vh; max-height:none;
      border-radius:0;
    }
    /* Use dynamic viewport on mobile browsers that support it */
    @supports (height: 100dvh){
      .floating-chat .chat-panel{ height:100dvh }
      .floating-chat .chat-iframe{ height:calc(100dvh - var(--header-h)) }
    }
    .floating-chat .card-header{
      border-radius:0; border-bottom:1px solid #eee;
    }
  }

  /* (Optional) prevent background scroll when chat is open on phones */
  .no-scroll,
  .no-scroll body{ overflow:hidden; touch-action:none; }

  /* Optional: slightly smaller header on very small phones */
  @media (max-width: 360px){
    .floating-chat .chat-panel{ --header-h: 40px; }
  }
</style>

<script>
(() => {
  const root   = document.getElementById('floatingChat');
  if (!root) return;

  const fab     = root.querySelector('.chat-fab');
  const listBtn = root.querySelector('.action-listonly');
  const miniBtn = root.querySelector('.action-minimize');
  const iframe  = root.querySelector('.chat-iframe');

  const isPhone = () => window.matchMedia('(max-width: 575.98px)').matches;

  function lockScroll(on){
    // only lock on phones so desktop can still scroll the page
    document.documentElement.classList.toggle('no-scroll', on && isPhone());
  }

  function openPanel(){
    root.classList.add('open');
    lockScroll(true);
  }
  function closePanel(){
    root.classList.remove('open');
    lockScroll(false);
  }

  // Open / close handlers
  fab?.addEventListener('click', openPanel);
  miniBtn?.addEventListener('click', closePanel);
  document.addEventListener('click', (e) => {
    if (!root.contains(e.target)) closePanel();
  });

  // (Optional) tooltips (Bootstrap 5)
  if (window.bootstrap) {
    [...root.querySelectorAll('[data-bs-toggle="tooltip"]')]
      .forEach(el => new bootstrap.Tooltip(el));
  }

  // List-only toggle persists in localStorage and reloads iframe with ?list=1
  const KEY = 'chat:listOnly';
  function setListOnly(on) {
    localStorage.setItem(KEY, on ? '1' : '0');
    try {
      const url = new URL(iframe.src, window.location.origin);
      if (on) url.searchParams.set('list', '1'); else url.searchParams.delete('list');
      iframe.src = url.toString();
    } catch {}
    listBtn?.setAttribute('aria-pressed', on ? 'true' : 'false');
  }
  if (listBtn) {
    const startListOnly = localStorage.getItem(KEY) === '1';
    if (startListOnly) setListOnly(true);
    listBtn.addEventListener('click', () => {
      const now = listBtn.getAttribute('aria-pressed') === 'true';
      setListOnly(!now);
    });
  }

  // Allow the iframe to tell us to minimize
  window.addEventListener('message', (ev) => {
    if (ev?.data?.type === 'CHAT_MINIMIZE') closePanel();
  });
})();
</script>


