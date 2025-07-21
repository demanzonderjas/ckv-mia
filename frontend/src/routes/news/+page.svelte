<script lang="ts">
	import { onMount } from 'svelte';
	import PageBlocks from '$lib/PageBlocks.svelte';
	import EditFab from '$lib/EditFab.svelte';
	import NewsItem from '$lib/NewsItem.svelte';
	import NewsCategorySelect from '$lib/NewsCategorySelect.svelte';
	import { page } from '$app/state';

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

	let pageData: any = $state(null);
	let news: any[] = $state([]);
	let loading = $state(true);
	let error = $state('');
	let selectedCategory: number|null = $state(null);
	// Remove pageTitle and its fetching logic

	function parseContent(content: any) {
		if (typeof content === 'string') return JSON.parse(content);
		return content;
	}

	function formatDutchDate(dateStr: string) {
		if (!dateStr) return '';
		const date = new Date(dateStr);
		return new Intl.DateTimeFormat('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
	}

	function filteredNews() {
		if (!selectedCategory) return news;
		return news.filter(n => n.news_category_id === selectedCategory || n.category?.id === selectedCategory);
	}

	onMount(async () => {
		try {
			const [pageRes, newsRes] = await Promise.all([
				fetch(`http://localhost:8000/api/pages/slug/news`),
				fetch('http://localhost:8000/api/news')
			]);
			if (pageRes.ok) {
				pageData = await pageRes.json();
			}
			if (newsRes.ok) news = await newsRes.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

<EditFab href="/cms/news" title="Beheer nieuws" />
<h1>Nieuws</h1>
<PageBlocks slug="news" />
<div class="news-filter-row">
	<label for="category-select">Categorie:</label>
	<NewsCategorySelect bind:value={selectedCategory} label="Alle categorieën" />
</div>
{#if loading}
	<p>Loading...</p>
{:else if error}
	<p class="error">{error}</p>
{:else}
	<div class="news-list news-list-grid">
		{#each filteredNews() as item}
			<NewsItem news={item} />
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
		flex-wrap: wrap;
		gap: 2rem;
		margin-top: 2rem;
	}
	.news-list-grid {
		flex-direction: row;
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
	.published-date {
		background: #eaf6ff;
		color: #1976d2;
		padding: 0.15rem 0.7rem;
		border-radius: 0.5rem;
		font-size: 0.98em;
		margin-left: 0.2rem;
		font-weight: 500;
		letter-spacing: 0.01em;
		display: inline-block;
	}
	.news-summary {
		color: #444;
		font-size: 1.08rem;
		margin-bottom: 0.7rem;
		margin-top: -0.3rem;
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
	.news-list-item-link {
		text-decoration: none;
		color: inherit;
		display: block;
		border-radius: 1rem;
		transition: box-shadow 0.18s, background 0.18s;
	}
	.news-list-item-link:hover .news-list-item {
		background: #fff7f2;
		box-shadow: 0 4px 16px #ff660033;
	}
	.news-filter-row {
		display: flex;
		align-items: center;
		gap: 1.2rem;
		margin-bottom: 1.5rem;
	}
</style>
