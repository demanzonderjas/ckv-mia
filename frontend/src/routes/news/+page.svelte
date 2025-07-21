<script lang="ts">
	import { onMount } from 'svelte';
	import PageBlocks from '$lib/PageBlocks.svelte';
	import EditFab from '$lib/EditFab.svelte';
	import NewsItem from '$lib/NewsItem.svelte';
	import NewsCategorySelect from '$lib/NewsCategorySelect.svelte';

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
	<NewsCategorySelect bind:value={selectedCategory}  />
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
	
	.error {
		color: red;
		font-weight: bold;
	}
	
	
	.news-filter-row {
		display: flex;
		align-items: center;
		gap: 1.2rem;
		margin-bottom: 1.5rem;
	}
</style>
