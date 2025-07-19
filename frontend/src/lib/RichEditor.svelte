<script lang="ts">
import { onMount, createEventDispatcher } from 'svelte';
import { browser } from '$app/environment';

let { value = $bindable(), placeholder = '' } = $props();
const dispatch = createEventDispatcher();

let quill: any = null;
let container: HTMLDivElement | null = null;
let lastSetByQuill = false;

onMount(async () => {
  if (browser && container) {
    await import('quill/dist/quill.snow.css');
    const Quill = (await import('quill')).default;
    quill = new Quill(container, {
      theme: 'snow',
      placeholder,
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          ['link']
        ]
      },
      formats: ['bold', 'italic', 'underline', 'list', 'link']
    });
    console.log(value);
    quill.root.innerHTML = value || '<p><br></p>';
    quill.on('text-change', () => {
      lastSetByQuill = true;
      value = quill.root.innerHTML;
      dispatch('input', value);
    });
  }
});

$effect(() => {
  if (quill && typeof value === 'string' && quill.root.innerHTML !== value && !lastSetByQuill) {
    quill.root.innerHTML = value || '<p><br></p>';
  }
  lastSetByQuill = false;
});
</script>

<div bind:this={container}></div>

<style>
.ql-container {
  min-height: 300px;
  background: #fff;
  border-radius: 0.7rem;
  box-shadow: 0 2px 8px #0001;
  padding: 1rem;
  font-size: 1.08rem;
  border: 1px solid #e0e0e0;
}
.ql-toolbar {
  border-radius: 0.7rem 0.7rem 0 0;
  background: #f7f7fa;
  border: 1px solid #e0e0e0;
  box-shadow: 0 1px 4px #0001;
  margin-bottom: 0.2rem;
}
:global(.ql-editor) {
  min-height: 180px !important;
  padding: 0.7rem;
  background: #fcfcff;
  border-radius: 0.5rem;
}
</style> 