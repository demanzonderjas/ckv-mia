<script lang="ts">
  import { onMount } from 'svelte';
  let { value = $bindable(), required = false } = $props<{ value?: number|null,  required?: boolean }>();
  let categories = $state<{ id: number; name: string }[]>([]);
  let loading = $state(true);
  let error = $state('');

  onMount(async () => {
    try {
      const res = await fetch('http://localhost:8000/api/news-categories');
      if (!res.ok) throw new Error('Failed to fetch categories');
      categories = await res.json();
    } catch (e) {
      error = e instanceof Error ? e.message : String(e);
    } finally {
      loading = false;
    }
  });

</script>
<div class="form-row">
  {#if loading}
    <span>Laden...</span>
  {:else if error}
    <span style="color:red">{error}</span>
  {:else}
    <select bind:value  required={required}>
      <option value="">-- Kies categorie --</option>
      {#each categories as c}
        <option value={c.id}>{c.name}</option>
      {/each}
    </select>
  {/if}
</div>
<style>
.form-row { display: flex; flex-direction: column; gap: 0.3rem; }
select { padding: 0.4rem 1rem; border-radius: 0.7rem; border: 1px solid #ccc; font-size: 1rem; }
</style> 