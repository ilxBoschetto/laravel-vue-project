<script setup>
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
import { Form, Field } from 'vee-validate';
import * as yup from 'yup';
import { useToastr } from '../../toastr';
import { debounce } from 'lodash';

const toastr = useToastr();
const users = ref([]);
const editing = ref(false);
let formValues = ref({});
const form = ref(null);
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

const search = () => {
    axios.get('/api/users/search', {
        params: {
            query: searchQuery.value,
        }
    }).then((response) => {
        users.value = response.data;
    }).catch((error) => {
        console.log(error);
    });
}

watch(searchQuery, debounce(() => {
    search();
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


const getUsers = () => {
    loadingCallback(true);
    axios.get('/api/users')
        .then((response) => {
            users.value = response.data;
            loadingCallback(false);
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
            users.value.unshift(response.data);
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
                const index = users.value.findIndex(user => user.id === id);
                users.value.splice(index, 1);
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
            const index = users.value.findIndex(user => user.id === response.data.id);
            users.value[index] = response.data;
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
                <button @click="addUser()" type="button" class="mb-2 btn btn-primary">
                    Add New User
                </button>
                <div>
                    <input type="text" v-model="searchQuery" class="form-control" placeholder="Search..." />
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <table id="users-table" class="table table-bordered">
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
                        <tbody v-if="users.length > 0">
                            <tr v-for="(user, index) in users" :key="user.id">
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
                        <tbody v-else>
                            <tr>
                                <td colspan="6" class="text-center">No result found ...</td>
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