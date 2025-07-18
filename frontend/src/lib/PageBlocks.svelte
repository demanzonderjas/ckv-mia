<script lang="ts">
	import { onMount } from 'svelte';
	import DOMPurify from 'dompurify';
	let { slug } = $props();

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

	let pageData: PageData | null = $state(null);
	let loading = $state(true);
	let error = $state('');

	function parseContent(content: any) {
		if (typeof content === 'string') return JSON.parse(content);
		return content;
	}

	onMount(async () => {
		try {
			const res = await fetch(`http://localhost:8000/api/pages/slug/${slug}`);
			if (res.ok) pageData = await res.json();
		} catch (e: any) {
			error = e.message;
		} finally {
			loading = false;
		}
	});
</script>

{#if loading}
	<p>Loading content...</p>
{:else if error}
	<p class="error">{error}</p>
{:else if pageData}
	{#if pageData.description}
		<p class="desc">{pageData.description}</p>
	{/if}
	{#each pageData.content_blocks as block}
		{@const content = parseContent(block.content)}
		{#if block.type === 'text'}
			<p class="block-text">{content.text}</p>
		{:else if block.type === 'image'}
			<img class="block-image" src={content.url} alt={content.alt} />
		{:else if block.type === 'rich'}
			<div class="block-rich">{@html DOMPurify.sanitize(content.html)}</div>
		{/if}
	{/each}
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
	.error {
		color: red;
		font-weight: bold;
	}
	.block-rich {
		font-size: 1.15rem;
		margin-bottom: 1.5rem;
		color: var(--primary-black);
		background: #fcfcff;
		border-radius: 1rem;
		box-shadow: 0 2px 12px #0001;
		padding: 1.2rem;
	}
</style>
