<script setup>
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr';
import { debounce } from 'lodash';
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

const toastr = useToastr();
const users = ref({ 'data': [] });
const editing = ref(false);
let formValues = ref({});
const form = ref(null);
const usersReady = ref(false);
const selectAll = ref(false);
const roles = ref([
    {
        name: 'ADMIN',
        value: 1,
    },
    {
        name: 'USER',
        value: 2,
    }
]);
const searchQuery = ref(null);

watch(searchQuery, debounce(() => {
    getUsers();
}), 300);

const loadingCallback = (isLoading) => {
    if (isLoading) {
        $('#spinner-wheel').removeClass('d-none');
        $('#spinner-wheel').addClass('d-flex');
    } else {
        $('#spinner-wheel').addClass('d-none');
        $('#spinner-wheel').removeClass('d-flex');
    }
}


const getUsers = (page = 1) => {
    loadingCallback(true);
    axios.get('/api/users?page=' + page,
        {
            params: {
                query: searchQuery.value,
            }
        })
        .then((response) => {
            users.value = response.data;
            loadingCallback(false);
            usersReady.value = true;
        })
        .catch(error => {
            loadingCallback(false);
        });
}
onMounted(() => {
    getUsers();
});


const createUser = (values, { setErrors }) => {
    axios.post('/api/users', values)
        .then((response) => {
            users.value.data.unshift(response.data);
            $('#userFormModal').modal('hide');
            form.value.resetForm();
            toastr.success('User created successfully!');
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }

        });
};

const createUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().required().min(8),
});

const editUserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().email().required(),
    password: yup.string().when((password, schema) => {
        return (password != "" ? schema.required().min(8) : schema);
    })
});

const addUser = () => {
    editing.value = false;
    form.value.resetForm();
    $('#userFormModal').modal('show');
}
const editUser = (user) => {
    editing.value = true;
    form.value.resetForm();
    formValues.value = {
        id: user.id,
        name: user.name,
        email: user.email,
    };
    form.value.setValues(formValues.value);
    $('#userFormModal').modal('show');
}

const deleteUser = (id) => {
    axios.delete('/api/users/' + id)
        .then((response) => {
            if (response.status = 200) {
                const index = users.value.data.findIndex(user => user.id === id);
                users.value.data.splice(index, 1);
                toastr.success('User deleted successfully!');
            }
        })
}

const handleSubmit = (values, actions) => {
    if (editing.value) {
        updateUser(values, actions);
    } else {
        createUser(values, actions);
    }
}

const updateUser = (values, { setErrors }) => {
    axios.put('/api/users/' + formValues.value.id, values)
        .then((response) => {
            const index = users.value.data.findIndex(user => user.id === response.data.id);
            users.value.data[index] = response.data;
            $('#userFormModal').modal('hide');
            form.value.resetForm();
            toastr.success('User updated successfully!');
        })
        .catch((error) => {
            if (error.response.data.errors) {
                setErrors(error.response.data.errors);
            }
        });
}

const changeRole = (user, role) => {
    axios.patch('/api/users/' + user.id + '/change-role', {
        role: role,
    })
        .then(() => {
            toastr.success('Role updated successfully!');
        })
}

const selectedUsers = ref([]);

const toggleUser = (user) => {
    const index = selectedUsers.value.indexOf(user.id);
    if (index === -1) {
        selectedUsers.value.push(user.id);
    } else {
        selectedUsers.value.splice(index, 1);
    }
    console.log(selectedUsers.value);
};

const bulkDelete = () => {
    axios.delete('/api/users', {
        data: {
            ids: selectedUsers.value,
        }
    }).then((response) => {
        if (response.status === 200) {
            users.value.data = users.value.data.filter(user => !selectedUsers.value.includes(user.id));
            selectedUsers.value = [];
            selectAll.value = false;
            toastr.success(response.data.message);
        }

    })
}


const selectAllUsers = () => {
    if (selectAll.value == true) {
        selectedUsers.value = users.value.data.map(user => user.id);
    } else {
        selectedUsers.value = [];
    }
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
            <div class="d-flex justify-content-between">
                <div class="d-flex ">
                    <button @click="addUser()" type="button" class="mb-2 btn btn-primary">
                        <i class="fa fa-plus-circle mr-1"></i>
                        Add New User
                    </button>
                    <div>
                        <button v-if="selectedUsers.length > 0" @click="bulkDelete()" type="button"
                            class="ml-2 mb-2 btn btn-danger">
                            <i class="fa fa-minus-circle mr-1"></i>
                            Delete Selected
                        </button>
                        <span style="text-decoration:underline" class="ml-2 mb-2" v-if="selectedUsers.length > 0">Selected
                            {{
                                selectedUsers.length }}
                            users</span>
                    </div>

                </div>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table id="users-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th><input type="checkbox" v-model="selectAll" @change="selectAllUsers"
                                        :select-all="selectAll" /></th>
                                <th style="width: 10px">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered Date</th>
                                <th>Role</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody v-if="users.data.length > 0">
                            <tr v-for="(user, index) in users.data" :key="user.id">
                                <td><input type="checkbox" @click="toggleUser(user)" :checked="selectAll" /></td>
                                <td>{{ index + 1 }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ new Date(user.created_at).toLocaleDateString() }} - {{ new
                                    Date(user.created_at).toLocaleTimeString() }}</td>
                                <td>
                                    <select class="form-control" @change="changeRole(user, $event.target.value)">
                                        <option v-for="role in roles" :value="role.value"
                                            :selected="(user.role === role.name)">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <a href="#" class="m-1" @click.prevent="editUser(user)">
                                        <i class="fa fa-edit">
                                        </i>
                                    </a>
                                    <a href="#" class="m-1 text-danger" @click.prevent="deleteUser(user.id)">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else-if="usersReady">
                            <tr>
                                <td colspan="7" class="text-center">No result found ...</td>
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
            <Bootstrap4Pagination :data="users" @pagination-change-page="getUsers" />
        </div>
    </div>
    <!-- The Modal -->
    <div class="modal fade" id="userFormModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">
                        <span v-if="editing">Edit User</span>
                        <span v-else>Insert User</span>
                    </h4>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Form to insert Name, Email, and Password -->
                    <Form ref="form" @submit="handleSubmit" :validation-schema="editing ? editUserSchema : createUserSchema"
                        v-slot="{ errors }">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <Field name="name" type="text" class="form-control" id="name"
                                :class="{ 'is-invalid': errors.name }" placeholder="Enter your name" />
                            <span class="invalid-feedback">{{ errors.name }}</span>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <Field name="email" type="email" class="form-control" :class="{ 'is-invalid': errors.email }"
                                id="email" placeholder="Enter your email" />
                            <span class="invalid-feedback">{{ errors.email }}</span>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <Field name="password" type="password" class="form-control"
                                :class="{ 'is-invalid': errors.password }" id="password"
                                placeholder="Enter your password" />
                            <span class="invalid-feedback">{{ errors.password }}</span>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </div>
</template>