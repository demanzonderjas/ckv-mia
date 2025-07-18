<script lang="ts">
	import SideMenu from '$lib/SideMenu.svelte';
	let { children } = $props();
</script>

<div class="content-row">
	<main>
		<div class="card"><slot /></div>
	</main>
	<div class="card side-menu-card"><SideMenu /></div>
</div>

<style>
	.content-row {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 2rem;
		max-width: 1400px;
		margin: 2.5rem auto 0 auto;
		padding: 0 1rem;
		flex: 1 0 auto;
		min-height: 0;
	}
	main {
		flex: 1 1 0%;
		min-width: 0;
		width: auto;
		display: flex;
		flex-direction: column;
		align-items: stretch;
		justify-content: flex-start;
		padding: 0;
	}
	.card {
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 1.5rem 1rem;
		width: 100%;
		min-width: 320px;
	}
	.side-menu-card {
		max-width: 300px;
		min-width: 180px;
		width: 100%;
		padding: 1.5rem 1rem;
	}
	@media (max-width: 900px) {
		.content-row {
			flex-direction: column;
			gap: 0;
			padding: 0;
		}
		.side-menu-card {
			max-width: 100%;
			margin: 1rem auto;
		}
	}
</style>
