<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';
import { page } from '$app/stores';

const id = $page.params.id;
let loading = $state(true);
let error = $state('');
let saveError = $state('');
let saveSuccess = $state('');
let newsItem = $state<any>(null);

const fieldsConfig = [
  { name: 'title', label: 'Title', type: 'text', required: true },
  { name: 'summary', label: 'Samenvatting', type: 'text', required: false, placeholder: 'Korte samenvatting voor in het overzicht' },
  { name: 'content', label: 'Content', type: 'textarea', required: true, rows: 6 },
  { name: 'published_at', label: 'Published At', type: 'date', required: false },
  { name: 'image_url', label: 'Afbeelding', type: 'image-upload', entity_type: 'App\\Models\\News', entity_id: id },
];

onMount(fetchNewsItem);

async function fetchNewsItem() {
  loading = true;
  error = '';
  try {
    const res = await fetch(`http://localhost:8000/api/news/${id}`);
    if (!res.ok) throw new Error('Failed to fetch news item');
    newsItem = await res.json();
  } catch (e: any) {
    error = (e as any).message;
  } finally {
    loading = false;
  }
}

function toFields(news: any) {
  return fieldsConfig.map(f => {
    let value = news?.[f.name] ?? '';
    if (f.type === 'date' && value) {
      value = value.slice(0, 10);
    }
    if (f.type === 'image-upload') {
      value = news?.image_url ? (news.image_url.startsWith('http') ? news.image_url : 'http://localhost:8000' + news.image_url) : '';
      return { ...f, value, entity_type: 'App\\Models\\News', entity_id: news?.id ?? id };
    }
    return { ...f, value };
  });
}

async function saveNews(values: any) {
  saveError = '';
  saveSuccess = '';
  try {
    const res = await fetch(`http://localhost:8000/api/news/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) {
      let msg = `Failed to update news (status ${res.status})`;
      try {
        const data = await res.json();
        if (data?.message) msg = data.message;
      } catch {}
      throw new Error(msg);
    }
    saveSuccess = 'News updated!';
    setTimeout(() => goto('/cms/news'), 800);
  } catch (e: any) {
    saveError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/news');
}
</script>

<h1>Edit News</h1>
{#if loading}
  <p>Loading...</p>
{:else if error}
  <p class="error">{error}</p>
{:else}
  <FormBuilder
    fields={toFields(newsItem)}
    on:submit={(e: any) => saveNews(e.detail)}
    submitLabel="Save News"
  />
  <button on:click={goBack} class="back-btn">Back</button>
  {#if saveError}<span class="error">{saveError}</span>{/if}
  {#if saveSuccess}<span class="success">{saveSuccess}</span>{/if}
{/if}

<style>
.back-btn {
  margin-top: 1.5rem;
  background: #eee;
  color: #333;
  border: none;
  padding: 0.5rem 1.5rem;
  border-radius: 1.2rem;
  font-size: 1rem;
  cursor: pointer;
}
.back-btn:hover {
  background: #ddd;
}
.error {
  color: red;
  margin-left: 1rem;
}
.success {
  color: green;
  margin-left: 1rem;
}
</style> 