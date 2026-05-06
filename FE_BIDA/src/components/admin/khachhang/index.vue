<template>
    <div class="kh-page">
        <div class="kh-header">
            <h2 class="serif" style="color: var(--natural-primary); font-size: 2rem; font-style: italic; margin-bottom: 8px;">Quản Lý Thành Viên & VIP</h2>
            <p class="label-xs" style="color: var(--natural-muted); letter-spacing: 0.1em;">THEO DÕI KHÁCH HÀNG THƯỜNG XUYÊN VÀ CHÍNH SÁCH ƯU ĐÃI</p>
        </div>

        <div class="kh-actions" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
            <div class="search-box">
                <i class="fa-solid fa-search" style="color: var(--natural-muted);"></i>
                <input type="text" v-model="searchQuery" placeholder="Tìm kiếm theo SĐT hoặc Tên..." class="kh-input" style="border: none; outline: none; background: transparent; width: 250px; padding: 10px;" />
            </div>
            <button class="btn-primary" @click="openModal()">
                <i class="fa-solid fa-plus"></i> Thêm Khách Hàng
            </button>
        </div>

        <div class="kh-table-wrap">
            <table class="kh-table">
                <thead>
                    <tr>
                        <th>TÊN KHÁCH HÀNG</th>
                        <th>SỐ ĐIỆN THOẠI</th>
                        <th>HẠNG THÀNH VIÊN</th>
                        <th>NGÀY TẠO</th>
                        <th style="text-align: right;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="filteredKhachHang.length === 0">
                        <td colspan="5" style="text-align: center; padding: 30px; color: var(--natural-muted); font-style: italic;">
                            Không có dữ liệu khách hàng.
                        </td>
                    </tr>
                    <tr v-for="item in filteredKhachHang" :key="item.khach_hang_id">
                        <td><strong>{{ item.ten_khach_hang }}</strong></td>
                        <td class="font-mono">{{ item.so_dien_thoai }}</td>
                        <td>
                            <span class="kh-status" :class="item.hang_thanh_vien === 'VIP' ? 'status-vip' : 'status-normal'">
                                <i :class="item.hang_thanh_vien === 'VIP' ? 'fa-solid fa-crown' : 'fa-solid fa-user'"></i> {{ item.hang_thanh_vien }}
                            </span>
                        </td>
                        <td class="font-mono">{{ formatDate(item.created_at) }}</td>
                        <td style="text-align: right;">
                            <button class="btn-icon btn-edit" @click="openModal(item)"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-icon btn-delete" @click="deleteItem(item.khach_hang_id)"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Thêm/Sửa -->
        <div class="modal fade" id="khachHangModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title serif" style="font-style: italic; color: var(--natural-primary);">
                            {{ isEdit ? 'Cập Nhật Khách Hàng' : 'Thêm Khách Hàng' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="label-xs" style="display: block; margin-bottom: 8px;">Tên Khách Hàng</label>
                            <input type="text" v-model="formData.ten_khach_hang" class="form-control" placeholder="Nhập tên khách hàng" />
                        </div>
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="label-xs" style="display: block; margin-bottom: 8px;">Số Điện Thoại</label>
                            <input type="text" v-model="formData.so_dien_thoai" class="form-control" placeholder="Nhập số điện thoại (viết liền)" />
                        </div>
                        <div class="form-group" style="margin-bottom: 16px;">
                            <label class="label-xs" style="display: block; margin-bottom: 8px;">Hạng Thành Viên</label>
                            <select v-model="formData.hang_thanh_vien" class="form-select">
                                <option value="Thường">Thường</option>
                                <option value="VIP">VIP (Giảm 10%)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" style="background: var(--natural-primary); border: none;" @click="saveData" :disabled="isLoading">
                            {{ isLoading ? 'Đang lưu...' : 'Lưu thông tin' }}
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
    name: 'KhachHang',
    data() {
        return {
            khachHangList: [],
            searchQuery: '',
            isLoading: false,
            isEdit: false,
            formData: {
                khach_hang_id: null,
                ten_khach_hang: '',
                so_dien_thoai: '',
                hang_thanh_vien: 'Thường'
            },
            modalInstance: null
        }
    },
    computed: {
        filteredKhachHang() {
            if (!this.searchQuery) return this.khachHangList;
            const q = this.searchQuery.toLowerCase();
            return this.khachHangList.filter(kh => 
                kh.ten_khach_hang.toLowerCase().includes(q) || 
                kh.so_dien_thoai.includes(q)
            );
        }
    },
    mounted() {
        this.loadData();
        this.modalInstance = new Modal(document.getElementById('khachHangModal'));
    },
    methods: {
        loadData() {
            axios.get('http://127.0.0.1:8000/api/admin/khach-hang/get-data')
                .then(res => {
                    this.khachHangList = res.data.data || [];
                }).catch(console.error);
        },
        openModal(item = null) {
            if (item) {
                this.isEdit = true;
                this.formData = { ...item };
            } else {
                this.isEdit = false;
                this.formData = {
                    khach_hang_id: null,
                    ten_khach_hang: '',
                    so_dien_thoai: '',
                    hang_thanh_vien: 'Thường'
                };
            }
            this.modalInstance.show();
        },
        saveData() {
            if (!this.formData.ten_khach_hang || !this.formData.so_dien_thoai) {
                alert('Vui lòng nhập đầy đủ thông tin');
                return;
            }
            this.isLoading = true;
            const url = this.isEdit ? 'http://127.0.0.1:8000/api/admin/khach-hang/update-data' : 'http://127.0.0.1:8000/api/admin/khach-hang/create-data';
            
            axios.post(url, this.formData)
                .then(res => {
                    this.loadData();
                    this.modalInstance.hide();
                    alert(res.data.message || 'Lưu thành công!');
                }).catch(err => {
                    alert(err.response?.data?.message || 'Có lỗi xảy ra!');
                }).finally(() => {
                    this.isLoading = false;
                });
        },
        deleteItem(id) {
            if (!confirm('Bạn có chắc muốn xóa khách hàng này?')) return;
            axios.post('http://127.0.0.1:8000/api/admin/khach-hang/delete-data', { khach_hang_id: id })
                .then(() => {
                    this.loadData();
                }).catch(err => alert('Lỗi xóa dữ liệu'));
        },
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('vi-VN');
        }
    }
}
</script>

<style scoped>
.kh-page { font-family: var(--font-sans); }
.search-box {
    display: flex; align-items: center; background: white;
    border: 1px solid var(--natural-border); border-radius: var(--radius-full);
    padding: 0 16px; box-shadow: var(--shadow-sm);
}
.btn-primary {
    padding: 10px 20px; background: var(--natural-primary); color: white;
    border: none; border-radius: var(--radius-md); font-weight: 600; font-size: 13px;
    cursor: pointer; transition: all var(--transition-fast);
}
.btn-primary:hover { background: var(--natural-primary-hover); transform: translateY(-1px); }

.kh-table-wrap {
    background: var(--natural-surface); border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm); border: 1px solid var(--natural-border);
    overflow: hidden;
}
.kh-table { width: 100%; border-collapse: collapse; }
.kh-table th {
    background: rgba(250, 250, 249, 1); padding: 14px 20px; text-align: left;
    font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em;
    color: var(--natural-muted); font-weight: 700; border-bottom: 1px solid var(--natural-border);
}
.kh-table td {
    padding: 16px 20px; border-bottom: 1px solid rgba(245, 245, 244, 1);
    color: var(--natural-text); font-size: 14px; vertical-align: middle;
}
.kh-status {
    padding: 4px 12px; border-radius: var(--radius-full); font-size: 11px; font-weight: 700;
}
.status-vip { background: rgba(217, 119, 6, 0.1); color: #d97706; }
.status-normal { background: rgba(120, 113, 108, 0.1); color: #78716c; }

.btn-icon {
    width: 32px; height: 32px; border-radius: 50%; border: none;
    cursor: pointer; transition: all var(--transition-fast); margin-left: 8px;
}
.btn-edit { background: rgba(5, 150, 105, 0.1); color: #059669; }
.btn-edit:hover { background: #059669; color: white; }
.btn-delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.btn-delete:hover { background: #ef4444; color: white; }
</style>
