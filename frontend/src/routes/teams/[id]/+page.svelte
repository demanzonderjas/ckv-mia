<script lang="ts">
	import { page } from '$app/stores';
	import { onMount } from 'svelte';
	import PageContent from '$lib/PageContent.svelte';
	import EditFab from '$lib/EditFab.svelte';

	interface Team {
		id: number;
		name: string;
		description: string;
		category: string;
	}
	interface Member {
		id: number;
		name: string;
		gender: string;
	}

	let team: Team | null = $state(null);
	let members: Member[] = $state([]);
	let loading = $state(true);
	let error = $state('');
	let type: string | null = null;

	onMount(async () => {
		const id = $page.params.id;
		type = $page.url.searchParams.get('type');
		try {
			const res = await fetch(`http://localhost:8000/api/teams/${id}`);
			if (!res.ok) throw new Error('Team not found');
			team = await res.json();
			const resMembers = await fetch(`http://localhost:8000/api/members?team_id=${id}`);
			if (!resMembers.ok) throw new Error('Could not fetch team members');
			members = await resMembers.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

<svelte:head>
	<title>{team?.name ? `${team.name} | CKV MIA` : 'CKV MIA'}</title>
</svelte:head>

{#if loading}
	<p>Loading team...</p>
{:else if error}
	<p class="error">{error}</p>
{:else if team}
	<article class="team-detail">
		<h1>{team.name}</h1>
		<p class="category">Categorie: {team.category}</p>
		{#if team.description}
			<p class="desc">{team.description}</p>
		{/if}
		<div class="team-picture-placeholder">Teamfoto (placeholder)</div>
		<h2>Leden</h2>
		<div class="member-groups">
			<div class="member-group">
				<h3>{(type ?? team?.type) === 'junior' ? 'Jongens' : 'Mannen'}</h3>
				<ul class="member-list">
					{#each members.filter((m) => m.gender === 'male') as member}
						<li>{member.name}</li>
					{/each}
				</ul>
			</div>
			<div class="member-group">
				<h3>{(type ?? team?.type) === 'junior' ? 'Meisjes' : 'Vrouwen'}</h3>
				<ul class="member-list">
					{#each members.filter((m) => m.gender === 'female') as member}
						<li>{member.name}</li>
					{/each}
				</ul>
			</div>
		</div>
		<EditFab href={`/cms/teams/edit/${team.id}`} title="Bewerk dit team" />
	</article>
{/if}

<style>
	.team-detail {
		padding: 1.5rem 1rem;
		width: 100%;
		min-width: 320px;
	}
	.team-detail h1 {
		color: var(--primary-orange);
		margin-bottom: 1rem;
	}
	.category {
		color: #888;
		font-size: 1.1rem;
		margin-bottom: 0.5rem;
	}
	.desc {
		margin-bottom: 1.5rem;
		color: #444;
	}
	.team-picture-placeholder {
		width: 100%;
		height: 180px;
		background: #eee;
		color: #aaa;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.2rem;
		border-radius: 0.75rem;
		margin-bottom: 2rem;
	}
	.member-list {
		list-style: none;
		padding: 0;
		margin: 1rem 0 0 0;
		display: flex;
		flex-wrap: wrap;
		gap: 1rem 2rem;
	}
	.member-list li {
		background: #f0f0f0;
		border-radius: 0.5rem;
		padding: 0.5rem 1rem;
		font-size: 1.05rem;
	}
	.error {
		color: red;
		font-weight: bold;
	}
	.member-groups {
		display: flex;
		gap: 2rem;
		flex-wrap: wrap;
		margin-top: 1.5rem;
	}
	.member-group {
		flex: 1 1 200px;
	}
	.member-group h3 {
		color: var(--primary-orange);
		font-size: 1.1rem;
		margin-bottom: 0.5rem;
	}
</style>
