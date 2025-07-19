<script lang="ts">
	import { onMount } from 'svelte';
	import Calendar from '$lib/Calendar.svelte';
	import EditFab from '$lib/EditFab.svelte';
	let news = $state([]);
	let loading = $state(true);
	let error = $state('');

	const now = new Date();
	const weekEnd = new Date(now);
	weekEnd.setDate(now.getDate() + 7);

	onMount(async () => {
		try {
			const res = await fetch('http://localhost:8000/api/news');
			if (!res.ok) throw new Error('Failed to fetch news');
			const allNews = await res.json();
			news = allNews.slice(0, 5);
		} catch (e) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>
<EditFab href="/cms/pages" title="Beheer pagina's" />

<section class="hero">
	<div class="hero-content">
		<h1>Welcome to Our Sports Club</h1>
		<p>Join our vibrant community, stay updated on events, teams, and more!</p>
		<a class="cta" href="/teams">Explore Teams</a>
	</div>
	<div class="hero-image">
		<!-- Replace with actual image in /static later -->
		<div class="image-placeholder">Hero Image</div>
	</div>
</section>

<section class="latest-news">
	<h2>Latest News</h2>
	{#if loading}
		<p>Loading news...</p>
	{:else if error}
		<p class="error">{error}</p>
	{:else if news.length === 0}
		<p>No news articles found.</p>
	{:else}
		<ul>
			{#each news as article}
				<li class="news-preview">
					<a href="/news/{article.id}"><strong>{article.title}</strong></a>
					<span class="date"
						>{article.published_at ? new Date(article.published_at).toLocaleDateString() : ''}</span
					>
					<p>{article.content.slice(0, 100)}{article.content.length > 100 ? '…' : ''}</p>
				</li>
			{/each}
		</ul>
	{/if}
	<a class="all-news-link" href="/news">View all news</a>
</section>

<section class="homepage-calendar">
	<h2>Upcoming Week</h2>
	<Calendar start={now} end={weekEnd} navigatable={false} />
	<a class="all-events-link" href="/events">View all events</a>
</section>

<style>
	.hero {
		display: flex;
		align-items: center;
		justify-content: space-between;
		background: linear-gradient(90deg, var(--primary-orange) 60%, var(--primary-white) 100%);
		color: var(--primary-white);
		border-radius: 1.5rem;
		padding: 3rem 2rem;
		margin-bottom: 2rem;
		gap: 2rem;
		flex-wrap: wrap;
	}
	.hero-content {
		flex: 1 1 350px;
		z-index: 1;
	}
	.hero-content h1 {
		font-size: 2.8rem;
		margin-bottom: 1rem;
		color: var(--primary-white);
		text-shadow: 1px 1px 8px var(--primary-black, #0002);
	}
	.hero-content p {
		font-size: 1.3rem;
		margin-bottom: 2rem;
		color: var(--primary-white);
	}
	.cta {
		display: inline-block;
		background: var(--primary-black);
		color: var(--primary-orange);
		padding: 0.8rem 2rem;
		border-radius: 2rem;
		font-weight: bold;
		font-size: 1.1rem;
		text-decoration: none;
		transition:
			background 0.2s,
			color 0.2s;
	}
	.cta:hover {
		background: var(--primary-white);
		color: var(--primary-black);
	}
	.hero-image {
		flex: 1 1 300px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.image-placeholder {
		width: 320px;
		height: 220px;
		background: var(--primary-black);
		color: var(--primary-white);
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.5rem;
		border-radius: 1rem;
		box-shadow: 0 4px 24px #0002;
	}
	@media (max-width: 900px) {
		.hero {
			flex-direction: column;
			text-align: center;
			padding: 2rem 1rem;
		}
		.hero-image {
			margin-top: 2rem;
		}
	}
	.latest-news {
		margin-top: 2rem;
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 2rem 1.5rem;
	}
	.latest-news h2 {
		color: var(--primary-orange);
		margin-bottom: 1.5rem;
	}
	.latest-news ul {
		list-style: none;
		padding: 0;
		margin: 0 0 1rem 0;
		display: flex;
		flex-direction: column;
		gap: 1.5rem;
	}
	.news-preview {
		border-bottom: 1px solid #eee;
		padding-bottom: 1rem;
	}
	.news-preview:last-child {
		border-bottom: none;
	}
	.news-preview a {
		color: var(--primary-black);
		font-size: 1.1rem;
		text-decoration: none;
		font-weight: bold;
	}
	.news-preview a:hover {
		color: var(--primary-orange);
	}
	.news-preview .date {
		display: inline-block;
		margin-left: 1rem;
		color: #888;
		font-size: 0.95rem;
	}
	.news-preview p {
		margin: 0.5rem 0 0 0;
		color: var(--primary-black);
	}
	.all-news-link {
		display: inline-block;
		margin-top: 1rem;
		color: var(--primary-orange);
		font-weight: bold;
		text-decoration: underline;
	}
	.error {
		color: red;
		font-weight: bold;
	}
	.homepage-calendar {
		margin-top: 2rem;
	}
	.all-events-link {
		display: inline-block;
		margin-top: 1.5rem;
		color: var(--primary-orange);
		font-weight: bold;
		text-decoration: underline;
		font-size: 1.1rem;
	}
</style>
