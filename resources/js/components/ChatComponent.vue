<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Chat </div>

                    <div class="card-body">
                        <template v-for="message in messages">
                            <div class="message message-received">
                                <p>
                                <strong class="primary-font">
                                    {{ message.user.name }}
                                </strong>
                                {{ message.message }}
                            </p>

                            </div>
                            <div class="message message-send">
                                <p>
                                <strong class="primary-font">
                                    {{ message.user.name }}
                                </strong>
                                {{ message.message }}
                            </p>

                            </div>
                            
                        </template>
                        <div class="chat-form input-group">
                            <input class="form-control" type="text" name="message" placeholder="Send Message">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" id="btn-chat"
                                    @click="addMessage">Send</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { reactive,inject,ref,onMounted,onUpdated } from 'vue';
    import axios from 'axios';
    export default {
        props:['user'],
        setup(props){
            let message = ref([])
            let newMessage = ref('')
            let hasScrolledToBottom = ref('')
            onMounted(() => {
                fetchMessages()
            })

            onUpdated(() => {
                scrollBottom()
            })

            Echo.private('chat').listen('MessageSentEvent',(e)=>{
                message.value.push({
                    message: e.message.message,
                    user: e.user
                });
            })

            const fetchMessages = async()=>{
                axios.get('/messages').then(res => {
                    message.value = res.data;
                });
            }

            const addMessage = async()=>{
                let user_message = {
                    user: props.user,
                    message: newMessage.value
                }
                
                message.value.push(user_message);
                axios.post('/message/store',user_message).then(res => {
                    console.log(response.data);
                });
                newMessage.value=''
            }

            const scrollBottom= () => {
                if(messages.value.length > 1){
                    let el = hasScrolledToBottom.value;
                    el.scrollTop = el.scrolHeight;
                }
            }
            
            return {
                messages,
                newMessage,
                addMessage,
                fetchMessages,
                hasScrolledToBottom

            }
        }
       
    }
</script>
