<template>
    <div>
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Quản lý <strong>Hóa Đơn</strong></h2>
                <p class="page-subtitle">Xem, theo dõi và quản lý tất cả hóa đơn.</p>
            </div>
            <button class="btn-organic btn-ghost-organic" @click="loadBills" :disabled="isLoading" style="padding: 12px 24px;">
                <i class="fa-solid fa-rotate-right" :class="{ 'fa-spin': isLoading }"></i>
                LÀM MỚI
            </button>
        </header>

        <!-- Stats -->
        <div class="hd-stats">
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <p class="stat-label">Tổng hóa đơn</p>
                <p class="stat-value" style="color: var(--natural-primary);">{{ bills.length }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.15s">
                <p class="stat-label">Chưa thanh toán</p>
                <p class="stat-value" style="color: #ef4444;">{{ unpaidCount }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <p class="stat-label">Đã thanh toán</p>
                <p class="stat-value" style="color: #059669;">{{ paidCount }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.25s">
                <p class="stat-label">Doanh thu hôm nay</p>
                <p class="stat-value" style="color: #0ea5e9; font-size: 1.1rem;">{{ formatPrice(todayRevenue) }}</p>
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
                <select class="select-organic" style="min-width: 200px;" v-model="statusFilter">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="unpaid">Chưa thanh toán</option>
                    <option value="paid">Đã thanh toán</option>
                </select>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" style="text-align: center; padding: 80px 40px;">
                <div class="spinner-border" style="color: var(--natural-primary);" role="status"></div>
            </div>

            <!-- Table -->
            <div v-else style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID HĐ</th>
                            <th>Bàn</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th style="text-align: right;">Tổng tiền</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(bill, index) in filteredBills" :key="bill.hoa_don_id">
                            <td>{{ index + 1 }}</td>
                            <td><span style="font-weight: 700; color: var(--natural-primary);" class="font-mono">#{{ bill.hoa_don_id }}</span></td>
                            <td><span class="badge-organic badge-info-organic">{{ bill.ban?.ban_name || 'Bàn ' + bill.ban_id }}</span></td>
                            <td><span class="font-mono" style="font-size: 13px;">{{ formatDateTime(bill.start_time) }}</span></td>
                            <td><span class="font-mono" style="font-size: 13px;">{{ bill.end_time ? formatDateTime(bill.end_time) : '—' }}</span></td>
                            <td style="text-align: center;">
                                <span :class="getStatusBadgeClass(bill.status)" class="badge-organic">
                                    <span class="dot dot-sm" :class="getStatusDotClass(bill.status)"></span>
                                    {{ getStatusText(bill.status) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <span class="font-mono" style="font-weight: 700; color: #059669;">{{ formatPrice(bill.total_amount) }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                    <button class="btn-organic btn-danger-organic" style="padding: 8px; font-size: 11px;"
                                        data-bs-toggle="modal" data-bs-target="#deleteHoaDonModal"
                                        @click="selectForDelete(bill)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredBills.length === 0">
                            <td colspan="8" style="text-align: center; padding: 60px 40px;">
                                <i class="fa-solid fa-file-invoice" style="font-size: 2rem; color: var(--natural-muted); opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                                <span style="color: var(--natural-muted); font-style: italic;">Không có hóa đơn nào</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot v-if="filteredBills.length > 0">
                        <tr>
                            <td colspan="7" style="text-align: right; padding: 20px 40px;">
                                <span class="label-xs" style="font-size: 11px;">TỔNG DOANH THU (bộ lọc hiện tại):</span>
                            </td>
                            <td style="text-align: right; padding: 20px 24px;">
                                <span class="font-mono" style="font-weight: 700; color: #059669; font-size: 1.2rem;">{{ formatPrice(filteredTotal) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Modal Xóa -->
        <div class="modal fade" id="deleteHoaDonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa Hóa Đơn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <p class="mb-0">Bạn có chắc chắn muốn xóa hóa đơn <strong>#{{ delete_bill.hoa_don_id }}</strong> ({{ delete_bill.ban?.ban_name || 'Bàn ' + delete_bill.ban_id }}) không? Hành động này không thể hoàn tác.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteBill()">Xóa</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    data() {
        return {
            bills: [],
            isLoading: false,
            searchQuery: '',
            statusFilter: 'all',
            delete_bill: {}
        }
    },
    computed: {
        unpaidCount() {
            return this.bills.filter(b => !this.isPaid(b)).length;
        },
        paidCount() {
            return this.bills.filter(b => this.isPaid(b)).length;
        },
        todayRevenue() {
            const today = new Date().toDateString();
            return this.bills
                .filter(b => this.isPaid(b) && new Date(b.updated_at || b.start_time).toDateString() === today)
                .reduce((sum, b) => sum + Number(b.total_amount || 0), 0);
        },
        filteredBills() {
            let result = this.bills;
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(b => String(b.hoa_don_id).includes(q) || String(b.ban_id).includes(q) || (b.ban?.ban_name && b.ban.ban_name.toLowerCase().includes(q)));
            }
            if (this.statusFilter === 'paid') {
                result = result.filter(b => this.isPaid(b));
            } else if (this.statusFilter === 'unpaid') {
                result = result.filter(b => !this.isPaid(b));
            }
            return result;
        },
        filteredTotal() {
            return this.filteredBills.reduce((sum, b) => sum + Number(b.total_amount || 0), 0);
        }
    },
    mounted() {
        this.loadBills();
    },
    methods: {
        loadBills() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/admin/hoa-don/get-data')
                .then((res) => {
                    this.bills = res.data.data || [];
                })
                .catch((error) => {
                    console.error('Lỗi khi lấy hóa đơn:', error);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        selectForDelete(bill) {
            this.delete_bill = { ...bill };
        },
        deleteBill() {
            axios.post('http://127.0.0.1:8000/api/admin/hoa-don/delete-data', { hoa_don_id: this.delete_bill.hoa_don_id })
                .then((res) => {
                    if (res.data.status === 1) {
                        this.loadBills();
                        alert(res.data.message || 'Xóa hóa đơn thành công!');
                    } else {
                        alert(res.data.message || 'Xóa thất bại!');
                    }
                })
                .catch((error) => {
                    alert('Lỗi khi xóa hóa đơn!');
                    console.error(error);
                });
        },
        isPaid(bill) {
            const s = bill.status;
            return s === 'đã thanh toán' || s === 2 || s === 'paid';
        },
        getStatusText(status) {
            const s = status;
            if (s === 'đã thanh toán' || s === 2 || s === 'paid') return 'Đã thanh toán';
            if (s === 'chưa thanh toán' || s === 1 || s === 'unpaid') return 'Chưa thanh toán';
            return String(s || 'N/A');
        },
        getStatusBadgeClass(status) {
            return this.isPaid({ status }) ? 'badge-success-organic' : 'badge-warning-organic';
        },
        getStatusDotClass(status) {
            return this.isPaid({ status }) ? 'dot-success dot-pulse' : 'dot-danger';
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0);
        },
        formatDateTime(dt) {
            if (!dt) return 'N/A';
            try {
                return new Date(dt).toLocaleString('vi-VN', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            } catch { return dt; }
        }
    }
}
</script>

<style scoped>
.hd-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

@media (max-width: 768px) {
    .hd-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
