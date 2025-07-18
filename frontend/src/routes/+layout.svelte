<script lang="ts">
	import '../app.css';
	import PageContent from '$lib/PageContent.svelte';
	let menuOpen = $state(false);
</script>

<nav class="navbar">
	<img
		src="https://www.ckvmia.nl/wp-content/uploads/ckvmia/logo-512.png"
		alt="CKV MIA Logo"
		class="logo-img"
	/>
	<div class="logo">CKV MIA</div>
	<button class="menu-toggle" on:click={() => (menuOpen = !menuOpen)} aria-label="Toggle menu">
		<span class="bar"></span>
		<span class="bar"></span>
		<span class="bar"></span>
	</button>
	<ul class:open={menuOpen}>
		<li><a href="/">Home</a></li>
		<li><a href="/teams">Teams</a></li>
		<li><a href="/events">Events</a></li>
		<li><a href="/members">Members</a></li>
		<li><a href="/about">About</a></li>
		<li><a href="/contact">Contact</a></li>
		<li><a href="/news">News</a></li>
		<li><a href="/gallery">Gallery</a></li>
		<li><a href="/sponsors">Sponsors</a></li>
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
			<span>Livestream &amp; samenvattingen op onze website en YouTube</span>
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
	.page-root {
		display: flex;
		flex-direction: column;
		min-height: 100vh;
	}
	.layout-wrapper {
		display: flex;
		align-items: flex-start;
		gap: 2rem;
		max-width: 1400px;
		margin: 0 auto;
		padding: 0 1rem;
	}
	.main-content {
		flex: 1 1 0%;
		min-width: 0;
	}
	.navbar {
		display: flex;
		align-items: center;
		justify-content: space-between;
		background: var(--primary-orange);
		color: var(--primary-white);
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
	ul {
		display: flex;
		gap: 1.5rem;
		list-style: none;
		margin: 0;
		padding: 0;
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
		.layout-wrapper {
			flex-direction: column;
			gap: 0;
			padding: 0;
		}
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
	main {
		flex: 1 1 0%;
		min-width: 0;
		width: 100%;
		display: flex;
		flex-direction: column;
		align-items: flex-start;
		justify-content: flex-start;
		padding: 2rem 1.5rem;
	}
	.content-row {
		display: flex;
		align-items: flex-start;
		justify-content: space-between;
		gap: 2rem;
		max-width: 1400px;
		margin: 0 auto;
		padding: 0 1rem;
	}
	@media (max-width: 900px) {
		.content-row {
			flex-direction: column;
			gap: 0;
			padding: 0;
		}
		.side-menu {
			margin: 1rem auto;
		}
	}
	.footer {
		flex-shrink: 0;
		background: var(--primary-black);
		color: var(--primary-white);
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
		color: var(--primary-white);
	}
	.footer-bottom {
		margin-top: 2rem;
		text-align: center;
		font-size: 0.95rem;
		color: #bbb;
	}
	@media (max-width: 900px) {
		.footer-main {
			flex-direction: column;
			gap: 1.5rem;
			align-items: flex-start;
		}
	}
</style>
