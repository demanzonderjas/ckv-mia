<script lang="ts">
	import { onMount, onDestroy } from 'svelte';
	import { goto } from '$app/navigation';
	import { Calendar } from '@fullcalendar/core';
	import dayGridPlugin from '@fullcalendar/daygrid';
	import interactionPlugin from '@fullcalendar/interaction';
	import { createEventDispatcher } from 'svelte';

	let { start, end, navigatable = true } = $props();

	interface Event {
		id: number;
		name: string;
		date: string;
		description: string;
		location: string;
		team_id: number;
	}

	let calendarEl = $state<HTMLDivElement | null>(null);
	let calendarInstance = $state<Calendar | null>(null);
	let events = $state<Event[]>([]);
	let loading = $state(true);
	let error = $state('');
	const dispatch = createEventDispatcher();

	function getCalendarEvents() {
		return events.map((e) => ({
			id: String(e.id),
			title: e.name,
			start: e.date,
			end: e.date,
			description: e.description,
			allDay: true,
			url: `/events/${e.id}`
		}));
	}

	async function fetchEventsForRange(start: Date, end: Date) {
		loading = true;
		try {
			const startStr = start.toISOString().slice(0, 19).replace('T', ' ');
			const endStr = end.toISOString().slice(0, 19).replace('T', ' ');
			const res = await fetch(
				`http://localhost:8000/api/events?start=${encodeURIComponent(startStr)}&end=${encodeURIComponent(endStr)}`
			);
			if (!res.ok) throw new Error('Failed to fetch events');
			events = await res.json();
			if (calendarInstance) {
				calendarInstance.removeAllEvents();
				calendarInstance.addEventSource(getCalendarEvents());
			}
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	}

	onMount(async () => {
		await fetchEventsForRange(start, end);
		if (calendarEl && !calendarInstance) {
			calendarInstance = new Calendar(calendarEl, {
				plugins: [dayGridPlugin, interactionPlugin],
				events: getCalendarEvents(),
				headerToolbar: navigatable
					? { left: 'prev,next today', center: 'title', right: '' }
					: { left: '', center: 'title', right: '' },
				height: 'auto',
				initialView: 'dayGridWeek',
				dayMaxEventRows: true,
				fixedWeekCount: false,
				contentHeight: 'auto',
				aspectRatio: 1.5,
				locale: 'nl',
				eventClick: function (info) {
					info.jsEvent.preventDefault();
					if (info.event.url) {
						goto(info.event.url);
					}
				},
				...(navigatable && {
					datesSet: async function (info) {
						const start = new Date(info.startStr);
						const end = new Date(info.endStr);
						await fetchEventsForRange(start, end);
					}
				})
			});
			calendarInstance.render();
		}
	});

	onDestroy(() => {
		if (calendarInstance) {
			calendarInstance.destroy();
			calendarInstance = null;
		}
	});
</script>

<div class="calendar-wrapper">
	{#if loading}
		<div class="loading-overlay">
			<div class="spinner"></div>
		</div>
	{/if}
	{#if error}
		<p class="error">{error}</p>
	{/if}
	<div bind:this={calendarEl} class="calendar"></div>
</div>

<style>
	.calendar-wrapper {
		position: relative;
	}
	.loading-overlay {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: center;
		background: rgba(255, 255, 255, 0.7);
		z-index: 20;
	}
	.spinner {
		border: 4px solid #eee;
		border-top: 4px solid var(--primary-orange);
		border-radius: 50%;
		width: 40px;
		height: 40px;
		animation: spin 1s linear infinite;
	}
	@keyframes spin {
		0% {
			transform: rotate(0deg);
		}
		100% {
			transform: rotate(360deg);
		}
	}
	.calendar :global(.fc) {
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 1.5rem;
		margin-top: 2rem;
	}
	.calendar :global(.fc-toolbar-title) {
		color: var(--primary-orange);
		font-size: 1.5rem;
		font-weight: bold;
	}
	.calendar :global(.fc-daygrid-event),
	.calendar :global(.fc-event) {
		background: var(--primary-orange);
		color: var(--primary-white);
		border: none;
		border-radius: 0.5rem;
		padding: 2px 6px;
		font-size: 1rem;
	}
	.error {
		color: red;
		font-weight: bold;
	}
</style>
