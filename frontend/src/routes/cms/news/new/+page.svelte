<script lang="ts">
import FormBuilder from '$lib/FormBuilder.svelte';
import { goto } from '$app/navigation';
import { onMount } from 'svelte';

const fields = [
  { name: 'title', label: 'Title', type: 'text', required: true },
  { name: 'content', label: 'Content', type: 'textarea', required: true, rows: 6 },
  // image_url handled separately
  { name: 'published_at', label: 'Published At', type: 'date', required: false },
];

let addError = $state('');
let addSuccess = $state('');
let imageUrl = $state('');
let imageUploading = $state(false);
let imageUploadError = $state('');

async function addNews(values: any) {
  addError = '';
  addSuccess = '';
  values.image_url = imageUrl;
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

async function handleImageUpload(e: Event) {
  imageUploadError = '';
  imageUploading = true;
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  const formData = new FormData();
  formData.append('image', file);
  try {
    const res = await fetch('http://localhost:8000/api/news-image', {
      method: 'POST',
      body: formData,
    });
    if (!res.ok) throw new Error('Failed to upload image');
    const data = await res.json();
    imageUrl = 'http://localhost:8000' + data.path;
  } catch (err: any) {
    imageUploadError = (err as any).message;
  } finally {
    imageUploading = false;
  }
}

function goBack() {
  goto('/cms/news');
}
</script>

<h1>Add News</h1>
<div class="image-upload-row">
  <label>Image Upload<br />
    <input type="file" accept="image/*" on:change={handleImageUpload} />
  </label>
  {#if imageUploading}<span>Uploading...</span>{/if}
  {#if imageUrl}
    <img src={imageUrl} alt="Preview" class="image-preview" />
  {/if}
  {#if imageUploadError}<span class="error">{imageUploadError}</span>{/if}
</div>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addNews(e.detail)}
  submitLabel="Add News"
/>
<button on:click={goBack} class="back-btn">Back</button>
{#if addError}<span class="error">{addError}</span>{/if}
{#if addSuccess}<span class="success">{addSuccess}</span>{/if}

<style>
.image-upload-row {
  margin-bottom: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
.image-preview {
  max-width: 320px;
  max-height: 180px;
  border-radius: 0.5rem;
  box-shadow: 0 1px 4px #0002;
}
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