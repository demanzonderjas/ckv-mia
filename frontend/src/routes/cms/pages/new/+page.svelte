<script lang="ts">
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';

const fields = [
  { name: 'title', label: 'Title', type: 'text', required: true },
  { name: 'slug', label: 'Slug', type: 'text', required: true },
  { name: 'description', label: 'Description', type: 'text', required: false },
];

let addError = $state('');
let addSuccess = $state('');

async function addPage(values: any) {
  addError = '';
  addSuccess = '';
  if (!values.title || !values.slug) {
    addError = 'Title and slug are required.';
    return;
  }
  try {
    const res = await fetch('http://localhost:8000/api/pages', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) {
      let msg = `Failed to add page (status ${res.status})`;
      try {
        const data = await res.json();
        if (data?.message && data.message.toLowerCase().includes('slug')) {
          msg = 'Slug already exists. Please choose a unique slug.';
        } else if (data?.message) {
          msg = data.message;
        }
      } catch {
        // Not JSON, keep default msg
      }
      throw new Error(msg);
    }
    addSuccess = 'Page added!';
    setTimeout(() => goto('/cms/pages'), 800);
  } catch (e: any) {
    if (e instanceof TypeError) {
      addError = 'Network error: Could not reach backend.';
    } else {
      addError = (e as any).message;
    }
  }
}

function goBack() {
  goto('/cms/pages');
}
</script>

<h1>Add New Page</h1>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addPage(e.detail)}
  submitLabel="Add Page"
/>
{#if addError}<span class="error">{addError}</span>{/if}
{#if addSuccess}<span class="success">{addSuccess}</span>{/if}
<button on:click={goBack} class="back-btn">Back</button>

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