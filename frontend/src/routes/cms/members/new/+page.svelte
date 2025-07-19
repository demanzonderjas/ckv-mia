<script lang="ts">
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';
import TeamSelect from '$lib/TeamSelect.svelte';

const fields = [
  { name: 'first_name', label: 'Voornaam', type: 'text', required: true },
  { name: 'last_name', label: 'Achternaam', type: 'text', required: true },
  { name: 'gender', label: 'Geslacht', type: 'select', required: true, options: [
    { value: 'male', label: 'Man' },
    { value: 'female', label: 'Vrouw' }
  ]},
  { name: 'email', label: 'E-mail', type: 'text', required: true },
  { name: 'phone', label: 'Telefoon', type: 'text', required: false },
  { name: 'team_id', label: 'Team', type: 'team-select', required: true }
];

let addError = $state('');
let addSuccess = $state('');

async function addMember(values: any) {
  addError = '';
  addSuccess = '';
  if (!values.first_name || !values.last_name || !values.gender || !values.email || !values.team_id) {
    addError = 'All required fields must be filled.';
    return;
  }
  try {
    const res = await fetch('http://localhost:8000/api/members', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to add member');
    addSuccess = 'Member added!';
    setTimeout(() => goto('/cms/members'), 800);
  } catch (e: any) {
    addError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/members');
}
</script>

<h1>Add New Member</h1>
<FormBuilder
  fields={fields}
  on:submit={(e: any) => addMember(e.detail)}
  submitLabel="Add Member"
/>
{#if addError}<span class="error">{addError}</span>{/if}
{#if addSuccess}<span class="success">{addSuccess}</span>{/if}
<button on:click={goBack} class="back-btn">Back</button> 