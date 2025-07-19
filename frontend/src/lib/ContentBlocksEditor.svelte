<script lang="ts">
import { onMount, createEventDispatcher } from 'svelte';
import { dndzone } from 'svelte-dnd-action';
import FormBuilder from '$lib/FormBuilder.svelte';
import { blockFieldConfigs, parseBlockContent } from '$lib/fieldConfigs';

let { pageId, blocks = $bindable(), onBlocksChange = () => {} } = $props();

let status = $state<'loading' | 'error' | 'ready' | 'editing' | 'adding'>('ready');
let error = $state<{ add?: string; edit?: string; remove?: string; reorder?: string }>({});
let blockIdInProgress = $state<number | null>(null);
let editingBlock: any = $state(null);

const dispatch = createEventDispatcher();

function blockToFields(block: any = {}, isEdit = false): any[] {
  const type: string = block.type || 'text';
  const config: any[] = (blockFieldConfigs as any)[type] || blockFieldConfigs.text;
  const title: string = block.title || '';
  const content: string = parseBlockContent(type, block.content);
  return config.map((field: any) => {
    if (field.name === 'type') return { ...field, value: type };
    if (field.name === 'title') return { ...field, value: title };
    if (field.name === 'content') return { ...field, value: content };
    return { ...field };
  });
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
      order: blocks.length,
      title: values.title
    };
    const res = await fetch('http://localhost:8000/api/content-blocks', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    if (!res.ok) throw new Error('Failed to add block');
    await reloadBlocks();
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
    await reloadBlocks();
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
    await reloadBlocks();
  } catch (e: any) {
    error.edit = e.message;
    status = 'editing';
  } finally {
    blockIdInProgress = null;
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
    await reloadBlocks();
  } catch (e: any) {
    error.reorder = e.message;
  }
}

function handleDndConsider({ detail }: { detail: { items: any[] } }) {
  if (detail?.items) {
    const reordered = detail.items.map((block, idx) => ({ ...block, order: idx }));
    blocks = reordered;
  }
}

function handleDndFinalize({ detail }: { detail: { items: any[] } }) {
  if (detail?.items) {
    const reordered = detail.items.map((block, idx) => ({ ...block, order: idx }));
    blocks = reordered;
    saveBlockOrder(reordered);
  }
}

async function reloadBlocks() {
  const res = await fetch(`http://localhost:8000/api/pages/${pageId}`);
  const data = await res.json();
  blocks = data.contentBlocks || data.content_blocks || [];
  status = 'ready';
  dispatch('blocksChange', blocks);
  if (onBlocksChange) onBlocksChange(blocks);
}
</script>

<style>
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
.add-btn, .remove-btn, .edit-btn {
  margin-left: 0.5rem;
  background: var(--primary-orange);
  color: #fff;
  border: none;
  padding: 0.25rem 0.7rem;
  border-radius: 0.7rem;
  font-weight: 500;
  font-size: 0.92rem;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 1px 2px #0001;
}
.add-btn:hover, .remove-btn:hover, .edit-btn:hover {
  background: #ff7f2a;
  box-shadow: 0 2px 6px #0002;
}
.add-btn:active, .remove-btn:active, .edit-btn:active {
  background: #e65c00;
}
.error {
  color: red;
  margin-left: 1rem;
}
</style>

<h2>Content Blocks</h2>
{#if status === 'ready'}
  <ul
    use:dndzone={{ items: blocks, flipDurationMs: 150 }}
    on:consider={handleDndConsider}
    on:finalize={handleDndFinalize}
  >
    {#each blocks as block, i (block.id)}
      <li class="dnd-block">
        <span class="drag-handle" title="Drag to reorder">☰</span>
        <strong>{block.title || '(no title)'}</strong>
        <span class="block-type">[{block.type}]</span>
        <button class="edit-btn" on:click={() => startEditBlock(block)}>Edit</button>
        <button
          class="remove-btn"
          on:click={() => removeBlock(block.id)}
          disabled={blockIdInProgress === block.id}
        >
          {blockIdInProgress === block.id ? 'Removing...' : 'Remove'}
        </button>
      </li>
    {/each}
  </ul>
  {#if error.remove}<span class="error">{error.remove}</span>{/if}
  {#if error.reorder}<span class="error">{error.reorder}</span>{/if}
  {#if error.add}<span class="error">{error.add}</span>{/if}
  {#if error.edit}<span class="error">{error.edit}</span>{/if}
  <button class="add-btn" on:click={() => (status = 'adding')}>Add Block</button>
{:else if status === 'adding'}
  <FormBuilder
    fields={blockToFields()}
    on:submit={(e: any) => addBlock(e.detail)}
    submitLabel="Add"
  />
  <button on:click={() => (status = 'ready')}>Cancel</button>
{:else if status === 'editing'}
  <FormBuilder
    fields={blockToFields(editingBlock || {}, true)}
    on:submit={(e: any) => saveEditBlock(editingBlock?.id, e.detail)}
    submitLabel="Save"
  />
  <button on:click={stopEditBlock}>Cancel</button>
{/if} 