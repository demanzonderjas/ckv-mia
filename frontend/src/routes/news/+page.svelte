<script lang="ts">
	import { onMount } from 'svelte';
	let news = $state([]);
	let loading = $state(true);
	let error = $state('');

	onMount(async () => {
		try {
			const res = await fetch('http://localhost:8000/api/news');
			if (!res.ok) throw new Error('Failed to fetch news');
			news = await res.json();
		} catch (e) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

<h1>News</h1>

{#if loading}
	<p>Loading news...</p>
{:else if error}
	<p class="error">{error}</p>
{:else if news.length === 0}
	<p>No news articles found.</p>
{:else}
	<div class="news-list">
		{#each news as article}
			<article class="news-item">
				{#if article.image_url}
					<img src={article.image_url} alt={article.title} class="news-img" />
				{/if}
				<div class="news-content">
					<h2>{article.title}</h2>
					<p class="date">
						{article.published_at ? new Date(article.published_at).toLocaleDateString() : ''}
					</p>
					<p>{article.content}</p>
				</div>
			</article>
		{/each}
	</div>
{/if}

<style>
	.news-list {
		display: flex;
		flex-direction: column;
		gap: 2rem;
	}
	.news-item {
		display: flex;
		gap: 2rem;
		background: var(--primary-white);
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 1.5rem;
		align-items: flex-start;
		flex-wrap: wrap;
	}
	.news-img {
		width: 180px;
		height: 120px;
		object-fit: cover;
		border-radius: 0.75rem;
		background: #eee;
	}
	.news-content {
		flex: 1 1 200px;
	}
	.news-content h2 {
		color: var(--primary-orange);
		margin: 0 0 0.5rem 0;
	}
	.date {
		color: var(--primary-black);
		font-size: 0.95rem;
		margin-bottom: 0.75rem;
		font-weight: 500;
	}
	.error {
		color: red;
		font-weight: bold;
	}
	@media (max-width: 700px) {
		.news-item {
			flex-direction: column;
			align-items: stretch;
		}
		.news-img {
			width: 100%;
			height: 180px;
		}
	}
</style>
