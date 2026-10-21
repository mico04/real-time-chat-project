<template>
    <div>
        <div class="flex flex-col justify-end h-80">
            <div ref="messagesContainer" class="p-4 overflow-y-auto max-h-fit">
                <div
                    v-for="(message, index) in messages"
                    :key="index"
                    class="flex items-center mb-2"
                >
                    <div
                        v-if="message.sender_id === currentUser"
                        class="p-2 ml-auto text-white bg-blue-500 rounded-lg"
                    >
                        {{ message.text }}
                    </div>
                    <div v-else class="p-2 mr-auto bg-gray-200 rounded-lg">
                        <div class="mb-1 text-xs font-semibold text-gray-600">
                            {{ message.sender_id }}
                        </div>
                        <div>
                            {{ message.text }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center">
            <input
                type="text"
                v-model="newMessage"
                @keydown="sendTypingEvent"
                @keyup.enter="sendMessage"
                placeholder="Type a message..."
                class="flex-1 px-2 py-1 border rounded-lg"
            />
            <button
                @click="sendMessage"
                class="px-4 py-1 ml-2 text-white bg-blue-500 rounded-lg"
            >
                Send
            </button>
        </div>
        <small v-if="isFriendTyping" class="text-gray-700">
            {{ friend }} is typing...
        </small>
    </div>
</template>

<script setup>
import axios from "axios";
axios.defaults.baseURL = 'https://real-time-chat-2j38.onrender.com';
import { nextTick, onMounted, ref, watch } from "vue";

const props = defineProps({
    // friend: {
    //     type: Object,
    //     required: true,
    // }
    currentUser: {
        type: String,
        required: true,
    },
});

const messages = ref([]);
const newMessage = ref("");
const friend = ref("");
const messagesContainer = ref(null);
const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);

watch(
    messages,
    () => {
        nextTick(() => {
            messagesContainer.value.scrollTo({
                top: messagesContainer.value.scrollHeight,
                behavior: "smooth",
            });
        });
    },
    { deep: true }
);

const sendMessage = () => {
    if (newMessage.value.trim() !== "") {
        // axios
        //     .post(`/messages/${props.friend.id}`, {
        //         message: newMessage.value,
        //     })
        //     .then((response) => {
        //         messages.value.push(response.data);
        //         newMessage.value = "";
        //     });

        axios
            .post(`/send-message`, {
                message: newMessage.value,
            })
            .then((response) => {
                // messages.value.push(response.data);
                newMessage.value = "";
            });
    }
};

const sendTypingEvent = () => {
    Echo.private(`chat.message`).whisper("typing", {
        userID: props.currentUser,
    });
};

onMounted(() => {
    axios.get(`/messages`).then((response) => {
        messages.value = response.data;
        console.log("Fetched messages:", messages.value);
    });

    Echo.private(`chat.message`)
        .listen("MessageSent", (response) => {
            messages.value.push(response.message);
        })
        .listenForWhisper("typing", (response) => {
            isFriendTyping.value = response.userID !== props.currentUser;

            if (isFriendTypingTimer.value) {
                friend.value = response.userID;
                clearTimeout(isFriendTypingTimer.value);
            }

            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 1000);
        });
});
</script>
