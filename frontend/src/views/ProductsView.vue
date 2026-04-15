<template>
  <div class="container-fluid py-4 px-3 px-md-4 px-xl-5">
    <section class="hero-card mb-4">
      <div class="row align-items-center g-4">
        <div class="col-12 col-lg-8">
          <span class="hero-badge">Dashboard</span>
          <h1 class="hero-title mt-3 mb-2">Gerenciamento de Produtos</h1>
          <p class="hero-text mb-0">
            Cadastre, edite, exclua e organize seus produtos com uma interface moderna,
            rápida e responsiva.
          </p>
        </div>

        <div class="col-12 col-lg-4">
          <div class="hero-stats">
            <div class="stat-box">
              <span class="stat-label">Produtos na página</span>
              <strong class="stat-value">{{ products.length }}</strong>
            </div>
            <div class="stat-box">
              <span class="stat-label">Página atual</span>
              <strong class="stat-value">{{ currentPage }}/{{ lastPage }}</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="row g-4">
      <div
        v-if="user?.role === 'admin' || user?.role === 'editor'"
        class="col-12 col-xl-4"
      >
        <div class="glass-card form-card h-100">
          <div class="form-panel-header">
            <div>
              <span class="form-panel-badge">
                {{ isEditing ? 'Modo edição' : 'Novo cadastro' }}
              </span>
              <h4 class="form-panel-title mb-1">
                {{ isEditing ? 'Editar Produto' : 'Cadastrar Produto' }}
              </h4>
              <p class="form-panel-subtitle mb-0">
                {{ isEditing
                  ? 'Atualize os campos abaixo e salve as alterações.'
                  : 'Preencha os dados para adicionar um novo produto ao catálogo.' }}
              </p>
            </div>
          </div>

          <div class="card-body-custom">
            <div v-if="formError" class="alert alert-danger custom-alert form-feedback">
              {{ formError }}
            </div>

            <div v-if="formSuccess" class="alert alert-success custom-alert form-feedback">
              {{ formSuccess }}
            </div>

            <form @submit.prevent="saveProduct" class="product-form">
              <div class="form-field">
                <label class="form-label custom-label">Nome</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control custom-input"
                  maxlength="50"
                  placeholder="Ex: Notebook Dell"
                />
              </div>

              <div class="form-field">
                <label class="form-label custom-label">Descrição</label>
                <textarea
                  v-model="form.description"
                  class="form-control custom-input custom-textarea"
                  rows="3"
                  maxlength="200"
                  placeholder="Descreva o produto"
                ></textarea>
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="form-field">
                    <label class="form-label custom-label">Preço</label>
                    <div class="input-decorated">
                      <span class="input-prefix">R$</span>
                      <input
                        v-model="form.price"
                        type="number"
                        step="0.01"
                        class="form-control custom-input with-prefix"
                        placeholder="1999.90"
                      />
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="form-field">
                    <label class="form-label custom-label">Data de validade</label>
                    <input
                      v-model="form.expiration_date"
                      type="date"
                      class="form-control custom-input"
                    />
                  </div>
                </div>
              </div>

              <div class="form-field">
                <label class="form-label custom-label">Categoria</label>
                <select v-model="form.category_id" class="form-select custom-input">
  <option disabled value="">Selecione uma categoria</option>
  <option
    v-for="category in categories"
    :key="category.id"
    :value="category.id"
  >
    {{ category.name }}
  </option>
</select>
              </div>

              <div class="form-field">
                <label class="form-label custom-label">Imagem</label>
                <input
                  type="file"
                  class="form-control custom-input"
                  accept="image/*"
                  @change="handleFileChange"
                />
                <small class="field-hint">Formatos aceitos: JPG, PNG, WEBP.</small>
              </div>

              <div v-if="isEditing && form.image_url" class="mb-3">
                <label class="form-label custom-label d-block">Imagem atual</label>
                <div class="image-preview-wrapper">
                  <img
                    :src="form.image_url"
                    alt="Imagem do produto"
                    class="img-preview"
                  />
                </div>
              </div>

              <div class="form-actions">
                <button class="btn btn-gradient flex-grow-1" type="submit" :disabled="saving">
                  {{ saving ? 'Salvando...' : isEditing ? 'Atualizar produto' : 'Cadastrar produto' }}
                </button>

                <button
                  v-if="isEditing"
                  class="btn btn-soft"
                  type="button"
                  @click="resetForm"
                >
                  Cancelar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div :class="user?.role === 'viewer' ? 'col-12' : 'col-12 col-xl-8'">
        <div v-if="user?.role === 'viewer'" class="glass-card viewer-banner mb-4">
          <h5 class="fw-bold mb-2">Modo visualização</h5>
          <p class="text-muted mb-0">
            Seu perfil permite apenas visualizar os produtos cadastrados.
          </p>
        </div>

        <div class="glass-card list-card h-100">
          <div class="card-header-custom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
              <h4 class="fw-bold mb-1">Lista de Produtos</h4>
              <p class="text-muted mb-0 small">
                Visualize, pesquise e gerencie os produtos cadastrados.
              </p>
            </div>

            <div v-if="user" class="role-chip">
              Perfil: <strong>{{ user.role }}</strong>
            </div>
          </div>

          <div class="card-body-custom">
            <div class="search-bar mb-4">
              <input
                v-model="search"
                @input="fetchProducts(1)"
                type="text"
                class="form-control search-input"
                placeholder="Buscar por nome ou descrição"
              />
              <button class="btn btn-search" @click="fetchProducts(1)">
                Buscar
              </button>
            </div>

            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="text-muted mt-3 mb-0">Carregando produtos...</p>
            </div>

            <div v-else>
              <div v-if="products.length === 0" class="empty-state">
                <div class="empty-icon">📦</div>
                <h5 class="fw-bold">Nenhum produto encontrado</h5>
                <p class="text-muted mb-0">
                  Cadastre um novo produto ou ajuste sua busca.
                </p>
              </div>

              <div v-else class="table-responsive">
                <table class="table modern-table align-middle">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Produto</th>
                      <th>Preço</th>
                      <th>Validade</th>
                      <th>Categoria</th>
                      <th class="text-end">Ações</th>
                    </tr>
                  </thead>

                  <tbody>
                    <tr v-for="product in products" :key="product.id">
                      <td>
                        <span class="badge custom-id-badge">#{{ product.id }}</span>
                      </td>

                      <td>
                        <div class="product-name">{{ product.name }}</div>
                        <div class="product-description">{{ product.description }}</div>
                      </td>

                      <td>
                        <span class="price-tag">
                          R$ {{ Number(product.price).toFixed(2) }}
                        </span>
                      </td>

                      <td>{{ product.expiration_date }}</td>

                      <td>
                        <span class="category-badge">
                          {{ product.category?.name ?? '-' }}
                        </span>
                      </td>

                      <td>
                        <div class="d-flex justify-content-end gap-2 flex-wrap">
                          <button
                            v-if="user?.role === 'admin' || user?.role === 'editor'"
                            class="btn btn-sm btn-edit"
                            @click="editProduct(product)"
                          >
                            Editar
                          </button>

                          <button
                            v-if="user?.role === 'admin'"
                            class="btn btn-sm btn-delete"
                            @click="deleteProduct(product.id)"
                          >
                            Excluir
                          </button>

                          <span
                            v-if="user?.role === 'viewer'"
                            class="text-muted small"
                          >
                            Sem ações
                          </span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 gap-3">
                <p class="text-muted mb-0">
                  Página {{ currentPage }} de {{ lastPage }}
                </p>

                <div class="d-flex gap-2">
                  <button
                    class="btn btn-soft"
                    :disabled="currentPage === 1"
                    @click="fetchProducts(currentPage - 1)"
                  >
                    Anterior
                  </button>

                  <button
                    class="btn btn-soft"
                    :disabled="currentPage === lastPage"
                    @click="fetchProducts(currentPage + 1)"
                  >
                    Próxima
                  </button>
                </div>
              </div>
            </div>

            <div v-if="generalError" class="alert alert-danger custom-alert mt-4 mb-0">
              {{ generalError }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/service/api'

interface Category {
  id: number
  name: string
}

interface Product {
  id: number
  name: string
  description: string
  price: number
  expiration_date: string
  image: string | null
  category_id: number
  category?: Category
}

interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'editor' | 'viewer'
}

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const user = ref<User | null>(null)

const loading = ref(false)
const saving = ref(false)
const search = ref('')
const currentPage = ref(1)
const lastPage = ref(1)
const isEditing = ref(false)
const editingId = ref<number | null>(null)
const formError = ref('')
const formSuccess = ref('')
const generalError = ref('')

const form = ref({
  name: '',
  description: '',
  price: '',
  expiration_date: '',
  category_id: null as number | null,
  image: null as File | null,
  image_url: '',
})

const fetchMe = async () => {
  try {
    const response = await api.get('/me')
    user.value = response.data
  } catch (error) {
    console.error(error)
    generalError.value = 'Erro ao carregar usuário logado.'
  }
}

const fetchProducts = async (page = 1) => {
  loading.value = true
  generalError.value = ''

  try {
    const response = await api.get('/products', {
      params: {
        page,
        search: search.value,
      },
    })

    products.value = response.data.data
    currentPage.value = response.data.current_page
    lastPage.value = response.data.last_page
  } catch (error) {
    console.error(error)
    generalError.value = 'Erro ao carregar produtos.'
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/categories')
    console.log('CATEGORIES RESPONSE:', response.data)
    categories.value = response.data.data ?? response.data ?? []
  } catch (error) {
    console.error(error)
    generalError.value = 'Erro ao carregar categorias.'
  }
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (file) {
    form.value.image = file
  }
}

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    price: '',
    expiration_date: '',
    category_id: null,
    image: null,
    image_url: '',
  }

  isEditing.value = false
  editingId.value = null
  formError.value = ''
  formSuccess.value = ''
}

const saveProduct = async () => {
  formError.value = ''
  formSuccess.value = ''
  saving.value = true

  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('description', form.value.description)
    formData.append('price', form.value.price)
    formData.append('expiration_date', form.value.expiration_date)
    formData.append('category_id', String(form.value.category_id ?? ''))

    if (form.value.image) {
      formData.append('image', form.value.image)
    }

    if (isEditing.value && editingId.value) {
      formData.append('_method', 'PUT')
      await api.post(`/products/${editingId.value}`, formData)
      formSuccess.value = 'Produto atualizado com sucesso.'
    } else {
      await api.post('/products', formData)
      formSuccess.value = 'Produto cadastrado com sucesso.'
    }

    resetForm()
    await fetchProducts(currentPage.value)
  } catch (error: any) {
    console.error('ERRO COMPLETO:', error)
    console.error('RESPOSTA:', error?.response?.data)
    console.error(error)

    if (error?.response?.data?.errors) {
      const errors = error.response.data.errors
      console.log('VALIDATION ERRORS:', errors)

      const keys = Object.keys(errors)

      if (keys.length > 0) {
        const firstKey = keys[0] as keyof typeof errors
        formError.value = errors[firstKey]?.[0] || 'Erro de validação.'
      } else {
        formError.value = 'Erro de validação.'
      }
    } else {
      formError.value =
        error?.response?.data?.message || 'Erro ao salvar produto.'
    }
  } finally {
    saving.value = false
  }
}

const editProduct = (product: Product) => {
  isEditing.value = true
  editingId.value = product.id
  formError.value = ''
  formSuccess.value = ''

  form.value = {
    name: product.name,
    description: product.description,
    price: String(product.price),
    expiration_date: product.expiration_date,
    category_id: product.category_id,
    image: null,
    image_url: product.image
      ? `http://127.0.0.1:8000/storage/${product.image}`
      : '',
  }

  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const deleteProduct = async (id: number) => {
  const confirmed = window.confirm('Deseja realmente excluir este produto?')
  if (!confirmed) return

  try {
    await api.delete(`/products/${id}`)
    await fetchProducts(currentPage.value)
  } catch (error) {
    console.error(error)
    generalError.value = 'Erro ao excluir produto.'
  }
}

onMounted(async () => {
  await fetchMe()
  await fetchCategories()
  await fetchProducts()
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
  transition: all 0.25s ease;
}

.glass-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
}

button {
  transition: all 0.2s ease;
}

button:hover {
  transform: translateY(-1px);
}

.card-header-custom {
  padding: 1.5rem 1.5rem 0;
}

.card-body-custom {
  padding: 1.5rem;
}

.form-card {
  padding: 1.25rem;
}

.form-panel-header {
  padding: 0.4rem 0.4rem 1rem;
  margin-bottom: 0.4rem;
  border-bottom: 1px solid rgba(148, 163, 184, 0.12);
}

.form-panel-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.38rem 0.72rem;
  border-radius: 999px;
  background: rgba(79, 70, 229, 0.08);
  color: #4338ca;
  font-size: 0.78rem;
  font-weight: 700;
  margin-bottom: 0.8rem;
}

.form-panel-title {
  font-size: 1.3rem;
  font-weight: 800;
  color: #0f172a;
}

.form-panel-subtitle {
  color: #64748b;
  font-size: 0.92rem;
  line-height: 1.45;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.form-field {
  margin-bottom: 0.9rem;
}

.custom-label {
  font-weight: 700;
  color: #334155;
  margin-bottom: 0.45rem;
}

.custom-input {
  border-radius: 16px;
  border: 1px solid #dbe3f0;
  padding: 0.95rem 1rem;
  box-shadow: none;
  background: rgba(255, 255, 255, 0.92);
}

.custom-textarea {
  min-height: 110px;
  resize: vertical;
}

.custom-input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.12);
}

.input-decorated {
  position: relative;
}

.input-prefix {
  position: absolute;
  top: 50%;
  left: 1rem;
  transform: translateY(-50%);
  color: #64748b;
  font-weight: 700;
  pointer-events: none;
  z-index: 2;
}

.with-prefix {
  padding-left: 3rem;
}

.field-hint {
  display: inline-block;
  margin-top: 0.45rem;
  color: #64748b;
  font-size: 0.78rem;
}

.form-actions {
  display: flex;
  gap: 0.65rem;
  margin-top: 0.5rem;
}

.btn-gradient {
  background: linear-gradient(135deg, #4f46e5 0%, #2563eb 100%);
  color: white;
  border: none;
  border-radius: 16px;
  padding: 0.95rem 1rem;
  font-weight: 700;
}

.btn-gradient:hover {
  color: white;
  opacity: 0.95;
}

.btn-soft {
  background: white;
  border: 1px solid #d9e0ee;
  border-radius: 16px;
  padding: 0.95rem 1rem;
  font-weight: 600;
}

.form-feedback {
  margin-bottom: 1rem;
}

.search-bar {
  display: flex;
  gap: 0.75rem;
  flex-direction: column;
}

@media (min-width: 768px) {
  .search-bar {
    flex-direction: row;
  }
}

.search-input {
  border-radius: 14px;
  border: 1px solid #dbe3f0;
  padding: 0.9rem 1rem;
}

.btn-search {
  white-space: nowrap;
  border-radius: 14px;
  padding: 0.9rem 1.1rem;
  background: #111827;
  color: white;
  border: none;
}

.btn-search:hover {
  color: white;
  opacity: 0.95;
}

.modern-table thead th {
  color: #6b7280;
  font-size: 0.86rem;
  font-weight: 700;
  border-bottom: 1px solid #e6ecf5;
}

.modern-table tbody tr {
  transition: 0.2s ease;
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

.product-name {
  font-weight: 700;
  color: #111827;
}

.product-description {
  color: #6b7280;
  font-size: 0.9rem;
  margin-top: 0.15rem;
}

.price-tag {
  font-weight: 800;
  color: #0f172a;
}

.category-badge {
  display: inline-block;
  background: linear-gradient(135deg, #dbeafe 0%, #e0e7ff 100%);
  color: #1d4ed8;
  border-radius: 999px;
  padding: 0.45rem 0.75rem;
  font-weight: 700;
  font-size: 0.82rem;
}

.btn-edit {
  border-radius: 12px;
  border: 1px solid #facc15;
  color: #a16207;
  background: #fefce8;
}

.btn-delete {
  border-radius: 12px;
  border: 1px solid #fca5a5;
  color: #b91c1c;
  background: #fef2f2;
}

.btn-edit:hover,
.btn-delete:hover {
  opacity: 0.92;
}

.empty-state {
  text-align: center;
  padding: 3rem 1rem;
}

.empty-icon {
  font-size: 3rem;
  margin-bottom: 0.75rem;
}

.custom-alert {
  border-radius: 16px;
}

.image-preview-wrapper {
  padding: 0.5rem;
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  display: inline-block;
}

.img-preview {
  width: 100%;
  max-width: 180px;
  display: block;
  border-radius: 12px;
  object-fit: cover;
}

.viewer-banner {
  padding: 1.2rem 1.4rem;
  margin-bottom: 1rem;
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
</style>