<template>
    <div class="admin-layout">
        <MenuRocker></MenuRocker>
        <div class="admin-main">
            <div class="admin-content">
                <router-view> </router-view>
            </div>
        </div>
        <BotRocker></BotRocker>
        <ChatBox></ChatBox>
    </div>
</template>
<script>
import BotRocker from "../components/BotRocker.vue";
import MenuRocker from "../components/MenuRocker.vue";
import ChatBox from "../../components/ChatBox.vue";

export default {
    name        :   "app",
    components  :   {
       MenuRocker, BotRocker, ChatBox
    }
}
</script>
<style>
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");

.admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--natural-bg);
}

.admin-main {
    flex: 1;
    margin-left: 280px;
    overflow-y: auto;
    min-height: 100vh;
}

.admin-content {
    padding: 40px;
    max-width: 1400px;
}
</style>