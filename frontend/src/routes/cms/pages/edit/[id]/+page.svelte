<script lang="ts">
	import { onMount } from 'svelte';
	import { page } from '$app/stores';
	import { goto } from '$app/navigation';
	import { dndzone } from 'svelte-dnd-action';
	import { browser } from '$app/environment';
	import FormBuilder from '$lib/FormBuilder.svelte';
	import { blockFieldConfigs, parseBlockContent } from '$lib/fieldConfigs';
	import ContentBlocksEditor from '$lib/ContentBlocksEditor.svelte';

	let pageId = Number($page.params.id);
	let editingPage = $state<any>(null);
	let contentBlocks = $state<any[]>([]);
	let status = $state<'loading' | 'error' | 'ready' | 'editing' | 'adding'>('loading');
	let error = $state<{ main?: string; add?: string; edit?: string; remove?: string; reorder?: string }>({});
	let blockIdInProgress = $state<number | null>(null); // for remove/edit loading
	let editingBlock: any = $state(null); // block being edited
	let saveError = $state('');
	let saveSuccess = $state('');

	const fieldsConfig = [
		{ name: 'title', label: 'Title', type: 'text', required: true },
		{ name: 'slug', label: 'Slug', type: 'text', required: true },
		{ name: 'description', label: 'Description', type: 'text', required: false },
		{ name: 'content_blocks', label: 'Content Blocks', type: 'content-blocks', pageId, blocks: contentBlocks },
	];

	function toFields(page: any) {
		return fieldsConfig.map(f => {
			if (f.type === 'content-blocks') {
				return { ...f, pageId, blocks: contentBlocks };
			}
			return { ...f, value: page?.[f.name] ?? '' };
		});
	}

	// Utility to extract field values from a block
	function blockToFields(block: any = {}, isEdit = false): any[] {
		const type: string = block.type || 'text';
		const config: any[] = (blockFieldConfigs as any)[type] || blockFieldConfigs.text;
		const title: string = block.title || '';
		const content: string = parseBlockContent(type, block.content);
		// Deep clone config and inject values
		return config.map((field: any) => {
			if (field.name === 'type') return { ...field, value: type };
			if (field.name === 'title') return { ...field, value: title };
			if (field.name === 'content') return { ...field, value: content };
			return { ...field };
		});
	}

	onMount(fetchPageAndBlocks);

	async function fetchPageAndBlocks() {
		status = 'loading';
		error = {};
		try {
			const res = await fetch(`http://localhost:8000/api/pages/${pageId}`);
			if (!res.ok) throw new Error('Failed to fetch page');
			const data = await res.json();
			editingPage = data;
			contentBlocks = data.contentBlocks || data.content_blocks || [];
			status = 'ready';
		} catch (e: any) {
			error.main = e.message;
			status = 'error';
		}
	}

	async function addBlock(values: any) {
		error.add = '';
		status = 'adding';
		try {
			const payload = {
				page_id: pageId,
				type: values.type,
				content: JSON.stringify(
					values.type === 'text'
						? { text: values.content }
						: values.type === 'image'
							? { url: values.content, alt: '' }
							: { html: values.content }
				),
				order: contentBlocks.length,
				title: values.title
			};
			const res = await fetch('http://localhost:8000/api/content-blocks', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			});
			if (!res.ok) throw new Error('Failed to add block');
			status = 'loading';
			await fetchPageAndBlocks();
		} catch (e: any) {
			error.add = e.message;
			status = 'ready';
		}
	}

	async function removeBlock(blockId: number) {
		error.remove = '';
		blockIdInProgress = blockId;
		try {
			const res = await fetch(`http://localhost:8000/api/content-blocks/${blockId}`, {
				method: 'DELETE'
			});
			if (!res.ok) throw new Error('Failed to remove block');
			await fetchPageAndBlocks();
		} catch (e: any) {
			error.remove = e.message;
		} finally {
			blockIdInProgress = null;
		}
	}

	function startEditBlock(block: any) {
		editingBlock = block;
		status = 'editing';
	}

	function stopEditBlock() {
		editingBlock = null;
		status = 'ready';
		error.edit = '';
	}

	async function saveEditBlock(blockId: number, values: any) {
		error.edit = '';
		blockIdInProgress = blockId;
		try {
			const payload = {
				type: values.type,
				content: JSON.stringify(
					values.type === 'text'
						? { text: values.content }
						: values.type === 'image'
							? { url: values.content, alt: '' }
							: { html: values.content }
				),
				title: values.title
			};
			const res = await fetch(`http://localhost:8000/api/content-blocks/${blockId}`, {
				method: 'PUT',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(payload)
			});
			if (!res.ok) throw new Error('Failed to update block');
			editingBlock = null;
			status = 'loading';
			await fetchPageAndBlocks();
		} catch (e: any) {
			error.edit = e.message;
			status = 'editing';
		} finally {
			blockIdInProgress = null;
		}
	}

	async function savePage(values: any) {
		saveError = '';
		saveSuccess = '';
		try {
			const res = await fetch(`http://localhost:8000/api/pages/${pageId}`, {
				method: 'PUT',
				headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
				body: JSON.stringify(values)
			});
			if (!res.ok) {
				let msg = `Failed to update page (status ${res.status})`;
				try {
					const data = await res.json();
					if (data?.message) msg = data.message;
				} catch {}
				throw new Error(msg);
			}
			saveSuccess = 'Page updated!';
			setTimeout(() => goto('/cms/pages'), 800);
			// await fetchPageAndBlocks();
		} catch (e: any) {
			saveError = (e as any).message;
		}
	}

	async function saveBlockOrder(newBlocks: any[]) {
		error.reorder = '';
		try {
			await Promise.all(
				newBlocks.map((block: any, idx: number) =>
					fetch(`http://localhost:8000/api/content-blocks/${block.id}`, {
						method: 'PATCH',
						headers: { 'Content-Type': 'application/json' },
						body: JSON.stringify({ order: idx })
					})
				)
			);
			await fetchPageAndBlocks();
		} catch (e: any) {
			error.reorder = e.message;
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

	function goBack() {
		goto('/cms');
	}
</script>

<h1>Edit Page</h1>

{#if status === 'loading'}
	<p>Loading...</p>
{:else if status === 'error'}
	<p class="error">{error.main}</p>
{:else if status === 'ready'}
	<FormBuilder
		fields={toFields(editingPage)}
		on:submit={(e: any) => savePage(e.detail)}
		submitLabel="Save Page"
	/>
	{#if saveError}<span class="error">{saveError}</span>{/if}
	{#if saveSuccess}<span class="success">{saveSuccess}</span>{/if}
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
	.success {
		color: green;
		margin-left: 1rem;
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
	.block-type {
		font-size: 0.8rem;
		color: #666;
		margin-left: 0.5rem;
	}
	.ql-container {
		min-height: 180px;
		background: #fff;
		border-radius: 0.7rem;
		box-shadow: 0 2px 8px #0001;
		padding: 1rem;
		font-size: 1.08rem;
		border: 1px solid #e0e0e0;
	}
	.ql-toolbar {
		border-radius: 0.7rem 0.7rem 0 0;
		background: #f7f7fa;
		border: 1px solid #e0e0e0;
		box-shadow: 0 1px 4px #0001;
		margin-bottom: 0.2rem;
	}
	.ql-editor {
		min-height: 120px;
		padding: 0.7rem;
		background: #fcfcff;
		border-radius: 0.5rem;
	}
</style>
