<script lang="ts">
import FormBuilder from '$lib/FormBuilder.svelte';
import { goto } from '$app/navigation';

const fields = [
  { name: 'title', label: 'Title', type: 'text', required: true },
  { name: 'summary', label: 'Samenvatting', type: 'text', required: false, placeholder: 'Korte samenvatting voor in het overzicht' },
  { name: 'content', label: 'Content', type: 'textarea', required: true, rows: 6 },
  { name: 'published_at', label: 'Published At', type: 'date', required: false },
  { name: 'image_url', label: 'Afbeelding', type: 'image-upload', entity_type: 'App\\Models\\News', entity_id: null },
];

let addError = $state('');
let addSuccess = $state('');

async function addNews(values: any) {
  addError = '';
  addSuccess = '';
  try {
    const res = await fetch('http://localhost:8000/api/news', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) {
      let msg = `Failed to add news (status ${res.status})`;
      try {
        const data = await res.json();
        if (data?.message) msg = data.message;
      } catch {}
      throw new Error(msg);
    }
    addSuccess = 'News added!';
    setTimeout(() => goto('/cms/news'), 800);
  } catch (e: any) {
    addError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/news');
}
</script>

<h1>Add News</h1>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addNews(e.detail)}
  submitLabel="Add News"
/>
<button on:click={goBack} class="back-btn">Back</button>
{#if addError}<span class="error">{addError}</span>{/if}
{#if addSuccess}<span class="success">{addSuccess}</span>{/if}

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