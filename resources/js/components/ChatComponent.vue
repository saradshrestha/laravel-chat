<!-- Required for chat room system as well as for global -->
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Chat User</div>

                    <div class="card-body">
                        <div v-for="user in users" :key="user.id">
                            <div v-if="user.chat_rooms && user.chat_rooms.length > 0">
                                <div v-for="room in user.chat_rooms" :key="room.id">
                                    <a :href="'chat-room/' + user.id + '/' + room.id" class="primary-font">
                                        <span>{{ room.title }}</span>
                                        <span v-if="room.unread_messages_count > 0">{{ room.unread_messages_count }}</span>
                                    </a>
                                </div>
                            </div>
                            <div v-else>
                                <a :href="'chat-room/create/' + user.id" class="primary-font">
                                    <h5>{{ user.name }}</h5> <!-- Assuming user has a 'name' property -->
                                </a>
                            </div>
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
    // props: ['user'],
    setup(props) {
        const users = ref([]);
        // const newMessage = ref('');

        onMounted(() => {
            fetchUsers();
        });

        const fetchUsers = async () => {
            try {
                const res = await axios.get('/getUsers');
                users.value = res.data;
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        };



        return {
            users,
            fetchUsers
        };
    }
};
</script>
