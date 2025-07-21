<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';
import FormBuilder from '$lib/FormBuilder.svelte';
import { page } from '$app/state';
import TeamMembersEditor from '$lib/TeamMembersEditor.svelte';

const id = page.params.id;
let loading = $state(true);
let error = $state('');
let saveError = $state('');
let saveSuccess = $state('');
let team = $state<any>(null);
let members = $state([]);

const fieldsConfig = [
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
  ]},
  { name: 'image_url', label: 'Teamfoto', type: 'image-upload', entity_type: 'App\\Models\\Team', entity_id: id, required: false },
];

onMount(fetchTeam);

async function fetchTeam() {
  loading = true;
  error = '';
  try {
    const res = await fetch(`http://localhost:8000/api/teams/${id}`);
    if (!res.ok) throw new Error('Failed to fetch team');
    team = await res.json();
  } catch (e: any) {
    error = (e as any).message;
  } finally {
    loading = false;
  }
}

function toFields(team: any) {
  return fieldsConfig.map(f => {
    let value = team?.[f.name] ?? '';
    if (f.name === 'image_url') {
      let path = team?.image?.path ?? '';
      if (path && !path.startsWith('http')) {
        path = 'http://localhost:8000' + path;
      }
      value = path;
    }
    return { ...f, value };
  });
}

async function saveTeam(values: any) {
  saveError = '';
  saveSuccess = '';
  try {
    const res = await fetch(`http://localhost:8000/api/teams/${id}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to update team');
    saveSuccess = 'Team updated!';
    setTimeout(() => goto('/cms/teams'), 800);
  } catch (e: any) {
    saveError = (e as any).message;
  }
}

function goBack() {
  goto('/cms/teams');
}
</script>

<h1>Edit Team</h1>
{#if loading}
  <p>Loading...</p>
{:else if error}
  <span class="error">{error}</span>
{:else}
  <FormBuilder
    fields={toFields(team)}
    on:submit={(e: any) => saveTeam(e.detail)}
    submitLabel="Save Changes"
  />
  {#if saveError}<span class="error">{saveError}</span>{/if}
  {#if saveSuccess}<span class="success">{saveSuccess}</span>{/if}
  <div class="members-section">
    <TeamMembersEditor teamId={id} on:membersChange={e => members = e.detail} showAddButtonOnTop={true} />
  </div>
  <button on:click={goBack} class="back-btn">Back</button>
{/if} 
<style>
.members-section {
  margin-top: 2.5rem;
}
</style> 