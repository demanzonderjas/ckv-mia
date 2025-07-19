<script>
let { href, title = 'Bewerk deze pagina' } = $props();
</script>
<a class="edit-fab" {href} {title}>
	<svg width="24" height="24" fill="none" viewBox="0 0 24 24"><path d="M4 21h17" stroke="#fff" stroke-width="2" stroke-linecap="round"/><path d="M17.7 6.3a1 1 0 0 1 0 1.4l-8.5 8.5-3.2.7.7-3.2 8.5-8.5a1 1 0 0 1 1.4 0l1.1 1.1z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
</a>

<style>
.edit-fab {
	position: fixed;
	top: 1.5rem;
	right: 1.5rem;
	z-index: 100;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 3.2rem;
	height: 3.2rem;
	background: var(--primary-orange, #ff6600);
	color: #fff;
	border-radius: 50%;
	box-shadow: 0 2px 12px #0003;
	transition: background 0.2s, box-shadow 0.2s;
	text-decoration: none;
	border: none;
	outline: none;
	cursor: pointer;
}
.edit-fab:hover {
	background: #ff7f2a;
	box-shadow: 0 4px 16px #ff660033;
}
.edit-fab svg {
	display: block;
}
</style> 