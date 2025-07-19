<script lang="ts">
import { createEventDispatcher, onMount } from 'svelte';
import { browser } from '$app/environment';
import RichEditor from '$lib/RichEditor.svelte';

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
      <label for={field.name}>{field.label}{field.required ? '*' : ''}</label>
      {#if field.type === 'text' || field.type === 'email' || field.type === 'number' || field.type === 'password' || field.type === 'date'}
        <input
          id={field.name}
          type={field.type}
          bind:value={values[field.name]}
          placeholder={field.placeholder}
          required={field.required}
          on:input={(e) => handleInput(e, field.name)}
        />
      {:else if field.type === 'textarea'}
        <textarea
          id={field.name}
          bind:value={values[field.name]}
          placeholder={field.placeholder}
          rows={field.rows || 3}
          required={field.required}
          on:input={(e) => handleInput(e, field.name)}
        />
      {:else if field.type === 'select'}
        <select
          id={field.name}
          bind:value={values[field.name]}
          required={field.required}
          on:change={(e) => handleInput(e, field.name)}
        >
          <option value="">-- Kies --</option>
          {#if field.options}
            {#each field.options as opt: {label: string, value: string}}
              <option value={opt.value}>{opt.label}</option>
            {/each}
          {/if}
        </select>
      {:else if field.type === 'rich'}
        <RichEditor
          bind:value={values[field.name]}
          placeholder={field.placeholder}
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
  font-weight: 500;
}
input, textarea, select {
  padding: 0.5rem;
  border-radius: 0.4rem;
  border: 1px solid #ccc;
  font-size: 1rem;
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