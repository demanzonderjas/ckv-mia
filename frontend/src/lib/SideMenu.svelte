<script lang="ts">
	import { onMount } from 'svelte';

	interface SideMenuLink {
		id: number;
		title: string;
		url: string;
		order: number;
		active: boolean;
		description?: string;
	}

	let links: SideMenuLink[] = $state([]);
	let loading = $state(true);
	let error = $state('');

	onMount(async () => {
		try {
			const res = await fetch('http://localhost:8000/api/side-menu-links');
			if (!res.ok) throw new Error('Failed to fetch menu links');
			links = await res.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

<aside class="side-menu">
	<h3>Quick Links</h3>
	{#if loading}
		<p>Loading...</p>
	{:else if error}
		<p class="error">{error}</p>
	{:else}
		<ul>
			{#each links as link}
				<li><a href={link.url} title={link.description}>{link.title}</a></li>
			{/each}
		</ul>
	{/if}
</aside>

<style>
	.side-menu {
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 1.5rem 1rem;
		min-width: 180px;
		max-width: 300px;
		width: 100%;
	}
	.side-menu h3 {
		color: var(--primary-orange);
		margin-bottom: 1rem;
		font-size: 1.2rem;
		font-weight: bold;
	}
	.side-menu ul {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}
	.side-menu li a {
		color: var(--primary-black);
		font-weight: 500;
		text-decoration: none;
		transition: color 0.2s;
	}
	.side-menu li a:hover {
		color: var(--primary-orange);
		text-decoration: underline;
	}
	.error {
		color: red;
		font-weight: bold;
	}
	@media (max-width: 900px) {
		.side-menu {
			max-width: 100%;
			margin: 1rem auto;
		}
	}
</style>
