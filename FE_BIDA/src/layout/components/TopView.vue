<template>
    <header class="user-header">
        <div class="user-header-left">
            <div class="user-header-tabs">
                <router-link to="/user/dat-ban" class="user-tab" :class="{ active: $route.path === '/user/dat-ban' }">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span class="user-tab-label">Phòng bàn</span>
                </router-link>
                <router-link to="/user/thuc-don" class="user-tab" :class="{ active: $route.path === '/user/thuc-don' }">
                    <i class="fa-solid fa-utensils"></i>
                    <span class="user-tab-label">Thực đơn</span>
                </router-link>
            </div>
        </div>
        <div class="user-header-right">
            <div class="user-header-info">
                <p class="user-header-name">{{ userName }}</p>
                <p class="label-xs">Nhân viên lễ tân - {{ shiftName }}</p>
            </div>
            <div class="user-header-avatar">{{ userInitials }}</div>
            <button class="btn btn-outline-danger btn-sm ms-2" @click="logout" title="Đăng xuất">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </div>
    </header>
</template>

<script>
export default {
    data() {
        return {
            user: null
        };
    },
    mounted() {
        this.loadUser();
    },
    methods: {
        loadUser() {
            const stored = localStorage.getItem('user') || sessionStorage.getItem('user');
            if (stored) {
                this.user = JSON.parse(stored);
            }
        },
        logout() {
            localStorage.removeItem('user');
            sessionStorage.removeItem('user');
            localStorage.removeItem('token');
            this.$router.push('/login');
        }
    },
    computed: {
        userName() {
            return this.user?.full_name || this.user?.username || 'Khách';
        },
        userInitials() {
            if (!this.user) return '??';
            const name = this.user.full_name || this.user.username;
            return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        },
        shiftName() {
            const hour = new Date().getHours();
            if (hour >= 6 && hour < 12) return 'Ca sáng';
            if (hour >= 12 && hour < 18) return 'Ca chiều';
            if (hour >= 18 && hour < 23) return 'Ca tối';
            return 'Ca khuya';
        }
    }
};
</script>

<style scoped>
.user-header {
    height: 72px;
    background-color: var(--natural-surface);
    border-bottom: 1px solid var(--natural-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    backdrop-filter: blur(12px);
    position: sticky;
    top: 0;
    z-index: 50;
}

.user-header-left {
    display: flex;
    align-items: center;
}

.user-header-tabs {
    display: flex;
    gap: 4px;
}

.user-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--natural-muted);
    font-size: 13px;
    font-weight: 600;
    transition: all var(--transition-fast);
}

.user-tab:hover {
    color: var(--natural-primary);
    background-color: rgba(250, 250, 249, 1);
}

.user-tab.active {
    color: var(--natural-primary);
    background-color: var(--natural-accent-light);
}

.user-tab-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-weight: 700;
}

.user-header-right {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-header-info {
    text-align: right;
}

.user-header-name {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.02em;
}

.user-header-avatar {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background-color: rgba(245, 245, 244, 1);
    border: 1px solid rgba(214, 211, 209, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
    color: var(--natural-muted);
}

.user-header-menu-btn {
    padding: 8px;
    background: none;
    border: 1px solid rgba(214, 211, 209, 1);
    border-radius: var(--radius-sm);
    color: var(--natural-muted);
    cursor: pointer;
    transition: all var(--transition-fast);
}

.user-header-menu-btn:hover {
    color: var(--natural-primary);
    border-color: var(--natural-primary);
}

.offcanvas-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.offcanvas-nav li {
    margin-bottom: 4px;
}

.offcanvas-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: var(--radius-md);
    text-decoration: none;
    color: var(--natural-muted);
    font-size: 13px;
    font-weight: 600;
    transition: all var(--transition-fast);
}

.offcanvas-nav-link:hover {
    color: var(--natural-primary);
    background-color: rgba(250, 250, 249, 1);
}

.offcanvas-nav-link i {
    width: 20px;
    text-align: center;
}
</style>
