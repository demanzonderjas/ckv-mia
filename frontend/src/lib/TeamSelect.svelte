<script lang="ts">
import { onMount } from 'svelte';
let { value = $bindable(), label = 'Team', required = false, name = '' } = $props();
let options: { value: number, label: string }[] = $state([]);
let loading = $state(true);
let error = $state('');

onMount(async () => {
  loading = true;
  error = '';
  try {
    const res = await fetch('http://localhost:8000/api/teams');
    if (!res.ok) throw new Error('Failed to fetch teams');
    const teams = await res.json();
    options = teams.map((team: any) => ({ value: team.id, label: team.name }));
  } catch (e: any) {
    error = e.message;
  } finally {
    loading = false;
  }
});
</script>

{#if loading}
  <span>Teams laden...</span>
{:else if error}
  <span class="error">{error}</span>
{:else}
  <select bind:value required={required} name={name}>
    <option value="">-- Kies team --</option>
    {#each options as opt}
      <option value={opt.value}>{opt.label}</option>
    {/each}
  </select>
{/if}

<style>
select {
  padding: 0.6rem;
  border-radius: 0.5rem;
  border: 1.5px solid #e0e0e0;
  font-size: 1rem;
  background: #fcfcff;
  box-shadow: 0 1px 4px #0001;
  transition: border 0.2s, box-shadow 0.2s;
}
select:focus {
  border: 1.5px solid var(--primary-orange, #ff6600);
  outline: none;
  box-shadow: 0 2px 8px #ff660022;
}
.error {
  color: red;
  font-size: 0.95rem;
}
</style> 