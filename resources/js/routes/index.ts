import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';

// Define your routes
const routes: Array<RouteRecordRaw> = [
    {
        path: '/plannerate',
        name: 'home',
        component: () => import('../views/Home.vue'), 
        redirect: { name: 'plannerate' },
        children: [
            {
                path: '',
                name: 'plannerate',
                component: () => import('../views/List.vue'),
            },
            {
                path: 'cadastrar',
                name: 'create',
                component: () => import('./../views/Create.vue')
            },
            {
                path: ':id/editar',
                name: 'edit',
                component: () => import('../views/Edit.vue'),
                props: true
            },
            {
                path: ':id/visualizar',
                name: 'view',
                component: () => import('../views/View.vue'),
                props: true
            }
        ]
    },

    // Add more routes as needed
];

// Create the router instance
const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes
});

// Navigation guards (optional)
router.beforeEach((to, from, next) => {
    // Add your navigation guard logic here
    next();
});

export default router;