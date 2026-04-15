<template>
  <div class="auth-page">
    <div class="auth-shell">
      <section class="auth-brand">
        <div class="brand-badge">INNYX System</div>

        <h1 class="brand-title">
          Controle seus produtos com uma interface clara e profissional.
        </h1>

        <p class="brand-text">
          Acesse o painel para cadastrar, editar, buscar e organizar seus produtos
          com mais agilidade.
        </p>

        <div class="brand-highlights">
          <div class="highlight-card">
            <span class="highlight-label">Gestão</span>
            <strong class="highlight-value">CRUD completo</strong>
          </div>

          <div class="highlight-card">
            <span class="highlight-label">Busca</span>
            <strong class="highlight-value">Rápida e paginada</strong>
          </div>

          <div class="highlight-card">
            <span class="highlight-label">Segurança</span>
            <strong class="highlight-value">Acesso por token</strong>
          </div>
        </div>
      </section>

      <section class="auth-form-wrapper">
        <div class="auth-card">
          <div class="auth-card-header">
            <h2 class="auth-title">Entrar</h2>
            <p class="auth-subtitle">Use suas credenciais para acessar o painel.</p>
          </div>

          <div v-if="errorMessage" class="alert alert-danger auth-alert">
            {{ errorMessage }}
          </div>

          <form @submit.prevent="handleLogin">
            <div class="mb-3">
              <label class="form-label auth-label">E-mail</label>
              <input
                v-model="email"
                type="email"
                class="form-control auth-input"
                placeholder="seu@email.com"
              />
            </div>

            <div class="mb-4">
              <label class="form-label auth-label">Senha</label>
              <input
                v-model="password"
                type="password"
                class="form-control auth-input"
                placeholder="••••••••"
              />
            </div>

            <button class="btn auth-btn-primary w-100 mb-3" type="submit" :disabled="loading">
              {{ loading ? 'Entrando...' : 'Entrar no sistema' }}
            </button>

            <div class="auth-footer text-center">
              <span class="text-muted">Ainda não tem conta?</span>
              <RouterLink to="/register" class="auth-link ms-1">
                Criar conta
              </RouterLink>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import api from '@/service/api'

const router = useRouter()

const email = ref('')
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
  errorMessage.value = ''
  loading.value = true

  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    localStorage.setItem('token', response.data.token)
    router.push('/')
  } catch (error: any) {
    console.error(error)
    errorMessage.value =
      error?.response?.data?.message ||
      error?.response?.data?.errors?.email?.[0] ||
      'Erro ao fazer login.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  display: flex;
  align-items: stretch;
  justify-content: center;
  background:
    radial-gradient(circle at top left, rgba(79, 70, 229, 0.14), transparent 30%),
    radial-gradient(circle at bottom right, rgba(16, 185, 129, 0.10), transparent 25%),
    linear-gradient(180deg, #f8faff 0%, #eef3ff 100%);
  padding: 2rem;
}

.auth-shell {
  width: 100%;
  max-width: 1400px;
  min-height: calc(100vh - 4rem);
  display: grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap: 1.5rem;
}

.auth-brand {
  border-radius: 28px;
  padding: 3rem;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #312e81 100%);
  color: white;
  display: flex;
  flex-direction: column;
  justify-content: center;
  box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
}

.brand-badge {
  display: inline-flex;
  align-self: flex-start;
  padding: 0.5rem 0.9rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.14);
  font-size: 0.82rem;
  font-weight: 700;
  margin-bottom: 1.25rem;
}

.brand-title {
  font-size: 2.5rem;
  line-height: 1.1;
  font-weight: 800;
  max-width: 620px;
  margin-bottom: 1rem;
}

.brand-text {
  max-width: 580px;
  color: rgba(255, 255, 255, 0.78);
  font-size: 1.05rem;
  margin-bottom: 2rem;
}

.brand-highlights {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.highlight-card {
  border-radius: 20px;
  padding: 1rem 1.1rem;
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(8px);
}

.highlight-label {
  display: block;
  font-size: 0.82rem;
  opacity: 0.75;
  margin-bottom: 0.35rem;
}

.highlight-value {
  font-size: 1rem;
  font-weight: 700;
}

.auth-form-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-card {
  width: 100%;
  max-width: 470px;
  background: rgba(255, 255, 255, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(10px);
  border-radius: 28px;
  padding: 2.2rem;
  box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.auth-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 24px 55px rgba(15, 23, 42, 0.1);
}

.auth-card-header {
  margin-bottom: 1.5rem;
}

.auth-title {
  font-size: 2rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.4rem;
}

.auth-subtitle {
  color: #64748b;
  margin-bottom: 0;
}

.auth-label {
  font-weight: 600;
  color: #334155;
}

.auth-input {
  border-radius: 16px;
  border: 1px solid #dbe3f0;
  padding: 0.95rem 1rem;
  box-shadow: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.auth-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.12);
}

.auth-btn-primary {
  background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 16px;
  padding: 0.95rem 1rem;
  font-weight: 700;
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.auth-btn-primary:hover {
  color: white;
  opacity: 0.96;
  transform: translateY(-1px);
}

.auth-alert {
  border-radius: 16px;
}

.auth-footer {
  font-size: 0.95rem;
}

.auth-link {
  text-decoration: none;
  font-weight: 700;
  color: #4338ca;
}

.auth-link:hover {
  color: #312e81;
}

@media (max-width: 1100px) {
  .auth-shell {
    grid-template-columns: 1fr;
  }

  .auth-brand {
    min-height: 320px;
  }

  .brand-highlights {
    grid-template-columns: 1fr;
  }

  .auth-form-wrapper {
    justify-content: stretch;
  }

  .auth-card {
    max-width: none;
  }
}

@media (max-width: 768px) {
  .auth-page {
    padding: 1rem;
  }

  .auth-shell {
    min-height: auto;
    gap: 1rem;
  }

  .auth-brand,
  .auth-card {
    padding: 1.5rem;
    border-radius: 22px;
  }

  .brand-title {
    font-size: 2rem;
  }
}
</style>