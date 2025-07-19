<script lang="ts">
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';
import { onMount } from 'svelte';

const categoryOptions = [
  { value: 'header', label: 'Header' },
  { value: 'side', label: 'Side' },
  { value: 'footer', label: 'Footer' }
];

let parentOptions = $state([{ value: '', label: '-- No parent --' }]);
let selectedCategory = $state('side');

async function fetchParentOptions(category: string) {
  const res = await fetch(`http://localhost:8000/api/side-menu-links?category=${category}`);
  if (res.ok) {
    const links = await res.json();
    parentOptions = [{ value: '', label: '-- No parent --' }, ...links.map((l: any) => ({ value: l.id, label: l.title }))];
  }
}

onMount(() => fetchParentOptions(selectedCategory));
$effect(() => { fetchParentOptions(selectedCategory); });

const fields = [
  { name: 'title', label: 'Title', type: 'text', required: true },
  { name: 'url', label: 'URL', type: 'text', required: true },
  { name: 'category', label: 'Category', type: 'select', required: true, options: categoryOptions, value: selectedCategory },
  { name: 'parent_id', label: 'Parent Link', type: 'parent-link-select', required: false },
  { name: 'active', label: 'Active', type: 'checkbox', required: false },
  { name: 'description', label: 'Description', type: 'text', required: false }
];

let addError = $state('');
let addSuccess = $state('');

async function addLink(values: any) {
  addError = '';
  addSuccess = '';
  if (!values.title || !values.url || !values.category) {
    addError = 'Title, URL, and Category are required.';
    return;
  }
  try {
    const res = await fetch('http://localhost:8000/api/side-menu-links', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to add link');
    addSuccess = 'Link added!';
    setTimeout(() => goto('/cms/side-menu-links'), 800);
  } catch (e: any) {
    addError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/side-menu-links');
}
</script>

<h1>Add Side Menu Link</h1>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addLink(e.detail)}
  submitLabel="Add Link"
/>
{#if addError}<span class="error">{addError}</span>{/if}
{#if addSuccess}<span class="success">{addSuccess}</span>{/if}
<button on:click={goBack} class="back-btn">Back</button> 