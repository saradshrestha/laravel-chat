<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Chat User</div>

                    <div class="card-body">
                        <div v-for="user in users" :key="user.id" >
                            <p>
                                <a :href="'chat/' + user.id" class="primary-font">
                                    {{user.name }}
                                </a>
                               
                            </p>
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
