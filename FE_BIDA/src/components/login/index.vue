<template>
    <div class="login-page">
        <div class="login-container">
            <div class="login-brand">
                <h1 class="login-title serif">Bida Vip</h1>
                <p class="login-subtitle label-xs" style="letter-spacing: 0.3em;">Hệ Thống Quản Lý Chuyên Nghiệp</p>
            </div>

            <div class="login-card card-organic">
                <form @submit.prevent>
                    <div class="login-field">
                        <label class="label-xs" style="margin-left: 8px;">Tên đăng nhập</label>
                        <input type="text" class="input-organic" v-model="username" placeholder="admin / nhanvien" required>
                    </div>

                    <div class="login-field">
                        <label class="label-xs" style="margin-left: 8px;">Mật khẩu</label>
                        <input type="password" class="input-organic" v-model="password" placeholder="••••••••" required>
                    </div>

                    <div class="login-options">
                        <label class="login-remember">
                            <input type="checkbox" v-model="remember">
                            <span>Ghi nhớ</span>
                        </label>
                        <a href="#" class="login-forgot">Quên mật khẩu?</a>
                    </div>

                    <div v-if="error" class="login-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        {{ error }}
                    </div>

                    <div class="login-actions">
                        <button type="button" class="login-role-btn login-role-admin" @click="handleLogin('admin')" :disabled="loading">
                            <span v-if="loadingType==='admin' && loading" class="spinner-border spinner-border-sm"></span>
                            <i v-else class="fa-solid fa-shield-halved login-role-icon"></i>
                            <span class="login-role-label">Quản lý</span>
                        </button>
                        <button type="button" class="login-role-btn login-role-staff" @click="handleLogin('user')" :disabled="loading">
                            <span v-if="loadingType==='user' && loading" class="spinner-border spinner-border-sm"></span>
                            <i v-else class="fa-solid fa-user login-role-icon"></i>
                            <span class="login-role-label">NHÂN VIÊN</span>
                        </button>
                    </div>
                </form>

                <div class="login-footer">
                    <p class="label-xs" style="letter-spacing: 0.2em;">© 2025 BIDA VIP System</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'LoginPage',
    data() {
        return {
            username: '',
            password: '',
            error: '',
            loading: false,
            loadingType: '',
            remember: false
        }
    },
    methods: {
        async handleLogin(type) {
            this.error = '';
            this.loading = true;
            this.loadingType = type;
            try {
                const res = await axios.post('http://127.0.0.1:8000/api/login', {
                    username: this.username,
                    password: this.password
                });
                if (res.data.status === 1) {
                    const userData = res.data.data;
                    if (this.remember) {
                        localStorage.setItem('user', JSON.stringify(userData));
                    } else {
                        sessionStorage.setItem('user', JSON.stringify(userData));
                    }
                    if (type === 'admin') {
                        this.$router.push('/admin/dashboard');
                    } else {
                        this.$router.push('/user/dat-ban');
                    }
                } else {
                    this.error = res.data.message || 'Đăng nhập thất bại';
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Đăng nhập thất bại';
            } finally {
                this.loading = false;
                this.loadingType = '';
            }
        }
    }
}
</script>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: url('@/assets/images/bg-login.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
}
.login-page::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4); /* Overlay mờ để nổi bật form */
}
.login-container {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 440px;
}
.login-card {
    backdrop-filter: blur(15px);
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 40px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    color: white;
}
.login-title {
    color: white !important;
    text-shadow: 0 4px 15px rgba(0,0,0,0.8);
    font-weight: 700;
}
.login-subtitle {
    color: rgba(255,255,255,0.9) !important;
    text-shadow: 0 2px 5px rgba(0,0,0,0.5);
}
.login-field label {
    color: rgba(255,255,255,0.9) !important;
}
.input-organic {
    background: rgba(255,255,255,0.1) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    color: white !important;
}
.input-organic::placeholder {
    color: rgba(255,255,255,0.5) !important;
}
.login-remember span, .login-forgot {
    color: rgba(255,255,255,0.8) !important;
}

.login-brand {
    text-align: center;
    margin-bottom: 48px;
}

.login-title {
    font-size: 4.5rem;
    font-weight: 300;
    color: var(--natural-primary);
    letter-spacing: -0.02em;
    margin-bottom: 8px;
    line-height: 1;
}

.login-subtitle {
    margin: 0;
}

.login-card {
    padding: 40px;
    box-shadow: var(--shadow-xl);
}

.login-field {
    margin-bottom: 24px;
}

.login-field label {
    display: block;
    margin-bottom: 10px;
}

.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    font-size: 13px;
}

.login-remember {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--natural-muted);
    cursor: pointer;
}

.login-remember input[type="checkbox"] {
    accent-color: var(--natural-primary);
    width: 16px;
    height: 16px;
}

.login-forgot {
    color: var(--natural-muted);
    text-decoration: none;
    transition: color var(--transition-fast);
}

.login-forgot:hover {
    color: var(--natural-primary);
}

.login-error {
    background-color: rgba(239, 68, 68, 0.05);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #dc2626;
    padding: 12px 18px;
    border-radius: var(--radius-md);
    margin-bottom: 16px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.login-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 24px;
}

.login-role-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 14px;
    padding: 28px 16px;
    border-radius: var(--radius-lg);
    border: 1px solid transparent;
    cursor: pointer;
    transition: all var(--transition-normal);
    font-family: var(--font-sans);
}

.login-role-btn:active {
    transform: scale(0.95);
}

.login-role-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.login-role-icon {
    font-size: 28px;
    transition: all var(--transition-normal);
}

.login-role-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
}

.login-role-admin {
    background-color: rgba(250, 250, 249, 1);
    border-color: rgba(214, 211, 209, 1);
    color: var(--natural-muted);
}

.login-role-admin:hover {
    background-color: rgba(245, 245, 244, 1);
    color: var(--natural-primary);
    border-color: var(--natural-primary);
}

.login-role-admin:hover .login-role-icon {
    color: var(--natural-primary);
}

.login-role-staff {
    background-color: var(--natural-primary);
    color: white;
    box-shadow: 0 8px 20px var(--natural-primary-shadow);
}

.login-role-staff:hover {
    background-color: var(--natural-primary-hover);
}

.login-role-staff:hover .login-role-icon {
    transform: scale(1.1);
}

.login-footer {
    margin-top: 40px;
    padding-top: 28px;
    border-top: 1px solid rgba(245, 245, 244, 1);
    text-align: center;
}

@media (max-width: 480px) {
    .login-title {
        font-size: 3rem;
    }
    .login-card {
        padding: 24px;
    }
}
</style>