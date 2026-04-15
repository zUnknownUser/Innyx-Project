<template>
  <div class="layout">

    <!-- SIDEBAR -->
    <aside v-if="showNavbar" class="sidebar">
      <div class="sidebar-header">
        <h4>INNYX</h4>
        <span>Dashboard</span>
      </div>

     <nav class="sidebar-nav">
  <router-link to="/" class="nav-item">
     Produtos
  </router-link>

  <router-link to="/users" class="nav-item">
    Usuários
  </router-link>
</nav>
      

      <div class="sidebar-footer">
        <button class="btn btn-logout" @click="logout">
          Sair
        </button>
      </div>
    </aside>

   
    <div class="main">
      <header v-if="showNavbar" class="topbar">
        <span class="topbar-title">Gerenciamento</span>
      </header>

      <main class="content">
        <RouterView />
      </main>
    </div>

  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const showNavbar = computed(() => {
  return !['/login', '/register'].includes(route.path)
})

const logout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<style scoped>
.layout {
  display: flex;
  min-height: 100vh;
  background: #f6f8fc;
}


.sidebar {
  width: 240px;
  background: #0f172a;
  color: white;
  display: flex;
  flex-direction: column;
  padding: 1.5rem 1rem;
}

.sidebar-header h4 {
  margin: 0;
  font-weight: 800;
}

.sidebar-header span {
  font-size: 0.8rem;
  opacity: 0.6;
}

.sidebar-nav {
  margin-top: 2rem;
  flex: 1;
}

.nav-item {
  display: block;
  padding: 0.8rem 1rem;
  border-radius: 10px;
  color: white;
  text-decoration: none;
  transition: 0.2s;
}

.nav-item:hover {
  background: rgba(255,255,255,0.08);
}

.sidebar-footer {
  margin-top: auto;
}

.btn-logout {
  width: 100%;
  background: #ef4444;
  border: none;
  color: white;
  border-radius: 10px;
  padding: 0.6rem;
}

/* MAIN */
.main {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.topbar {
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0 2rem;
  background: white;
  border-bottom: 1px solid #e5e7eb;
}

.topbar-title {
  font-weight: 700;
}

.content {
  padding: 2rem;
}
</style>