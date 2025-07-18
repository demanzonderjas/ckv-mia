<script lang="ts">
	import { onMount } from 'svelte';
	import PageBlocks from '$lib/PageBlocks.svelte';

	interface ContentBlock {
		id: number;
		type: string;
		content: any;
		order: number;
	}
	interface PageData {
		id: number;
		title: string;
		description: string;
		content_blocks: ContentBlock[];
	}
	interface NewsItem {
		id: number;
		title: string;
		content: string;
		image_url?: string;
		published_at?: string;
	}

	let pageData: PageData | null = $state(null);
	let news: NewsItem[] = $state([]);
	let loading = $state(true);
	let error = $state('');

	function parseContent(content: any) {
		if (typeof content === 'string') return JSON.parse(content);
		return content;
	}

	onMount(async () => {
		try {
			const [pageRes, newsRes] = await Promise.all([
				fetch('http://localhost:8000/api/pages/slug/news'),
				fetch('http://localhost:8000/api/news')
			]);
			if (pageRes.ok) pageData = await pageRes.json();
			if (newsRes.ok) news = await newsRes.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

<h1>Nieuws</h1>
<PageBlocks slug="news" />
{#if loading}
	<p>Loading...</p>
{:else if error}
	<p class="error">{error}</p>
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
	.desc {
		color: #666;
		margin-bottom: 1.5rem;
	}
	.block-text {
		font-size: 1.15rem;
		margin-bottom: 1.5rem;
		color: var(--primary-black);
	}
	.block-image {
		max-width: 100%;
		display: block;
		margin: 2rem 0;
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
	}
	.news-list {
		display: flex;
		flex-direction: column;
		gap: 2rem;
		margin-top: 2rem;
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
