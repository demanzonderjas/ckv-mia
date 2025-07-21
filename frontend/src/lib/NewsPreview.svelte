<script lang="ts">
  import { onMount } from 'svelte';
  import NewsItem from './NewsItem.svelte';
  type NewsItemType = {
    id: number;
    title: string;
    summary?: string;
    image_url?: string;
    category?: { name: string };
  };
  let news: NewsItemType[] = [];
  let loading = true;
  let error: string = '';

  onMount(async () => {
    try {
      const res = await fetch('http://localhost:8000/api/news');
      if (!res.ok) throw new Error('Failed to fetch news');
      const allNews = await res.json();
      news = (allNews as NewsItemType[]).slice(0, 3);
    } catch (e) {
      error = e instanceof Error ? e.message : String(e);
      // fallback mock data
      news = [
        { image_url: '/news1.jpg', title: 'Nieuwe coach voor A1', id: 1 },
        { image_url: '/news2.jpg', title: 'Verslag van de jeugdtoernooi', id: 2 },
        { image_url: '/news3.jpg', title: 'Inschrijving nieuwe leden', id: 3 }
      ];
    } finally {
      loading = false;
    }
  });
</script>
<section class="news-preview">
  <h3>Nieuws</h3>
  {#if loading}
    <div>Laden...</div>
  {:else if error}
    <div style="color: red">{error}</div>
  {/if}
  <div class="news-list">
    {#each news as item}
      <NewsItem news={item} />
    {/each}
  </div>
</section>
<style>
.news-preview { padding: 2rem 0; background: var(--background); }
.news-preview h3 { color: var(--primary); font-size: 1.5rem; margin-bottom: 1.2rem; }
.news-list { display: flex; gap: 2rem; }
@media (max-width: 900px) {
  .news-list { flex-direction: column; gap: 1.2rem; }
}
</style> 