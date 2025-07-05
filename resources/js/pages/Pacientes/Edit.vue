<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import dayjs from 'dayjs'

const props = defineProps<{
  paciente: {
    id: number,
    nombre: string,
    apellido: string,
    cedula: string,
    fecha_nacimiento?: string | Date,
    telefono?: string,
    correo?: string,
    direccion?: string
  }
}>();

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Pacientes',
    href: '/pacientes',
  },
  {
    title: 'Editar paciente',
    href: `/pacientes/${props.paciente.id}`,
  },
];

const schema = yup.object({
  nombre: yup.string().required('El nombre es obligatorio'),
  apellido: yup.string().required('El apellido es obligatorio'),
  cedula: yup.string().required('La cédula es obligatoria'),
  fecha_nacimiento: yup.date().nullable(),
  telefono: yup.string().nullable(),
  correo: yup.string().email('Correo no válido').required('El correo es obligatorio'),
  direccion: yup.string().nullable(),
});

const initialValues = {
  ...props.paciente,
  fecha_nacimiento: props.paciente.fecha_nacimiento
    ? dayjs(props.paciente.fecha_nacimiento).format('YYYY-MM-DD')
    : '',
};

function submit(values: typeof initialValues) {
  router.put(`/pacientes/${props.paciente.id}`, values, {
    onSuccess: () => {
      router.visit('/pacientes');
    },
    onError: (errors) => {
      console.error('Errores al actualizar el paciente:', errors);
    },
  });
}
</script>

<template>
  <Head title="Pacientes" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-4">
        <div class="col-span-3">
          <h3 class="text-3xl font-semibold text-white">Editar paciente</h3>
        </div>
      </div>

      <div class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
        <Form
          :validation-schema="schema"
          :initial-values="initialValues"
          @submit="submit"
          class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4"
        >
          <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-xl">Nombre:</label>
            <Field
              name="nombre"
              type="text"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
              placeholder="Ingresa el nombre"
            />
            <ErrorMessage name="nombre" class="text-red-500" />
          </div>

          <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-xl">Apellido:</label>
            <Field
              name="apellido"
              type="text"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
              placeholder="Ingresa el apellido"
            />
            <ErrorMessage name="apellido" class="text-red-500" />
          </div>

          <div class="flex flex-col gap-2">
            <label class="text-xl">Cédula:</label>
            <Field
              name="cedula"
              type="text"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
              placeholder="Ingresa la cédula"
            />
            <ErrorMessage name="cedula" class="text-red-500" />
          </div>

          <div class="flex flex-col gap-2">
            <label class="text-xl">Fecha de nacimiento:</label>
            <Field
              name="fecha_nacimiento"
              type="date"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
            />
            <ErrorMessage name="fecha_nacimiento" class="text-red-500" />
          </div>

          <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-xl">Teléfono:</label>
            <Field
              name="telefono"
              type="text"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
              placeholder="Ingresa el teléfono"
            />
            <ErrorMessage name="telefono" class="text-red-500" />
          </div>

          <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-xl">Correo:</label>
            <Field
              name="correo"
              type="email"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
              placeholder="Ingresa el correo"
            />
            <ErrorMessage name="correo" class="text-red-500" />
          </div>

          <div class="flex flex-col gap-2 md:col-span-2">
            <label class="text-xl">Dirección:</label>
            <Field
              name="direccion"
              type="text"
              class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
              placeholder="Ingresa la dirección"
            />
            <ErrorMessage name="direccion" class="text-red-500" />
          </div>

          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-4 text-2xl font-black cursor-pointer hover:bg-blue-700 rounded w-full md:col-span-4 mt-4"
          >
            Guardar
          </button>
        </Form>
      </div>
    </div>
  </AppLayout>
</template>
