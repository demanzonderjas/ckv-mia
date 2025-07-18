<script lang="ts">
	import { page } from '$app/stores';
	import PageBlocks from '$lib/PageBlocks.svelte';
</script>

<PageBlocks slug={$page.params.slug} />
