<script lang="ts">
	import { page } from '$app/stores';
	import { onMount } from 'svelte';

	interface Event {
		id: number;
		name: string;
		date: string;
		location: string;
		description: string;
		team_id: number;
	}

	let event: Event | null = $state(null);
	let loading = $state(true);
	let error = $state('');

	onMount(async () => {
		const id = $page.params.id;
		try {
			const res = await fetch(`http://localhost:8000/api/events/${id}`);
			if (!res.ok) throw new Error('Event not found');
			event = await res.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

{#if loading}
	<p>Loading event...</p>
{:else if error}
	<p class="error">{error}</p>
{:else if event}
	<article class="event-detail">
		<h1>{event.name}</h1>
		<p class="date">{event.date ? new Date(event.date).toLocaleString() : ''}</p>
		<p class="location"><strong>Location:</strong> {event.location}</p>
		<p class="description">{event.description}</p>
		{#if event.team_id}
			<p class="team"><strong>Team ID:</strong> {event.team_id}</p>
		{/if}
	</article>
{/if}

<style>
	.event-detail {
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 2rem 1.5rem;
		margin-top: 2rem;
		max-width: 600px;
	}
	.event-detail h1 {
		color: var(--primary-orange);
		margin-bottom: 1rem;
	}
	.date {
		color: #888;
		font-size: 1.1rem;
		margin-bottom: 1rem;
	}
	.location,
	.team {
		font-size: 1.1rem;
		margin-bottom: 0.5rem;
	}
	.description {
		margin-top: 1.5rem;
		font-size: 1.15rem;
	}
	.error {
		color: red;
		font-weight: bold;
	}
</style>
