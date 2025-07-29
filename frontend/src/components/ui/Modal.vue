<template>
  <div class="modal-backdrop" @click.self="close">
    <div class="modal-content">
      <slot></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
  const emits = defineEmits(['close']);
  const close = () => emits('close');
</script>

<style scoped>
  .modal-backdrop {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex; justify-content: center; align-items: center;
  }
  
  .modal-content {
    background: #fff; padding: 20px; border-radius: 8px; min-width: 300px;
  }
</style>
