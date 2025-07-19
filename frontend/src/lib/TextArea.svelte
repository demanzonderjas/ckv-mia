<script>
let { value = $bindable(), label = '', placeholder = '', required = false, name = '', rows = 3 } = $props();
</script>

<style>
textarea {
  padding: 0.6rem;
  border-radius: 0.5rem;
  border: 1.5px solid #e0e0e0;
  font-size: 1rem;
  background: #fcfcff;
  box-shadow: 0 1px 4px #0001;
  transition: border 0.2s, box-shadow 0.2s;
}
textarea:focus {
  border: 1.5px solid var(--primary-orange, #ff6600);
  outline: none;
  box-shadow: 0 2px 8px #ff660022;
}
</style>

<textarea
  bind:value
  placeholder={placeholder}
  required={required}
  name={name}
  rows={rows}
/> 