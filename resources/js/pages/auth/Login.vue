<script setup>
import axios from 'axios';
import { reactive, ref } from 'vue';

const errorMessage = ref('');
const loadingRequest = ref(false);


const form = reactive({
    email: '',
    password: '',
});
const handleSubmit = () => {
    loadingRequest.value = true;
    errorMessage.value = '';
    axios.post('/login', form)
        .then(() => {
            window.location.href = "/admin/dashboard";
        })
        .catch((error) => {
            errorMessage.value = error.response.data.message;
        })
        .finally(() => {
            loadingRequest.value = false;
        })
}
</script>
<template>
    <div class="login-box">

        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>Admin</b>Login</a>
            </div>
            <div class="card-body">
                <div v-if="errorMessage" class="alert alert-danger" role="alert">
                    {{ errorMessage }}
                </div>
                <p class="login-box-msg">Sign in to start your session</p>
                <form @submit.prevent="handleSubmit">
                    <div class="input-group mb-3">
                        <input type="email" v-model="form.email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" v-model="form.password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember" class="ml-1">
                                    Remember Me
                                </label>
                            </div>
                        </div>

                        <div class="col-4">
                            <button type="submit" id="signInButton" class="btn btn-primary btn-block"
                                :disabled="loadingRequest">
                                <div v-if="loadingRequest">
                                    <div class="spinner-border text-light spinner-border-sm" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>
                                <span v-else>
                                    Sign In
                                </span>
                            </button>
                        </div>

                    </div>
                </form>

                <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p>
            </div>

        </div>
    </div>
</template>