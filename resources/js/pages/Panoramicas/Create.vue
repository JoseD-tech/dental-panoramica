<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3'
import { ref, reactive, watch } from 'vue'
import debounce from 'lodash/debounce'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Panorámicas',
        href: '/panoramicas',
    },
    {
        title: 'Crear registro de panoramicas',
        href: '/panoramicas/create',
    },
];

// 1️⃣ Valores del formulario
const form = reactive({
  paciente_id: '',
  medico_id: '',
  nota: '',
  archivo: /** @type File|null */ (null),
})

// 2️⃣ Listas de búsqueda
const searchPaciente = ref('')
const pacientesFiltrados = ref<any[]>([])
const searchMedico = ref('')
const medicosFiltrados = ref<any[]>([])

// 3️⃣ Errores
const errors = reactive({
  paciente_id: '',
  medico_id: '',
  nota: '',
  archivo: '',
})

// 4️⃣ Búsquedas debounced
const buscarPacientes = debounce(async (term: string) => {
  if (term.length < 2) {
    pacientesFiltrados.value = []
  } else {
    const res = await fetch(`/api/pacientes?cedula=${term}`)
    pacientesFiltrados.value = await res.json()
  }
}, 300)

const buscarMedicos = debounce(async (term: string) => {
  if (term.length < 2) {
    medicosFiltrados.value = []
  } else {
    const res = await fetch(`/api/medicos?nombre=${term}`)
    medicosFiltrados.value = await res.json()
  }
}, 300)

watch(searchPaciente, val => buscarPacientes(val))
watch(searchMedico, val => buscarMedicos(val))

// 5️⃣ Manejador del input file
function onFileChange(e: Event) {
  const files = (e.target as HTMLInputElement).files
  form.archivo = files && files[0] ? files[0] : null
}

// 6️⃣ Validación básica
function validate() {
  let ok = true
  errors.paciente_id = form.paciente_id ? '' : 'Debe seleccionar un paciente'
  errors.medico_id   = form.medico_id   ? '' : 'Debe seleccionar un médico'
  errors.nota        = form.nota.trim() ? '' : 'Agrega una nota corta para el archivo.'
  errors.archivo     = form.archivo      ? '' : 'El archivo es obligatorio'
  if (form.archivo) {
    if (form.archivo.size > 5*1024*1024) {
      errors.archivo = 'El archivo es muy grande'
    } else if (!['application/pdf','image/jpeg','image/png'].includes(form.archivo.type)) {
      errors.archivo = 'Formato inválido'
    }
  }
  Object.values(errors).forEach(e => { if (e) ok = false })
  return ok
}

// 7️⃣ Envío
async function submit() {
  if (!validate()) return
  const fd = new FormData()
  fd.append('paciente_id', form.paciente_id)
  fd.append('medico_id',   form.medico_id)
  fd.append('notas',        form.nota)
  if (form.archivo) fd.append('archivo', form.archivo)

  await router.post('/panoramicas', fd, {
    forceFormData: true,
    onSuccess: () => router.visit('/panoramicas'),
    onError:   e => console.error(e),
  })
}
</script>

<template>
  <Head title="Registrar Panorámica" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4 space-y-6">
      <h3 class="text-2xl font-semibold text-white">Registrar Panorámica</h3>

      <!-- Paciente -->
      <div>
        <label class="text-white block mb-1">Buscar Paciente por Cédula</label>
        <input v-model="searchPaciente"
               class="w-full p-2 rounded border"
               placeholder="Cédula del paciente" />
        <select v-model="form.paciente_id"
                class="w-full mt-2 p-2 rounded border">
          <option disabled value="">Selecciona un paciente</option>
          <option v-for="p in pacientesFiltrados" :key="p.id" :value="p.id" class="text-black">
            {{ p.nombre }} {{ p.apellido }} - {{ p.cedula }}
          </option>
        </select>
        <p v-if="errors.paciente_id" class="text-red-500">{{ errors.paciente_id }}</p>
      </div>

      <!-- Médico -->
      <div>
        <label class="text-white block mb-1">Buscar Médico por Nombre</label>
        <input v-model="searchMedico"
               class="w-full p-2 rounded border"
               placeholder="Nombre del médico" />
        <select v-model="form.medico_id"
                class="w-full mt-2 p-2 rounded border">
          <option disabled value="">Selecciona un médico</option>
          <option v-for="m in medicosFiltrados" :key="m.id" :value="m.id" class="text-black">
            {{ m.nombre }}
          </option>
        </select>
        <p v-if="errors.medico_id" class="text-red-500">{{ errors.medico_id }}</p>
      </div>

      <!-- Nota -->
      <div>
        <label class="text-white block mb-1">Nota</label>
        <textarea v-model="form.nota"
                  rows="4"
                  class="w-full p-2 rounded border"
                  placeholder="Agrega una nota"></textarea>
        <p v-if="errors.nota" class="text-red-500">{{ errors.nota }}</p>
      </div>

      <!-- Archivo -->
      <div>
        <label class="text-white block mb-1">Archivo</label>
        <input type="file" accept="application/pdf"  @change="onFileChange" class="w-full p-2 rounded border" />
        <p v-if="errors.archivo" class="text-red-500">{{ errors.archivo }}</p>
      </div>

      <!-- Botón -->
      <button @click.prevent="submit"
              class="bg-blue-600 w-full py-3 rounded text-white font-bold hover:bg-blue-700">
        Guardar Panorámica
      </button>
    </div>
  </AppLayout>
</template>

<style scoped>
/* ejemplo pequeño para que el bloque se destaque */
</style>
