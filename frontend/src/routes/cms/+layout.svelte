<script>
  // No logic needed for now
</script>

<div class="cms-layout">
  <nav class="cms-menu">
    <a href="/cms">Dashboard</a>
    <a href="/cms/pages">Pages</a>
    <a href="/cms/news">News</a>
    <a href="/cms/events">Events</a>
    <a href="/cms/teams">Teams</a>
    <a href="/cms/members">Members</a>
    <a href="/cms/side-menu-links">Side Menu Links</a>
    <a href="/">← Back to site</a>
  </nav>
  <main>
    <slot />
  </main>
</div>

<style>
  .cms-layout { display: flex; min-height: 100vh; }
  .cms-menu { width: 220px; background: #f7f7fa; padding: 2rem 1rem; }
  .cms-menu a { display: block; margin-bottom: 1rem; color: #333; font-weight: 500; text-decoration: none; }
  .cms-menu a:hover { color: var(--primary-orange); }
  main { flex: 1; padding: 2rem; }
</style> 