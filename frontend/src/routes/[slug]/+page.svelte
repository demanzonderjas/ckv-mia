<script lang="ts">
import { page } from '$app/state';
import PageBlocks from '$lib/PageBlocks.svelte';
import EditFab from '$lib/EditFab.svelte';
import { onMount } from 'svelte';

let pageData = $state(null);

onMount(async () => {
  const slug = page.params.slug;
  try {
    const res = await fetch(`http://localhost:8000/api/pages/slug/${slug}`);
    if (res.ok) {
      const data = await res.json();
      pageData = data;
    }
  } catch {}
});
</script>

{#if pageData}
  <EditFab href={`/cms/pages/edit/${pageData?.id}`} title="Bewerk deze pagina" />
{/if}

<PageBlocks slug={page.params.slug} />
