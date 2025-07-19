<script lang="ts">
import { onMount } from 'svelte';
import { goto } from '$app/navigation';

interface NewsItem {
	id: number;
	title: string;
	content: string;
	image_url?: string;
	published_at?: string;
}

let news: NewsItem[] = $state([]);
let loading = $state(true);
let error = $state('');
let deleteError = $state('');
let deletingId = $state<number|null>(null);

async function fetchNews() {
	loading = true;
	try {
		const res = await fetch('http://localhost:8000/api/news');
		if (!res.ok) throw new Error('Failed to fetch news');
		news = await res.json();
	} catch (e: any) {
		error = e.message;
	} finally {
		loading = false;
	}
}

onMount(fetchNews);

function goToAddNews() {
	goto('/cms/news/new');
}

function startEdit(newsItem: NewsItem) {
	goto(`/cms/news/edit/${newsItem.id}`);
}

async function deleteNews(id: number) {
	if (!confirm('Are you sure you want to delete this news item?')) return;
	deleteError = '';
	deletingId = id;
	try {
		const res = await fetch(`http://localhost:8000/api/news/${id}`, {
			method: 'DELETE',
			headers: { 'Accept': 'application/json' },
		});
		if (!res.ok) throw new Error('Failed to delete news item');
		await fetchNews();
	} catch (e: any) {
		deleteError = (e as any).message;
	} finally {
		deletingId = null;
	}
}

function formatDutchDate(dateStr: string) {
  if (!dateStr) return '';
  const date = new Date(dateStr);
  return new Intl.DateTimeFormat('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
}
</script>

<h1>CMS: News</h1>
<button class="add-btn" on:click={goToAddNews}>Add News</button>

<section class="cms-news-list">
	<h2>All News</h2>
	{#if loading}
		<p>Loading...</p>
	{:else if error}
		<p class="error">{error}</p>
	{:else}
		<ul class="news-list">
			{#each news as article}
				<li class="news-list-item">
					<div class="news-info">
						<strong>{article.title}</strong>
						{#if article.published_at}
							<span class="published-date">{formatDutchDate(article.published_at)}</span>
						{/if}
					</div>
					<div class="news-actions">
						<button class="edit-btn" on:click={() => startEdit(article)}>Edit</button>
						<button class="delete-btn" on:click={() => deleteNews(article.id)} disabled={deletingId === article.id}>
							{deletingId === article.id ? 'Deleting...' : 'Delete'}
						</button>
					</div>
				</li>
			{/each}
		</ul>
		{#if deleteError}<span class="error">{deleteError}</span>{/if}
	{/if}
</section>

<style>
.cms-news-list {
	margin-top: 2rem;
	background: var(--primary-white);
	border-radius: 1rem;
	box-shadow: 0 2px 12px #0001;
	padding: 2rem 1.5rem;
	max-width: 700px;
}
.cms-news-list h2 {
	color: var(--primary-orange);
	margin-bottom: 1rem;
}
.add-btn {
	background: var(--primary-orange);
	color: #fff;
	border: none;
	padding: 0.7rem 2rem;
	border-radius: 2rem;
	font-weight: bold;
	font-size: 1.1rem;
	cursor: pointer;
	transition: background 0.2s;
	margin-bottom: 1.5rem;
}
.add-btn:hover {
	background: #ff7f2a;
}
.news-list {
	list-style: none;
	padding: 0;
	margin: 0;
	display: flex;
	flex-direction: column;
	gap: 1.2rem;
}
.news-list-item {
	display: flex;
	align-items: center;
	justify-content: space-between;
	background: #f8f8f8;
	border-radius: 0.7rem;
	box-shadow: 0 1px 4px #0001;
	padding: 1rem 1.5rem;
	gap: 1.5rem;
}
.news-info {
	display: flex;
	align-items: center;
	gap: 0.7rem;
}
.published {
	background: #f3f3f3;
	padding: 0.1rem 0.6rem;
	border-radius: 0.4rem;
	font-size: 0.97em;
	color: #666;
}
.news-actions {
	display: flex;
	gap: 0.7rem;
}
.edit-btn {
	margin-left: 0;
	background: var(--primary-orange);
	color: #fff;
	border: none;
	padding: 0.4rem 1.2rem;
	border-radius: 1.2rem;
	font-weight: bold;
	font-size: 1rem;
	cursor: pointer;
	transition: background 0.2s;
}
.edit-btn:hover {
	background: #ff7f2a;
}
.delete-btn {
	margin-left: 0.7rem;
	background: #e74c3c;
	color: #fff;
	border: none;
	padding: 0.4rem 1.2rem;
	border-radius: 1.2rem;
	font-weight: bold;
	font-size: 1rem;
	cursor: pointer;
	transition: background 0.2s;
}
.delete-btn:hover {
	background: #c0392b;
}
.error {
	color: red;
	margin-left: 1rem;
}
.published-date {
  background: #eaf6ff;
  color: #1976d2;
  padding: 0.15rem 0.7rem;
  border-radius: 0.5rem;
  font-size: 0.98em;
  margin-left: 0.7rem;
  font-weight: 500;
  letter-spacing: 0.01em;
  display: inline-block;
}
</style> 