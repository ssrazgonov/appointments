<template>
  <div class="landing-app">
    <Header />
    <router-view />
    <Footer />
  </div>
</template>

<script setup>
import Header from '@/landing/components/Header.vue';
import Footer from '@/landing/components/Footer.vue';
</script>

<style>
.landing-app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>
