<script setup>
import axios from 'axios';
import { onMounted, reactive, ref } from 'vue';
import { useToastr } from '@/toastr';

const loadingRequest = ref(false);
const errors = ref(false);
const toastr = useToastr();
const changePasswordForm = reactive({
    current_password: '',
    new_password: '',
    confirm_new_password: '',
});
const form = ref({
    name: '',
    email: '',
    role: '',
});
const getUser = () => {
    axios.get('/api/profile')
        .then((response) => {
            form.value = response.data;
        })
}
const updateUserPassword = () => {
    errors.value = false;
    axios.post('/api/change-user-password', changePasswordForm)
        .then((response) => {
            toastr.success(response.data.message);
            for (const field in changePasswordForm) {
                changePasswordForm[field] = '';
            }
        })
        .catch((error) => {
            errors.value = error.response.data.errors;
        })
};
const updateUser = () => {
    loadingRequest.value = true;
    errors.value = false;
    axios.put('/api/profile', form.value)
        .then((response) => {
            toastr.success("User updated");
        })
        .catch((error) => {
            if (error.response && error.response.status === 422) {
                errors.value = error.response.data.errors;
            }
        })
        .finally(() => {
            loadingRequest.value = false;
        })
}
onMounted(() => {
    getUser();
})

</script>
<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><router-link to="/admin/dashboard">Home</router-link></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <input type="file" class="d-none">
                                <img class="profile-user-img img-circle" src="/noimage.png" alt="User profile picture">
                            </div>

                            <h3 class="profile-username text-center">{{ form.name }}</h3>

                            <p class="text-muted text-center">{{ form.role }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab"><i
                                            class="fa fa-user mr-1"></i> Edit Profile</a></li>
                                <li class="nav-item"><a class="nav-link" href="#changePassword" data-toggle="tab"><i
                                            class="fa fa-key mr-1"></i> Change
                                        Password</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="profile">
                                    <form @submit.prevent="updateUser()" class="form-horizontal">
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                            <div class="col-sm-10">
                                                <input v-model="form.name" type="text" class="form-control" id="inputName"
                                                    placeholder="Name">
                                                <span class="text-danger text-sm" v-if="errors && errors.name">{{
                                                    errors.name[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input v-model="form.email" type="email" class="form-control "
                                                    id="inputEmail" placeholder="Email">
                                                <span class="text-danger text-sm" v-if="errors && errors.email">{{
                                                    errors.email[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <div v-if="loadingRequest">
                                                    <button type="submit" class="btn btn-success"
                                                        :disabled="loadingRequest"><i class="fa fa-save mr-1"></i>
                                                        <div class="spinner-border text-light spinner-border-sm"
                                                            role="status">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                    </button>

                                                </div>
                                                <div v-else>
                                                    <button type="submit" class="btn btn-success"><i
                                                            class="fa fa-save mr-1"></i> Save
                                                        Changes</button>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane" id="changePassword">
                                    <form @submit.prevent="updateUserPassword()" class="form-horizontal">
                                        <div class="form-group row">
                                            <label for="currentPassword" class="col-sm-3 col-form-label">Current
                                                Password</label>
                                            <div class="col-sm-9">
                                                <input v-model="changePasswordForm.current_password" type="password"
                                                    class="form-control " id="currentPassword"
                                                    placeholder="Current Password">
                                                <span class="text-danger text-sm"
                                                    v-if="errors && errors.current_password">{{
                                                        errors.current_password[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="newPassword" class="col-sm-3 col-form-label">New
                                                Password</label>
                                            <div class="col-sm-9">
                                                <input v-model="changePasswordForm.new_password" type="password"
                                                    class="form-control " id="newPassword" placeholder="New Password">
                                                <span class="text-danger text-sm" v-if="errors && errors.password">{{
                                                    errors.password[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="passwordConfirmation" class="col-sm-3 col-form-label">Confirm
                                                New Password</label>
                                            <div class="col-sm-9">
                                                <input v-model="changePasswordForm.confirm_new_password" type="password"
                                                    class="form-control " id="passwordConfirmation"
                                                    placeholder="Confirm New Password">
                                                <span class="text-danger text-sm" v-if="errors && errors.password">{{
                                                    errors.password[1] }}</span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <div>
                                                    <button type="submit" class="btn btn-success"><i
                                                            class="fa fa-save mr-1"></i> Save Changes</button>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>