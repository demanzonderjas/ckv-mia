<script lang="ts">
  import { onMount } from 'svelte';
  type MenuLink = {
    id: number;
    title: string;
    url: string;
    icon?: string;
  };
  let links: MenuLink[] = [];
  let loading = true;
  let error: string = '';

  onMount(async () => {
    try {
      const res = await fetch('http://localhost:8000/api/menu-links?category=side');
      if (!res.ok) throw new Error('Failed to fetch links');
      links = await res.json();
    } catch (e) {
      error = e instanceof Error ? e.message : String(e);
      // fallback static links
      links = [
        { title: 'Teams', url: '/teams', icon: '👥', id: 1 },
        { title: 'Competities', url: '/competitions', icon: '🏆', id: 2 },
        { title: 'Evenementen', url: '/events', icon: '📅', id: 3 },
        { title: 'Contact', url: '/contact', icon: '✉️', id: 4 }
      ];
    } finally {
      loading = false;
    }
  });
</script>
<section class="quick-links-section">
  <h3>Snel naar</h3>
  {#if loading}
    <div>Laden...</div>
  {:else if error}
    <div style="color: red">{error}</div>
  {/if}
  <div class="quick-links-row">
    {#each links as link}
      <a class="quick-link" href={link.url}>
        <span class="icon">{link.icon || '🔗'}</span> {link.title}
      </a>
    {/each}
  </div>
</section>
<style>
.quick-links-section { margin: 2.5rem 0 0 0; }
.quick-links-section h3 { color: var(--primary); font-size: 1.2rem; margin-bottom: 1rem; }
.quick-links-row { display: flex; gap: 2rem; flex-wrap: wrap; }
.quick-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #fff;
  border-radius: 2rem;
  padding: 0.7rem 1.5rem;
  color: var(--primary);
  font-weight: 600;
  text-decoration: none;
  font-size: 1.08rem;
  box-shadow: 0 2px 8px #0001;
  transition: background 0.15s, color 0.15s, box-shadow 0.15s;
}
.quick-link .icon { font-size: 1.3rem; }
.quick-link:hover {
  background: var(--primary);
  color: #fff;
  box-shadow: 0 4px 16px #0002;
}
</style> 