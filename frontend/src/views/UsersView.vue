<template>
  <div class="container-fluid py-4 px-3 px-md-4 px-xl-5">
    <section class="hero-card mb-4">
      <div class="row align-items-center g-4">
        <div class="col-12 col-lg-8">
          <span class="hero-badge">Administração</span>
          <h1 class="hero-title mt-3 mb-2">Gerenciamento de Usuários</h1>
          <p class="hero-text mb-0">
            Visualize os usuários do sistema e altere seus níveis de acesso.
          </p>
        </div>

        <div class="col-12 col-lg-4">
          <div class="hero-stats">
            <div class="stat-box">
              <span class="stat-label">Usuários</span>
              <strong class="stat-value">{{ users.length }}</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="text-muted mt-3 mb-0">Carregando usuários...</p>
    </div>

    <div v-else class="glass-card">
      <div class="card-body-custom">
        <div v-if="errorMessage" class="alert alert-danger custom-alert mb-4">
          {{ errorMessage }}
        </div>

        <div v-if="successMessage" class="alert alert-success custom-alert mb-4">
          {{ successMessage }}
        </div>

        <div class="table-responsive">
          <table class="table modern-table align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Role atual</th>
                <th class="text-end">Alterar role</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in users" :key="item.id">
                <td>
                  <span class="badge custom-id-badge">#{{ item.id }}</span>
                </td>
                <td>{{ item.name }}</td>
                <td>{{ item.email }}</td>
                <td>
                  <span class="role-chip">
                    {{ item.role }}
                  </span>
                </td>
                <td>
                  <div class="d-flex justify-content-end gap-2">
                    <select
                      v-model="item.role"
                      class="form-select role-select"
                      style="max-width: 160px;"
                    >
                      <option value="viewer">viewer</option>
                      <option value="editor">editor</option>
                      <option value="admin">admin</option>
                    </select>

                    <button
                      class="btn btn-gradient"
                      @click="updateRole(item.id, item.role)"
                      :disabled="savingId === item.id"
                    >
                      {{ savingId === item.id ? 'Salvando...' : 'Salvar' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/service/api'

interface ManagedUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'editor' | 'viewer'
  created_at: string
}

const users = ref<ManagedUser[]>([])
const loading = ref(false)
const savingId = ref<number | null>(null)
const errorMessage = ref('')
const successMessage = ref('')

const fetchUsers = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get('/users')
    users.value = response.data
  } catch (error: any) {
    console.error(error)
    errorMessage.value =
      error?.response?.data?.message || 'Erro ao carregar usuários.'
  } finally {
    loading.value = false
  }
}

const updateRole = async (id: number, role: string) => {
  savingId.value = id
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const response = await api.put(`/users/${id}/role`, { role })
    successMessage.value = response.data.message
  } catch (error: any) {
    console.error(error)
    errorMessage.value =
      error?.response?.data?.message || 'Erro ao atualizar role.'
  } finally {
    savingId.value = null
  }
}

onMounted(() => {
  fetchUsers()
})
</script>

<style scoped>
.hero-card {
  background: linear-gradient(135deg, #312e81 0%, #1d4ed8 50%, #0f766e 100%);
  color: white;
  border-radius: 28px;
  padding: 2rem;
  box-shadow: 0 20px 40px rgba(49, 46, 129, 0.18);
}

.hero-badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 0.45rem 0.85rem;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 600;
}

.hero-title {
  font-size: 2rem;
  font-weight: 800;
}

.hero-text {
  max-width: 640px;
  color: rgba(255, 255, 255, 0.82);
}

.hero-stats {
  display: grid;
  gap: 1rem;
}

.stat-box {
  background: rgba(255, 255, 255, 0.14);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 20px;
  padding: 1rem 1.2rem;
  backdrop-filter: blur(6px);
}

.stat-label {
  display: block;
  font-size: 0.85rem;
  opacity: 0.8;
}

.stat-value {
  font-size: 1.4rem;
  font-weight: 800;
}

.glass-card {
  background: rgba(255, 255, 255, 0.82);
  backdrop-filter: blur(10px);
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.7);
}

.card-body-custom {
  padding: 1.5rem;
}

.modern-table thead th {
  color: #6b7280;
  font-size: 0.86rem;
  font-weight: 700;
  border-bottom: 1px solid #e6ecf5;
}

.modern-table tbody tr:hover {
  background: rgba(79, 70, 229, 0.03);
}

.modern-table td {
  padding-top: 1rem;
  padding-bottom: 1rem;
  border-color: #eef2f7;
}

.custom-id-badge {
  background: #eef2ff;
  color: #4338ca;
  border-radius: 999px;
  padding: 0.55rem 0.75rem;
  font-weight: 700;
}

.role-chip {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.8rem;
  border-radius: 999px;
  background: rgba(79, 70, 229, 0.08);
  color: #4338ca;
  font-size: 0.82rem;
  font-weight: 600;
}

.role-select {
  border-radius: 14px;
  border: 1px solid #dbe3f0;
}

.btn-gradient {
  background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 14px;
  padding: 0.7rem 1rem;
  font-weight: 700;
}

.custom-alert {
  border-radius: 16px;
}
</style>