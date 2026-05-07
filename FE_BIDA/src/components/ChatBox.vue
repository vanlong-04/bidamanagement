<template>
    <div class="bic-container" :class="{ 'bic-closed': !isOpen }">
        <!-- Toggle Button -->
        <button class="bic-toggle" @click="toggleChat">
            <i class="fa-solid fa-comments"></i>
            <span v-if="unreadCount > 0" class="bic-unread-badge">{{ unreadCount }}</span>
        </button>

        <!-- Chat Window -->
        <div v-if="isOpen" class="bic-window card-organic">
            <div class="bic-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div class="bic-online-dot"></div>
                    <h5 class="serif" style="margin: 0; color: white; font-style: italic; font-size: 1rem;">Chat Nội Bộ</h5>
                </div>
                <button class="bic-close-btn" @click="toggleChat">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="bic-messages" ref="messageBox">
                <div v-if="messages.length === 0" class="bic-empty">
                    <i class="fa-solid fa-message-dots" style="font-size: 2rem; opacity: 0.1; margin-bottom: 10px;"></i>
                    <p style="font-size: 11px; opacity: 0.5;">Chưa có tin nhắn nào</p>
                </div>
                <div v-for="msg in messages" :key="msg.id" :class="['bic-msg-wrap', { 'bic-msg-mine': msg.sender_id === currentUser.nhan_vien_id }]">
                    <div class="bic-msg-content">
                        <p class="bic-msg-sender">{{ msg.sender?.ho_va_ten || 'Hệ thống' }}</p>
                        <div class="bic-msg-bubble">
                            {{ msg.message }}
                        </div>
                        <p class="bic-msg-time">{{ formatTime(msg.created_at) }}</p>
                    </div>
                </div>
            </div>

            <div class="bic-footer">
                <div class="bic-input-wrap">
                    <input type="text" 
                        class="bic-input" 
                        v-model="newMessage" 
                        @keyup.enter="send" 
                        placeholder="Nhập nội dung...">
                    <button class="bic-btn-send" @click="send" :disabled="!newMessage.trim()">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            isOpen: false,
            messages: [],
            newMessage: '',
            currentUser: {},
            unreadCount: 0,
            pollTimer: null
        }
    },
    mounted() {
        const userData = localStorage.getItem('user') || sessionStorage.getItem('user');
        if (userData) {
            this.currentUser = JSON.parse(userData);
            this.fetchMessages();
            this.pollTimer = setInterval(this.fetchMessages, 4000); 
        }
    },
    beforeUnmount() {
        if (this.pollTimer) clearInterval(this.pollTimer);
    },
    methods: {
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unreadCount = 0;
                setTimeout(this.scrollToBottom, 100);
            }
        },
        fetchMessages() {
            axios.get('http://127.0.0.1:8000/api/admin/chat/get-messages')
                .then(res => {
                    const newMsgs = res.data.data || [];
                    if (this.isOpen) {
                        this.messages = newMsgs;
                        this.$nextTick(this.scrollToBottom);
                    } else if (newMsgs.length > this.messages.length) {
                        this.unreadCount += (newMsgs.length - this.messages.length);
                        this.messages = newMsgs;
                    }
                });
        },
        send() {
            if (!this.newMessage.trim()) return;
            const payload = {
                sender_id: this.currentUser.nhan_vien_id,
                receiver_id: 0,
                message: this.newMessage
            };
            axios.post('http://127.0.0.1:8000/api/admin/chat/send', payload)
                .then(() => {
                    this.newMessage = '';
                    this.fetchMessages();
                });
        },
        formatTime(dt) {
            return new Date(dt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        },
        scrollToBottom() {
            const box = this.$refs.messageBox;
            if (box) box.scrollTop = box.scrollHeight;
        }
    }
}
</script>

<style scoped>
.bic-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 10000;
}

.bic-toggle {
    width: 56px;
    height: 56px;
    border-radius: 28px;
    background: var(--natural-primary);
    color: white;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    transition: all 0.3s ease;
}

.bic-toggle:hover { transform: scale(1.05); background: #3a4a3e; }

.bic-unread-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ff4d4f;
    color: white;
    font-size: 10px;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    font-weight: bold;
}

.bic-window {
    position: absolute;
    bottom: 72px;
    right: 0;
    width: 320px;
    height: 450px;
    background: white;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 12px 48px rgba(0,0,0,0.15);
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.05);
}

.bic-header {
    background: var(--natural-primary);
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.bic-online-dot {
    width: 8px;
    height: 8px;
    background: #52c41a;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(82,196,26,0.2);
}

.bic-close-btn {
    background: none;
    border: none;
    color: white;
    opacity: 0.7;
    cursor: pointer;
    font-size: 18px;
}

.bic-close-btn:hover { opacity: 1; }

.bic-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #fdfdfd;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.bic-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--natural-muted);
}

.bic-msg-wrap { display: flex; flex-direction: column; }
.bic-msg-mine { align-items: flex-end; }

.bic-msg-sender {
    font-size: 10px;
    font-weight: 600;
    color: #8c8c8c;
    margin: 0 0 4px 4px;
}
.bic-msg-mine .bic-msg-sender { margin: 0 4px 4px 0; }

.bic-msg-bubble {
    padding: 8px 14px;
    border-radius: 14px;
    font-size: 13px;
    max-width: 85%;
    line-height: 1.4;
    word-break: break-word;
}

.bic-msg-wrap:not(.bic-msg-mine) .bic-msg-bubble {
    background: #f0f0f0;
    color: #262626;
    border-bottom-left-radius: 2px;
}

.bic-msg-mine .bic-msg-bubble {
    background: var(--natural-primary);
    color: white;
    border-bottom-right-radius: 2px;
}

.bic-msg-time {
    font-size: 9px;
    color: #bfbfbf;
    margin-top: 4px;
}

.bic-footer {
    padding: 12px;
    border-top: 1px solid #f0f0f0;
    background: white;
}

.bic-input-wrap {
    display: flex;
    gap: 8px;
    background: #f5f5f5;
    padding: 4px;
    border-radius: 8px;
}

.bic-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 8px 12px;
    font-size: 13px;
    outline: none;
}

.bic-btn-send {
    background: var(--natural-primary);
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s;
}

.bic-btn-send:disabled { opacity: 0.3; cursor: not-allowed; }
</style>
