
require('./bootstrap');

import {createApp} from 'vue';


import ChatComponent from './components/ChatComponent.vue';
import ChatUserComponent from './components/ChatUserComponent.vue';
import ChatroomComponent from './components/One/ChatroomComponent.vue';


const app = createApp({});

app.component('chat',ChatComponent);
app.component('chatuser',ChatUserComponent);
app.component('Chatroom',ChatroomComponent);

app.mount('#app');