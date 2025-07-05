<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'
import dayjs from 'dayjs'

const props = defineProps<{
    medico: {
        id: number,
        nombre: string,
        especialidad: string,
        cedula: string,
        telefono?: string,
        correo?: string,
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Médicos',
        href: '/medicos',
    },
    {
        title: 'Editar médicos',
        href: `/medicos/${props.medico.id}`,
    },
];

// 🧪 Esquema de validación
const schema = yup.object({
    nombre: yup.string().required('El nombre es obligatorio'),
    especialidad: yup.string().required('La especialidad es obligatoria'),
    cedula: yup.string().required('La cédula es obligatoria'),
    telefono: yup.string().nullable(),
    correo: yup.string().email('Correo no válido').required('El correo es obligatorio'),
})

const initialValues = {
    ...props.medico,
};

// Submit
function submit(values: typeof initialValues) {
    router.put(`/medicos/${props.medico.id}`, values, {
        onSuccess: () => {
            router.visit('/medicos');
        },
        onError: (errors) => {
            console.error('Errores al actualizar el médico:', errors);
        },
    });
}
</script>

<template>
    <Head title="Médicos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="col-span-3">
                    <h3 class="text-3xl font-semibold text-white">Editar Médico</h3>
                </div>
            </div>
            <div class="relative rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <Form :validation-schema="schema" :initial-values="initialValues" @submit="submit"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl">Nombre y Apellido:</label>
                        <Field name="nombre" type="text"
                            class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
                            placeholder="Ingresa tu nombre" />
                        <ErrorMessage name="nombre" class="text-red-500" />
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl">Especialidad:</label>
                        <Field name="especialidad" type="text"
                            class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
                            placeholder="Ingresa tu especialidad" />
                        <ErrorMessage name="especialidad" class="text-red-500" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xl">Cédula:</label>
                        <Field name="cedula" type="text"
                            class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
                            placeholder="Ingresa tu cédula" />
                        <ErrorMessage name="cedula" class="text-red-500" />
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl">Teléfono:</label>
                        <Field name="telefono" type="text"
                            class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
                            placeholder="Ingresa tu teléfono" />
                        <ErrorMessage name="telefono" class="text-red-500" />
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl">Correo:</label>
                        <Field name="correo" type="email"
                            class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl"
                            placeholder="Ingresa tu correo" />
                        <ErrorMessage name="correo" class="text-red-500" />
                    </div>

                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-4 text-2xl font-black cursor-pointer hover:bg-blue-700 rounded w-full md:col-span-4 mt-4">
                        Guardar
                    </button>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
