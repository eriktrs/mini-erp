<template>
  <div>
    <Navbar />
    <main class="p-4 max-w-7xl mx-auto">
      <router-view />
    </main>
  </div>
</template>

<script lang="ts" setup>
import Navbar from './components/Navbar.vue'
</script>
