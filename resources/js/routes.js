import Dashboard from './components/dashboard.vue';
import ListAppointments from './pages/appointments/ListAppointments.vue';
import AppointmentForm from './pages/appointments/AppointmentForm.vue';
import ListUsers from './pages/users/ListUsers.vue';
import UpdateSetting from './pages/settings/UpdateSetting.vue';
import Login from './pages/auth/Login.vue';
import UpdateProfile from './pages/profile/UpdateProfile.vue';
export default [
    {
        path : '/login',
        name : 'admin.login',
        component: Login,
    },
    
    {
        path : '/admin/dashboard',
        name : 'admin.dashboard',
        component: Dashboard,
    },
    
    {
        path : '/admin/appointments',
        name : 'admin.appointments',
        component: ListAppointments,
    },

    {
        path : '/admin/users',
        name : 'admin.users',
        component: ListUsers,
    },

    {
        path : '/admin/settings',
        name : 'admin.settings',
        component: UpdateSetting,
    },

    {
        path : '/admin/profile',
        name : 'admin.profile',
        component: UpdateProfile,
    },

    {
        path : '/admin/appointment/create',
        name : 'admin.appointments.create',
        component: AppointmentForm,
    },
    {
        path : '/admin/appointment/:id/edit',
        name : 'admin.appointment.edit',
        component: AppointmentForm,
    },
]