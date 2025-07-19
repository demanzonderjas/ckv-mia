<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';

interface EventItem {
  id: number;
  name: string;
  description?: string;
  date: string;
  location: string;
  team_id: number;
}

let events: EventItem[] = $state([]);
let loading = $state(true);
let error = $state('');
let deleteError = $state('');
let deletingId = $state<number|null>(null);

async function fetchEvents() {
  loading = true;
  try {
    const res = await fetch('http://localhost:8000/api/events');
    if (!res.ok) throw new Error('Failed to fetch events');
    events = await res.json();
  } catch (e: any) {
    error = e.message;
  } finally {
    loading = false;
  }
}

onMount(fetchEvents);

function goToAddEvent() {
  goto('/cms/events/new');
}

function startEdit(event: EventItem) {
  goto(`/cms/events/edit/${event.id}`);
}

async function deleteEvent(id: number) {
  if (!confirm('Weet je zeker dat je dit event wilt verwijderen?')) return;
  deleteError = '';
  deletingId = id;
  try {
    const res = await fetch(`http://localhost:8000/api/events/${id}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json' },
    });
    if (!res.ok) throw new Error('Failed to delete event');
    await fetchEvents();
  } catch (e: any) {
    deleteError = (e as any).message;
  } finally {
    deletingId = null;
  }
}

function formatDutchDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
}
</script>

<h1>CMS: Events</h1>
<button class="add-btn" on:click={goToAddEvent}>Add Event</button>

<section class="cms-events-list">
  <h2>All Events</h2>
  {#if loading}
    <p>Loading...</p>
  {:else if error}
    <p class="error">{error}</p>
  {:else}
    <ul class="event-list">
      {#each events as event}
        <li class="event-list-item">
          <div class="event-info">
            <strong>{event.name}</strong>
            <span class="event-date">{formatDutchDate(event.date)}</span>
            <span class="event-location">{event.location}</span>
          </div>
          <div class="event-actions">
            <a class="view-btn" href={`/events/${event.id}`} target="_blank">View</a>
            <button class="edit-btn" on:click={() => startEdit(event)}>Edit</button>
            <button class="delete-btn" on:click={() => deleteEvent(event.id)} disabled={deletingId === event.id}>
              {deletingId === event.id ? 'Deleting...' : 'Delete'}
            </button>
          </div>
        </li>
      {/each}
    </ul>
    {#if deleteError}<span class="error">{deleteError}</span>{/if}
  {/if}
</section>

<style>
.cms-events-list {
  margin-top: 2rem;
  background: var(--primary-white);
  border-radius: 1rem;
  box-shadow: 0 2px 12px #0001;
  padding: 2rem 1.5rem;
  max-width: 700px;
}
.cms-events-list h2 {
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
.event-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
}
.event-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #f8f8f8;
  border-radius: 0.7rem;
  box-shadow: 0 1px 4px #0001;
  padding: 1rem 1.5rem;
  gap: 1.5rem;
}
.event-info {
  display: flex;
  align-items: center;
  gap: 0.7rem;
}
.event-date {
  background: #eaf6ff;
  color: #1976d2;
  padding: 0.15rem 0.7rem;
  border-radius: 0.5rem;
  font-size: 0.98em;
  margin-left: 0.7rem;
  font-weight: 500;
  letter-spacing: 0.01em;
  display: inline-block;
}
.event-location {
  background: #f3f3f3;
  padding: 0.1rem 0.6rem;
  border-radius: 0.4rem;
  font-size: 0.97em;
  color: #666;
  margin-left: 0.7rem;
}
.event-actions {
  display: flex;
  gap: 0.7rem;
}
.edit-btn {
  margin-left: 0;
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
.error {
  color: red;
  margin-left: 1rem;
}
.view-btn {
  background: #fff;
  color: var(--primary-orange);
  border: 2px solid var(--primary-orange);
  padding: 0.4rem 1.2rem;
  border-radius: 1.2rem;
  font-weight: 500;
  font-size: 1rem;
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