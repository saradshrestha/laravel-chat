<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Chat</div>

                    <div class="card-body">
                        <div v-for="message in messages" :key="message.id" :class="{'message-received': message.user.id !== user.id, 'message-sent': message.user.id === user.id}">
                            <p>
                                <strong class="primary-font">
                                    {{ message.user.name }}
                                </strong>
                                {{ message.message }}
                            </p>
                        </div>
                        <div class="chat-form input-group">
                            <input class="form-control" type="text" v-model="newMessage" placeholder="Send Message">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" id="btn-chat" @click="addMessage">Send</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive, inject, ref, onMounted, onUpdated } from 'vue';
import axios from 'axios';

export default {
    props: ['user'],
    setup(props) {
        const messages = ref([]);
        const newMessage = ref('');

        onMounted(() => {
            fetchMessages();
        });

        onUpdated(() => {
            scrollBottom();
        });

        Echo.private('chat').listen('MessageSentEvent', (e) => {
            messages.value.push({
                id: e.message.id,
                message: e.message.message,
                user: e.user
            });
        });

        const fetchMessages = async () => {
            try {
                const res = await axios.get('/messages');
                messages.value = res.data;
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        };

        const addMessage = async () => {
            const user_message = {
                user: props.user,
                message: newMessage.value
            };

            messages.value.push(user_message);
            try {
                const res = await axios.post('/message/store', user_message);
                console.log(res.data);
            } catch (error) {
                console.error('Error adding message:', error);
            }
            newMessage.value = '';
        };

        const scrollBottom = () => {
            if (messages.value.length > 1) {
                const el = document.querySelector('.card-body');
                el.scrollTop = el.scrollHeight;
            }
        };

        return {
            messages,
            newMessage,
            addMessage,
            fetchMessages
        };
    }
};
</script>
