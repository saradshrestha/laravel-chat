<!-- Not Required for chat room system -->
<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                       <span>Chat User: {{ user.name }}</span>
                       <span>Chat Room : {{room_id }}</span>
                    </div>

                    <div class="card-body" style="">
                        <div class="view-height">
                            <div v-for="message in messages" :key="message.id" class="message-body">

                                <div v-if="loggeduser.id !== message.from.id" class="left">
                                    <strong >
                                            {{  user.name  }}
                                        </strong><br>
                                    <span class="primary-font card">

                                        {{ message.message }}
                                    </span>
                                    <span v-if="message.read_at" class="seen">Seen</span>
                                    <span v-else="message.read_at" class="seen">Not Seen</span>
                                </div>
                                <div v-else class="right  mt-2">
                                    <div class="card">
                                        <span class="">
                                            {{ message.message }}
                                        </span>
                                        <span v-if="message.read_at" class="seen">Seen</span>
                                        <span v-else="message.read_at" class="seen">Not Seen</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="chat-form input-group mt-4">
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
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {

    props: {
        loggeduser: Object,
        room_id: Number,
        messages: Array,
        user: Object
    },

    setup(props) {

        const messages = ref([]);
        const newMessage = ref('');

        onMounted(() => {
            fetchMessages();

            console.log('mount');
        });

        Echo.private('message.'+props.room_id).listen('.chatroom-message', (e) => {
            console.log(e, 'Channel message');
            messages.value.push({
                message: e.message,
                id: e.id,
                from: e.from,
                read_at: e.read_at
            });

            if(props.loggeduser.id !== e.from.id){
                markMessagesAsSeen(e.id);
            }
        });

        Echo.private('message-seen.'+props.room_id).listen('.chatroom-message-seen', (e) => {
            console.log(e, 'Channel message Seen');
            console.log(messages, 'from event');

            messages.value.filter(function (message, index) {
                if (message.id === e.message_id) {
                    message.read_at = e.read_at
                }
            });
        });

        const fetchMessages = async () => {
            try {
                const res = await axios.get('/get-all-messages/'+props.room_id);
                messages.value = res.data;
                console.log(res.data,"fetchMessages");
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
                room_id : room_id
            };

            if(newMessage.value == null || newMessage.value == ''){
                alert('message is required.');
                return;
            }

            try {
                const res = await axios.post('/chat-room/send-message', user_message);
            } catch (error) {
                console.error('Error adding message:', error);
            }
            newMessage.value = '';
        };

        const markMessagesAsSeen = async (message_id) => {
            // for (let message of messages) {
                // if (!message.read_at) {
                     try {
                        const res = await axios.post('/chat-room/mark-as-seen/'+props.room_id,{
                                message_id: message_id
                            });
                        // message.read_at = new Date().toISOString(); // Optimistic update

                    } catch (error) {
                        console.error('Failed to mark message as seen:', error);
                    }
                // }
            // }
        };

        return {
            messages,
            newMessage,
            addMessage,
            fetchMessages,
            markMessagesAsSeen
        };
    }
};
</script>

<style scoped>

.message-body{
    margin: 10px;
}
.message-body .left{
    text-align: left;
    /* padding-right:10px; */
    width: 50%;

}

.seen{
    font-size: 10px;
}

.message-body .left .card{
    margin-right: auto !important;
    background: rgb(51, 174, 162);
    width: fit-content;
    padding: 2px 6px 2px 6px;
}

.message-body .left span{
    padding-right:10px;
    background: rgb(51, 174, 162);
    margin-right: auto;
}

.message-body .right{
    text-align: right;
    /* padding-right:10px; */
    width: 50%;
    /* background: red; */
    margin-left: auto;
}

.message-body .right .card{
    margin-left: auto !important;
    background: rgb(51, 174, 162);
    width: fit-content;
    padding: 2px 0px 2px 6px;
}

.message-body .right span{
    padding-right:10px;
    background: rgb(51, 174, 162);
    margin-left: auto;
}

.view-height{
    overflow-y: scroll;
    height:65vh;
}


</style>
