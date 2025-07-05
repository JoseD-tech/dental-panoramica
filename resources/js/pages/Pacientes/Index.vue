<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: '/pacientes',
    },
];

const props = defineProps<{
    pacientes: any[];
}>();

const registrar = () => {
    router.get('/pacientes/create');
};

const search = ref('')
const sortKey = ref('id')
const sortAsc = ref(true)

const filteredPacientes = computed(() => {
    return [...props.pacientes.data]
        .filter(paciente => paciente.cedula.includes(search.value))
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
</script>

<template>
    <Head title="Pacientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="col-span-3">
                    <h3 class="text-3xl font-semibold dark:text-white">
                        Pacientes
                    </h3>
                </div>
                <div class="">
                    <Button @click="registrar" class="w-full cursor-pointer text-md">Registrar Paciente</Button>
                </div>
            </div>

            <template v-if="props.pacientes.data.length === 0">
                <div class="py-4 px-4 w-full bg-zinc-800 rounded-2xl text-xl">
                    No hay registros de pacientes.
                </div>
            </template>

            <template v-else>
                <div
                    class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div class="p-4">

                        <input v-model="search" type="number" placeholder="Buscar por cédula…"
                            class="border p-2 mb-4 w-full rounded-md" />

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="cursor-pointer" @click="toggleSort('id')">ID</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('name')">Nombre</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('apellido')">Apellido</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('cedula')">Cédula</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('telefono')">Teléfono</TableHead>
                                    <TableHead class="cursor-pointer">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="paciente in filteredPacientes" :key="paciente.id">
                                    <TableCell>{{ paciente.id }}</TableCell>
                                    <TableCell>{{ paciente.nombre }}</TableCell>
                                    <TableCell>{{ paciente.apellido }}</TableCell>
                                    <TableCell>{{ paciente.cedula }}</TableCell>
                                    <TableCell>{{ paciente.telefono }}</TableCell>
                                    <TableCell>
                                        <Button @click="router.get(`/pacientes/${paciente.id}`)"
                                            class="bg-blue-500 cursor-pointer mr-3 hover:bg-blue-600 text-white">Editar</Button>
                                        <Button @click="router.delete(`/pacientes/${paciente.id}`)"
                                            class="bg-red-500 cursor-pointer hover:bg-red-600 text-white">Eliminar</Button>
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
