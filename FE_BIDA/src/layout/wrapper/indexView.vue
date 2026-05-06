<template>
    <div class="user-layout">
        <TopView></TopView>
        <div class="user-body">
            <div class="user-main">
                <!-- Hiển thị danh sách bàn / thực đơn -->
                <router-view v-slot="{ Component }">
                    <component 
                        :is="Component" 
                        ref="mainView"
                        @select-ban="onSelectBan"
                        @order-added="onOrderAdded"
                        :selected-ban="selectedBan"
                    />
                </router-view>
            </div>
            <div class="user-panel" :class="{ 'panel-active': selectedBan }">
                <div v-if="selectedBan" class="user-panel-inner">
                    <router-view
                        name="detail"
                        :selected-ban="selectedBan"
                        @reload-ban="onReloadBan"
                    ></router-view>
                </div>
                <div v-else class="user-panel-empty">
                    <i class="fa-solid fa-receipt" style="font-size: 3rem; color: var(--natural-muted); opacity: 0.1;"></i>
                    <p class="serif" style="font-style: italic; font-size: 1.2rem; color: var(--natural-muted); margin-top: 16px;">Chọn một bàn để xem chi tiết</p>
                </div>
            </div>
        </div>
        
        <ChatBox></ChatBox>
    </div>
</template>

<script>

import TopView from '../components/TopView.vue';
import ChatBox from '../../components/ChatBox.vue';

export default {
    name: "UserLayout",
    data() {
        return {
            selectedBan: null
        }
    },
    methods: {
        onSelectBan(ban) {
            this.selectedBan = { ...ban };
        },
        onOrderAdded() {
            if (this.selectedBan) {
                this.selectedBan = { ...this.selectedBan, _refresh: Date.now() };
            }
        },
        // Sau khi thanh toán xong → reset panel + reload danh sách bàn
        onReloadBan() {
            this.selectedBan = null;
            // Gọi hàm refresh tương ứng với component con (Bàn hoặc Thực đơn)
            if (this.$refs.mainView) {
                if (typeof this.$refs.mainView.getBanData === 'function') {
                    this.$refs.mainView.getBanData();
                }
                if (typeof this.$refs.mainView.getActiveHoaDon === 'function') {
                    // Nếu ở trang Thực đơn thì tải lại ds hóa đơn
                    this.$refs.mainView.getActiveHoaDon();
                }
            }
        }
    },
    components: {
        TopView, ChatBox
    }
}
</script>

<style scoped>
.user-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: var(--natural-bg);
}

.user-body {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.user-main {
    flex: 1;
    overflow-y: auto;
    padding: 28px;
    min-width: 0;
}

.user-panel {
    width: 420px;
    flex-shrink: 0;
    background-color: var(--natural-surface);
    border-left: 1px solid var(--natural-border);
    overflow-y: auto;
    box-shadow: -4px 0 20px rgba(0,0,0,0.03);
    transition: all var(--transition-normal);
}

.user-panel-inner {
    height: 100%;
}

.user-panel-empty {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
}

@media (max-width: 992px) {
    .user-body {
        flex-direction: column;
    }
    .user-panel {
        width: 100%;
        border-left: none;
        border-top: 1px solid var(--natural-border);
        max-height: 50vh;
    }
}
</style>