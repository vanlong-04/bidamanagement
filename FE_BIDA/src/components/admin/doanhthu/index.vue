<template>
    <div>
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Thống kê <strong>Doanh thu</strong></h2>
                <p class="page-subtitle">Theo dõi doanh thu và lịch sử thanh toán.</p>
            </div>
            <button class="btn-organic btn-ghost-organic" @click="getBills" :disabled="isLoading" style="padding: 12px 24px;">
                <i class="fa-solid fa-rotate-right" :class="{ 'fa-spin': isLoading }"></i>
                LÀM MỚI
            </button>
        </header>

        <!-- Stats Grid -->
        <div class="dt-stats">
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <p class="stat-label">Tổng doanh thu</p>
                <p class="stat-value" style="color: #059669;">{{ formatPrice(totalRevenue) }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.15s">
                <p class="stat-label">Tổng hóa đơn</p>
                <p class="stat-value" style="color: var(--natural-primary);">{{ bills.length }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <p class="stat-label">TB / đơn</p>
                <p class="stat-value" style="color: #0ea5e9;">{{ formatPrice(averageRevenue) }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.25s">
                <p class="stat-label">Đã thanh toán</p>
                <p class="stat-value" style="color: #f59e0b;">{{ paidBills }}</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card-organic card-organic-xl">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-organic-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="input-organic" style="padding-left: 52px;" v-model="searchQuery" placeholder="Tìm theo ID hoặc bàn...">
                </div>
                <select class="select-organic" style="min-width: 180px;" v-model="statusFilter">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="paid">Đã thanh toán</option>
                    <option value="unpaid">Chưa thanh toán</option>
                </select>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" style="text-align: center; padding: 80px 40px;">
                <div class="spinner-border" style="color: var(--natural-primary);" role="status"></div>
            </div>

            <!-- Empty -->
            <div v-else-if="filteredBills.length === 0" style="text-align: center; padding: 80px 40px;">
                <i class="fa-solid fa-chart-line" style="font-size: 3rem; color: var(--natural-muted); opacity: 0.15; display: block; margin-bottom: 16px;"></i>
                <h3 class="serif" style="font-style: italic; font-weight: 300; color: var(--natural-muted);">Không có dữ liệu doanh thu</h3>
            </div>

            <!-- Table -->
            <div v-else style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID HĐ</th>
                            <th>Bàn</th>
                            <th>Thời gian</th>
                            <th>Dịch vụ đã dùng</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th style="text-align: right;">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(bill, index) in filteredBills" :key="bill.hoa_don_id">
                            <td>{{ index + 1 }}</td>
                            <td><span style="font-weight: 700; color: var(--natural-primary);" class="font-mono">#{{ bill.hoa_don_id }}</span></td>
                            <td><span class="badge-organic badge-info-organic">Bàn {{ bill.ban?.name || bill.ban_id }}</span></td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span class="font-mono" style="font-size: 12px; color: var(--natural-text);">{{ formatDateTime(bill.start_time) }}</span>
                                    <span class="font-mono" style="font-size: 12px; color: var(--natural-muted);">→ {{ bill.end_time ? formatDateTime(bill.end_time) : 'đang chơi' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dt-services-list">
                                    <template v-if="bill.chi_tiet_hoa_dons && bill.chi_tiet_hoa_dons.length > 0">
                                        <div v-for="item in bill.chi_tiet_hoa_dons" :key="item.chi_tiet_hoa_don_id" class="dt-service-tag">
                                            {{ item.dich_vu?.dich_vu_name }} (x{{ item.quantity }})
                                        </div>
                                    </template>
                                    <span v-else style="color: var(--natural-muted); font-style: italic; font-size: 12px;">Chỉ chơi giờ</span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span :class="getStatusBadgeClass(bill.status)" class="badge-organic">
                                    <span class="dot dot-sm" :class="getStatusDotClass(bill.status)"></span>
                                    {{ getStatusText(bill.status) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="font-mono" style="font-weight: 700; color: #059669; font-size: 1rem;">{{ formatPrice(bill.total_amount) }}</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" style="text-align: right; padding: 24px 40px;">
                                <span class="label-xs" style="font-size: 11px;">TỔNG DOANH THU:</span>
                            </td>
                            <td style="text-align: right; padding: 24px 40px;">
                                <span class="font-mono" style="font-weight: 700; color: #059669; font-size: 1.3rem;">{{ formatPrice(filteredTotalRevenue) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            isLoading: false,
            bills: [],
            searchQuery: '',
            statusFilter: 'all'
        }
    },
    computed: {
        totalRevenue() {
            return this.bills.reduce((sum, b) => sum + (Number(b.total_amount) || 0), 0)
        },
        averageRevenue() {
            return this.bills.length > 0 ? this.totalRevenue / this.bills.length : 0
        },
        paidBills() {
            return this.bills.filter(b => this.isPaid(b)).length
        },
        filteredBills() {
            let result = this.bills
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase()
                result = result.filter(b => String(b.hoa_don_id).includes(q) || String(b.ban_id).includes(q))
            }
            if (this.statusFilter === 'paid') {
                result = result.filter(b => this.isPaid(b))
            } else if (this.statusFilter === 'unpaid') {
                result = result.filter(b => !this.isPaid(b))
            }
            return result
        },
        filteredTotalRevenue() {
            return this.filteredBills.reduce((sum, b) => sum + (Number(b.total_amount) || 0), 0)
        }
    },
    mounted() {
        this.getBills()
    },
    methods: {
        getBills() {
            this.isLoading = true
            axios.get('http://127.0.0.1:8000/api/admin/hoa-don/get-data')
                .then((res) => {
                    this.bills = res.data.data || []
                })
                .catch((error) => {
                    console.error('API error:', error)
                })
                .finally(() => {
                    this.isLoading = false
                })
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
        },
        formatDateTime(dt) {
            if (!dt) return 'N/A'
            try {
                return new Date(dt).toLocaleString('vi-VN', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                })
            } catch { return dt }
        },
        // Helper tập trung kiểm tra trạng thái đã thanh toán (hỗ trợ cả string & number)
        isPaid(bill) {
            const s = bill.status;
            return s === 'đã thanh toán' || s === 2 || s === 'paid';
        },
        getStatusText(status) {
            const s = status;
            if (s === 'đã thanh toán' || s === 2 || s === 'paid') return 'Đã thanh toán'
            if (s === 'chưa thanh toán' || s === 1 || s === 'unpaid') return 'Chưa thanh toán'
            return String(s || 'N/A')
        },
        getStatusBadgeClass(status) {
            const s = status;
            if (s === 'đã thanh toán' || s === 2 || s === 'paid') return 'badge-success-organic'
            return 'badge-warning-organic'
        },
        getStatusDotClass(status) {
            const s = status;
            if (s === 'đã thanh toán' || s === 2 || s === 'paid') return 'dot-success dot-pulse'
            return 'dot-danger'
        }
    }
}
</script>

<style scoped>
.dt-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

@media (max-width: 768px) {
    .dt-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
.dt-services-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    max-width: 300px;
}

.dt-service-tag {
    font-size: 11px;
    background-color: rgba(245, 245, 244, 1);
    color: var(--natural-muted);
    padding: 2px 8px;
    border-radius: 4px;
    border: 1px solid rgba(214, 211, 209, 0.5);
    white-space: nowrap;
}
</style>
