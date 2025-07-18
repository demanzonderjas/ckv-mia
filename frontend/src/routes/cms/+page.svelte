<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	interface Page {
		id: number;
		title: string;
		slug: string;
		description?: string;
	}

	let pages: Page[] = $state([]);
	let loading = $state(true);
	let error = $state('');

	let newTitle = $state('');
	let newSlug = $state('');
	let newDescription = $state('');
	let addError = $state('');
	let addSuccess = $state('');

	let editingPage: Page | null = $state(null);
	let contentBlocks: any[] = $state([]);
	let blockLoading = $state(false);
	let blockError = $state('');

	async function fetchPages() {
		loading = true;
		try {
			const res = await fetch('http://localhost:8000/api/pages');
			if (!res.ok) throw new Error('Failed to fetch pages');
			pages = await res.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	}

	onMount(fetchPages);

	async function addPage() {
		addError = '';
		addSuccess = '';
		if (!newTitle || !newSlug) {
			addError = 'Title and slug are required.';
			return;
		}
		try {
			const res = await fetch('http://localhost:8000/api/pages', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ title: newTitle, slug: newSlug, description: newDescription })
			});
			if (!res.ok) throw new Error('Failed to add page');
			addSuccess = 'Page added!';
			newTitle = '';
			newSlug = '';
			newDescription = '';
			await fetchPages();
		} catch (e: any) {
			addError = e.message;
		}
	}

	function startEdit(page: Page) {
		goto(`/cms/edit/${page.id}`);
	}
</script>

<h1>CMS: Pages</h1>
<section class="cms-add-page">
	<h2>Add New Page</h2>
	<form on:submit|preventDefault={addPage}>
		<label>Title<br /><input bind:value={newTitle} required /></label><br />
		<label>Slug<br /><input bind:value={newSlug} required /></label><br />
		<label>Description<br /><input bind:value={newDescription} /></label><br />
		<button type="submit">Add Page</button>
		{#if addError}<span class="error">{addError}</span>{/if}
		{#if addSuccess}<span class="success">{addSuccess}</span>{/if}
	</form>
</section>

<section class="cms-page-list">
	<h2>All Pages</h2>
	{#if loading}
		<p>Loading...</p>
	{:else if error}
		<p class="error">{error}</p>
	{:else}
		<ul>
			{#each pages as page}
				<li>
					<strong>{page.title}</strong> (<code>{page.slug}</code>)
					<button class="edit-btn" on:click={() => startEdit(page)}>Edit</button>
				</li>
			{/each}
		</ul>
	{/if}
</section>

<style>
	.cms-add-page,
	.cms-page-list {
		margin-bottom: 2.5rem;
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 2rem 1.5rem;
		max-width: 500px;
	}
	.cms-add-page h2,
	.cms-page-list h2 {
		color: var(--primary-orange);
		margin-bottom: 1rem;
	}
	input {
		padding: 0.5rem;
		margin-bottom: 1rem;
		border-radius: 0.5rem;
		border: 1px solid #ccc;
		width: 100%;
		font-size: 1rem;
	}
	button {
		background: var(--primary-orange);
		color: #fff;
		border: none;
		padding: 0.7rem 2rem;
		border-radius: 2rem;
		font-weight: bold;
		font-size: 1.1rem;
		cursor: pointer;
		transition: background 0.2s;
	}
	button:hover {
		background: #ff7f2a;
	}
	.error {
		color: red;
		margin-left: 1rem;
	}
	.success {
		color: green;
		margin-left: 1rem;
	}
	ul {
		list-style: none;
		padding: 0;
	}
	li {
		margin-bottom: 0.7rem;
	}
	code {
		background: #f3f3f3;
		padding: 0.1rem 0.4rem;
		border-radius: 0.3rem;
		font-size: 0.95em;
	}
	.edit-btn,
	.remove-btn,
	.add-btn {
		margin-left: 1rem;
		background: var(--primary-orange);
		color: #fff;
		border: none;
		padding: 0.4rem 1.2rem;
		border-radius: 1.2rem;
		font-weight: bold;
		font-size: 1rem;
		cursor: pointer;
		transition: background 0.2s;
	}
	.edit-btn:hover,
	.remove-btn:hover,
	.add-btn:hover {
		background: #ff7f2a;
	}
	.cms-edit-blocks {
		margin-bottom: 2.5rem;
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 2rem 1.5rem;
		max-width: 600px;
	}
</style>
