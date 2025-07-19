<script lang="ts">
import { onMount, createEventDispatcher } from 'svelte';
import FormBuilder from './FormBuilder.svelte';

let { teamId, onMembersChange = () => {}, showAddButtonOnTop = false } = $props();

let status = $state<'loading' | 'error' | 'ready' | 'editing' | 'adding'>('ready');
let error = $state<{ add?: string; edit?: string; remove?: string }>({});
let memberIdInProgress = $state<number | null>(null);
let editingMember: any = $state(null);
let members: any[] = $state([]);

const dispatch = createEventDispatcher();

const memberFields = [
  { name: 'first_name', label: 'Voornaam', type: 'text', required: true },
  { name: 'last_name', label: 'Achternaam', type: 'text', required: true },
  { name: 'gender', label: 'Gender', type: 'select', required: true, options: [
    { value: 'male', label: 'Man' },
    { value: 'female', label: 'Vrouw' }
  ]}
];

onMount(reloadMembers);

async function reloadMembers() {
  status = 'loading';
  try {
    const res = await fetch(`http://localhost:8000/api/cms/members?team_id=${teamId}`);
    if (!res.ok) throw new Error('Failed to fetch members');
    const result = await res.json();
    members = result.data || [];
    status = 'ready';
    dispatch('membersChange', members);
    if (onMembersChange) onMembersChange(members);
  } catch (e: any) {
    error.add = e.message;
    status = 'error';
  }
}

async function addMember(values: any) {
  error.add = '';
  status = 'adding';
  try {
    const payload = { ...values, team_id: teamId };
    const res = await fetch('http://localhost:8000/api/members', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    if (!res.ok) throw new Error('Failed to add member');
    await reloadMembers();
    status = 'ready';
  } catch (e: any) {
    error.add = e.message;
    status = 'ready';
  }
}

async function removeMember(memberId: number) {
  error.remove = '';
  memberIdInProgress = memberId;
  try {
    const res = await fetch(`http://localhost:8000/api/members/${memberId}`, {
      method: 'DELETE'
    });
    if (!res.ok) throw new Error('Failed to remove member');
    await reloadMembers();
  } catch (e: any) {
    error.remove = e.message;
  } finally {
    memberIdInProgress = null;
  }
}

function startEditMember(member: any) {
  editingMember = member;
  status = 'editing';
}

function stopEditMember() {
  editingMember = null;
  status = 'ready';
  error.edit = '';
}

async function saveEditMember(memberId: number, values: any) {
  error.edit = '';
  memberIdInProgress = memberId;
  try {
    const res = await fetch(`http://localhost:8000/api/members/${memberId}`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(values)
    });
    if (!res.ok) throw new Error('Failed to update member');
    editingMember = null;
    await reloadMembers();
    status = 'ready';
  } catch (e: any) {
    error.edit = e.message;
    status = 'editing';
  } finally {
    memberIdInProgress = null;
  }
}
</script>

<h2>Teamleden</h2>
{#if status === 'error'}
  <span class="error">{error.add}</span>
{:else if status === 'ready'}
  {#if showAddButtonOnTop}
    <div class="add-btn-row">
      <button class="add-btn" on:click={() => (status = 'adding')}>Add Member</button>
    </div>
  {/if}
  <div class="member-groups">
    <div class="member-group">
      <h3>Mannen</h3>
      <ul class="member-list">
        {#each members.filter((m: any) => m.gender === 'male') as member}
          <li class="member-item">
            <strong>{member.first_name ? member.first_name + (member.last_name ? ' ' + member.last_name : '') : member.last_name}</strong>
            <button class="edit-btn" on:click={() => startEditMember(member)}>Edit</button>
            <button class="remove-btn" on:click={() => removeMember(member.id)} disabled={memberIdInProgress === member.id}>
              {memberIdInProgress === member.id ? 'Removing...' : 'Remove'}
            </button>
          </li>
        {/each}
      </ul>
    </div>
    <div class="member-group">
      <h3>Vrouwen</h3>
      <ul class="member-list">
        {#each members.filter((m: any) => m.gender === 'female') as member}
          <li class="member-item">
            <strong>{member.first_name ? member.first_name + (member.last_name ? ' ' + member.last_name : '') : member.last_name}</strong>
            <button class="edit-btn" on:click={() => startEditMember(member)}>Edit</button>
            <button class="remove-btn" on:click={() => removeMember(member.id)} disabled={memberIdInProgress === member.id}>
              {memberIdInProgress === member.id ? 'Removing...' : 'Remove'}
            </button>
          </li>
        {/each}
      </ul>
    </div>
  </div>
  {#if error.remove}<span class="error">{error.remove}</span>{/if}
  {#if !showAddButtonOnTop}
    <button class="add-btn" on:click={() => (status = 'adding')}>Add Member</button>
  {/if}
{:else if status === 'adding'}
  <FormBuilder
    fields={memberFields}
    on:submit={(e: any) => addMember(e.detail)}
    submitLabel="Add"
  />
  <button on:click={() => (status = 'ready')}>Cancel</button>
{:else if status === 'editing'}
  <FormBuilder
    fields={memberFields.map(f => ({ ...f, value: editingMember?.[f.name] ?? '' }))}
    on:submit={(e: any) => saveEditMember(editingMember?.id, e.detail)}
    submitLabel="Save"
  />
  <button on:click={stopEditMember}>Cancel</button>
{/if}

<style>
.member-groups {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
  margin-top: 1.5rem;
}
.member-group {
  flex: 1 1 200px;
  background: #f8f8f8;
  border-radius: 1rem;
  box-shadow: 0 2px 8px #0001;
  padding: 1.2rem 1.5rem;
  min-width: 220px;
}
.member-group h3 {
  color: var(--primary-orange);
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}
.member-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.member-item {
  background: #fff;
  border-radius: 0.7rem;
  box-shadow: 0 1px 4px #0001;
  padding: 0.7rem 1.2rem;
  display: flex;
  align-items: center;
  gap: 1.2rem;
}
.member-item strong {
  font-size: 1.08rem;
  color: var(--primary-orange);
}
.add-btn, .remove-btn, .edit-btn {
  margin-left: 0.5rem;
  background: var(--primary-orange);
  color: #fff;
  border: none;
  padding: 0.25rem 0.9rem;
  border-radius: 0.7rem;
  font-weight: 500;
  font-size: 0.98rem;
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
.add-btn-top {
  margin-bottom: 1.2rem;
  margin-left: 0;
}
.add-btn-row {
  width: 100%;
  display: block;
  margin-bottom: 1.2rem;
}
@media (max-width: 700px) {
  .member-groups {
    flex-direction: column;
    gap: 1.2rem;
  }
  .member-group {
    min-width: 0;
    width: 100%;
  }
}
</style> 