<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';
import { page } from '$app/state';

const id = page.params.id;
let loading = $state(true);
let error = $state('');
let saveError = $state('');
let saveSuccess = $state('');
let eventItem = $state<any>(null);
let imageUrl = $state('');
let imageUploadId = $state<number|null>(null);
let imageUploading = $state(false);
let imageUploadError = $state('');
let fileInput: HTMLInputElement | null = null;

const fieldsConfig = [
  { name: 'name', label: 'Naam', type: 'text', required: true },
  { name: 'description', label: 'Beschrijving', type: 'rich', required: false },
  { name: 'date', label: 'Datum', type: 'date', required: true },
  { name: 'location', label: 'Locatie', type: 'text', required: true },
  { name: 'team_id', label: 'Team', type: 'team-select', required: false },
  { name: 'image', label: 'Afbeelding', type: 'image-upload', entity_type: 'App\\Models\\Event', entity_id: id },
];

onMount(fetchEventItem);

async function fetchEventItem() {
  loading = true;
  error = '';
  try {
    const res = await fetch(`http://localhost:8000/api/events/${id}`);
    if (!res.ok) throw new Error('Failed to fetch event');
    eventItem = await res.json();
    imageUrl = eventItem.image?.path ? ('http://localhost:8000' + eventItem.image.path) : '';
    imageUploadId = eventItem.image?.id || null;
  } catch (e: any) {
    error = (e as any).message;
  } finally {
    loading = false;
  }
}

function toFields(event: any) {
  return fieldsConfig.map(f => {
    let value = event?.[f.name] ?? '';
    if (f.type === 'date' && value) {
      value = value.slice(0, 10);
    }
    if (f.type === 'image-upload') {
      value = event?.image?.path ? 'http://localhost:8000' + event.image.path : '';
    }
    return { ...f, value };
  });
}

async function saveEvent(values: any) {
  saveError = '';
  saveSuccess = '';
  try {
    const res = await fetch(`http://localhost:8000/api/events/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) {
      let msg = `Failed to update event (status ${res.status})`;
      try {
        const data = await res.json();
        if (data?.message) msg = data.message;
      } catch {}
      throw new Error(msg);
    }
    saveSuccess = 'Event bijgewerkt!';
    setTimeout(() => goto('/cms/events'), 800);
  } catch (e: any) {
    saveError = (e as any).message;
  }
}

async function handleImageUpload(e: Event) {
  imageUploadError = '';
  imageUploading = true;
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  const formData = new FormData();
  formData.append('image', file);
  formData.append('entity_type', 'App\\Models\\Event');
  formData.append('entity_id', id);
  try {
    const res = await fetch('http://localhost:8000/api/upload-image', {
      method: 'POST',
      body: formData,
    });
    if (!res.ok) throw new Error('Failed to upload image');
    const data = await res.json();
    imageUrl = 'http://localhost:8000' + data.path;
    imageUploadId = data.id;
    if (fileInput) fileInput.value = '';
  } catch (err: any) {
    imageUploadError = (err as any).message;
  } finally {
    imageUploading = false;
  }
}

async function deleteImage() {
  imageUploadError = '';
  if (!imageUrl) return;
  if (!confirm('Weet je zeker dat je deze afbeelding wilt verwijderen?')) return;
  try {
    const res = await fetch('http://localhost:8000/api/delete-image', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ path: imageUrl.replace('http://localhost:8000', '') })
    });
    if (!res.ok) throw new Error('Failed to delete image');
    imageUrl = '';
    imageUploadId = null;
    if (fileInput) fileInput.value = '';
  } catch (err: any) {
    imageUploadError = (err as any).message;
  }
}

function goBack() {
  goto('/cms/events');
}
</script>

<h1>Event bewerken</h1>
{#if loading}
  <p>Loading...</p>
{:else if error}
  <p class="error">{error}</p>
{:else}
  <FormBuilder
    fields={toFields(eventItem)}
    on:submit={(e: any) => saveEvent(e.detail)}
    submitLabel="Event opslaan"
  >
    <div slot="beforeSubmit">
      <div class="image-upload-row">
        <label>Afbeelding uploaden<br />
          <input type="file" accept="image/*" bind:this={fileInput} on:change={handleImageUpload} />
        </label>
        {#if imageUploading}<span>Uploading...</span>{/if}
        {#if imageUrl}
          <div class="image-preview-row">
            <img src={imageUrl} alt="Preview" class="image-preview" />
            <button type="button" class="delete-image-btn" on:click={deleteImage}>Verwijder afbeelding</button>
          </div>
        {/if}
        {#if imageUploadError}<span class="error">{imageUploadError}</span>{/if}
      </div>
    </div>
  </FormBuilder>
  <button on:click={goBack} class="back-btn">Terug</button>
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