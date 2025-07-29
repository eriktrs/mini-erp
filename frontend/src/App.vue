<template>
  <Navbar />
  <main class="container mx-auto p-4">
    <router-view />
  </main>
</template>

<script lang="ts">
  import { defineComponent } from "vue";
  import Navbar from "./components/ui/Navbar.vue";

  export default defineComponent({
    name: "App",
    components: { Navbar },
  });
</script>
