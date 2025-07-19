<script lang="ts">
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';

const fields = [
  { name: 'name', label: 'Name', type: 'text', required: true },
  { name: 'description', label: 'Description', type: 'textarea', required: false },
  { name: 'category', label: 'Category', type: 'select', required: true, options: [
    { value: 'wedstrijdkorfbal', label: 'Wedstrijdkorfbal' },
    { value: 'breedtekorfbal', label: 'Breedtekorfbal' },
    { value: 'midweek', label: 'Midweek' }
  ]},
  { name: 'type', label: 'Type', type: 'select', required: true, options: [
    { value: 'senior', label: 'Senior' },
    { value: 'junior', label: 'Junior' }
  ]}
];

let addError = $state('');
let addSuccess = $state('');

async function addTeam(values: any) {
  addError = '';
  addSuccess = '';
  if (!values.name || !values.category || !values.type) {
    addError = 'Name, category, and type are required.';
    return;
  }
  try {
    const res = await fetch('http://localhost:8000/api/teams', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to add team');
    addSuccess = 'Team added!';
    setTimeout(() => goto('/cms/teams'), 800);
  } catch (e: any) {
    addError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/teams');
}
</script>

<h1>Add New Team</h1>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addTeam(e.detail)}
  submitLabel="Add Team"
/>
{#if addError}<span class="error">{addError}</span>{/if}
{#if addSuccess}<span class="success">{addSuccess}</span>{/if}
<button on:click={goBack} class="back-btn">Back</button> 