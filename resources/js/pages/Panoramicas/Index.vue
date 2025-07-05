<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Panorámicas',
        href: '/panoramicas',
    },
];

const props = defineProps<{
    panoramicas: any[];
}>();

const registrar = () => {
    router.get('/panoramicas/create');
};

const search = ref('')
const sortKey = ref('id')
const sortAsc = ref(true)

const filteredPanoramicas = computed(() => {
    return [...props.panoramicas.data]
        .filter(panoramica => panoramica.paciente.cedula.includes(search.value))
        .sort((a, b) => {
            if (a[sortKey.value] < b[sortKey.value]) return sortAsc.value ? -1 : 1
            if (a[sortKey.value] > b[sortKey.value]) return sortAsc.value ? 1 : -1
            return 0
        })
})

function toggleSort(key) {
    if (sortKey.value === key) {
        sortAsc.value = !sortAsc.value
    } else {
        sortKey.value = key
        sortAsc.value = true
    }
}

const enviarCorreo = (id: any) => {
  router.post(`/panoramicas/${id}/enviar`, {}, {
    onSuccess: () => alert('Correo enviado')
  })
}
</script>

<template>
    <Head title="Panorámicas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="col-span-3">
                    <h3 class="text-3xl font-semibold dark:text-white">
                        Panorámicas
                    </h3>
                </div>
                <div class="">
                    <Button @click="registrar" class="w-full cursor-pointer text-md">Registrar panorámica</Button>
                </div>
            </div>
            <template v-if="props.panoramicas.data.length === 0">
                <div class="py-4 px-4 w-full bg-gray-100 dark:bg-zinc-800 rounded-2xl text-xl">
                    No hay registros de panorámicas
                </div>
            </template>
            <template v-else>
                <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div class="p-4">

                        <input v-model="search" type="number" placeholder="Buscar por cédula del paciente..."
                            class="border p-2 mb-4 w-full rounded-md" />

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="cursor-pointer" @click="toggleSort('id')">ID</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('medico_nombre')">Nombre del médico</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('paciente_nombre')">Nombre del paciente</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('paciente_cedula')">Cédula del paciente</TableHead>
                                    <TableHead class="cursor-pointer">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="panoramica in filteredPanoramicas" :key="panoramica.id">
                                    <TableCell>{{ panoramica.id }}</TableCell>
                                    <TableCell>{{ panoramica.medico.nombre }}</TableCell>
                                    <TableCell>{{ `${panoramica.paciente.nombre} ${panoramica.paciente.apellido}` }}</TableCell>
                                    <TableCell>{{ panoramica.paciente.cedula }}</TableCell>
                                    <TableCell>
                                        <Button @click="enviarCorreo(panoramica.id)"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white">
                                            Enviar por correo
                                        </Button>
                                        <a :href="`/storage/${panoramica.archivo}`" target="_blank"
                                            class="bg-green-500 cursor-pointer mr-3 hover:bg-green-600 text-white py-2.5 px-4 rounded-lg">
                                            Ver archivo
                                        </a>
                                        <Button @click="router.delete(`/panoramicas/${panoramica.id}`)"
                                            class="bg-red-500 cursor-pointer hover:bg-red-600 text-white">
                                            Eliminar
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>

                    </div>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
