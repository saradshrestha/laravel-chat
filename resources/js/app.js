
require('./bootstrap');

import {createApp} from 'vue';


import ChatComponent from './components/ChatComponent.vue';

import ChatUserComponent from './components/ChatUserComponent.vue';


const app = createApp({});

app.component('chat',ChatComponent);

app.component('chatuser',ChatUserComponent);


app.mount('#app');