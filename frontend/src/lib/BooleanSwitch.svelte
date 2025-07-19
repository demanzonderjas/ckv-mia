<script lang="ts">
let { value = $bindable(), label = '', name = '' } = $props();
</script>

<div class="switch-row">
  <label class="switch">
    <input type="checkbox" bind:checked={value} name={name} />
    <span class="slider"></span>
  </label>
  {#if label}
    <span class="switch-label">{label}</span>
  {/if}
</div>

<style>
.switch-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin: 0.3rem 0 0.7rem 0;
}
.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 24px;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #eee;
  transition: 0.2s;
  border-radius: 24px;
}
.switch input:checked + .slider {
  background-color: var(--primary-orange, #ff6600);
}
.slider:before {
  position: absolute;
  content: '';
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: #fff;
  transition: 0.2s;
  border-radius: 50%;
  box-shadow: 0 1px 4px #0001;
}
.switch input:checked + .slider:before {
  transform: translateX(20px);
}
.switch-label {
  font-size: 1.08rem;
  color: var(--primary-orange, #ff6600);
  font-weight: 500;
}
</style> 