<script setup>
import axios from 'axios';
import { onMounted, ref, computed } from 'vue';
import { useToastr } from '../../toastr';

const toastr = useToastr();
const appointments = ref([]);
const appointmentStatus = ref([]);
const selectedStatus = ref();

const getAppointmentStatus = () => {
    axios.get('/api/appointment-status')
        .then((response) => {
            appointmentStatus.value = response.data;
        })
}
const loadingCallback = (isLoading) => {
    if (isLoading) {
        $('#spinner-wheel').removeClass('d-none');
        $('#spinner-wheel').addClass('d-flex');
    } else {
        $('#spinner-wheel').addClass('d-none');
        $('#spinner-wheel').removeClass('d-flex');
    }
}
const appointmentsCount = computed(() => {
    return appointmentStatus.value.map(status => status.count).reduce((acc, value) => acc + value, 0);
})
const getAppointments = (status) => {
    selectedStatus.value = status;
    const params = {};
    if (status) {
        params.status = status;
    }
    loadingCallback(true);
    axios.get('/api/appointments', {
        params: params
    })
        .then((response) => {
            appointments.value = response.data;
            loadingCallback(false);
        })
}
const deleteAppointment = (id) => {
    axios.delete(`/api/appointment/${id}/delete`)
        .then((response) => {
            if (response.status = 200) {
                const indexToRemove = appointments.value.data.findIndex(appointment => appointment.id === id);
                appointments.value.data.splice(indexToRemove, 1);
                toastr.success(response.data.message);
                getAppointmentStatus();
            }
        })
        .catch((error) => {
            toastr.error(error.message);
        });

}
onMounted(() => {
    getAppointments();
    getAppointmentStatus();
});
</script>

<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
                        <li class="breadcrumb-item active">Appointments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <router-link to="/admin/appointment/create">
                                        <button class="btn btn-primary"><i class="fa fa-plus-circle mr-1"></i> Add New
                                            Appointment</button>
                                    </router-link>
                                </div>
                                <div class="btn-group">
                                    <button @click="getAppointments()" type="button" class="btn"
                                        :class="[typeof selectedStatus === 'undefined' ? ' btn-secondary' : ' btn-default']">
                                        <span class="mr-1">All</span>
                                        <span class="badge badge-pill badge-info">{{ appointmentsCount }}</span>
                                    </button>

                                    <button v-for="(status, index) in appointmentStatus"
                                        @click="getAppointments(status.value)" type="button" class="btn"
                                        :class="[selectedStatus === status.value ? 'btn-secondary' : 'btn-default']">
                                        <span class="mr-1">{{ status.name }}</span>
                                        <span class="badge badge-pill" :class="`badge-${status.color}`">{{ status.count
                                        }}</span>
                                    </button>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Client Name</th>
                                                <th scope="col">StartDate</th>
                                                <th scope="col">Time</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Options</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(appointment, index) in appointments.data" :key="appointment.id">
                                                <td>{{ index + 1 }}</td>
                                                <td>{{ appointment.client.first_name }} {{ appointment.client.last_name }}
                                                </td>
                                                <td>{{ appointment.start_time }}</td>
                                                <td>{{ appointment.end_time }}</td>
                                                <td>
                                                    <span class="badge" :class="`badge-${appointment.status.color}`">{{
                                                        appointment.status.name }}</span>
                                                </td>
                                                <td>
                                                    <router-link :to="`/admin/appointment/${appointment.id}/edit`">
                                                        <i class="fa fa-edit mr-2"></i>
                                                    </router-link>

                                                    <a href="#" @click.prevent="deleteAppointment(appointment.id)">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div id="spinner-wheel" class="d-flex m-3 justify-content-center">
                                        <div class="spinner-border opacity-50" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>