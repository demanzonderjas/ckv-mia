<script lang="ts">
  import { onMount } from 'svelte';
  type NewsItem = {
    id: number;
    title: string;
    image_url?: string;
  };
  let news: NewsItem[] = [];
  let loading = true;
  let error: string = '';

  onMount(async () => {
    try {
      const res = await fetch('http://localhost:8000/api/news');
      if (!res.ok) throw new Error('Failed to fetch news');
      const allNews = await res.json();
      news = (allNews as NewsItem[]).slice(0, 3);
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
      <a class="news-item" href={item.id ? `/news/${item.id}` : '#'}>
        <img src={item.image_url || '/news1.jpg'} alt={item.title} />
        <div>
          <h4>{item.title}</h4>
          <span class="read-more">Read more &rarr;</span>
        </div>
      </a>
    {/each}
  </div>
</section>
<style>
.news-preview { padding: 2rem 0; background: var(--background); }
.news-preview h3 { color: var(--primary); font-size: 1.5rem; margin-bottom: 1.2rem; }
.news-list { display: flex; gap: 2rem; }
.news-item {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px #0001;
  width: 240px;
  text-decoration: none;
  color: inherit;
  transition: box-shadow 0.15s, transform 0.15s;
}
.news-item:hover {
  box-shadow: 0 4px 16px #0002;
  transform: translateY(-2px) scale(1.02);
}
.news-item img { width: 100%; height: 110px; object-fit: cover; }
.news-item h4 { margin: 0.5rem 1rem 0.25rem; font-size: 1.05rem; font-weight: 600; }
.news-item a { margin: 0 1rem 1rem; color: var(--primary); text-decoration: none; font-weight: 500; font-size: 0.98rem; display: block; }
.news-item a:hover { text-decoration: underline; }
.read-more {
  margin: 0 1rem 1rem;
  color: var(--primary);
  font-weight: 500;
  font-size: 0.98rem;
  display: block;
}
@media (max-width: 900px) {
  .news-list { flex-direction: column; gap: 1.2rem; }
  .news-item { width: 100%; }
}
</style> 