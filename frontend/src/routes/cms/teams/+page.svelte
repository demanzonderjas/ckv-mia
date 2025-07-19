<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';

interface Team {
  id: number;
  name: string;
  description: string;
  category: string;
  type: string;
}

let teams: Team[] = $state([]);
let loading = $state(true);
let error = $state('');
let deleteError = $state('');
let deletingId = $state<number|null>(null);
let showType = $state<'senior' | 'junior'>('senior');

async function fetchTeams() {
  loading = true;
  try {
    const res = await fetch('http://localhost:8000/api/teams');
    if (!res.ok) throw new Error('Failed to fetch teams');
    teams = await res.json();
  } catch (e: any) {
    error = e.message;
  } finally {
    loading = false;
  }
}

onMount(fetchTeams);

function goToAddTeam() {
  goto('/cms/teams/new');
}

function startEdit(team: Team) {
  goto(`/cms/teams/edit/${team.id}`);
}

async function deleteTeam(id: number) {
  if (!confirm('Are you sure you want to delete this team?')) return;
  deleteError = '';
  deletingId = id;
  try {
    const res = await fetch(`http://localhost:8000/api/teams/${id}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json' },
    });
    if (!res.ok) throw new Error('Failed to delete team');
    await fetchTeams();
  } catch (e: any) {
    deleteError = (e as any).message;
  } finally {
    deletingId = null;
  }
}

function groupByCategory(teams: Team[]) {
  return {
    wedstrijdkorfbal: teams.filter((t) => t.category === 'wedstrijdkorfbal'),
    breedtekorfbal: teams.filter((t) => t.category === 'breedtekorfbal'),
    midweek: teams.filter((t) => t.category === 'midweek')
  };
}
</script>

<h1>CMS: Teams</h1>
<div class="team-toggle">
  <button class:active={showType === 'senior'} on:click={() => (showType = 'senior')}>Senioren</button>
  <button class:active={showType === 'junior'} on:click={() => (showType = 'junior')}>Jeugd</button>
  <button class="add-btn" on:click={goToAddTeam}>Add Team</button>
</div>
{#if loading}
  <p>Loading teams...</p>
{:else if error}
  <p class="error">{error}</p>
{:else}
  <div class="cms-team-list">
    {#if showType === 'senior'}
      {#each Object.entries(groupByCategory(teams.filter((t) => t.type === 'senior'))) as [cat, group]}
        {#if group.length}
          <h2 class="team-category">{cat.charAt(0).toUpperCase() + cat.slice(1)}</h2>
          <ul class="team-list">
            {#each group as team}
              <li class="team-list-item">
                <div class="team-info">
                  <strong>{team.name}</strong>
                  <span class="category">{team.category}</span>
                  {#if team.description}
                    <span class="desc">{team.description}</span>
                  {/if}
                </div>
                <div class="team-actions">
                  <a class="view-btn" href={`/teams/${team.id}`} target="_blank">View</a>
                  <button class="edit-btn" on:click={() => startEdit(team)}>Edit</button>
                  <button class="delete-btn" on:click={() => deleteTeam(team.id)} disabled={deletingId === team.id}>
                    {deletingId === team.id ? 'Deleting...' : 'Delete'}
                  </button>
                </div>
              </li>
            {/each}
          </ul>
        {/if}
      {/each}
    {:else}
      <h2 class="team-category">Jeugdteams</h2>
      <ul class="team-list">
        {#each teams.filter((t) => t.type === 'junior') as team}
          <li class="team-list-item">
            <div class="team-info">
              <strong>{team.name}</strong>
              <span class="category">{team.category}</span>
              {#if team.description}
                <span class="desc">{team.description}</span>
              {/if}
            </div>
            <div class="team-actions">
              <a class="view-btn" href={`/teams/${team.id}`} target="_blank">View</a>
              <button class="edit-btn" on:click={() => startEdit(team)}>Edit</button>
              <button class="delete-btn" on:click={() => deleteTeam(team.id)} disabled={deletingId === team.id}>
                {deletingId === team.id ? 'Deleting...' : 'Delete'}
              </button>
            </div>
          </li>
        {/each}
      </ul>
    {/if}
    {#if deleteError}<span class="error">{deleteError}</span>{/if}
  </div>
{/if}

<style>
.team-toggle {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  align-items: center;
}
.team-toggle button {
  background: #fff;
  border: 2px solid var(--primary-orange);
  color: var(--primary-orange);
  font-weight: bold;
  padding: 0.5rem 1.5rem;
  border-radius: 2rem;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
}
.team-toggle button.active,
.team-toggle button:focus {
  background: var(--primary-orange);
  color: #fff;
  outline: none;
}
.add-btn {
  margin-left: auto;
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
.add-btn:hover {
  background: #ff7f2a;
}
.cms-team-list {
  margin-top: 2rem;
  background: var(--primary-white);
  border-radius: 1rem;
  box-shadow: 0 2px 12px #0001;
  padding: 2rem 1.5rem;
  max-width: 1200px;
}
.team-category {
  margin-top: 2rem;
  color: var(--primary-orange);
  font-size: 1.3rem;
  font-weight: bold;
}
.team-list {
  list-style: none;
  padding: 0;
  margin: 1rem 0 2rem 0;
  display: flex;
  flex-wrap: wrap;
  gap: 1.5rem;
}
.team-list-item {
  background: #f8f8f8;
  border-radius: 0.75rem;
  box-shadow: 0 2px 8px #0001;
  padding: 1.2rem 1.5rem;
  min-width: 180px;
  max-width: 260px;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  transition: box-shadow 0.2s, background 0.2s;
  position: relative;
}
.team-list-item:hover {
  box-shadow: 0 6px 24px #0002;
  background: #fff7f0;
}
.team-info {
  flex: 1 1 0%;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}
.team-info .category {
  color: #888;
  font-size: 1.05rem;
}
.team-info .desc {
  color: #666;
  font-size: 0.98rem;
  margin-top: 0.2rem;
}
.team-actions {
  display: flex;
  gap: 0.7rem;
  margin-top: 0.7rem;
}
.edit-btn, .delete-btn {
  background: var(--primary-orange);
  color: #fff;
  border: none;
  padding: 0.3rem 0.9rem;
  font-size: 0.97rem;
  border-radius: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}
.edit-btn:hover {
  background: #ff7f2a;
}
.delete-btn {
  background: #e74c3c;
}
.delete-btn:hover {
  background: #c0392b;
}
.view-btn {
  background: #fff;
  color: var(--primary-orange);
  border: 2px solid var(--primary-orange);
  padding: 0.3rem 0.9rem;
  font-size: 0.97rem;
  border-radius: 1rem;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  margin-right: 0.7rem;
  transition: background 0.2s, color 0.2s;
}
.view-btn:hover {
  background: var(--primary-orange);
  color: #fff;
}
.error {
  color: red;
  font-weight: bold;
  margin-top: 1rem;
}
</style> 