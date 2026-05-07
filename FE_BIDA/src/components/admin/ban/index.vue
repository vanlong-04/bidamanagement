<template>
    <div>
        <!-- Cấu hình giá giờ bàn -->
        <div class="card-organic card-organic-xl" style="margin-bottom: 32px; border: 1px solid rgba(74,93,78,0.08); background: linear-gradient(135deg, rgba(74,93,78,0.02) 0%, transparent 100%);">
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 24px 28px; border-bottom: 1px solid var(--natural-border);">
                <div>
                    <h3 class="serif" style="font-size: 1.5rem; font-weight: 300; font-style: italic; color: var(--natural-primary); margin: 0;">⚙️ Cấu hình giá giờ bàn</h3>
                    <p class="label-xs" style="margin-top: 4px; color: var(--natural-muted);">Quản lý đơn giá cho bàn Thường và VIP</p>
                </div>
            </div>
            <div style="padding: 28px;">
                <form @submit.prevent="saveHourlyRates">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                        <!-- Bida Lỗ -->
                        <div class="price-input-group">
                            <label class="label-xs mb-2">GIÁ BIDA LỖ</label>
                            <input type="number" class="form-control" v-model.number="hourlyRates.lo" min="0" step="1000" required>
                        </div>
                        <!-- Bida Phăng -->
                        <div class="price-input-group">
                            <label class="label-xs mb-2">GIÁ BIDA PHĂNG</label>
                            <input type="number" class="form-control" v-model.number="hourlyRates.phang" min="0" step="1000" required>
                        </div>
                        <!-- Bida Lỗ VIP -->
                        <div class="price-input-group">
                            <label class="label-xs mb-2">GIÁ LỖ VIP</label>
                            <input type="number" class="form-control" v-model.number="hourlyRates.lo_vip" min="0" step="1000" required>
                        </div>
                        <!-- Bida Phăng VIP -->
                        <div class="price-input-group">
                            <label class="label-xs mb-2">GIÁ PHĂNG VIP</label>
                            <input type="number" class="form-control" v-model.number="hourlyRates.phang_vip" min="0" step="1000" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-organic btn-primary-organic" :disabled="savingRates" style="width: 100%; padding: 14px;">
                        <span v-if="savingRates"><i class="fa-solid fa-spinner fa-spin"></i> Đang lưu...</span>
                        <span v-else><i class="fa-solid fa-save"></i> Lưu cấu hình giá mới</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Quản lý <strong>Bàn</strong></h2>
                <p class="page-subtitle">Cấu hình sơ đồ bàn và loại phòng của quán.</p>
            </div>
            <button class="btn-organic btn-primary-organic" style="padding: 16px 36px; font-size: 11px;" data-bs-toggle="modal" data-bs-target="#addTableModal">
                <i class="fa-solid fa-plus"></i>
                THÊM BÀN MỚI
            </button>
        </header>

        <!-- Table Card -->
        <div class="card-organic card-organic-xl">
            <!-- Search/Filter Bar -->
            <div class="filter-bar">
                <div class="search-organic-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="input-organic" style="padding-left: 52px;" placeholder="Tìm kiếm bàn..." v-model="searchQuery">
                </div>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>Tên Bàn</th>
                            <th>Loại bàn</th>
                            <th>Giá giờ</th>
                            <th>Trạng thái</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(value, index) in filteredTables" :key="index">
                            <td>
                                <span class="table-name serif">{{ value.ban_name }}</span>
                            </td>
                            <td>
                                <div class="table-type-pill" :class="getTypeClass(value.loai_ban)">
                                    <i :class="getTypeIcon(value.loai_ban)"></i>
                                    <span v-if="Number(value.loai_ban) === 1">Bida Lỗ</span>
                                    <span v-else-if="Number(value.loai_ban) === 2">Bida Phăng</span>
                                    <span v-else-if="Number(value.loai_ban) === 3">Bida Lỗ VIP</span>
                                    <span v-else-if="Number(value.loai_ban) === 4">Bida Phăng VIP</span>
                                    <span v-else>Khác</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-mono" style="font-weight: 700; color: var(--natural-primary);">
                                    {{ formatPrice(value.hourly_rate || 0) }}/giờ
                                </span>
                            </td>
                            <td>
                                <span v-if="value.status == 1" class="badge-organic badge-success-organic">
                                    <span class="dot dot-sm dot-success dot-pulse"></span>
                                    Trống
                                </span>
                                <span v-else-if="value.status == 2" class="badge-organic badge-danger-organic">
                                    <span class="dot dot-sm dot-danger"></span>
                                    Đang sử dụng
                                </span>
                                <span v-else-if="value.status == 3" class="badge-organic" style="background: rgba(217,119,6,0.1); color: #d97706; border: 1px solid rgba(217,119,6,0.2);">
                                    <span class="dot dot-sm" style="background: #d97706;"></span>
                                    Đã đặt
                                </span>
                                <span v-else class="badge-organic">Khác</span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-cell" style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                    <button class="btn-organic btn-ghost-organic" style="padding: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#updateTableModal"
                                        v-on:click="Object.assign(update_table, value)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-organic btn-danger-organic" style="padding: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#deleteTableModal"
                                        v-on:click="Object.assign(delete_table, value)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Thêm -->
        <div class="modal fade" id="addTableModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Thêm bàn</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên bàn</label>
                            <input type="text" class="form-control" v-model="create_table.ban_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại bàn</label>
                            <select class="form-select" v-model="create_table.loai_ban">
                                <option :value="1">Bida Lỗ</option>
                                <option :value="2">Bida Phăng</option>
                                <option :value="3">Bida Lỗ VIP</option>
                                <option :value="4">Bida Phăng VIP</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" v-model="create_table.status">
                                <option :value="1">Trống</option>
                                <option :value="2">Đang sử dụng</option>
                                <option :value="3">Đã đặt</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="createTable()">Thêm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sửa -->
        <div class="modal fade" id="updateTableModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Cập nhật bàn</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên bàn</label>
                            <input type="text" class="form-control" v-model="update_table.ban_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại bàn</label>
                            <select class="form-select" v-model="update_table.loai_ban">
                                <option :value="1">Bida Lỗ</option>
                                <option :value="2">Bida Phăng</option>
                                <option :value="3">Bida Lỗ VIP</option>
                                <option :value="4">Bida Phăng VIP</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" v-model="update_table.status">
                                <option :value="1">Trống</option>
                                <option :value="2">Đang sử dụng</option>
                                <option :value="3">Đã đặt</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="updateTable()">Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xóa -->
        <div class="modal fade" id="deleteTableModal" tabindex="-1" aria-labelledby="deleteTableModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteTableModalLabel">Xóa bàn</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            <p> Bạn có chắc chắn muốn xóa bàn {{ delete_table.ban_name }} không?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal"
                            v-on:click="deleteTable()">Xóa</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {}
    }
}
</script>
