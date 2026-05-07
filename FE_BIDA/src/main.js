import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Default from './layout/wrapper/index.vue'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import * as bootstrap from 'bootstrap';
import './style.css'

const app = createApp(App)

app.use(router)
app.component("default-layout", Default);

app.mount("#app")