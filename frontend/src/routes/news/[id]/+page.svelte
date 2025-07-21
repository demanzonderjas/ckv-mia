<script lang="ts">
import { onMount } from 'svelte';
import { page } from '$app/state';
import EditFab from '$lib/EditFab.svelte';
interface News {
  id: number;
  title: string;
  image_url?: string;
  content: string;
  published_at?: string;
  category?: { name: string };
}
let news = $state<News|null>(null);
let loading = $state(true);
let error = $state('');

onMount(async () => {
  const id = page.params.id;
  try {
    const res = await fetch(`http://localhost:8000/api/news/${id}`);
    if (!res.ok) throw new Error('Nieuwsbericht niet gevonden');
    news = await res.json();
  } catch (e: any) {
    error = e.message;
  } finally {
    loading = false;
  }
});
</script>

<svelte:head>
  <title>{news?.title ? `${news.title} | CKV MIA` : 'CKV MIA'}</title>
</svelte:head>

{#if loading}
  <p>Loading...</p>
{:else if error}
  <p class="error">{error}</p>
{:else if news}
  <EditFab href={`/cms/news/edit/${news.id}`} title="Bewerk dit nieuwsbericht" />
  <article class="news-detail">
    {#if news.image_url}
      <div class="news-header-image" style={`background-image: url('${news.image_url}')`}>
        <div class="news-header-overlay">
          {#if news.category?.name}
            <span class="category-tag"><span class="tag-icon">🏷️</span> {news.category.name.toUpperCase()}</span>
          {/if}
          <h1 class="news-title">{news.title}</h1>
        </div>
      </div>
    {:else}
      {#if news.category?.name}
        <span class="category-tag"><span class="tag-icon">🏷️</span> {news.category.name.toUpperCase()}</span>
      {/if}
      <h1 class="news-title">{news.title}</h1>
    {/if}
    <div class="news-content-card">
      {#if news.published_at}
        <p class="date">{new Date(news.published_at).toLocaleDateString('nl-NL', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
      {/if}
      <div class="news-content">{@html news.content}</div>
    </div>
  </article>
{/if}

<style>
.news-detail {
  padding: 0 0 1.5rem 0;
  width: 100vw;
  max-width: 100vw;
  margin-inline: 0;
  background: #fff;
}
.news-header-image {
  position: relative;
  height: 280px;
  width: 100%;
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: flex-end;
  margin-bottom: 1.2rem;
  box-shadow: 0 2px 12px #0002;
}
.news-header-overlay {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  padding-bottom: 2.2rem;
  background: linear-gradient(180deg, rgba(0,0,0,0) 60%, rgba(0,0,0,0.65) 100%);
}
.news-title {
  color: #fff;
  font-size: 2.2rem;
  font-weight: 900;
  text-shadow: 0 4px 24px #000b, 0 1px 2px #000a;
  margin: 0.2rem 0 0 0;
  line-height: 1.1;
  text-align: center;
  letter-spacing: -0.5px;
}
.news-content-card {
  background: #fff;
  padding: 2rem 1.2rem 2.2rem 1.2rem;
  max-width: 700px;
  margin: -2.5rem auto 0 auto;
  position: relative;
  z-index: 2;
}
.date {
  color: #ff6600;
  font-size: 1.08rem;
  margin-bottom: 0.7rem;
  font-weight: 600;
}
.news-content {
  font-size: 1.13rem;
  color: var(--primary-black);
  margin-top: 1.5rem;
}
.error {
  color: red;
  font-weight: bold;
}
.category-tag {
  display: inline-flex;
  align-items: center;
  background: var(--primary, #ff6600);
  color: #fff;
  font-size: 0.92rem;
  font-weight: 700;
  border-radius: 999px;
  padding: 0.25rem 1rem 0.25rem 0.7rem;
  margin: 1.2rem 1rem 0 1rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  box-shadow: 0 2px 8px #0002;
  gap: 0.5em;
}
.tag-icon {
  font-size: 1.1em;
  margin-right: 0.3em;
}
@media (min-width: 900px) {
  .news-detail {
    width: 100%;
    max-width: 900px;
    margin-inline: auto;
    border-radius: 1.2rem;
    box-shadow: 0 2px 12px #0001;
    padding: 0 0 1.5rem 0;
  }
  .news-header-image {
    border-radius: 1.2rem 1.2rem 0 0;
  }
  .news-content-card {
    border-radius: 1.2rem;
    box-shadow: 0 2px 12px #0001;
    margin: -3.5rem auto 0 auto;
  }
}
</style> 