<template>
    <div class="cc-page">
        <div class="cc-header">
            <h2 class="serif" style="color: var(--natural-primary); font-size: 2rem; font-style: italic; margin-bottom: 8px;">Quản Lý Chấm Công</h2>
            <p class="label-xs" style="color: var(--natural-muted); letter-spacing: 0.1em;">THEO DÕI THỜI GIAN LÀM VIỆC CỦA NHÂN VIÊN</p>
        </div>

        <div class="cc-actions">
            <div class="cc-action-card">
                <h4 style="margin-bottom: 16px; font-weight: 600;">Check-in / Check-out Mới</h4>
                <div style="display: flex; gap: 12px; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label class="label-xs" style="display: block; margin-bottom: 6px;">CHỌN NHÂN VIÊN</label>
                        <select v-model="selectedNhanVien" class="cc-select">
                            <option value="" disabled>-- Chọn nhân viên --</option>
                            <option v-for="nv in nhanVienList" :key="nv.nhan_vien_id" :value="nv.nhan_vien_id">
                                {{ nv.full_name }} ({{ nv.username }})
                            </option>
                        </select>
                    </div>
                    <button class="btn-checkin" @click="handleCheckIn" :disabled="!selectedNhanVien || isLoading">
                        <i class="fa-solid fa-sign-in-alt"></i> CHECK-IN
                    </button>
                    <button class="btn-checkout" @click="handleCheckOut" :disabled="!selectedNhanVien || isLoading">
                        <i class="fa-solid fa-sign-out-alt"></i> CHECK-OUT
                    </button>
                </div>
            </div>

            <div class="cc-filter-card">
                <h4 style="margin-bottom: 16px; font-weight: 600;">Lọc Dữ Liệu</h4>
                <div>
                    <label class="label-xs" style="display: block; margin-bottom: 6px;">NGÀY</label>
                    <input type="date" v-model="filterDate" class="cc-input" @change="loadChamCong" />
                </div>
            </div>
        </div>

        <div class="cc-table-wrap">
            <table class="cc-table">
                <thead>
                    <tr>
                        <th>NHÂN VIÊN</th>
                        <th>NGÀY</th>
                        <th>GIỜ VÀO</th>
                        <th>GIỜ RA</th>
                        <th>TRẠNG THÁI</th>
                        <th style="text-align: right;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="chamCongList.length === 0">
                        <td colspan="6" style="text-align: center; padding: 30px; color: var(--natural-muted); font-style: italic;">
                            Không có dữ liệu chấm công cho ngày này.
                        </td>
                    </tr>
                    <tr v-for="item in chamCongList" :key="item.id">
                        <td>
                            <strong>{{ item.nhan_vien?.full_name }}</strong>
                            <div class="label-xs" style="margin-top: 4px;">{{ item.nhan_vien?.username }}</div>
                        </td>
                        <td class="font-mono">{{ formatDateDisplay(item.date) }}</td>
                        <td class="font-mono" style="color: #059669; font-weight: 600;">{{ formatTime(item.check_in_time) }}</td>
                        <td class="font-mono">
                            <span v-if="item.check_out_time" style="color: #dc2626; font-weight: 600;">{{ formatTime(item.check_out_time) }}</span>
                            <span v-else style="color: #f59e0b; font-style: italic;">Chưa check-out</span>
                        </td>
                        <td>
                            <span class="cc-status" :class="item.status === 'đang làm' ? 'status-active' : 'status-done'">
                                {{ item.status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <button class="btn-delete" @click="handleDelete(item.id)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ChamCong',
    data() {
        return {
            nhanVienList: [],
            chamCongList: [],
            selectedNhanVien: "",
            filterDate: new Date().toISOString().slice(0, 10),
            isLoading: false
        }
    },
    mounted() {
        this.loadNhanVien();
        this.loadChamCong();
    },
    methods: {
        loadNhanVien() {
            axios.get('http://127.0.0.1:8000/api/admin/nhan-vien/get-data')
                .then(res => {
                    this.nhanVienList = res.data.data || [];
                }).catch(console.error);
        },
        loadChamCong() {
            let url = 'http://127.0.0.1:8000/api/admin/cham-cong/get-data';
            if (this.filterDate) {
                url += `?date=${this.filterDate}`;
            }
            axios.get(url)
                .then(res => {
                    this.chamCongList = res.data.data || [];
                }).catch(console.error);
        },
        handleCheckIn() {
            if (!this.selectedNhanVien) return;
            this.isLoading = true;
            axios.post('http://127.0.0.1:8000/api/admin/cham-cong/check-in', {
                nhan_vien_id: this.selectedNhanVien
            }).then(res => {
                alert('Check-in thành công!');
                this.loadChamCong();
            }).catch(err => {
                alert(err.response?.data?.message || 'Lỗi check-in');
            }).finally(() => {
                this.isLoading = false;
            });
        },
        handleCheckOut() {
            if (!this.selectedNhanVien) return;
            this.isLoading = true;
            axios.post('http://127.0.0.1:8000/api/admin/cham-cong/check-out', {
                nhan_vien_id: this.selectedNhanVien
            }).then(res => {
                alert('Check-out thành công!');
                this.loadChamCong();
            }).catch(err => {
                alert(err.response?.data?.message || 'Lỗi check-out (Ca chưa được mở hôm nay)');
            }).finally(() => {
                this.isLoading = false;
            });
        },
        handleDelete(id) {
            if (!confirm('Bạn có chắc muốn xoá lịch sử chấm công này?')) return;
            axios.post('http://127.0.0.1:8000/api/admin/cham-cong/delete-data', { id })
                .then(() => {
                    this.loadChamCong();
                }).catch(err => {
                    alert('Lỗi xoá dữ liệu');
                });
        },
        formatTime(datetimeStr) {
            if (!datetimeStr) return '';
            const d = new Date(datetimeStr);
            return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        },
        formatDateDisplay(dateStr) {
            if (!dateStr) return '';
            const parts = dateStr.split('-');
            if(parts.length === 3) return `${parts[2]}/${parts[1]}/${parts[0]}`;
            return dateStr;
        }
    }
}
</script>

<style scoped>
.cc-page {
    font-family: var(--font-sans);
}
.cc-header {
    margin-bottom: 24px;
}
.cc-actions {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}
.cc-action-card, .cc-filter-card {
    background: var(--natural-surface);
    border-radius: var(--radius-lg);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--natural-border);
}
.cc-select, .cc-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--natural-border);
    border-radius: var(--radius-md);
    background: white;
    font-size: 14px;
    transition: border-color var(--transition-fast);
}
.cc-select:focus, .cc-input:focus {
    outline: none;
    border-color: var(--natural-primary);
}
.btn-checkin, .btn-checkout {
    padding: 10px 20px;
    border: none;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
    transition: all var(--transition-fast);
}
.btn-checkin { background: #059669; }
.btn-checkin:hover:not(:disabled) { background: #047857; transform: translateY(-1px); }
.btn-checkout { background: #dc2626; }
.btn-checkout:hover:not(:disabled) { background: #b91c1c; transform: translateY(-1px); }
.btn-checkin:disabled, .btn-checkout:disabled { opacity: 0.5; cursor: not-allowed; }

.cc-table-wrap {
    background: var(--natural-surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--natural-border);
    overflow: hidden;
}
.cc-table {
    width: 100%;
    border-collapse: collapse;
}
.cc-table th {
    background: rgba(250, 250, 249, 1);
    padding: 14px 20px;
    text-align: left;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--natural-muted);
    font-weight: 700;
    border-bottom: 1px solid var(--natural-border);
}
.cc-table td {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(245, 245, 244, 1);
    color: var(--natural-text);
    font-size: 14px;
    vertical-align: middle;
}
.cc-table tbody tr:hover td {
    background-color: rgba(74, 93, 78, 0.02);
}
.cc-status {
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
}
.status-active { background: rgba(5, 150, 105, 0.1); color: #059669; }
.status-done { background: rgba(120, 113, 108, 0.1); color: #78716c; }

.btn-delete {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    cursor: pointer;
    transition: all var(--transition-fast);
}
.btn-delete:hover {
    background: #ef4444;
    color: white;
}
</style>
