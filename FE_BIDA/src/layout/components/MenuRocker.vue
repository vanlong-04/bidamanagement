<template>
    <div class="admin-sidebar">
        <!-- Logo -->
        <div class="sidebar-brand">
            <h1 class="sidebar-logo serif">BIDA VIP</h1>
            <p class="label-xs" style="letter-spacing: 0.3em; margin-top: 8px;">Admin Control</p>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <router-link 
                v-for="item in navItems" 
                :key="item.path"
                :to="item.path" 
                class="sidebar-nav-item"
                :class="{ active: $route.path === item.path }"
                active-class=""
            >
                <i :class="item.icon" class="sidebar-nav-icon"></i>
                <span class="sidebar-nav-label">{{ item.label }}</span>
            </router-link>
        </nav>

        <!-- Logout -->
        <div class="sidebar-footer">
            <button class="sidebar-logout" @click="handleLogout">
                <i class="fa-solid fa-right-from-bracket sidebar-logout-icon"></i>
                <span class="sidebar-nav-label">Đăng xuất</span>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "MenuRocker",
    data() {
        return {
            navItems: [
                { path: '/admin/dashboard', icon: 'fa-solid fa-chart-pie', label: 'Dashboard' },
                { path: '/admin/ban', icon: 'fa-solid fa-table-cells', label: 'Quản lý Bàn' },
                { path: '/admin/dat-ban', icon: 'fa-solid fa-calendar-check', label: 'Đặt Bàn Trước' },
                { path: '/admin/dich-vu', icon: 'fa-solid fa-mug-hot', label: 'Dịch vụ & Kho' },
                { path: '/admin/hoa-don', icon: 'fa-solid fa-file-invoice', label: 'Quản lý HĐ' },
                { path: '/admin/in-hoa-don', icon: 'fa-solid fa-print', label: 'In Hóa Đơn' },
                { path: '/admin/doanh-thu', icon: 'fa-solid fa-chart-line', label: 'Doanh thu' },
                { path: '/admin/nhan-vien', icon: 'fa-solid fa-users', label: 'Nhân Viên' },
                { path: '/admin/cham-cong', icon: 'fa-solid fa-clock-rotate-left', label: 'Chấm Công' },
                { path: '/admin/khach-hang', icon: 'fa-solid fa-crown', label: 'Thành viên / VIP' },
            ]
        }
    },
    methods: {
        handleLogout() {
            localStorage.removeItem('user');
            sessionStorage.removeItem('user');
            this.$router.push('/login');
        }
    }
}
</script>

<style scoped>
.admin-sidebar {
    width: 280px;
    min-height: 100vh;
    background-color: var(--natural-surface);
    border-right: 1px solid var(--natural-border);
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 20px rgba(214, 211, 209, 0.2);
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
}

.sidebar-brand {
    padding: 36px 32px;
}

.sidebar-logo {
    font-size: 2rem;
    font-weight: 300;
    font-style: italic;
    color: var(--natural-primary);
    letter-spacing: -0.02em;
    margin: 0;
}

.sidebar-nav {
    flex: 1;
    padding: 0 20px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.sidebar-nav-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-radius: var(--radius-md);
    text-decoration: none;
    transition: all var(--transition-normal);
    color: var(--natural-muted);
    cursor: pointer;
}

.sidebar-nav-item:hover {
    color: var(--natural-primary);
    background-color: rgba(250, 250, 249, 1);
}

.sidebar-nav-item.active {
    background-color: var(--natural-primary);
    color: white;
    font-weight: 700;
    box-shadow: 0 4px 15px var(--natural-primary-shadow);
}

.sidebar-nav-item.active .sidebar-nav-icon {
    color: white;
}

.sidebar-nav-icon {
    font-size: 18px;
    width: 20px;
    text-align: center;
    transition: color var(--transition-fast);
}

.sidebar-nav-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-weight: 700;
}

.sidebar-footer {
    padding: 20px;
    border-top: 1px solid rgba(245, 245, 244, 1);
}

.sidebar-logout {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-radius: var(--radius-md);
    color: var(--natural-muted);
    text-decoration: none;
    transition: all var(--transition-normal);
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    font-family: var(--font-sans);
}

.sidebar-logout:hover {
    color: #ef4444;
    background-color: rgba(239, 68, 68, 0.05);
}

.sidebar-logout-icon {
    font-size: 18px;
    transition: transform var(--transition-fast);
}

.sidebar-logout:hover .sidebar-logout-icon {
    transform: translateX(4px);
}
</style>