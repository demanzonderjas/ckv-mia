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
let link = $state<any>(null);

const categoryOptions = [
  { value: 'header', label: 'Header' },
  { value: 'side', label: 'Side' },
  { value: 'footer', label: 'Footer' }
];
let parentOptions = $state([{ value: '', label: '-- No parent --' }]);
let selectedCategory = $state('side');

async function fetchParentOptions(category: string) {
  const res = await fetch(`http://localhost:8000/api/menu-links?category=${category}`);
  if (res.ok) {
    const links = await res.json();
    parentOptions = [{ value: '', label: '-- No parent --' }, ...links.filter((l: any) => l.id != id).map((l: any) => ({ value: l.id, label: l.title }))];
  }
}

onMount(async () => {
  await fetchLink();
  selectedCategory = link?.category || 'side';
  await fetchParentOptions(selectedCategory);
});
$effect(() => { fetchParentOptions(selectedCategory); });

const fieldsConfig = [
  { name: 'title', label: 'Title', type: 'text', required: true },
  { name: 'url', label: 'URL', type: 'text', required: true },
  { name: 'category', label: 'Category', type: 'select', required: true, options: categoryOptions, value: selectedCategory },
  { name: 'parent_id', label: 'Parent Link', type: 'parent-link-select', required: false, excludeId: id },
  { name: 'active', label: 'Active', type: 'checkbox', required: false },
  { name: 'description', label: 'Description', type: 'text', required: false }
];

async function fetchLink() {
  loading = true;
  error = '';
  try {
    const res = await fetch(`http://localhost:8000/api/menu-links/${id}`);
    if (!res.ok) throw new Error('Failed to fetch link');
    link = await res.json();
  } catch (e: any) {
    error = (e as any).message;
  } finally {
    loading = false;
  }
}

function toFields(link: any) {
  return fieldsConfig.map(f => ({ ...f, value: link?.[f.name] ?? (f.type === 'checkbox' ? false : '') }));
}

async function saveLink(values: any) {
  saveError = '';
  saveSuccess = '';
  try {
    const res = await fetch(`http://localhost:8000/api/menu-links/${id}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to update link');
    saveSuccess = 'Link updated!';
    setTimeout(() => goto('/cms/menu-links'), 800);
  } catch (e: any) {
    saveError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/menu-links');
}
</script>

<h1>Edit Side Menu Link</h1>
{#if loading}
  <p>Loading...</p>
{:else if error}
  <span class="error">{error}</span>
{:else}
  <FormBuilder
    fields={toFields(link)}
    on:submit={(e: any) => saveLink(e.detail)}
    submitLabel="Save Changes"
  />
  {#if saveError}<span class="error">{saveError}</span>{/if}
  {#if saveSuccess}<span class="success">{saveSuccess}</span>{/if}
  <button on:click={goBack} class="back-btn">Back</button>
{/if} 