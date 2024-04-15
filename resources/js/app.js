
require('./bootstrap');

import {createApp} from 'vue';


import ChatComponent from './components/ChatComponent.vue';

const app = createApp({});

app.component('chat',ChatComponent);

app.mount('#app');