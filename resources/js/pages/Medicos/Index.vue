<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button'
import { ref, computed } from 'vue'
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from '@/components/ui/table'


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Medicos',
        href: '/medicos',
    },
];

const props = defineProps<{
    medicos: any[];
}>();


const registrar = () => {
    router.get('/medicos/create');
};

const search = ref('')
const sortKey = ref('id')
const sortAsc = ref(true)

const filteredMedicos = computed(() => {
    return [...props.medicos.data]
        .filter(medico => medico.cedula.toLowerCase().includes(search.value.toLowerCase()))
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

    <Head title="Medicos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-4 grid-cols-2">
                <div class="md:col-span-3 col-span-1">
                    <h3 class="text-3xl font-semibold text-white">
                        Medicos
                    </h3>
                </div>
                <div class="col-span-1 md:col-span-1">
                    <Button @click="registrar" class="w-full cursor-pointer text-md">Registrar Medico</Button>
                </div>
            </div>
            <template v-if="props.medicos.data.length === 0">
                <div class="py-4 px-4 w-full bg-zinc-800 rounded-2xl text-xl">No hay registros de medicos</div>
            </template>
            <template v-else>
                <div
                    class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <div class="p-4">

                        <input v-model="search" type="text" placeholder="Buscar por cedula..."
                            class="border p-2 mb-4 w-full rounded-md" />

                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="cursor-pointer" @click="toggleSort('id')">ID</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('nombre')">Nombre</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('cedula')">Cedula</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('apellido')">Especialidad
                                    </TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('cedula')">Correo</TableHead>
                                    <TableHead class="cursor-pointer" @click="toggleSort('telefono')">telefono
                                    </TableHead>
                                    <TableHead class="cursor-pointer">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="medico in filteredMedicos" :key="medico.id">
                                    <TableCell>{{ medico.id }}</TableCell>
                                    <TableCell>{{ medico.nombre }}</TableCell>
                                    <TableCell>{{ medico.cedula }}</TableCell>
                                    <TableCell>{{ medico.especialidad }}</TableCell>
                                    <TableCell>{{ medico.correo }}</TableCell>
                                    <TableCell>{{ medico.telefono }}</TableCell>
                                    <TableCell>
                                        <Button @click="router.get(`/medicos/${medico.id}`)"
                                            class="bg-blue-500 cursor-pointer mr-3 hover:bg-blue-600 text-white">Editar</Button>
                                        <Button @click="router.delete(`/medicos/${medico.id}`)"
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
