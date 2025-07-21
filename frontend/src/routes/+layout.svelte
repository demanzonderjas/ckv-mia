<script lang="ts">
	import '../app.css';
	import PageContent from '$lib/PageContent.svelte';
	import { page } from '$app/state';
	import { onMount } from 'svelte';
	let menuOpen = $state(false);
	let dynamicTitle = $state('');
	let headerLinks: any[] = $state([]);
	let footerLinks: any[] = $state([]);
	let activeDropdown = $state(null);

	function buildMenuTree(links: any[]) {
		const map = new Map();
		links.forEach(l => map.set(l.id, { ...l, children: [] }));
		const tree: any[] = [];
		for (const link of map.values()) {
			if (link.parent_id && map.has(link.parent_id)) {
				map.get(link.parent_id).children.push(link);
			} else {
				tree.push(link);
			}
		}
		return tree;
	}

	function handleDropdownToggle(id: any) {
		activeDropdown = activeDropdown === id ? null : id;
	}

	function handleMenuLinkClick() {
		menuOpen = false;
		activeDropdown = null;
	}

	$effect(() => {
		// Only run for public site pages (not /, not /cms/*)
		const path = page.url.pathname;
		menuOpen = false; // Close menu on navigation
		if (path === '/' || path.startsWith('/cms')) {
			dynamicTitle = '';
			return;
		}
		let slug = path.split('/')[1];
		if (!slug) {
			dynamicTitle = '';
			return;
		}
		fetch(`http://localhost:8000/api/pages/slug/${slug}`)
			.then(res => res.ok ? res.json() : null)
			.then(data => {
				dynamicTitle = data?.title || '';
			})
			.catch(() => {
				dynamicTitle = '';
			});
	});

	onMount(async () => {
		try {
			const res = await fetch('http://localhost:8000/api/menu-links?category=header');
			if (res.ok) {
				headerLinks = (await res.json()).filter((l: any) => l.active);
			}
		} catch {}
		try {
			const res = await fetch('http://localhost:8000/api/menu-links?category=footer');
			if (res.ok) {
				footerLinks = (await res.json()).filter((l: any) => l.active);
			}
		} catch {}
	});
</script>

<svelte:head>
	<title>{dynamicTitle ? `${dynamicTitle} | CKV MIA` : 'CKV MIA'}</title>
</svelte:head>

<nav class="navbar">
	<a href="/" class="logo-link">
		<img
			src="https://www.ckvmia.nl/wp-content/uploads/ckvmia/logo-512.png"
			alt="CKV MIA Logo"
			class="logo-img"
		/>
		<div class="logo">CKV MIA</div>
	</a>
	<button class="menu-toggle" on:click={() => (menuOpen = !menuOpen)} aria-label="Toggle menu">
		<span class="bar"></span>
		<span class="bar"></span>
		<span class="bar"></span>
	</button>
	<ul class:open={menuOpen}>
		{#each buildMenuTree(headerLinks) as link}
			<li class:has-dropdown={link.children.length} class:dropdown-open={activeDropdown === link.id}>
				<a href={link.url} on:click={link.children.length && window.innerWidth <= 900 ? (e) => { e.preventDefault(); handleDropdownToggle(link.id); } : handleMenuLinkClick} tabindex="0">
					{link.title}
				</a>
				{#if link.children.length && (window.innerWidth > 900 || activeDropdown === link.id)}
					<ul class="dropdown">
						{#each link.children as child}
							<li><a href={child.url} on:click={handleMenuLinkClick}>{child.title}</a></li>
						{/each}
					</ul>
				{/if}
			</li>
		{/each}
	</ul>
</nav>

<PageContent>
	<slot />
</PageContent>

<footer class="footer">
	<div class="footer-main">
		<div class="footer-section">
			<strong>ckv MIA</strong><br />
			Schothorsterlaan 5<br />
			3822NA Amersfoort
		</div>
		<div class="footer-section">
			<a href="https://www.facebook.com/ckvmia" target="_blank" rel="noopener">Facebook</a><br />
			<a href="/lid-worden">Lid worden</a><br />
			<a href="/upload-verslag">Upload wedstrijdverslag</a>
		</div>
		<div class="footer-section">
			{#each footerLinks as link}
				<a href={(link as any).url}>{(link as any).title}</a><br />
			{/each}
		</div>
	</div>
	<div class="footer-bottom">
		&copy; {new Date().getFullYear()} CKV MIA. Alle rechten voorbehouden.
	</div>
</footer>

<style>
	:global(body) {
		min-height: 100vh;
		margin: 0;
		display: flex;
		flex-direction: column;
	}
	
	.navbar {
		display: flex;
		align-items: center;
		justify-content: space-between;
		background: var(--primary);
		color: var(--text-highlight);
		padding: 0.5rem 2rem;
		position: sticky;
		top: 0;
		z-index: 10;
	}
	.logo-img {
		height: 48px;
		width: 48px;
		object-fit: contain;
		margin-right: 1rem;
		background: var(--primary-white);
		border-radius: 50%;
		box-shadow: 0 2px 8px #0002;
		border: 2px solid var(--primary-white);
	}
	.logo {
		font-size: 1.5rem;
		font-weight: bold;
		letter-spacing: 1px;
		margin-right: auto;
	}
	.menu-toggle {
		display: none;
		flex-direction: column;
		gap: 4px;
		background: none;
		border: none;
		cursor: pointer;
	}
	.menu-toggle .bar {
		width: 24px;
		height: 3px;
		background: var(--primary-white);
		border-radius: 2px;
	}
	.navbar ul {
		display: flex;
		gap: 1.5rem;
		list-style: none;
		margin: 0;
		padding: 0;
		position: relative;
	}
	.navbar ul li {
		position: relative;
	}
	.navbar ul li.has-dropdown > a:after {
		content: ' ▼';
		font-size: 0.5em;
		color: #fff;
		display: inline-block;
		vertical-align: middle;
		margin-left: 0.5em;
	}
	.navbar ul li .dropdown {
		display: none;
		position: absolute;
		left: 0;
		top: 100%;
		background: var(--primary-orange);
		min-width: 180px;
		border-radius: 0 0 0.7rem 0.7rem;
		box-shadow: 0 2px 12px #0002;
		z-index: 20;
		padding: 0.5rem 0;
	}
	.navbar ul li.has-dropdown:hover .dropdown,
	.navbar ul li.has-dropdown:focus-within .dropdown {
		display: block;
	}
	.navbar ul li .dropdown li {
		padding: 0;
		margin: 0;
	}
	.navbar ul li .dropdown a {
		color: #fff;
		display: block;
		padding: 0.7rem 1.2rem;
		text-decoration: none;
		font-weight: 500;
		border-radius: 0.3rem;
		transition: background 0.18s;
	}
	.navbar ul li .dropdown a:hover {
		background: #ff7f2a;
	}
	ul li a {
		color: var(--primary-white);
		font-weight: 500;
		text-decoration: none;
		transition: color 0.2s;
	}
	ul li a:hover {
		color: var(--primary-black);
	}
	@media (max-width: 900px) {
		.side-menu {
			margin: 1rem auto;
		}
		ul {
			display: none;
			flex-direction: column;
			position: absolute;
			top: 60px;
			left: 0;
			width: 100vw;
			background: var(--primary-orange);
			padding: 1rem 0;
			gap: 1rem;
			z-index: 9;
		}
		ul.open {
			display: flex;
		}
		.menu-toggle {
			display: flex;
		}
		.logo-img {
			height: 40px;
			width: 40px;
			margin-right: 0.5rem;
		}
	}
	
	.footer {
		flex-shrink: 0;
		background: var(--primary-orange);
		color: var(--text-highlight);
		padding: 2rem 1rem 1rem 1rem;
		/* margin-top: 3rem; */
	}
	.footer-main {
		display: flex;
		flex-wrap: wrap;
		gap: 2rem;
		justify-content: space-between;
		max-width: 1400px;
		margin: 0 auto;
	}
	.footer-section {
		min-width: 180px;
		font-size: 1rem;
		line-height: 1.7;
	}
	.footer-section a {
		color: var(--primary-orange);
		text-decoration: underline;
		font-weight: 500;
		transition: color 0.2s;
	}
	.footer-section a:hover {
		color: var(--text-highlight);
	}
	.footer-bottom {
		margin-top: 2rem;
		text-align: center;
		font-size: 0.95rem;
		color: var(--text-highlight);
	}
	@media (max-width: 900px) {
		.footer-main {
			flex-direction: column;
			gap: 1.5rem;
			align-items: flex-start;
		}
	}
.logo-link {
	display: flex;
	align-items: center;
	text-decoration: none;
	color: inherit;
	margin-right: auto;
}
@media (max-width: 900px) {
	.navbar ul {
		display: none;
		flex-direction: column;
		align-items: flex-start;
		position: fixed;
		top: 60px;
		left: 0;
		width: 100vw;
		background: var(--primary-orange);
		padding: 1.5rem 0 2.5rem 0;
		gap: 0.5rem;
		z-index: 99;
		box-shadow: 0 8px 32px #0003;
		border-radius: 0 0 1.5rem 1.5rem;
	}
	.navbar ul.open {
		display: flex;
	}
	.navbar ul li {
		width: 100%;
		position: relative;
		font-size: 1.15rem;
		font-weight: 600;
		padding: 0.5rem 1.5rem;
		box-sizing: border-box;
		flex: none;
	}
	.navbar ul li a {
		color: #fff;
		width: 100%;
		display: block;
		padding: 0.7rem 0;
		font-size: 1.08rem;
		font-weight: 600;
		border-radius: 0.5rem;
		text-align: left;
	}
	.navbar ul li .dropdown {
		display: none;
		position: static;
		width: 100%;
		background: #fff7f0;
		color: var(--primary-orange);
		border-radius: 0.5rem;
		box-shadow: none;
		padding: 0.3rem 0;
		margin: 0;
		max-height: none;
		overflow-y: visible;
	}
	.navbar ul li.dropdown-open .dropdown {
		display: block;
	}
	.navbar ul li .dropdown a {
		color: var(--primary-orange);
		background: transparent;
		padding: 0.7rem 1rem;
		border-radius: 0.3rem;
		font-size: 1.05rem;
		font-weight: 500;
		text-align: left;
	}
	.navbar ul li .dropdown a:hover {
		background: #ffe3d0;
	}
	.navbar ul li .dropdown li {
		padding: 0;
		margin: 0;
	}
	.menu-toggle {
		display: flex;
		margin-left: 1.2rem;
		margin-top: 0.2rem;
		z-index: 100;
	}
	.logo-link {
		margin-left: 1.2rem;
		margin-bottom: 0.5rem;
	}
}
</style>
