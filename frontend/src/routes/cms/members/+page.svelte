<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';

interface Member {
  id: number;
  first_name: string;
  last_name: string;
  gender: string;
  email: string;
  phone: string;
  team_id: number;
}

let members: Member[] = $state([]);
let loading = $state(true);
let error = $state('');
let page = $state(1);
let totalPages = $state(1);
let perPage = 20;
let search = $state('');
let searchInput = $state('');
let searchTimeout: any = null;

async function fetchMembers() {
  loading = true;
  try {
    const params = new URLSearchParams({ page: String(page) });
    if (search) params.append('search', search);
    const res = await fetch(`http://localhost:8000/api/cms/members?${params.toString()}`);
    if (!res.ok) throw new Error('Failed to fetch members');
    const data = await res.json();
    members = data.data;
    totalPages = data.last_page;
  } catch (e: any) {
    error = e.message;
  } finally {
    loading = false;
  }
}

onMount(fetchMembers);
$effect(() => { fetchMembers(); });

function startEdit(member: Member) {
  goto(`/cms/members/edit/${member.id}`);
}

function handleSearchInput(e: Event) {
  searchInput = (e.target as HTMLInputElement).value;
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    page = 1;
    search = searchInput.trim();
  }, 350);
}

function clearSearch() {
  searchInput = '';
  search = '';
  page = 1;
}

function goToAddMember() {
  goto('/cms/members/new');
}
</script>

<h1>CMS: Members</h1>
<div class="members-header-row">
  <button class="add-btn" on:click={goToAddMember}>Add Member</button>
  <div class="search-row">
    <input
      type="text"
      class="search-input"
      placeholder="Zoek op voornaam, achternaam of e-mail..."
      bind:value={searchInput}
      on:input={handleSearchInput}
    />
    {#if search}
      <button class="clear-btn" on:click={clearSearch}>×</button>
    {/if}
  </div>
</div>
{#if loading}
  <p>Loading members...</p>
{:else if error}
  <p class="error">{error}</p>
{:else}
  <div class="cms-table-wrapper">
    <table class="cms-table">
      <thead>
        <tr>
          <th>Voornaam</th>
          <th>Achternaam</th>
          <th>Geslacht</th>
          <th>Email</th>
          <th>Telefoon</th>
          <th>Team ID</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        {#each members as member}
          <tr>
            <td>{member.first_name}</td>
            <td>{member.last_name}</td>
            <td>{member.gender === 'male' ? 'Man' : member.gender === 'female' ? 'Vrouw' : member.gender}</td>
            <td>{member.email}</td>
            <td>{member.phone}</td>
            <td>{member.team_id}</td>
            <td><button class="edit-btn" on:click={() => startEdit(member)}>Edit</button></td>
          </tr>
        {/each}
      </tbody>
    </table>
    <div class="pagination">
      <button on:click={() => page = Math.max(1, page - 1)} disabled={page === 1}>Previous</button>
      <span>Page {page} of {totalPages}</span>
      <button on:click={() => page = Math.min(totalPages, page + 1)} disabled={page === totalPages}>Next</button>
    </div>
  </div>
{/if}

<style>
.members-header-row {
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
.search-row {
  flex: 1 1 0%;
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin-bottom: 0;
}
.search-input {
  flex: 1 1 0%;
  padding: 0.6rem 1.2rem;
  border-radius: 1.2rem;
  border: 1.5px solid #e0e0e0;
  font-size: 1.08rem;
  background: #fcfcff;
  box-shadow: 0 1px 4px #0001;
  transition: border 0.2s, box-shadow 0.2s;
  height: 2.8rem;
  box-sizing: border-box;
}
.search-input:focus {
  border: 1.5px solid var(--primary-orange, #ff6600);
  outline: none;
  box-shadow: 0 2px 8px #ff660022;
}
.clear-btn {
  background: #eee;
  color: #888;
  border: none;
  font-size: 1.5rem;
  border-radius: 50%;
  width: 2.2rem;
  height: 2.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, color 0.2s;
}
.clear-btn:hover {
  background: #ffebe0;
  color: var(--primary-orange);
}
.cms-table-wrapper {
  margin-top: 2rem;
  background: var(--primary-white);
  border-radius: 1rem;
  box-shadow: 0 2px 12px #0001;
  padding: 2rem 1.5rem;
  max-width: 1100px;
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
.pagination {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-top: 1.5rem;
}
.pagination button {
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
.pagination button:disabled {
  background: #eee;
  color: #aaa;
  cursor: not-allowed;
}
.error {
  color: red;
  font-weight: bold;
}
</style> 