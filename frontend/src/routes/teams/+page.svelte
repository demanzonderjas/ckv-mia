<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';
	import EditFab from '$lib/EditFab.svelte';
	import { page } from '$app/state';

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
	let showType = $state<'senior' | 'junior'>('senior');
	let pageTitle = undefined; // Remove $state and fetching for pageTitle

	onMount(async () => {
		try {
			const res = await fetch('http://localhost:8000/api/teams');
			if (!res.ok) throw new Error('Failed to fetch teams');
			teams = await res.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
		// Remove pageTitle fetching
	});

	function groupByCategory(teams: Team[]) {
		return {
			wedstrijdkorfbal: teams.filter((t) => t.category === 'wedstrijdkorfbal'),
			breedtekorfbal: teams.filter((t) => t.category === 'breedtekorfbal'),
			midweek: teams.filter((t) => t.category === 'midweek')
		};
	}
</script>
<EditFab href="/cms/teams" title="Beheer teams" />
<h1>Teams</h1>
<div class="team-toggle">
	<button class:active={showType === 'senior'} on:click={() => (showType = 'senior')}
		>Senioren</button
	>
	<button class:active={showType === 'junior'} on:click={() => (showType = 'junior')}>Jeugd</button>
</div>
{#if loading}
	<p>Loading teams...</p>
{:else if error}
	<p class="error">{error}</p>
{:else if showType === 'senior'}
	{#each Object.entries(groupByCategory(teams.filter((t) => t.type === 'senior'))) as [cat, group]}
		{#if group.length}
			<h2 class="team-category">{cat.charAt(0).toUpperCase() + cat.slice(1)}</h2>
			<ul class="team-list">
				{#each group as team}
					<li
						class="team-item"
						on:click={() => goto(`/teams/${team.id}?type=senior`)}
						tabindex="0"
						role="button"
						aria-label={`Bekijk ${team.name}`}
					>
						<a class="team-link" href={`/teams/${team.id}?type=senior`} tabindex="-1"
							><strong>{team.name}</strong></a
						>
						{#if team.description}
							<span class="desc">{team.description}</span>
						{/if}
					</li>
				{/each}
			</ul>
		{/if}
	{/each}
{:else}
	<h2 class="team-category">Jeugdteams</h2>
	<ul class="team-list">
		{#each teams.filter((t) => t.type === 'junior') as team}
			<li
				class="team-item"
				on:click={() => goto(`/teams/${team.id}?type=junior`)}
				tabindex="0"
				role="button"
				aria-label={`Bekijk ${team.name}`}
			>
				<a class="team-link" href={`/teams/${team.id}?type=junior`} tabindex="-1"
					><strong>{team.name}</strong></a
				>
				{#if team.description}
					<span class="desc">{team.description}</span>
				{/if}
			</li>
		{/each}
	</ul>
{/if}

<style>
	h1 {
		font-size: 2.8rem;
		font-weight: 800;
		margin-bottom: 1.5rem;
		color: var(--primary-orange);
		letter-spacing: -1px;
	}
	.team-toggle {
		display: flex;
		gap: 1rem;
		margin-bottom: 2rem;
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
		transition:
			background 0.2s,
			color 0.2s;
	}
	.team-toggle button.active,
	.team-toggle button:focus {
		background: var(--primary-orange);
		color: #fff;
		outline: none;
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
	.team-item {
		background: var(--primary-white);
		border-radius: 0.75rem;
		box-shadow: 0 2px 8px #0001;
		padding: 1rem 1.5rem;
		min-width: 180px;
		max-width: 260px;
		display: flex;
		flex-direction: column;
		gap: 0.5rem;
		transition:
			box-shadow 0.2s,
			transform 0.18s cubic-bezier(0.4, 1.5, 0.5, 1),
			background 0.2s;
		cursor: pointer;
	}
	.team-item:hover {
		box-shadow: 0 6px 24px #0002;
		background: #fff7f0;
		transform: scale(1.045) translateY(-2px);
	}
	.team-item .desc {
		color: #666;
		font-size: 0.98rem;
	}
	.error {
		color: red;
		font-weight: bold;
	}
	.team-link {
		color: var(--primary-black);
		text-decoration: none;
		font-weight: bold;
		font-size: 1.1rem;
		transition: color 0.2s;
	}
	.team-link:hover {
		color: var(--primary-orange);
		text-decoration: underline;
	}
</style>
