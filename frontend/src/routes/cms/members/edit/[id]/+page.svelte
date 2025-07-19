<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';
import { page } from '$app/state';
import TeamSelect from '$lib/TeamSelect.svelte';

const id = page.params.id;
let loading = $state(true);
let error = $state('');
let saveError = $state('');
let saveSuccess = $state('');
let member = $state<any>(null);

const fieldsConfig = [
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

onMount(fetchMember);

async function fetchMember() {
  loading = true;
  error = '';
  try {
    const res = await fetch(`http://localhost:8000/api/members/${id}`);
    if (!res.ok) throw new Error('Failed to fetch member');
    member = await res.json();
  } catch (e: any) {
    error = (e as any).message;
  } finally {
    loading = false;
  }
}

function toFields(member: any) {
  return fieldsConfig.map(f => ({ ...f, value: member?.[f.name] ?? '' }));
}

async function saveMember(values: any) {
  saveError = '';
  saveSuccess = '';
  try {
    const res = await fetch(`http://localhost:8000/api/members/${id}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to update member');
    saveSuccess = 'Member updated!';
    setTimeout(() => goto('/cms/members'), 800);
  } catch (e: any) {
    saveError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/members');
}
</script>

<h1>Edit Member</h1>
{#if loading}
  <p>Loading...</p>
{:else if error}
  <span class="error">{error}</span>
{:else}
  <FormBuilder
    fields={toFields(member)}
    on:submit={(e: any) => saveMember(e.detail)}
    submitLabel="Save Changes"
  />
  {#if saveError}<span class="error">{saveError}</span>{/if}
  {#if saveSuccess}<span class="success">{saveSuccess}</span>{/if}
  <button on:click={goBack} class="back-btn">Back</button>
{/if} 