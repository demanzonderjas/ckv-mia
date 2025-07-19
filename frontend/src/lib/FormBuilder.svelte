<script lang="ts">
import { createEventDispatcher, onMount } from 'svelte';
import { browser } from '$app/environment';
import RichEditor from '$lib/RichEditor.svelte';
import ImageUpload from './ImageUpload.svelte';
import TextInput from './TextInput.svelte';
import TextArea from './TextArea.svelte';
import NumberInput from './NumberInput.svelte';
import DateInput from './DateInput.svelte';
import SelectInput from './SelectInput.svelte';
import TeamSelect from './TeamSelect.svelte';
import ContentBlocksEditor from './ContentBlocksEditor.svelte';
import BooleanSwitch from './BooleanSwitch.svelte';
import ParentLinkSelect from './ParentLinkSelect.svelte';

const { fields = [], onSubmit = undefined, submitLabel = 'Opslaan' } = $props();

const dispatch = createEventDispatcher();
let values = $state<Record<string, any>>({});
let errors: Record<string, string> = {};

// Init values
fields.forEach(f => {
  values[f.name] = f.value ?? '';
});

function handleInput(e: Event, name: string) {
  values[name] = (e.target as HTMLInputElement).value;
}

function validate() {
  errors = {};
  for (const field of fields) {
    if (field.required && !values[field.name]) {
      errors[field.name] = 'Verplicht veld';
    }
  }
  return Object.keys(errors).length === 0;
}

function submitForm(e: Event) {
  e.preventDefault();
  if (validate()) {
    if (onSubmit) onSubmit(values);
    dispatch('submit', values);
  }
}
</script>

<form on:submit|preventDefault={submitForm} class="form-builder">
  {#each fields as field (field.name + ':' + field.value)}
    <div class="form-row">
      {#if field.type !== 'image-upload' && field.type !== 'rich' && field.type !== 'content-blocks'}
        <label for={field.name}>{field.label}{field.required ? '*' : ''}</label>
      {/if}
      {#if field.type === 'text'}
        <TextInput
          bind:value={values[field.name]}
          label={field.label}
          placeholder={field.placeholder}
          required={field.required}
          name={field.name}
        />
      {:else if field.type === 'textarea'}
        <TextArea
          bind:value={values[field.name]}
          label={field.label}
          placeholder={field.placeholder}
          required={field.required}
          name={field.name}
          rows={field.rows}
        />
      {:else if field.type === 'number'}
        <NumberInput
          bind:value={values[field.name]}
          label={field.label}
          placeholder={field.placeholder}
          required={field.required}
          name={field.name}
        />
      {:else if field.type === 'date'}
        <DateInput
          bind:value={values[field.name]}
          label={field.label}
          placeholder={field.placeholder}
          required={field.required}
          name={field.name}
        />
      {:else if field.type === 'select'}
        <SelectInput
          bind:value={values[field.name]}
          label={field.label}
          required={field.required}
          name={field.name}
          options={field.options}
        />
      {:else if field.type === 'team-select'}
        <TeamSelect
          bind:value={values[field.name]}
          label={field.label}
          required={field.required}
          name={field.name}
        />
      {:else if field.type === 'image-upload'}
        <ImageUpload
          bind:value={values[field.name]}
          label={field.label}
          entity_type={field.entity_type}
          entity_id={field.entity_id}
        />
      {:else if field.type === 'rich'}
        <RichEditor
          bind:value={values[field.name]}
          placeholder={field.placeholder}
        />
      {:else if field.type === 'content-blocks'}
        <ContentBlocksEditor pageId={field.pageId} blocks={field.blocks} onBlocksChange={field.onBlocksChange} />
      {:else if field.type === 'checkbox' || field.type === 'boolean'}
        <BooleanSwitch
          bind:value={values[field.name]}
          label={field.label}
          name={field.name}
        />
      {:else if field.type === 'parent-link-select'}
        <ParentLinkSelect
          bind:value={values[field.name]}
          label={field.label}
          required={field.required}
          name={field.name}
          category={values['category'] || 'side'}
          excludeId={field.excludeId}
        />
      {/if}
      {#if errors[field.name]}
        <span class="error">{errors[field.name]}</span>
      {/if}
    </div>
  {/each}
  <button type="submit">{submitLabel}</button>
</form>

<style>
.form-builder {
  display: flex;
  flex-direction: column;
  gap: 1.2rem;
  max-width: 500px;
}
.form-row {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
label {
  font-weight: 600;
  font-size: 1.08rem;
  color: var(--primary-orange, #ff6600);
  margin-bottom: 0.25rem;
  letter-spacing: 0.01em;
  text-shadow: 0 1px 2px #0001;
}
input, textarea, select {
  padding: 0.6rem;
  border-radius: 0.5rem;
  border: 1.5px solid #e0e0e0;
  font-size: 1rem;
  background: #fcfcff;
  box-shadow: 0 1px 4px #0001;
  transition: border 0.2s, box-shadow 0.2s;
}
input:focus, textarea:focus, select:focus {
  border: 1.5px solid var(--primary-orange, #ff6600);
  outline: none;
  box-shadow: 0 2px 8px #ff660022;
}
button[type="submit"] {
  margin-top: 1rem;
  background: var(--primary-orange);
  color: #fff;
  border: none;
  padding: 0.5rem 1.2rem;
  border-radius: 0.7rem;
  font-weight: 500;
  font-size: 1.05rem;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 1px 2px #0001;
}
button[type="submit"]:hover {
  background: #ff7f2a;
}
.error {
  color: red;
  font-size: 0.95rem;
}
</style> 