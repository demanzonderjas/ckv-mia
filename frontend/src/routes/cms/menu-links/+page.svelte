<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';
import { dndzone } from 'svelte-dnd-action';

interface MenuLink {
  id: number;
  title: string;
  url: string;
  order: number;
  active: boolean;
  description?: string;
  category: string;
  parent_id?: number;
}

let links: MenuLink[] = $state([]);
let loading = $state(true);
let error = $state('');
let savingOrder = $state(false);

const categories = [
  { value: 'header', label: 'Header' },
  { value: 'side', label: 'Side' },
  { value: 'footer', label: 'Footer' }
];

function getParentTitle(link: MenuLink) {
  const parent = links.find(l => l.id === link.parent_id);
  return parent ? parent.title : '-';
}

function linksByCategory(cat: string) {
  return links.filter(l => l.category === cat).sort((a, b) => a.order - b.order);
}

async function fetchLinks() {
  loading = true;
  try {
    const res = await fetch('http://localhost:8000/api/menu-links');
    if (!res.ok) throw new Error('Failed to fetch links');
    links = await res.json();
  } catch (e: any) {
    error = e.message;
  } finally {
    loading = false;
  }
}

onMount(fetchLinks);

function goToAdd() {
  goto('/cms/menu-links/new');
}
function startEdit(link: MenuLink) {
  goto(`/cms/menu-links/edit/${link.id}`);
}

function handleDndConsider({ detail }: any, cat: string) {
  if (!detail?.items) return;
  // Only update local order for this category
  const other = links.filter(l => l.category !== cat);
  const reordered = detail.items.map((item: MenuLink, idx: number) => ({ ...item, order: idx, category: cat }));
  links = [...other, ...reordered];
}

function handleDndFinalize({ detail }: any, cat: string) {
  if (!detail?.items) return;
  // If a link was dropped from another category, update its category
  const other = links.filter(l => l.category !== cat);
  const reordered = detail.items.map((item: MenuLink, idx: number) => ({ ...item, order: idx, category: cat }));
  links = [...other, ...reordered];
  saveOrder(reordered);
}

async function saveOrder(catLinks: MenuLink[]) {
  savingOrder = true;
  try {
    await Promise.all(
      catLinks.map(link =>
        fetch(`http://localhost:8000/api/menu-links/${link.id}`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ order: link.order, category: link.category })
        })
      )
    );
  } finally {
    savingOrder = false;
  }
}
</script>

<h1>CMS: Side Menu Links</h1>
<div class="links-header-row">
  <button class="add-btn" on:click={goToAdd}>Add Link</button>
  {#if savingOrder}<span class="saving-order">Saving order…</span>{/if}
</div>
{#if loading}
  <p>Loading links...</p>
{:else if error}
  <p class="error">{error}</p>
{:else}
  {#each categories as cat}
    <section class="category-section">
      <h2>{cat.label}</h2>
      <div class="cms-table-wrapper">
        <table class="cms-table">
          <thead>
            <tr>
              <th style="width:2.5rem"></th>
              <th>Title</th>
              <th>URL</th>
              <th>Parent</th>
              <th>Active</th>
              <th>Description</th>
              <th></th>
            </tr>
          </thead>
          <tbody use:dndzone={{ items: linksByCategory(cat.value), flipDurationMs: 150, dragDisabled: false, dropFromOthersDisabled: false, dropTargetStyle: { background: '#fff7f0' } }} on:consider={e => handleDndConsider(e, cat.value)} on:finalize={e => handleDndFinalize(e, cat.value)}>
            {#if linksByCategory(cat.value).length === 0}
              <tr class="empty-drop-row"><td colspan="7">Drop here</td></tr>
            {/if}
            {#each linksByCategory(cat.value) as link, i (link.id)}
              <tr>
                <td class="drag-handle-cell"><span class="drag-handle" title="Drag to reorder">☰</span></td>
                <td>{link.title}</td>
                <td>{link.url}</td>
                <td>{getParentTitle(link)}</td>
                <td>{link.active ? 'Yes' : 'No'}</td>
                <td>{link.description}</td>
                <td><button class="edit-btn" on:click={() => startEdit(link)}>Edit</button></td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    </section>
  {/each}
{/if}

<style>
.links-header-row {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-bottom: 1.5rem;
}
.add-btn {
  background: var(--primary-orange);
  color: #fff;
  border: none;
  padding: 0.6rem 1.5rem;
  border-radius: 1.2rem;
  font-weight: bold;
  font-size: 1.08rem;
  cursor: pointer;
  transition: background 0.2s;
  height: 2.8rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
}
.add-btn:hover {
  background: #ff7f2a;
}
.category-section {
  margin-bottom: 2.5rem;
}
.category-section h2 {
  color: var(--primary-orange);
  font-size: 1.25rem;
  margin-bottom: 0.7rem;
  font-weight: bold;
}
.cms-table-wrapper {
  margin-top: 2rem;
  background: var(--primary-white);
  border-radius: 1rem;
  box-shadow: 0 2px 12px #0001;
  padding: 2rem 1.5rem;
  max-width: 900px;
}
.cms-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 1.05rem;
}
.cms-table th, .cms-table td {
  padding: 0.7rem 1rem;
  text-align: left;
}
.cms-table th {
  background: #f7f7fa;
  color: var(--primary-orange);
  font-weight: bold;
  border-bottom: 2px solid #eee;
}
.cms-table tr:nth-child(even) td {
  background: #fcfcff;
}
.edit-btn {
  background: var(--primary-orange);
  color: #fff;
  border: none;
  padding: 0.4rem 1.2rem;
  border-radius: 1.2rem;
  font-weight: 500;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
}
.edit-btn:hover {
  background: #ff7f2a;
}
.error {
  color: red;
  font-weight: bold;
}
.drag-handle-cell {
  text-align: center;
  cursor: grab;
  width: 2.5rem;
}
.drag-handle {
  font-size: 1.3rem;
  color: #bbb;
  user-select: none;
}
.drag-handle:active {
  color: var(--primary-orange);
}
.saving-order {
  color: var(--primary-orange);
  font-size: 1.05rem;
  margin-left: 1.2rem;
  font-weight: 500;
}
.empty-drop-row td {
  min-height: 48px;
  height: 48px;
  color: #bbb;
  text-align: center;
  font-size: 1.05rem;
  background: #fcfcff;
  border-radius: 0.7rem;
  border: 1.5px dashed #eee;
}
</style> 