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
	let deleteError = $state('');
	let deletingId = $state<number|null>(null);

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

	function startEdit(page: Page) {
		goto(`/cms/pages/edit/${page.id}`);
	}

	function goToAddPage() {
		goto('/cms/pages/new');
	}

	async function deletePage(id: number) {
		if (!confirm('Are you sure you want to delete this page?')) return;
		deleteError = '';
		deletingId = id;
		try {
			const res = await fetch(`http://localhost:8000/api/pages/${id}`, {
				method: 'DELETE',
				headers: { 'Accept': 'application/json' },
			});
			if (!res.ok) throw new Error('Failed to delete page');
			await fetchPages();
		} catch (e: any) {
			deleteError = (e as any).message;
		} finally {
			deletingId = null;
		}
	}
</script>

<h1>CMS: Pages</h1>
<button class="add-btn" on:click={goToAddPage}>Add Page</button>

<section class="cms-page-list">
	<h2>All Pages</h2>
	{#if loading}
		<p>Loading...</p>
	{:else if error}
		<p class="error">{error}</p>
	{:else}
		<ul class="page-list">
			{#each pages as page}
				<li class="page-list-item">
					<div class="page-info">
						<strong>{page.title}</strong>
						<span class="slug">/{page.slug}</span>
					</div>
					<div class="page-actions">
						<a class="view-btn" href={`/${page.slug}`} target="_blank">View</a>
						<button class="edit-btn" on:click={() => startEdit(page)}>Edit</button>
						<button class="delete-btn" on:click={() => deletePage(page.id)} disabled={deletingId === page.id}>
							{deletingId === page.id ? 'Deleting...' : 'Delete'}
						</button>
					</div>
				</li>
			{/each}
		</ul>
		{#if deleteError}<span class="error">{deleteError}</span>{/if}
	{/if}
</section>

<style>
	.cms-page-list {
		margin-top: 2rem;
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 2rem 1.5rem;
		max-width: 1200px;
	}
	.cms-page-list h2 {
		color: var(--primary-orange);
		margin-bottom: 1rem;
	}
	.add-btn {
		background: var(--primary-orange);
		color: #fff;
		border: none;
		padding: 0.7rem 2rem;
		border-radius: 2rem;
		font-weight: bold;
		font-size: 1.1rem;
		cursor: pointer;
		transition: background 0.2s;
		margin-bottom: 1.5rem;
	}
	.add-btn:hover {
		background: #ff7f2a;
	}
	.error {
		color: red;
		margin-left: 1rem;
	}
	.page-list {
		list-style: none;
		padding: 0;
		margin: 0;
		display: flex;
		flex-direction: column;
		gap: 1.2rem;
	}
	.page-list-item {
		display: flex;
		align-items: center;
		justify-content: space-between;
		background: #f8f8f8;
		border-radius: 0.7rem;
		box-shadow: 0 1px 4px #0001;
		padding: 1rem 1.5rem;
		gap: 1.5rem;
	}
	.page-info {
		display: flex;
		align-items: center;
		gap: 1.2rem;
	}
	.slug {
		background: #f3f3f3;
		padding: 0.1rem 0.6rem;
		border-radius: 0.4rem;
		font-size: 0.97em;
		color: #666;
	}
	.page-actions {
		display: flex;
		gap: 0.7rem;
		align-items: center;
		justify-content: flex-end;
	}
	code {
		background: #f3f3f3;
		padding: 0.1rem 0.4rem;
		border-radius: 0.3rem;
		font-size: 0.95em;
	}
	.edit-btn {
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
	.edit-btn:hover {
		background: #ff7f2a;
	}
	.delete-btn {
		margin-left: 0.7rem;
		background: #e74c3c;
		color: #fff;
		border: none;
		padding: 0.4rem 1.2rem;
		border-radius: 1.2rem;
		font-weight: bold;
		font-size: 1rem;
		cursor: pointer;
		transition: background 0.2s;
	}
	.delete-btn:hover {
		background: #c0392b;
	}
	.view-btn {
		background: #fff;
		color: var(--primary-orange);
		border: 2px solid var(--primary-orange);
		padding: 0.3rem 0.9rem;
		border-radius: 1rem;
		font-weight: 500;
		font-size: 0.97rem;
		cursor: pointer;
		text-decoration: none;
		margin-right: 0.7rem;
		transition: background 0.2s, color 0.2s;
	}
	.view-btn:hover {
		background: var(--primary-orange);
		color: #fff;
	}
</style> 