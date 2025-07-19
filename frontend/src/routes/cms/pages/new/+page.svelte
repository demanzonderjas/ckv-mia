<script lang="ts">
import { goto } from '$app/navigation';

let newTitle = $state('');
let newSlug = $state('');
let newDescription = $state('');
let addError = $state('');
let addSuccess = $state('');

async function addPage() {
	addError = '';
	addSuccess = '';
	if (!newTitle || !newSlug) {
		addError = 'Title and slug are required.';
		return;
	}
	try {
		const res = await fetch('http://localhost:8000/api/pages', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json',
			},
			body: JSON.stringify({ title: newTitle, slug: newSlug, description: newDescription })
		});
		if (!res.ok) {
			let msg = `Failed to add page (status ${res.status})`;
			try {
				const data = await res.json();
				if (data?.message && data.message.toLowerCase().includes('slug')) {
					msg = 'Slug already exists. Please choose a unique slug.';
				} else if (data?.message) {
					msg = data.message;
				}
			} catch {
				// Not JSON, keep default msg
			}
			throw new Error(msg);
		}
		addSuccess = 'Page added!';
		setTimeout(() => goto('/cms/pages'), 800);
	} catch (e: any) {
		if (e instanceof TypeError) {
			addError = 'Network error: Could not reach backend.';
		} else {
			addError = (e as any).message;
		}
	}
}

function goBack() {
	goto('/cms/pages');
}
</script>

<h1>Add New Page</h1>
<form on:submit|preventDefault={addPage} class="cms-add-page">
	<label>Title<br /><input bind:value={newTitle} required /></label><br />
	<label>Slug<br /><input bind:value={newSlug} required /></label><br />
	<label>Description<br /><input bind:value={newDescription} /></label><br />
	<button type="submit">Add Page</button>
	{#if addError}<span class="error">{addError}</span>{/if}
	{#if addSuccess}<span class="success">{addSuccess}</span>{/if}
</form>
<button on:click={goBack} class="back-btn">Back</button>

<style>
.cms-add-page {
	margin-bottom: 2.5rem;
	background: var(--primary-white);
	border-radius: 1rem;
	box-shadow: 0 2px 12px #0001;
	padding: 2rem 1.5rem;
	max-width: 500px;
}
.cms-add-page label {
	font-weight: 500;
}
input {
	padding: 0.5rem;
	margin-bottom: 1rem;
	border-radius: 0.5rem;
	border: 1px solid #ccc;
	width: 100%;
	font-size: 1rem;
}
button[type="submit"] {
	background: var(--primary-orange);
	color: #fff;
	border: none;
	padding: 0.7rem 2rem;
	border-radius: 2rem;
	font-weight: bold;
	font-size: 1.1rem;
	cursor: pointer;
	transition: background 0.2s;
	margin-top: 0.5rem;
}
button[type="submit"]:hover {
	background: #ff7f2a;
}
.error {
	color: red;
	margin-left: 1rem;
}
.success {
	color: green;
	margin-left: 1rem;
}
.back-btn {
	margin-top: 1.5rem;
	background: #eee;
	color: #333;
	border: none;
	padding: 0.5rem 1.5rem;
	border-radius: 1.2rem;
	font-size: 1rem;
	cursor: pointer;
}
.back-btn:hover {
	background: #ddd;
}
</style> 