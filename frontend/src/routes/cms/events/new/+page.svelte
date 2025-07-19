<script lang="ts">
import FormBuilder from '$lib/FormBuilder.svelte';
import { goto } from '$app/navigation';

const fields = [
  { name: 'name', label: 'Naam', type: 'text', required: true },
  { name: 'description', label: 'Beschrijving', type: 'rich', required: false },
  { name: 'date', label: 'Datum', type: 'date', required: true },
  { name: 'location', label: 'Locatie', type: 'text', required: true },
  { name: 'team_id', label: 'Team', type: 'team-select', required: false },
  { name: 'image', label: 'Afbeelding', type: 'image-upload', entity_type: 'App\\Models\\Event' },
];

let addError = $state('');
let addSuccess = $state('');

async function addEvent(values: any) {
  addError = '';
  addSuccess = '';
  try {
    const res = await fetch('http://localhost:8000/api/events', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) {
      let msg = `Failed to add event (status ${res.status})`;
      try {
        const data = await res.json();
        if (data?.message) msg = data.message;
      } catch {}
      throw new Error(msg);
    }
    addSuccess = 'Event toegevoegd!';
    setTimeout(() => goto('/cms/events'), 800);
  } catch (e: any) {
    addError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/events');
}
</script>

<h1>Event toevoegen</h1>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addEvent(e.detail)}
  submitLabel="Event toevoegen"
/>
<button on:click={goBack} class="back-btn">Terug</button>
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