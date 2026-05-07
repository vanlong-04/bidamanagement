<template>
    <div class="reservation-page">
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Quản lý <strong>Đặt Bàn Trước</strong></h2>
                <p class="page-subtitle">Quản lý lịch hẹn, thông tin khách hàng và trạng thái đặt chỗ.</p>
            </div>
            <button class="btn-organic btn-primary-organic" @click="openModal" style="padding: 12px 28px;">
                <i class="fa-solid fa-calendar-plus"></i> ĐẶT BÀN MỚI
            </button>
        </header>

        <!-- Main Content -->
        <div class="card-organic card-organic-xl">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-organic-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="input-organic" style="padding-left: 52px;" v-model="searchQuery" placeholder="Tìm theo tên khách hoặc SĐT...">
                </div>
                <div style="display: flex; gap: 12px;">
                    <select class="select-organic" v-model="statusFilter">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="cancelled">Đã hủy</option>
                        <option value="completed">Đã nhận bàn</option>
                    </select>
                </div>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" style="text-align: center; padding: 80px 40px;">
                <div class="spinner-border" style="color: var(--natural-primary);"></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="filteredList.length === 0" style="text-align: center; padding: 80px 40px;">
                <i class="fa-solid fa-calendar-xmark" style="font-size: 3.5rem; color: var(--natural-muted); opacity: 0.15; margin-bottom: 20px; display: block;"></i>
                <h3 class="serif" style="font-style: italic; color: var(--natural-muted); font-weight: 300;">Chưa có lịch đặt bàn nào</h3>
            </div>

            <!-- Table -->
            <div v-else style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Khách hàng</th>
                            <th>SĐT</th>
                            <th>Thời gian</th>
                            <th>Loại bàn</th>
                            <th>Bàn cụ thể</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in filteredList" :key="item.id">
                            <td>{{ index + 1 }}</td>
                            <td><span style="font-weight: 700; color: var(--natural-text);">{{ item.ten_khach_hang }}</span></td>
                            <td><span class="font-mono">{{ item.so_dien_thoai }}</span></td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; font-size: 14px;">{{ formatTime(item.thoi_gian_dat) }}</span>
                                    <span class="label-xs" style="color: var(--natural-muted);">{{ formatDate(item.thoi_gian_dat) }}</span>
                                </div>
                            </td>
                            <td>
                                <span v-if="item.loai_ban === 1" class="badge-organic">Bida Lỗ</span>
                                <span v-else-if="item.loai_ban === 2" class="badge-organic badge-info-organic">Bida Phăng</span>
                                <span v-else-if="item.loai_ban === 3" class="badge-organic badge-warning-organic">Lỗ VIP</span>
                                <span v-else-if="item.loai_ban === 4" class="badge-organic badge-danger-organic">Phăng VIP</span>
                                <span v-else class="badge-organic">Khác</span>
                            </td>
                            <td>
                                <span v-if="item.ban" class="badge-organic" style="background-color: var(--natural-accent-light); color: var(--natural-primary); border: 1px solid var(--natural-primary);">
                                    {{ item.ban.name }}
                                </span>
                                <span v-else style="color: var(--natural-muted); font-style: italic; font-size: 12px;">Chưa chọn bàn</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="dropdown">
                                    <button class="badge-organic" :class="getStatusBadgeClass(item.status)" data-bs-toggle="dropdown" style="border: none; cursor: pointer;">
                                        <span class="dot dot-sm" :class="getStatusDotClass(item.status)"></span>
                                        {{ getStatusText(item.status) }}
                                        <i class="fa-solid fa-chevron-down" style="font-size: 8px; margin-left: 6px;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-organic">
                                        <li><a class="dropdown-item" @click="updateStatus(item.id, 'confirmed')">Xác nhận</a></li>
                                        <li><a class="dropdown-item" @click="updateStatus(item.id, 'cancelled')">Hủy lịch</a></li>
                                        <li><a class="dropdown-item" @click="updateStatus(item.id, 'completed')">Đã nhận bàn</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <button class="btn-organic btn-ghost-organic" @click="deleteItem(item.id)" style="padding: 8px 12px; color: #ef4444;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        <div class="modal fade" id="modalDatBan" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content modal-content-organic">
                    <div class="modal-header border-0" style="padding: 32px 32px 16px;">
                        <h4 class="modal-title serif" style="font-style: italic;">Đặt bàn mới</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 16px 32px 32px;">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="label-xs mb-2">TÊN KHÁCH HÀNG</label>
                                <input type="text" class="input-organic" v-model="newItem.ten_khach_hang" placeholder="Nhập tên khách...">
                            </div>
                            <div class="col-md-6">
                                <label class="label-xs mb-2">SỐ ĐIỆN THOẠI</label>
                                <input type="text" class="input-organic" v-model="newItem.so_dien_thoai" placeholder="09xxx...">
                            </div>
                            <div class="col-md-6">
                                <label class="label-xs mb-2">SỐ NGƯỜI</label>
                                <input type="number" class="input-organic" v-model="newItem.so_luong_nguoi">
                            </div>
                            <div class="col-12">
                                <label class="label-xs mb-2">THỜI GIAN ĐẶT</label>
                                <input type="datetime-local" class="input-organic" v-model="newItem.thoi_gian_dat">
                            </div>
                            <div class="col-md-6">
                                <label class="label-xs mb-2">LOẠI BÀN</label>
                                <select class="select-organic" v-model="newItem.loai_ban">
                                    <option :value="1">Bida Lỗ</option>
                                    <option :value="2">Bida Phăng</option>
                                    <option :value="3">Bida Lỗ VIP</option>
                                    <option :value="4">Bida Phăng VIP</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="label-xs mb-2">CHỌN BÀN (TÙY CHỌN)</label>
                                <select class="select-organic" v-model="newItem.ban_id">
                                    <option :value="null">Chưa gán bàn</option>
                                    <option v-for="ban in availableTables" :key="ban.ban_id" :value="ban.ban_id">
                                        {{ ban.ban_name }} ({{ getTableStatusText(ban.status) }})
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="label-xs mb-2">GHI CHÚ</label>
                                <textarea class="input-organic" v-model="newItem.ghi_chu" rows="2" placeholder="Yêu cầu đặc biệt..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0" style="padding: 16px 32px 32px;">
                        <button type="button" class="btn-organic btn-ghost-organic" data-bs-dismiss="modal">HỦY</button>
                        <button type="button" class="btn-organic btn-primary-organic" @click="createItem" :disabled="isSaving">
                            <span v-if="isSaving" class="spinner-border spinner-border-sm me-2"></span>
                            XÁC NHẬN ĐẶT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Modal } from 'bootstrap';

export default {
    data() {
        return {
            isLoading: false,
            isSaving: false,
            list: [],
            tableList: [],
            searchQuery: '',
            statusFilter: 'all',
            modal: null,
            newItem: {
                ten_khach_hang: '',
                so_dien_thoai: '',
                thoi_gian_dat: '',
                loai_ban: 1,
                ban_id: null,
                so_luong_nguoi: 1,
                ghi_chu: ''
            }
        }
    },
    computed: {
        filteredList() {
            let res = this.list;
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                res = res.filter(i => i.ten_khach_hang.toLowerCase().includes(q) || i.so_dien_thoai.includes(q));
            }
            if (this.statusFilter !== 'all') {
                res = res.filter(i => i.status === this.statusFilter);
            }
            return res;
        },
        availableTables() {
            // Lọc bàn theo loại bàn đã chọn
            return this.tableList.filter(ban => Number(ban.loai_ban) === Number(this.newItem.loai_ban));
        }
    },
    mounted() {
        this.getData();
        this.getTables();
        this.modal = new Modal(document.getElementById('modalDatBan'));
    },
    methods: {
        getData() {
            this.isLoading = true;
            axios.get('http://127.0.0.1:8000/api/admin/dat-ban/get-data')
                .then(res => { this.list = res.data.data; })
                .finally(() => { this.isLoading = false; });
        },
        getTables() {
            axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
                .then(res => { this.tableList = res.data.data; });
        },
        openModal() {
            this.newItem = {
                ten_khach_hang: '',
                so_dien_thoai: '',
                thoi_gian_dat: new Date().toISOString().slice(0, 16),
                loai_ban: 1,
                ban_id: null,
                so_luong_nguoi: 1,
                ghi_chu: ''
            };
            this.modal.show();
        },
        createItem() {
            if (!this.newItem.ten_khach_hang || !this.newItem.so_dien_thoai || !this.newItem.thoi_gian_dat) {
                alert('Vui lòng nhập đầy đủ thông tin bắt buộc');
                return;
            }
            this.isSaving = true;
            axios.post('http://127.0.0.1:8000/api/admin/dat-ban/create-data', this.newItem)
                .then(() => {
                    this.getData();
                    this.getTables();
                    this.modal.hide();
                })
                .finally(() => { this.isSaving = false; });
        },
        updateStatus(id, status) {
            axios.post('http://127.0.0.1:8000/api/admin/dat-ban/update-status', { id, status })
                .then(() => { 
                    this.getData(); 
                    this.getTables();
                });
        },
        deleteItem(id) {
            if (confirm('Bạn có chắc chắn muốn xóa lịch đặt bàn này?')) {
                axios.post('http://127.0.0.1:8000/api/admin/dat-ban/delete-data', { id })
                    .then(() => { this.getData(); });
            }
        },
        formatDate(dt) {
            return new Date(dt).toLocaleDateString('vi-VN');
        },
        formatTime(dt) {
            return new Date(dt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        },
        getStatusText(s) {
            const map = { 
                pending: 'Chờ xác nhận', 
                confirmed: 'Đã xác nhận', 
                cancelled: 'Đã hủy', 
                completed: 'Đã nhận bàn' 
            };
            return map[s] || s;
        },
        getTableStatusText(s) {
            const map = {
                1: 'TRỐNG',
                2: 'ĐANG SỬ DỤNG',
                3: 'ĐÃ ĐẶT'
            };
            return map[s] || 'KHÁC';
        },
        getStatusBadgeClass(s) {
            const map = { pending: 'badge-warning-organic', confirmed: 'badge-info-organic', cancelled: 'badge-danger-organic', completed: 'badge-success-organic' };
            return map[s] || '';
        },
        getStatusDotClass(s) {
            const map = { pending: 'dot-warning dot-pulse', confirmed: 'dot-info', cancelled: 'dot-danger', completed: 'dot-success' };
            return map[s] || '';
        }
    }
}
</script>

<style scoped>
.dropdown-menu-organic {
    border: 1px solid var(--natural-border);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-lg);
    padding: 8px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}
.dropdown-item {
    border-radius: var(--radius-sm);
    padding: 8px 16px;
    font-size: 13px;
    cursor: pointer;
}
.dropdown-item:hover {
    background-color: var(--natural-bg);
    color: var(--natural-primary);
}
</style>
