<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Form, Field, useForm, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medicos',
        href: '/medicos',
    },
    {
        title: 'Crear medicos',
        href: '/medicos/create',
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

const { handleSubmit } = useForm({
    validationSchema: schema
})

function submit(values: any) {
    // Aquí puedes manejar el envío del formulario
    console.log('Formulario enviado con los siguientes valores:', values);

    router.post('/medicos', values, {
        onSuccess: () => {
            // Redirigir a la lista de pacientes después de crear uno nuevo
            router.visit('/medicos');
        },
        onError: (errors) => {
            console.error('Errores al enviar el formulario:', errors);
        }
    });

}

</script>

<template>

    <Head title="Medicos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="col-span-3">
                    <h3 class="text-3xl font-semibold text-white">
                        Registrar medico
                    </h3>
                </div>

            </div>
            <div
                class="relative   rounded-xl border border-sidebar-border/70  dark:border-sidebar-border">

                <Form @submit="submit" :validation-schema="schema" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4">
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl" >Nombre:</label>
                        <Field name="nombre" type="text" class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl" placeholder="Ingresa tu nombre" />
                        <ErrorMessage name="nombre" class="text-red-500" />
                    </div>
                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl" >Especialidad:</label>
                        <Field name="especialidad" type="text" class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl" placeholder="Ingresa tu apellido" />
                        <ErrorMessage name="especialidad" class="text-red-500" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-xl" >Cedula:</label>
                        <Field name="cedula" type="text" class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl" placeholder="Ingresa tu cedula" />
                        <ErrorMessage name="cedula" class="text-red-500" />
                    </div>

                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl" >Telefono:</label>
                        <Field name="telefono" type="text" class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl" placeholder="Ingresa tu telefono" />
                        <ErrorMessage name="telefono" class="text-red-500" />
                    </div>


                    <div class="flex flex-col gap-2 md:col-span-2">
                        <label class="text-xl" >Correo:</label>
                        <Field name="correo" type="email" class="py-2 px-4 border border-3 bg-transparent text-white rounded-xl" placeholder="Ingresa tu correo" />
                        <ErrorMessage name="correo" class="text-red-500" />
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-4 text-2xl font-black cursor-pointer hover:bg-blue-700 rounded w-full md:col-span-4 mt-4">
                        Guardar
                    </button>
                </Form>

            </div>
        </div>
    </AppLayout>
</template>
