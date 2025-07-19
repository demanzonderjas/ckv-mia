<script lang="ts">
	import { page } from '$app/stores';
	import { onMount } from 'svelte';
	import DOMPurify from 'dompurify';
	import EditFab from '$lib/EditFab.svelte';

	// Placeholder: replace with real auth/role check
	const canEdit = true;

	interface Event {
		id: number;
		name: string;
		date: string;
		location: string;
		description: string;
		team_id: number;
		team?: { id: number; name: string };
		image?: { path: string };
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

<svelte:head>
  <title>{event?.name ? `${event.name} | CKV MIA` : 'CKV MIA'}</title>
</svelte:head>

{#if loading}
	<p>Loading event...</p>
{:else if error}
	<p class="error">{error}</p>
{:else if event}
	<article class="event-detail">
		{#if canEdit}
			<EditFab href={`/cms/events/edit/${event.id}`} title="Bewerk dit evenement" />
		{/if}
		{#if event.image?.path}
			<div class="event-header-image" style={`background-image: url('http://localhost:8000${event.image.path}')`}>
				{#if event.team?.name}
					<div class="team-link-topright">
						<a href={`/teams/${event.team.id}`}>{event.team.name}</a>
					</div>
				{/if}
				<div class="event-header-overlay">
					<h1 class="event-title">{event.name}</h1>
				</div>
			</div>
		{:else}
			{#if event.team?.name}
				<div class="team-link-topright">
					<a href={`/teams/${event.team.id}`}>{event.team.name}</a>
				</div>
			{/if}
			<h1 class="event-title">{event.name}</h1>
		{/if}
		<div class="event-content-card">
			<p class="date">{event.date ? new Date(event.date).toLocaleString() : ''}</p>
			<p class="location"><strong>Locatie:</strong> {event.location}</p>
			{#if event.description}
				<div class="event-rich">{@html DOMPurify.sanitize(event.description)}</div>
			{/if}
		</div>
	</article>
{/if}

<style>
.event-detail {
	padding: 0 0 1.5rem 0;
	width: 100vw;
	max-width: 100vw;
	margin-inline: 0;
	background: #fff;
}
.event-header-image {
	position: relative;
	height: 340px;
	width: 100%;
	background-size: cover;
	background-position: center;
	display: flex;
	align-items: flex-end;
	margin-bottom: 1.2rem;
	box-shadow: 0 2px 12px #0002;
	border-radius: 0;
}
.event-header-overlay {
	width: 100%;
	height: 100%;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: flex-end;
	padding-bottom: 2.2rem;
	background: linear-gradient(180deg, rgba(0,0,0,0) 60%, rgba(0,0,0,0.65) 100%);
}
.event-title {
	color: #fff;
	font-size: 2.6rem;
	font-weight: 900;
	text-shadow: 0 4px 24px #000b, 0 1px 2px #000a;
	margin: 0.2rem 0 0 0;
	line-height: 1.1;
	text-align: center;
	letter-spacing: -0.5px;
	margin-bottom: 0.7rem;
}
.team-link {
	margin-bottom: 0.7rem;
	font-size: 1.08rem;
	color: #fff;
	text-shadow: 0 1px 6px #000a;
	font-weight: 500;
	text-align: center;
}
.team-link a {
	color: #fff;
	font-weight: 600;
	text-decoration: underline;
	transition: color 0.2s;
}
.team-link a:hover {
	color: #ff7f2a;
}
.date {
	color: #ff6600;
	font-size: 1.08rem;
	margin-bottom: 0.7rem;
	font-weight: 600;
}
.location {
	font-size: 1.12rem;
	margin-bottom: 1.2rem;
	font-weight: 500;
}
.event-rich {
	margin-top: 1.5rem;
	font-size: 1.15rem;
	color: var(--primary-black);
	background: none;
	border-radius: 0.7rem;
	box-shadow: none;
	padding: 0;
}
.error {
	color: red;
	font-weight: bold;
}
.event-content-card {
	background: #fff;
	padding: 2rem 1.2rem 2.2rem 1.2rem;
	max-width: 700px;
	position: relative;
	z-index: 2;
	/* No margin, border-radius, or box-shadow on mobile */
}
.team-link-topright {
	position: absolute;
	top: 1.2rem;
	right: 1.2rem;
	background: rgba(0,0,0,0.55);
	color: #fff;
	padding: 0.45rem 1.1rem;
	border-radius: 1.2rem;
	font-size: 1.05rem;
	font-weight: 600;
	z-index: 3;
	box-shadow: 0 2px 8px #0003;
}
.team-link-topright a {
	color: #fff;
	text-decoration: none;
	transition: color 0.2s;
}
.team-link-topright a:hover {
	color: #ff7f2a;
}
.edit-fab {
	position: fixed;
	top: 1.5rem;
	right: 1.5rem;
	z-index: 100;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 3.2rem;
	height: 3.2rem;
	background: var(--primary-orange, #ff6600);
	color: #fff;
	border-radius: 50%;
	box-shadow: 0 2px 12px #0003;
	transition: background 0.2s, box-shadow 0.2s;
	text-decoration: none;
	border: none;
	outline: none;
	cursor: pointer;
}
.edit-fab:hover {
	background: #ff7f2a;
	box-shadow: 0 4px 16px #ff660033;
}
.edit-fab svg {
	display: block;
}
@media (min-width: 900px) {
	.event-detail {
		width: 100%;
		max-width: 900px;
		margin-inline: auto;
		border-radius: 1.2rem;
		box-shadow: 0 2px 12px #0001;
		padding: 0 0 1.5rem 0;
	}
	.event-header-image {
		border-radius: 1.2rem 1.2rem 0 0;
	}
	.event-content-card {
		border-radius: 1.2rem;
		box-shadow: 0 2px 12px #0001;
		margin: -3.5rem auto 0 auto;
	}
}
</style>
