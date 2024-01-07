<script setup>
import axios from 'axios';
import { ref, onMounted, reactive } from 'vue';

const users = ref([]);
const form = reactive({
    name: '',
    email: '',
    password: '',
});
const getUsers = () => {
    axios.get('/api/users')
        .then((response) => {
            users.value = response.data;
        })
        .catch(error => console.log(error));
}
onMounted(() => {
    getUsers();
});

const createUser = () => {
    axios.post('/api/users', form)
        .then((response) => {
            users.value.unshift(response.data);
            form.name = "";
            form.email = "";
            form.password = "";
            $('#createUserModal').modal('hide');
        });
}
</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Users</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Users</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <button type="button" class="mb-2 btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                Add New User
            </button>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.created_at }}</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- The Modal -->
    <div class="modal fade" id="createUserModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Insert Information</h4>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Form to insert Name, Email, and Password -->
                    <form>
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input v-model="form.name" type="text" class="form-control" id="name"
                                placeholder="Enter your name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input v-model="form.email" type="email" class="form-control" id="email"
                                placeholder="Enter your email">
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input v-model="form.password" type="password" class="form-control" id="password"
                                placeholder="Enter your password">
                        </div>

                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" @click="createUser" class="btn btn-primary">Save</button>
                </div>

            </div>
        </div>
    </div>
</template>