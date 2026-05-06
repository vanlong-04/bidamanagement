<template>
    <div class="dashboard">
        <!-- Header -->
        <header class="dash-header">
            <div>
                <h2 class="page-title">Chào buổi sáng, <strong>Admin</strong></h2>
                <p class="page-subtitle">Tóm tắt hoạt động hệ thống trong tuần này.</p>
            </div>
            <div class="dash-date-badge">
                <p class="label-xs" style="margin-bottom: 4px;">Hôm nay</p>
                <p class="dash-date-text">{{ currentDate }}</p>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="dash-stats">
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fa-solid fa-dollar-sign" style="color: var(--natural-primary); font-size: 20px;"></i>
                    </div>
                    <span class="badge-trend-positive">+12.5%</span>
                </div>
                <p class="stat-label">Doanh thu hôm nay</p>
                <p class="stat-value">{{ formatPrice(todayRevenue) }}</p>
            </div>

            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fa-solid fa-clock" style="color: var(--natural-secondary); font-size: 20px;"></i>
                    </div>
                    <span class="badge-trend-positive">+5.2%</span>
                </div>
                <p class="stat-label">Giờ chơi tích lũy</p>
                <p class="stat-value">{{ totalHours }} Giờ</p>
            </div>

            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.3s">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 20px;"></i>
                    </div>
                    <span class="badge-trend-positive">+{{ completedBills }}</span>
                </div>
                <p class="stat-label">Hóa đơn hoàn tất</p>
                <p class="stat-value">{{ completedBills }}</p>
            </div>

            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.4s">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fa-solid fa-circle-exclamation" style="color: #d97706; font-size: 20px;"></i>
                    </div>
                    <span class="badge-trend-neutral">{{ activeTables }}/{{ totalTables }}</span>
                </div>
                <p class="stat-label">Bàn đang hoạt động</p>
                <p class="stat-value">{{ activeTables }} / {{ totalTables }}</p>
            </div>
        </div>

        <!-- Quick Actions + Top Services -->
        <div class="dash-grid">
            <!-- Quick Navigation -->
            <div class="card-organic card-organic-xl dash-quick">
                <div class="dash-section-header">
                    <h3 class="dash-section-title serif">Truy cập nhanh</h3>
                </div>
                <div class="dash-quick-grid">
                    <router-link to="/admin/ban" class="dash-quick-item">
                        <i class="fa-solid fa-table-cells dash-quick-icon"></i>
                        <span class="label-xs">Quản lý Bàn</span>
                    </router-link>
                    <router-link to="/admin/dich-vu" class="dash-quick-item">
                        <i class="fa-solid fa-mug-hot dash-quick-icon"></i>
                        <span class="label-xs">Dịch vụ</span>
                    </router-link>
                    <router-link to="/admin/hoa-don" class="dash-quick-item">
                        <i class="fa-solid fa-file-invoice dash-quick-icon"></i>
                        <span class="label-xs">Hóa đơn</span>
                    </router-link>
                    <router-link to="/admin/doanh-thu" class="dash-quick-item">
                        <i class="fa-solid fa-chart-line dash-quick-icon"></i>
                        <span class="label-xs">Doanh thu</span>
                    </router-link>
                    <router-link to="/admin/nhan-vien" class="dash-quick-item">
                        <i class="fa-solid fa-users dash-quick-icon"></i>
                        <span class="label-xs">Nhân viên</span>
                    </router-link>
                    <router-link to="/admin/cham-cong" class="dash-quick-item">
                        <i class="fa-solid fa-calendar-check dash-quick-icon"></i>
                        <span class="label-xs">Chấm công</span>
                    </router-link>
                    <router-link to="/admin/khach-hang" class="dash-quick-item">
                        <i class="fa-solid fa-address-book dash-quick-icon"></i>
                        <span class="label-xs">Khách hàng</span>
                    </router-link>
                    <router-link to="/user/dat-ban" class="dash-quick-item">
                        <i class="fa-solid fa-cash-register dash-quick-icon"></i>
                        <span class="label-xs">Bán hàng</span>
                    </router-link>
                </div>
            </div>

            <!-- Top Services -->
            <div class="card-organic card-organic-xl dash-services">
                <div class="dash-section-header">
                    <h3 class="dash-section-title serif">Dịch vụ bán chạy</h3>
                </div>
                <div class="dash-services-list">
                    <div v-for="(service, idx) in topServices" :key="idx" class="dash-service-item">
                        <div class="dash-service-info">
                            <span class="dash-service-name">{{ service.name }}</span>
                            <span class="dash-service-count">{{ service.count }} lượt</span>
                        </div>
                        <div class="progress-organic">
                            <div class="progress-bar-organic" :style="{ width: service.percentage + '%', backgroundColor: service.color }"></div>
                        </div>
                    </div>
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
            todayRevenue: 0,
            totalHours: 0,
            completedBills: 0,
            activeTables: 0,
            totalTables: 0,
            topServices: [
                { name: 'Sting Dâu', count: 42, percentage: 85, color: '#f87171' },
                { name: 'Mì xào trứng', count: 35, percentage: 70, color: '#fbbf24' },
                { name: 'Redbull', count: 28, percentage: 55, color: '#60a5fa' },
                { name: 'Khăn lạnh', count: 25, percentage: 50, color: '#a8a29e' },
                { name: 'Trái cây dĩa', count: 18, percentage: 35, color: '#34d399' },
            ]
        }
    },
    computed: {
        currentDate() {
            return new Date().toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }
    },
    mounted() {
        this.loadDashboardData();
    },
    methods: {
        async loadDashboardData() {
            try {
                // Load tables
                const tablesRes = await axios.get('http://127.0.0.1:8000/api/admin/ban/get-data');
                const tables = tablesRes.data.data || [];
                this.totalTables = tables.length;
                this.activeTables = tables.filter(t => t.status === 2).length;

                // Load bills
                // Load top services and stats
                const statsRes = await axios.get('http://127.0.0.1:8000/api/admin/dashboard/stats');
                if (statsRes.data.status === 1) {
                    this.topServices = statsRes.data.top_services;
                    this.todayRevenue = statsRes.data.today_revenue;
                    this.completedBills = statsRes.data.completed_bills;
                    this.totalHours = statsRes.data.total_hours;
                }
            } catch (err) {
                console.error('Dashboard load error:', err);
            }
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price || 0) + 'đ';
        }
    }
}
</script>

<style scoped>
.dashboard {
    position: relative;
    z-index: 10;
}

.dash-header {
    margin-bottom: 48px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.dash-date-badge {
    background: white;
    padding: 14px 28px;
    border-radius: var(--radius-lg);
    border: 1px solid rgba(245, 245, 244, 1);
    box-shadow: var(--shadow-sm);
}

.dash-date-text {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--natural-text);
    margin: 0;
}

.dash-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 48px;
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

/* Grid */
.dash-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 32px;
}

.dash-quick,
.dash-services {
    padding: 36px;
}

.dash-section-header {
    margin-bottom: 32px;
}

.dash-section-title {
    font-size: 1.5rem;
    font-weight: 300;
    font-style: italic;
    letter-spacing: -0.02em;
    color: var(--natural-text);
    margin: 0;
}

/* Quick nav */
.dash-quick-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.dash-quick-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 28px 16px;
    border-radius: var(--radius-lg);
    border: 1px solid var(--natural-border);
    background-color: rgba(250, 250, 249, 0.5);
    text-decoration: none;
    color: var(--natural-muted);
    transition: all var(--transition-normal);
}

.dash-quick-item:hover {
    background-color: var(--natural-primary);
    color: white;
    border-color: var(--natural-primary);
    box-shadow: 0 8px 20px var(--natural-primary-shadow);
    transform: translateY(-4px);
}

.dash-quick-item:hover .label-xs {
    color: white;
}

.dash-quick-icon {
    font-size: 28px;
    transition: transform var(--transition-normal);
}

.dash-quick-item:hover .dash-quick-icon {
    transform: scale(1.15);
}

/* Services */
.dash-services-list {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.dash-service-item {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.dash-service-info {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
}

.dash-service-name {
    font-weight: 700;
    color: var(--natural-text);
}

.dash-service-count {
    color: var(--natural-muted);
    font-weight: 500;
}

/* Responsive */
@media (max-width: 1200px) {
    .dash-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    .dash-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .dash-stats {
        grid-template-columns: 1fr;
    }
    .dash-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    .dash-quick-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>