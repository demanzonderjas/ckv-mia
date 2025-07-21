<script lang="ts">
  import { onMount } from 'svelte';
  type EventItem = {
    id: number;
    name: string;
    date: string;
    location?: string;
  };
  let events: EventItem[] = [];
  let loading = true;
  let error: string = '';

  onMount(async () => {
    try {
      const res = await fetch('http://localhost:8000/api/events');
      if (!res.ok) throw new Error('Failed to fetch events');
      const allEvents = await res.json();
      // Only show events with a date in the future, sorted ascending
      const now = new Date();
      events = (allEvents as EventItem[])
        .filter(e => new Date(e.date) >= now)
        .sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime())
        .slice(0, 4);
    } catch (e) {
      error = e instanceof Error ? e.message : String(e);
      // fallback mock data
      events = [
        { name: 'Start seizoenstraining', date: '2025-08-01', id: 1 },
        { name: 'MIA Clubdag', date: '2025-08-15', id: 2 },
        { name: 'Ouderavond', date: '2025-08-25', id: 3 },
        { name: 'Ouderavond', date: '2025-08-25', id: 4 }
      ];
    } finally {
      loading = false;
    }
  });
  function formatDate(dateStr: string) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('nl-NL', { day: 'numeric', month: 'short', year: 'numeric' });
  }
</script>
<section class="upcoming-events">
  <h3>Aankomende Evenementen</h3>
  {#if loading}
    <div>Laden...</div>
  {:else if error}
    <div style="color: red">{error}</div>
  {/if}
  <div class="events-list">
    {#each events as e}
      <a class="event" href={`/events/${e.id}`}> 
        <span class="icon">&#128197;</span>
        <div>
          <strong>{e.name}</strong>
          <div class="date">{formatDate(e.date)}</div>
        </div>
      </a>
    {/each}
  </div>
</section>
<style>
.upcoming-events { padding: 2rem 0; background: var(--background); }
.upcoming-events h3 { color: var(--primary); font-size: 1.5rem; margin-bottom: 1.2rem; }
.events-list { display: flex; flex-wrap: wrap; gap: 2rem; }
.event {
  display: flex;
  align-items: center;
  gap: 1rem;
  background: #fff;
  border-radius: 8px;
  padding: 1rem 1.5rem;
  min-width: 220px;
  box-shadow: 0 2px 8px #0001;
  text-decoration: none;
  color: inherit;
  transition: box-shadow 0.15s, transform 0.15s;
}
.event:hover {
  box-shadow: 0 4px 16px #0002;
  transform: translateY(-2px) scale(1.02);
}
.event strong { color: var(--primary); }
.icon { font-size: 1.5rem; color: var(--primary); }
.date { color: #888; font-size: 0.98rem; }
.event a { color: var(--primary); text-decoration: none; }
.event a:hover { text-decoration: underline; }
@media (max-width: 900px) {
  .events-list { flex-direction: column; gap: 1.2rem; }
  .event { min-width: 0; }
}
</style> 