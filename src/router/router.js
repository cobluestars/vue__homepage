import { createWebHistory, createRouter } from "vue-router";
import Home from "@/components/Home"
import About from "@/components/About"
import MyUmzzal from "@/components/MyUmzzal"


const routes = [
    { path : "/", name : "Home", component : Home },
    { path : "/About", name : "About", component : About },
    { path : "/MyUmzzal", name : "MyUmzzal", component : MyUmzzal },
]

const router = createRouter({
    history : createWebHistory(),
    routes : routes
});

export default router;