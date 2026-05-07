<template>
    <div>
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Quản lý <strong>Nhân Viên</strong></h2>
                <p class="page-subtitle">Thêm, sửa, xóa tài khoản nhân viên trong hệ thống.</p>
            </div>
            <button class="btn-organic btn-primary-organic" style="padding: 16px 36px; font-size: 11px;"
                data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="fa-solid fa-plus"></i>
                THÊM NHÂN VIÊN
            </button>
        </header>

        <!-- Stats -->
        <div class="nv-stats">
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <p class="stat-label">Tổng nhân viên</p>
                <p class="stat-value" style="color: var(--natural-primary);">{{ staffList.length }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.15s">
                <p class="stat-label">Đang hoạt động</p>
                <p class="stat-value" style="color: #059669;">{{ activeCount }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <p class="stat-label">Nghỉ việc</p>
                <p class="stat-value" style="color: #ef4444;">{{ inactiveCount }}</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card-organic card-organic-xl">
            <!-- Search Bar -->
            <div class="filter-bar">
                <div class="search-organic-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="input-organic" style="padding-left: 52px;" placeholder="Tìm theo tên, username, email..." v-model="searchQuery">
                </div>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>Nhân Viên</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Ngày vào làm</th>
                            <th style="text-align: center;">Trạng thái</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(staff, index) in filteredStaff" :key="staff.nhan_vien_id">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="nv-avatar">{{ getInitials(staff.full_name) }}</div>
                                    <span class="nv-name">{{ staff.full_name }}</span>
                                </div>
                            </td>
                            <td><span class="font-mono" style="font-size: 13px;">{{ staff.username }}</span></td>
                            <td><span style="color: var(--natural-muted); font-size: 13px;">{{ staff.email }}</span></td>
                            <td><span class="font-mono" style="font-size: 13px;">{{ staff.phone || '—' }}</span></td>
                            <td><span style="font-size: 13px;">{{ formatDate(staff.hire_date) }}</span></td>
                            <td style="text-align: center;">
                                <span v-if="staff.status === 'active'" class="badge-organic badge-success-organic">
                                    <span class="dot dot-sm dot-success dot-pulse"></span>
                                    Hoạt động
                                </span>
                                <span v-else class="badge-organic badge-danger-organic">
                                    <span class="dot dot-sm dot-danger"></span>
                                    Nghỉ việc
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                    <button class="btn-organic btn-ghost-organic" style="padding: 10px;"
                                        data-bs-toggle="modal" data-bs-target="#editStaffModal"
                                        @click="selectForEdit(staff)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-organic btn-danger-organic" style="padding: 10px;"
                                        data-bs-toggle="modal" data-bs-target="#deleteStaffModal"
                                        @click="selectForDelete(staff)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredStaff.length === 0">
                            <td colspan="7" style="text-align: center; padding: 60px 40px;">
                                <i class="fa-solid fa-users" style="font-size: 2rem; color: var(--natural-muted); opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                                <span style="color: var(--natural-muted); font-style: italic;">Không tìm thấy nhân viên nào...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Thêm -->
        <div class="modal fade" id="addStaffModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Nhân Viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên <span style="color:red">*</span></label>
                            <input type="text" class="form-control" v-model="create_staff.full_name" placeholder="Nguyễn Văn A">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username <span style="color:red">*</span></label>
                            <input type="text" class="form-control" v-model="create_staff.username" placeholder="nhanvien01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu <span style="color:red">*</span></label>
                            <input type="password" class="form-control" v-model="create_staff.password" placeholder="Tối thiểu 6 ký tự">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email <span style="color:red">*</span></label>
                            <input type="email" class="form-control" v-model="create_staff.email" placeholder="email@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" v-model="create_staff.phone" placeholder="0901234567">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày vào làm <span style="color:red">*</span></label>
                            <input type="date" class="form-control" v-model="create_staff.hire_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="createStaff()">Thêm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sửa -->
        <div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập Nhật Nhân Viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" v-model="update_staff.full_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" v-model="update_staff.username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới <span style="color: var(--natural-muted); font-size: 12px;">(để trống nếu không đổi)</span></label>
                            <input type="password" class="form-control" v-model="update_staff.password" placeholder="Để trống nếu không đổi">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" v-model="update_staff.email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" v-model="update_staff.phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ngày vào làm</label>
                            <input type="date" class="form-control" v-model="update_staff.hire_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" v-model="update_staff.status">
                                <option value="active">Hoạt động</option>
                                <option value="inactive">Nghỉ việc</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="updateStaff()">Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xóa -->
        <div class="modal fade" id="deleteStaffModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa Nhân Viên</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <p class="mb-0">Bạn có chắc chắn muốn xóa nhân viên <strong>{{ delete_staff.full_name }}</strong> ({{ delete_staff.username }}) không?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteStaff()">Xóa</button>
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
            staffList: [],
            searchQuery: '',
            create_staff: {
                full_name: '',
                username: '',
                password: '',
                email: '',
                phone: '',
                hire_date: new Date().toISOString().slice(0, 10)
            },
            update_staff: {},
            delete_staff: {}
        }
    },
    computed: {
        filteredStaff() {
            if (!this.searchQuery) return this.staffList;
            const q = this.searchQuery.toLowerCase();
            return this.staffList.filter(s =>
                (s.full_name && s.full_name.toLowerCase().includes(q)) ||
                (s.username && s.username.toLowerCase().includes(q)) ||
                (s.email && s.email.toLowerCase().includes(q))
            );
        },
        activeCount() {
            return this.staffList.filter(s => s.status === 'active').length;
        },
        inactiveCount() {
            return this.staffList.filter(s => s.status !== 'active').length;
        }
    },
    mounted() {
        this.getStaff();
    },
    methods: {
        getStaff() {
            axios.get('http://127.0.0.1:8000/api/admin/nhan-vien/get-data')
                .then((res) => {
                    this.staffList = res.data.data || [];
                })
                .catch((error) => {
                    console.error('Lỗi khi lấy danh sách nhân viên:', error);
                });
        },
        createStaff() {
            if (!this.create_staff.full_name || !this.create_staff.username || !this.create_staff.password || !this.create_staff.email || !this.create_staff.hire_date) {
                alert('Vui lòng điền đầy đủ các trường bắt buộc!');
                return;
            }
            axios.post('http://127.0.0.1:8000/api/admin/nhan-vien/create-data', this.create_staff)
                .then((res) => {
                    if (res.data.status === 1) {
                        this.getStaff();
                        this.create_staff = { full_name: '', username: '', password: '', email: '', phone: '', hire_date: new Date().toISOString().slice(0, 10) };
                        alert(res.data.message || 'Thêm nhân viên thành công!');
                    } else {
                        alert(res.data.message || 'Thêm nhân viên thất bại!');
                    }
                })
                .catch((error) => {
                    const msg = error.response?.data?.message || 'Lỗi khi thêm nhân viên!';
                    alert(msg);
                });
        },
        selectForEdit(staff) {
            this.update_staff = { ...staff, password: '' };
        },
        updateStaff() {
            axios.post('http://127.0.0.1:8000/api/admin/nhan-vien/update-data', this.update_staff)
                .then((res) => {
                    if (res.data.status === 1) {
                        this.getStaff();
                        alert(res.data.message || 'Cập nhật nhân viên thành công!');
                    } else {
                        alert(res.data.message || 'Cập nhật thất bại!');
                    }
                })
                .catch((error) => {
                    alert('Lỗi khi cập nhật nhân viên!');
                    console.error(error);
                });
        },
        selectForDelete(staff) {
            this.delete_staff = { ...staff };
        },
        deleteStaff() {
            axios.post('http://127.0.0.1:8000/api/admin/nhan-vien/delete-data', { nhan_vien_id: this.delete_staff.nhan_vien_id })
                .then((res) => {
                    if (res.data.status === 1) {
                        this.getStaff();
                        alert(res.data.message || 'Xóa nhân viên thành công!');
                    } else {
                        alert(res.data.message || 'Xóa thất bại!');
                    }
                })
                .catch((error) => {
                    alert('Lỗi khi xóa nhân viên!');
                    console.error(error);
                });
        },
        getInitials(name) {
            if (!name) return '?';
            const parts = name.trim().split(' ');
            return parts.length > 1
                ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
                : parts[0][0].toUpperCase();
        },
        formatDate(dateStr) {
            if (!dateStr) return '—';
            try {
                return new Date(dateStr).toLocaleDateString('vi-VN');
            } catch { return dateStr; }
        }
    }
}
</script>

<style scoped>
.nv-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.nv-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--natural-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    flex-shrink: 0;
}

.nv-name {
    font-weight: 700;
    color: var(--natural-text);
    font-size: 14px;
    letter-spacing: -0.02em;
    transition: color var(--transition-fast);
}

tr:hover .nv-name {
    color: var(--natural-primary);
}

@media (max-width: 768px) {
    .nv-stats {
        grid-template-columns: 1fr;
    }
}
</style>
