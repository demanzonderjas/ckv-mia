<script>
import { createEventDispatcher } from 'svelte';
let { value = $bindable(), label = 'Afbeelding', entity_type = '', entity_id = null } = $props();

const dispatch = createEventDispatcher();
let imageUrl = value;
let imageUploadId = null;
let imageUploading = false;
let imageUploadError = '';
let fileInput = null;

function triggerFileInput() {
  if (fileInput) fileInput.click();
}

async function handleImageUpload(e) {
  imageUploadError = '';
  imageUploading = true;
  const file = e && e.target && e.target.files ? e.target.files[0] : null;
  if (!file) return;
  const formData = new FormData();
  formData.append('image', file);
  if (entity_type) formData.append('entity_type', entity_type);
  if (entity_id) formData.append('entity_id', entity_id.toString());
  try {
    const res = await fetch('http://localhost:8000/api/upload-image', {
      method: 'POST',
      body: formData,
    });
    if (!res.ok) throw new Error('Failed to upload image');
    const data = await res.json();
    imageUrl = 'http://localhost:8000' + data.path;
    imageUploadId = data.id;
    value = imageUrl;
    dispatch('input', imageUrl);
    if (fileInput?.value !== undefined) fileInput.value = '';
  } catch (err) {
    imageUploadError = (err && err.message) ? err.message : 'Upload error';
  } finally {
    imageUploading = false;
  }
}

async function deleteImage() {
  imageUploadError = '';
  if (!imageUrl) return;
  if (!confirm('Weet je zeker dat je deze afbeelding wilt verwijderen?')) return;
  try {
    const res = await fetch('http://localhost:8000/api/delete-image', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ path: imageUrl.replace('http://localhost:8000', '') })
    });
    if (!res.ok) throw new Error('Failed to delete image');
    imageUrl = '';
    imageUploadId = null;
    value = '';
    dispatch('input', '');
    if (fileInput?.value !== undefined) fileInput.value = '';
  } catch (err) {
    imageUploadError = (err && err.message) ? err.message : 'Delete error';
  }
}
</script>

<div class="image-upload-row">
  {#if !value}
    <div class="upload-placeholder" on:click={triggerFileInput} title="Klik om een afbeelding te uploaden">
      <svg class="upload-icon" xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 48 48"><circle cx="24" cy="24" r="22" stroke="#ff6600" stroke-width="2" fill="#fff8f3"/><path d="M24 34V14M24 14l-7 7m7-7l7 7" stroke="#ff6600" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><rect x="14" y="34" width="20" height="2" rx="1" fill="#ff6600"/></svg>
      <span class="upload-text">Klik om een afbeelding te uploaden</span>
    </div>
    <input type="file" accept="image/*" bind:this={fileInput} on:change={handleImageUpload} class="hidden-input" />
  {/if}
  {#if imageUploading}<span>Uploading...</span>{/if}
  {#if value}
    <div class="image-preview-row">
      <img src={value} alt="Preview" class="image-preview" />
      <button type="button" class="delete-image-btn" on:click={deleteImage}>Verwijder afbeelding</button>
    </div>
  {/if}
  {#if imageUploadError}<span class="error">{imageUploadError}</span>{/if}
</div>

<style>
.image-upload-row {
  margin-bottom: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
}
.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ff660055;
  border-radius: 1rem;
  background: #fff8f3;
  color: var(--primary-orange, #ff6600);
  font-weight: 600;
  font-size: 1.08rem;
  text-align: center;
  cursor: pointer;
  padding: 2.2rem 0.7rem 1.2rem 0.7rem;
  margin-bottom: 0.5rem;
  transition: border 0.2s, background 0.2s;
}
.upload-placeholder:hover {
  border-color: #ff6600;
  background: #fff3e6;
}
.upload-icon {
  margin-bottom: 0.5rem;
  display: block;
}
.upload-text {
  color: #ff6600;
  font-size: 1.08rem;
  font-weight: 500;
}
.hidden-input {
  display: none;
}
.image-preview-row {
  display: flex;
  align-items: center;
  gap: 1.2rem;
  margin-bottom: 0.7rem;
  justify-content: flex-start;
}
.image-preview {
  max-width: 320px;
  max-height: 180px;
  border-radius: 0.7rem;
  box-shadow: 0 2px 8px #ff660022;
  border: 2px solid #ff660033;
  background: #fff;
}
.delete-image-btn {
  background: #e74c3c;
  color: #fff;
  border: none;
  padding: 0.4rem 1.2rem;
  border-radius: 1.2rem;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.2s;
  box-shadow: 0 1px 4px #e74c3c22;
}
.delete-image-btn:hover {
  background: #c0392b;
}
.error {
  color: red;
  margin-left: 1rem;
}
</style> 