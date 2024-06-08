<script setup>
import { defineProps, ref, watch, onMounted } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DeleteButton from '@/Components/DeleteButton.vue'
import { useForm } from '@inertiajs/vue3'
import Chart from 'chart.js/auto'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    chartData: Object,
    filters: Object,
    links: Object,
    startDate: {
        type: String,
    },
    endDate: {
        type: String,
    },
    chart: {
        type: Object,
        default: null,
    },
    keyword: {
        type: Object
    },
    lastMonths: {
        type: Object
    },
    selectedMonth: {
        type: Object
    }
})

const { props: pageProps } = usePage()

var form = useForm({
    start_date: props.startDate,
    end_date: props.endDate,
})

const chartRef = ref(null)
const chartInstance = ref(null)
const selectedMonth = ref(null)

const fetchData = () => {
    form.post(
        route('keyword-positions.report', {
            keyword_id: props.keyword.id,
            start_date: form.start_date,
            end_date: form.end_date,
        }),
        {
            preserveScroll: true,
            onSuccess: () => {
                renderChart()
            },
            onError: (errors) => {
                // Hata durumunda uyarı göster
                alert('Bir hata oluştu: ' + errors)
            },
            onFinish: () => form.reset(),
        },
    )
}

const setDateRange = () => {

    form.start_date = selectedMonth.value.startDate;
    form.end_date =  selectedMonth.value.endDate;

    fetchData();
}

const renderChart = () => {
    if (chartInstance.value) {
        chartInstance.value.destroy()
    }

    chartInstance.value = new Chart(chartRef.value.getContext('2d'), {
        type: 'line',
        data: {
            labels: props.chartData.labels,
            datasets: props.chartData.datasets,
        },
        options: {
            
            responsive: true,
             maintainAspectRatio: false,
        },
    })
}

onMounted(() => {
    fetchData()
})
</script>

<template>
    <Head title="Keyword Positions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Keyword Position Report: {{ keyword.keyword }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="">
                        <div>
                            <div class="p-4">
                                <div class="grid grid-cols-4 gap-5">
                                    <div>
                                        <InputLabel
                                            for="start_date"
                                            value="Start date"
                                        />
                                        <TextInput
                                            id="start_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.start_date"
                                            required
                                            autofocus
                                            autocomplete="start_date"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel
                                            for="end_date"
                                            value="End date"
                                        />
                                        <TextInput
                                            id="end_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.end_date"
                                            required
                                            autofocus
                                            autocomplete="end_date"
                                        />
                                    </div>
                                    <div>
                                        <InputLabel
                                            for="month_select"
                                            value="Select Month"
                                        />
                                        <select
                                            id="month_select"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            v-model="selectedMonth"
                                            @change="setDateRange"
                                        >
                                            <option value="">
                                                Select Month
                                            </option>
                                            <option
                                                v-for="(
                                                    month, index
                                                ) in lastMonths"
                                                :key="index"
                                                :value="month"
                                            >
                                                {{ month.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <PrimaryButton
                                            @click="fetchData"
                                            class="mt-7"
                                        >
                                            Fetch Data
                                        </PrimaryButton>
                                    </div>
                                </div>

                                <div style="height: 400px">
                                    <canvas ref="chartRef"></canvas>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
                        >
                            <Link
                                :href="route('keywords.index')"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded"
                                >Return</Link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>