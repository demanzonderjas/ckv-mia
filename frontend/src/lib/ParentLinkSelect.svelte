<script lang="ts">
import { onMount } from 'svelte';
let { value = $bindable(), category = 'side', excludeId = null, label = 'Parent Link', required = false, name = '' } = $props();
let options = $state([{ value: '', label: '-- No parent --' }]);

async function fetchOptions() {
  const res = await fetch(`http://localhost:8000/api/menu-links?category=${category}`);
  if (res.ok) {
    const links = await res.json();
    options = [{ value: '', label: '-- No parent --' }, ...links.filter((l: any) => !excludeId || l.id != excludeId).map((l: any) => ({ value: l.id, label: l.title }))];
  }
}

onMount(fetchOptions);
$effect(() => { fetchOptions(); });
</script>

<div class="parent-link-select-row">
  <select bind:value required={required} name={name}>
    {#each options as opt}
      <option value={opt.value}>{opt.label}</option>
    {/each}
  </select>
</div>

<style>
.parent-link-select-row {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
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
label {
  font-weight: 600;
  font-size: 1.08rem;
  color: var(--primary-orange, #ff6600);
  margin-bottom: 0.25rem;
  letter-spacing: 0.01em;
  text-shadow: 0 1px 2px #0001;
}
</style> 