<script lang="ts">
	import { onMount } from 'svelte';
	import { goto } from '$app/navigation';

	interface Team {
		id: number;
		name: string;
		description: string;
		category: string;
	}

	let teams: Team[] = $state([]);
	let loading = $state(true);
	let error = $state('');

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
	});

	function groupByCategory(teams: Team[]) {
		return {
			wedstrijdkorfbal: teams.filter((t) => t.category === 'wedstrijdkorfbal'),
			breedtekorfbal: teams.filter((t) => t.category === 'breedtekorfbal'),
			midweek: teams.filter((t) => t.category === 'midweek')
		};
	}
</script>

<h1>Teams</h1>
{#if loading}
	<p>Loading teams...</p>
{:else if error}
	<p class="error">{error}</p>
{:else}
	{#each Object.entries(groupByCategory(teams)) as [cat, group]}
		{#if group.length}
			<h2 class="team-category">{cat.charAt(0).toUpperCase() + cat.slice(1)}</h2>
			<ul class="team-list">
				{#each group as team}
					<li
						class="team-item"
						on:click={() => goto(`/teams/${team.id}`)}
						tabindex="0"
						role="button"
						aria-label={`Bekijk ${team.name}`}
					>
						<a class="team-link" href={`/teams/${team.id}`} tabindex="-1"
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
{/if}

<style>
	h1 {
		font-size: 2.8rem;
		font-weight: 800;
		margin-bottom: 1.5rem;
		color: var(--primary-orange);
		letter-spacing: -1px;
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
