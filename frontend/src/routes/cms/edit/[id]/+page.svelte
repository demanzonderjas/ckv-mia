<script lang="ts">
	import { onMount } from 'svelte';
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import { dndzone } from 'svelte-dnd-action';
	import QuillEditor from 'svelte-quill';

	let pageId = Number($page.params.id);
	let editingPage = $state<any>(null);
	let contentBlocks = $state<any[]>([]);
	let blockLoading = $state(false);
	let blockError = $state('');
	let showAddBlock = $state(false);
	let newBlockType = $state('text');
	let newBlockContent = $state('');
	let newBlockTitle = $state('');
	let addBlockError = $state('');
	let addBlockLoading = $state(false);
	let removeBlockLoading = $state<number | null>(null);
	let removeBlockError = $state('');
	let editingBlockId = $state<number | null>(null);
	let editBlockType = $state('text');
	let editBlockContent = $state('');
	let editBlockLoading = $state(false);
	let editBlockError = $state('');
	let editBlockTitle = $state('');
	let reorderLoading = $state(false);
	let reorderError = $state('');

	async function fetchPageAndBlocks() {
		if (!pageId) return;
		blockLoading = true;
		blockError = '';
		try {
			const res = await fetch(`http://localhost:8000/api/pages/${pageId}`);
			if (!res.ok) throw new Error('Failed to fetch page');
			const data = await res.json();
			editingPage = data;
			contentBlocks = data.contentBlocks || data.content_blocks || [];
		} catch (e: any) {
			blockError = e.message;
		} finally {
			blockLoading = false;
		}
	}

	async function addBlock() {
		addBlockError = '';
		addBlockLoading = true;
		try {
			const payload = {
				page_id: pageId,
				type: newBlockType,
				content: JSON.stringify(
					newBlockType === 'text'
						? { text: newBlockContent }
						: newBlockType === 'image'
							? { url: newBlockContent, alt: '' }
							: { html: newBlockContent }
				),
				order: contentBlocks.length,
				title: newBlockTitle
			};
			const res = await fetch('http://localhost:8000/api/content-blocks', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			});
			if (!res.ok) throw new Error('Failed to add block');
			showAddBlock = false;
			newBlockContent = '';
			newBlockTitle = '';
			await fetchPageAndBlocks();
		} catch (e: any) {
			addBlockError = e.message;
		} finally {
			addBlockLoading = false;
		}
	}

	async function removeBlock(blockId: number) {
		removeBlockError = '';
		removeBlockLoading = blockId;
		try {
			const res = await fetch(`http://localhost:8000/api/content-blocks/${blockId}`, {
				method: 'DELETE'
			});
			if (!res.ok) throw new Error('Failed to remove block');
			await fetchPageAndBlocks();
		} catch (e: any) {
			removeBlockError = e.message;
		} finally {
			removeBlockLoading = null;
		}
	}

	function startEditBlock(block: any) {
		editingBlockId = block.id;
		editBlockType = block.type;
		editBlockTitle = block.title || '';
		if (block.type === 'rich') {
			try {
				const parsed =
					typeof block.content === 'string' ? JSON.parse(block.content) : block.content;
				editBlockContent = parsed.html || '';
			} catch {
				editBlockContent = '';
			}
		} else if (block.type === 'image') {
			try {
				const parsed =
					typeof block.content === 'string' ? JSON.parse(block.content) : block.content;
				editBlockContent = parsed.url || '';
			} catch {
				editBlockContent = '';
			}
		} else {
			editBlockContent = '';
		}
	}

	function stopEditBlock() {
		editingBlockId = null;
		editBlockType = 'text';
		editBlockContent = '';
		editBlockError = '';
	}

	async function saveEditBlock(blockId: number) {
		editBlockError = '';
		editBlockLoading = true;
		try {
			const payload = {
				type: editBlockType,
				content: JSON.stringify(
					editBlockType === 'text'
						? { text: editBlockContent }
						: editBlockType === 'image'
							? { url: editBlockContent, alt: '' }
							: { html: editBlockContent }
				),
				title: editBlockTitle
			};
			const res = await fetch(`http://localhost:8000/api/content-blocks/${blockId}`, {
				method: 'PUT',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			});
			if (!res.ok) throw new Error('Failed to update block');
			editingBlockId = null;
			await fetchPageAndBlocks();
		} catch (e: any) {
			editBlockError = e.message;
		} finally {
			editBlockLoading = false;
		}
	}

	async function saveBlockOrder(newBlocks) {
		reorderLoading = true;
		reorderError = '';
		try {
			// Update order in backend for all blocks
			await Promise.all(
				newBlocks.map((block, idx) =>
					fetch(`http://localhost:8000/api/content-blocks/${block.id}`, {
						method: 'PATCH',
						headers: { 'Content-Type': 'application/json' },
						body: JSON.stringify({ order: idx })
					})
				)
			);
			await fetchPageAndBlocks();
		} catch (e) {
			reorderError = e.message;
		} finally {
			reorderLoading = false;
		}
	}

	function handleDndConsider({ detail }: { detail: { items: any[] } }) {
		if (detail?.items) {
			const reordered = detail.items.map((block, idx) => ({ ...block, order: idx }));
			contentBlocks = reordered;
		}
	}

	function handleDndFinalize({ detail }: { detail: { items: any[] } }) {
		if (detail?.items) {
			const reordered = detail.items.map((block, idx) => ({ ...block, order: idx }));
			contentBlocks = reordered;
			saveBlockOrder(reordered);
		}
	}

	onMount(fetchPageAndBlocks);

	function goBack() {
		goto('/cms');
	}
</script>

<h1>Edit Content Blocks</h1>
<button on:click={goBack}>Back to CMS</button>

{#if blockLoading}
	<p>Loading blocks...</p>
{:else if blockError}
	<p class="error">{blockError}</p>
{:else if editingPage}
	<h2>{editingPage.title}</h2>
	<ul
		use:dndzone={{ items: contentBlocks, flipDurationMs: 150 }}
		on:consider={handleDndConsider}
		on:finalize={handleDndFinalize}
	>
		{#each contentBlocks as block, i (block.id)}
			<li class="dnd-block">
				<span class="drag-handle" title="Drag to reorder">☰</span>
				{#if editingBlockId === block.id}
					<form on:submit|preventDefault={() => saveEditBlock(block.id)} class="edit-block-form">
						<label
							>Type:
							<select bind:value={editBlockType}>
								<option value="text">Text</option>
								<option value="image">Image</option>
								<option value="rich">Rich</option>
							</select>
						</label>
						<label>Title:<br /><input bind:value={editBlockTitle} required /></label>
						{#if editBlockType === 'text'}
							<label
								>Text:<br /><textarea bind:value={editBlockContent} required rows="3"
								></textarea></label
							>
						{:else if editBlockType === 'image'}
							<label>Image URL:<br /><input bind:value={editBlockContent} required /></label>
						{:else if editBlockType === 'rich'}
							<label
								>Rich Content:<br />
								<QuillEditor bind:value={editBlockContent} theme="snow" />
							</label>
						{/if}
						<button type="submit" disabled={editBlockLoading}>Save</button>
						<button type="button" on:click={stopEditBlock}>Cancel</button>
						{#if editBlockError}<span class="error">{editBlockError}</span>{/if}
					</form>
				{:else if block.type === 'rich'}
					<div class="rich-content">{@html JSON.parse(block.content).html}</div>
				{:else}
					<strong>{block.title || '(no title)'}</strong>
					<span class="block-type">[{block.type}]</span>
					<button class="edit-btn" on:click={() => startEditBlock(block)}>Edit</button>
					<button
						class="remove-btn"
						on:click={() => removeBlock(block.id)}
						disabled={removeBlockLoading === block.id}
					>
						{removeBlockLoading === block.id ? 'Removing...' : 'Remove'}
					</button>
				{/if}
			</li>
		{/each}
	</ul>
	{#if showAddBlock}
		<form on:submit|preventDefault={addBlock} class="add-block-form">
			<label
				>Type:
				<select bind:value={newBlockType}>
					<option value="text">Text</option>
					<option value="image">Image</option>
					<option value="rich">Rich</option>
				</select>
			</label>
			<label>Title:<br /><input bind:value={newBlockTitle} required /></label>
			{#if newBlockType === 'text'}
				<label
					>Text:<br /><textarea bind:value={newBlockContent} required rows="3"></textarea></label
				>
			{:else if newBlockType === 'image'}
				<label>Image URL:<br /><input bind:value={newBlockContent} required /></label>
			{:else if newBlockType === 'rich'}
				<label
					>Rich Content:<br />
					<QuillEditor bind:value={newBlockContent} theme="snow" />
				</label>
			{/if}
			<button type="submit" disabled={addBlockLoading}>Add</button>
			<button type="button" on:click={() => (showAddBlock = false)}>Cancel</button>
			{#if addBlockError}<span class="error">{addBlockError}</span>{/if}
		</form>
	{:else}
		<button class="add-btn" on:click={() => (showAddBlock = true)}>Add Block</button>
	{/if}
	{#if removeBlockError}<span class="error">{removeBlockError}</span>{/if}
	{#if reorderLoading}<span class="info">Saving order...</span>{/if}
	{#if reorderError}<span class="error">{reorderError}</span>{/if}
{/if}

<style>
	.remove-btn,
	.add-btn,
	.edit-btn {
		margin-left: 0.5rem;
		background: var(--primary-orange);
		color: #fff;
		border: none;
		padding: 0.25rem 0.7rem;
		border-radius: 0.7rem;
		font-weight: 500;
		font-size: 0.92rem;
		cursor: pointer;
		transition:
			background 0.2s,
			box-shadow 0.2s;
		box-shadow: 0 1px 2px #0001;
	}
	.remove-btn:hover,
	.add-btn:hover,
	.edit-btn:hover {
		background: #ff7f2a;
		box-shadow: 0 2px 6px #0002;
	}
	.remove-btn:active,
	.add-btn:active,
	.edit-btn:active {
		background: #e65c00;
	}
	.error {
		color: red;
		margin-left: 1rem;
	}
	.add-block-form {
		margin: 1.5rem 0;
		background: #fff7f0;
		padding: 1rem;
		border-radius: 0.7rem;
		box-shadow: 0 2px 8px #0001;
		max-width: 400px;
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}
	.add-block-form label {
		font-weight: 500;
	}
	.add-block-form textarea,
	.add-block-form input,
	.add-block-form select {
		width: 100%;
		padding: 0.5rem;
		border-radius: 0.4rem;
		border: 1px solid #ccc;
		font-size: 1rem;
	}
	.add-block-form button {
		margin-top: 0.5rem;
		width: fit-content;
	}
	.edit-block-form {
		margin: 1.5rem 0;
		background: #f0f7ff;
		padding: 1rem;
		border-radius: 0.7rem;
		box-shadow: 0 2px 8px #0001;
		max-width: 400px;
		display: flex;
		flex-direction: column;
		gap: 1rem;
	}
	.edit-block-form label {
		font-weight: 500;
	}
	.edit-block-form textarea,
	.edit-block-form input,
	.edit-block-form select {
		width: 100%;
		padding: 0.5rem;
		border-radius: 0.4rem;
		border: 1px solid #ccc;
		font-size: 1rem;
	}
	.edit-block-form button {
		margin-top: 0.5rem;
		width: fit-content;
	}
	.dnd-block {
		display: flex;
		align-items: center;
		gap: 0.7rem;
		margin-bottom: 0.7rem;
		background: #f8f8f8;
		border-radius: 0.5rem;
		padding: 0.5rem 0.7rem;
		box-shadow: 0 1px 4px #0001;
	}
	.drag-handle {
		cursor: grab;
		font-size: 1.3rem;
		color: #bbb;
		user-select: none;
	}
	.drag-handle:active {
		color: var(--primary-orange);
	}
	.info {
		color: #888;
		margin-left: 1rem;
	}
	.block-type {
		font-size: 0.8rem;
		color: #666;
		margin-left: 0.5rem;
	}
	.rich-content {
		margin: 0.5rem 0;
		padding: 0.5rem;
		background: #f9f9f9;
		border-radius: 0.4rem;
		font-size: 1rem;
	}
</style>
