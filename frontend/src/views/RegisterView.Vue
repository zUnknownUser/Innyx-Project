<template>
  <div class="auth-page">
    <div class="auth-shell">

      <!-- LADO VISUAL -->
      <section class="auth-brand">
        <div class="brand-badge">INNYX System</div>

        <h1 class="brand-title">
          Crie sua conta e comece a organizar seus produtos.
        </h1>

        <p class="brand-text">
          Tenha controle total do seu catálogo com uma experiência rápida,
          moderna e segura.
        </p>

        <div class="brand-highlights">
          <div class="highlight-card">
            <span class="highlight-label">Cadastro</span>
            <strong class="highlight-value">Rápido e simples</strong>
          </div>

          <div class="highlight-card">
            <span class="highlight-label">Controle</span>
            <strong class="highlight-value">Total dos produtos</strong>
          </div>

          <div class="highlight-card">
            <span class="highlight-label">Experiência</span>
            <strong class="highlight-value">Interface moderna</strong>
          </div>
        </div>
      </section>

      
      <section class="auth-form-wrapper">
        <div class="auth-card">
          <div class="auth-card-header">
            <h2 class="auth-title">Criar conta</h2>
            <p class="auth-subtitle">
              Preencha os dados para começar.
            </p>
          </div>

          <div v-if="errorMessage" class="alert alert-danger auth-alert">
            {{ errorMessage }}
          </div>

          <div v-if="successMessage" class="alert alert-success auth-alert">
            {{ successMessage }}
          </div>

          <form @submit.prevent="handleRegister">
            <div class="mb-3">
              <label class="form-label auth-label">Nome</label>
              <input
                v-model="name"
                type="text"
                class="form-control auth-input"
                placeholder="Seu nome"
              />
            </div>

            <div class="mb-3">
              <label class="form-label auth-label">E-mail</label>
              <input
                v-model="email"
                type="email"
                class="form-control auth-input"
                placeholder="seu@email.com"
              />
            </div>

            <div class="mb-3">
              <label class="form-label auth-label">Senha</label>
              <input
                v-model="password"
                type="password"
                class="form-control auth-input"
                placeholder="••••••••"
              />
            </div>

            <div class="mb-4">
              <label class="form-label auth-label">Confirmar senha</label>
              <input
                v-model="passwordConfirmation"
                type="password"
                class="form-control auth-input"
                placeholder="••••••••"
              />
            </div>

            <button
              class="btn auth-btn-primary w-100 mb-3"
              type="submit"
              :disabled="loading"
            >
              {{ loading ? 'Criando...' : 'Criar conta' }}
            </button>

            <div class="auth-footer text-center">
              <span class="text-muted">Já tem conta?</span>
              <RouterLink to="/login" class="auth-link ms-1">
                Entrar
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

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)

const errorMessage = ref('')
const successMessage = ref('')

const handleRegister = async () => {
  errorMessage.value = ''
  successMessage.value = ''
  loading.value = true

  try {
    await api.post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    successMessage.value = 'Conta criada com sucesso! Redirecionando...'

    setTimeout(() => {
      router.push('/login')
    }, 1200)

  } catch (error: any) {
    console.error(error)

    if (error?.response?.data?.errors) {
      const errors = error.response.data.errors
      const firstKey = Object.keys(errors)[0]
      // @ts-ignore
      errorMessage.value = errors[firstKey][0]
    } else {
      errorMessage.value =
        error?.response?.data?.message || 'Erro ao criar conta.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* MESMO ESTILO DO LOGIN — consistência total */

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
  font-size: 2.4rem;
  font-weight: 800;
  margin-bottom: 1rem;
}

.brand-text {
  color: rgba(255, 255, 255, 0.78);
  margin-bottom: 2rem;
}

.brand-highlights {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.highlight-card {
  background: rgba(255,255,255,0.1);
  padding: 1rem;
  border-radius: 16px;
}

.auth-form-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.auth-card {
  width: 100%;
  max-width: 470px;
  background: rgba(255,255,255,0.9);
  border-radius: 28px;
  padding: 2rem;
  box-shadow: 0 20px 50px rgba(0,0,0,0.08);
  transition: 0.25s;
}

.auth-card:hover {
  transform: translateY(-3px);
}

.auth-title {
  font-size: 2rem;
  font-weight: 800;
}

.auth-input {
  border-radius: 14px;
  padding: 0.9rem;
}

.auth-btn-primary {
  background: linear-gradient(135deg, #4f46e5, #2563eb);
  color: white;
  border-radius: 14px;
  padding: 0.9rem;
  font-weight: 700;
}

.auth-link {
  color: #4338ca;
  font-weight: 700;
}
</style>