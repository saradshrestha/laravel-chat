<!-- Not Required for chat room system -->
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                       <span>Chat User: {{ user.name }}</span> 
                       <span>Chat Room : {{room_id }}</span>
                       {{ authUser.name }}
                    </div>

                    <div class="card-body">
                        <div v-for="message in messages" :key="message.id" class="message-body">
                            <p v-if="message.from.id == authUser.id" class="right">
                                <strong class="primary-font">
                                    {{ message.from.name }}
                                </strong><br>
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
    props: ['user','room_id','message','authUser'],

    setup(props) {
        const isActive = ref(false);

        const messages = ref([]);
        const newMessage = ref('');
        
        console.log(props.authUser);
        onMounted(() => {
            fetchMessages();
        });

        Echo.private('message.'+props.room_id).listen('.chatroom-message', (e) => {
            console.log(e, 'Chages');
            messages.value.push({
                id: e.id,
                message: e.message,

                // $user,$id,$fromId,$status
                user: e.name
            });
        });
      

        const fetchMessages = async () => {
            try {
                const res = await axios.get('/get-all-messages/'+props.room_id);
               
                messages.value = res.data;
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        };



        const addMessage = async () => {
            const user_id = props.user.id;
            const room_id = props.room_id;
            const user_message = {
                message: newMessage.value,
                to_user_id:  user_id,
                // status: 1,
                room_id : room_id              
            };
            try {
                const res = await axios.post('/chat-room/send-message', user_message);
                messages.value.push(user_message);
                // console.log(res.data);
            } catch (error) {
                console.error('Error adding message:', error);
            }
            newMessage.value = '';
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

<style scoped>
.message-body p.left{
    text-align: left;
} 

.message-body p.right{
    text-align: right;
} 
</style>
