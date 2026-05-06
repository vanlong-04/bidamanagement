<template>
    <div>
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Kho <strong>Dịch vụ</strong></h2>
                <p class="page-subtitle">Quản lý thực đơn, đồ uống và các dịch vụ đi kèm.</p>
            </div>
            <button class="btn-organic btn-primary-organic" style="padding: 16px 36px; font-size: 11px;" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="fa-solid fa-plus"></i>
                THÊM DỊCH VỤ
            </button>
        </header>

        <!-- Stats Row -->
        <div class="dv-stats">
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.1s">
                <p class="stat-label">Tổng dịch vụ</p>
                <p class="stat-value" style="color: var(--natural-primary);">{{ services.length }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.15s">
                <p class="stat-label">Nước uống</p>
                <p class="stat-value" style="color: #0ea5e9;">{{ countByCategory('Nước uống') }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.2s">
                <p class="stat-label">Đồ ăn</p>
                <p class="stat-value" style="color: #f59e0b;">{{ countByCategory('Đồ ăn nhẹ') + countByCategory('Đồ ăn chính') }}</p>
            </div>
            <div class="stat-card animate-fade-in-up" style="animation-delay: 0.25s">
                <p class="stat-label">Khác</p>
                <p class="stat-value" style="color: #ef4444;">{{ countByCategory('Tráng miệng') + countByCategory('Khác') }}</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card-organic card-organic-xl">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-organic-wrap">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" class="input-organic" style="padding-left: 52px;" placeholder="Tìm kiếm món..." v-model="searchQuery" @input="filterServices">
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <select class="select-organic" v-model="activeFilter" @change="filterServices">
                        <option value="all">Tất cả loại</option>
                        <option value="Nước uống">🥤 Nước uống</option>
                        <option value="Đồ ăn nhẹ">🍿 Đồ ăn nhẹ</option>
                        <option value="Đồ ăn chính">🍗 Đồ ăn chính</option>
                        <option value="Tráng miệng">🍰 Tráng miệng</option>
                        <option value="Khác">📦 Khác</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div style="overflow-x: auto;">
                <table class="table-organic">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Phân loại</th>
                            <th>Đơn giá</th>
                            <th>Mô tả</th>
                            <th style="text-align: right;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(service, index) in filteredServices" :key="service.dich_vu_id">
                            <td>
                                <div class="dv-thumb-wrap">
                                    <img v-if="service.image_url" :src="service.image_url" :alt="service.dich_vu_name" class="dv-thumb" />
                                    <div v-else class="dv-thumb-placeholder">{{ getCategoryEmoji(service.loai_dich_vu) }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="dv-name">{{ service.dich_vu_name }}</span>
                            </td>
                            <td>
                                <span class="badge-organic badge-info-organic">
                                    {{ service.loai_dich_vu || 'Khác' }}
                                </span>
                            </td>
                            <td>
                                <span class="dv-price font-mono">{{ formatPrice(service.price) }}</span>
                            </td>
                            <td>
                                <span class="dv-desc">{{ truncateText(service.description, 40) }}</span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-cell" style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                    <button class="btn-organic btn-ghost-organic" style="padding: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#updateServiceModal"
                                        @click="selectServiceForEdit(service)">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn-organic btn-danger-organic" style="padding: 10px;" data-bs-toggle="modal"
                                        data-bs-target="#deleteServiceModal"
                                        @click="selectServiceForDelete(service)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="filteredServices.length === 0">
                            <td colspan="6" style="text-align: center; padding: 60px 40px;">
                                <i class="fa-solid fa-box-open" style="font-size: 2rem; color: var(--natural-muted); opacity: 0.3; display: block; margin-bottom: 12px;"></i>
                                <span style="color: var(--natural-muted); font-style: italic;">Không có dịch vụ nào</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Thêm -->
        <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm Dịch Vụ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên dịch vụ</label>
                            <input type="text" class="form-control" v-model="create_service.dich_vu_name" placeholder="VD: Coca Cola, Trà đá...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại dịch vụ</label>
                            <select class="form-select" v-model="create_service.loai_dich_vu">
                                <option value="Nước uống">🥤 Nước uống</option>
                                <option value="Đồ ăn nhẹ">🍿 Đồ ăn nhẹ</option>
                                <option value="Đồ ăn chính">🍗 Đồ ăn chính</option>
                                <option value="Tráng miệng">🍰 Tráng miệng</option>
                                <option value="Khác">📦 Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input type="number" class="form-control" v-model.number="create_service.price" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" v-model="create_service.description" rows="2" placeholder="Mô tả (tùy chọn)"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" @change="onCreateImageChange" ref="createImageInput">
                            <div v-if="createImagePreview" class="dv-image-preview mt-2">
                                <img :src="createImagePreview" alt="Preview" />
                                <button type="button" class="dv-remove-preview" @click="removeCreateImage">&times;</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="createService()">Thêm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sửa -->
        <div class="modal fade" id="updateServiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập Nhật Dịch Vụ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên dịch vụ</label>
                            <input type="text" class="form-control" v-model="update_service.dich_vu_name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại dịch vụ</label>
                            <select class="form-select" v-model="update_service.loai_dich_vu">
                                <option value="Nước uống">🥤 Nước uống</option>
                                <option value="Đồ ăn nhẹ">🍿 Đồ ăn nhẹ</option>
                                <option value="Đồ ăn chính">🍗 Đồ ăn chính</option>
                                <option value="Tráng miệng">🍰 Tráng miệng</option>
                                <option value="Khác">📦 Khác</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá (VNĐ)</label>
                            <input type="number" class="form-control" v-model.number="update_service.price" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" v-model="update_service.description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <div v-if="update_service.image_url && !updateImagePreview" class="dv-image-preview mb-2">
                                <img :src="update_service.image_url" alt="Ảnh hiện tại" />
                                <span class="dv-current-label">Ảnh hiện tại</span>
                            </div>
                            <input type="file" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" @change="onUpdateImageChange" ref="updateImageInput">
                            <div v-if="updateImagePreview" class="dv-image-preview mt-2">
                                <img :src="updateImagePreview" alt="Preview" />
                                <button type="button" class="dv-remove-preview" @click="removeUpdateImage">&times;</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" @click="updateService()">Cập nhật</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Xóa -->
        <div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa Dịch Vụ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <p class="mb-0">Bạn có chắc chắn muốn xóa dịch vụ <strong>{{ delete_service.dich_vu_name }}</strong> không?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" @click="deleteService()">Xóa</button>
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
            services: [],
            filteredServices: [],
            activeFilter: 'all',
            searchQuery: '',
            create_service: {
                dich_vu_name: '',
                loai_dich_vu: 'Nước uống',
                price: 0,
                description: ''
            },
            update_service: {},
            delete_service: {},
            createImageFile: null,
            createImagePreview: null,
            updateImageFile: null,
            updateImagePreview: null
        }
    },
    mounted() {
        this.getServices()
    },
    methods: {
        getServices() {
            axios.get('http://127.0.0.1:8000/api/admin/dich-vu/get-data')
                .then((res) => {
                    this.services = res.data.data || []
                    this.filterServices()
                })
                .catch((error) => {
                    console.error('API error:', error)
                })
        },
        filterServices() {
            let result = this.services;
            if (this.activeFilter !== 'all') {
                result = result.filter(s => s.loai_dich_vu === this.activeFilter);
            }
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(s => s.dich_vu_name && s.dich_vu_name.toLowerCase().includes(q));
            }
            this.filteredServices = result;
        },
        countByCategory(cat) {
            return this.services.filter(s => s.loai_dich_vu === cat).length
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
        },
        truncateText(text, max) {
            if (!text) return ''
            return text.length > max ? text.substring(0, max) + '...' : text
        },
        getCategoryEmoji(category) {
            const map = { 'Nước uống': '🥤', 'Đồ ăn nhẹ': '🍿', 'Đồ ăn chính': '🍗', 'Tráng miệng': '🍰', 'Khác': '📦' }
            return map[category] || '📦'
        },
        // === Image upload handlers ===
        onCreateImageChange(e) {
            const file = e.target.files[0]
            if (file) {
                this.createImageFile = file
                this.createImagePreview = URL.createObjectURL(file)
            }
        },
        removeCreateImage() {
            this.createImageFile = null
            this.createImagePreview = null
            if (this.$refs.createImageInput) this.$refs.createImageInput.value = ''
        },
        onUpdateImageChange(e) {
            const file = e.target.files[0]
            if (file) {
                this.updateImageFile = file
                this.updateImagePreview = URL.createObjectURL(file)
            }
        },
        removeUpdateImage() {
            this.updateImageFile = null
            this.updateImagePreview = null
            if (this.$refs.updateImageInput) this.$refs.updateImageInput.value = ''
        },
        // === CRUD with FormData ===
        createService() {
            const formData = new FormData()
            formData.append('dich_vu_name', this.create_service.dich_vu_name)
            formData.append('loai_dich_vu', this.create_service.loai_dich_vu)
            formData.append('price', this.create_service.price)
            formData.append('description', this.create_service.description || '')
            if (this.createImageFile) {
                formData.append('image', this.createImageFile)
            }
            axios.post('http://127.0.0.1:8000/api/admin/dich-vu/create-data', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
                .then((res) => {
                    if (res.data.status) {
                        this.getServices()
                        this.create_service = { dich_vu_name: '', loai_dich_vu: 'Nước uống', price: 0, description: '' }
                        this.removeCreateImage()
                        alert(res.data.message || 'Thêm dịch vụ thành công')
                    } else {
                        alert(res.data.message)
                    }
                })
                .catch((error) => console.error(error))
        },
        selectServiceForEdit(service) {
            this.update_service = { ...service }
            this.updateImageFile = null
            this.updateImagePreview = null
        },
        updateService() {
            const formData = new FormData()
            formData.append('dich_vu_id', this.update_service.dich_vu_id)
            formData.append('dich_vu_name', this.update_service.dich_vu_name)
            formData.append('loai_dich_vu', this.update_service.loai_dich_vu)
            formData.append('price', this.update_service.price)
            formData.append('description', this.update_service.description || '')
            if (this.updateImageFile) {
                formData.append('image', this.updateImageFile)
            }
            axios.post('http://127.0.0.1:8000/api/admin/dich-vu/update-data', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
                .then((res) => {
                    if (res.data.status) {
                        this.getServices()
                        this.updateImageFile = null
                        this.updateImagePreview = null
                        alert(res.data.message || 'Cập nhật thành công')
                    } else {
                        alert(res.data.message)
                    }
                })
                .catch((error) => console.error(error))
        },
        selectServiceForDelete(service) {
            this.delete_service = { ...service }
        },
        deleteService() {
            axios.post('http://127.0.0.1:8000/api/admin/dich-vu/delete-data', this.delete_service)
                .then((res) => {
                    if (res.data.status) {
                        this.getServices()
                        alert(res.data.message || 'Xóa thành công')
                    } else {
                        alert(res.data.message)
                    }
                })
                .catch((error) => console.error(error))
        }
    }
}
</script>

<style scoped>
.dv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.dv-name {
    font-weight: 700;
    color: var(--natural-text);
    letter-spacing: -0.02em;
    font-size: 1.05rem;
    transition: color var(--transition-fast);
}

tr:hover .dv-name {
    color: var(--natural-primary);
}

.dv-price {
    color: var(--natural-secondary);
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: -0.02em;
}

.dv-desc {
    color: var(--natural-muted);
    font-size: 13px;
}

/* Thumbnail trong bảng */
.dv-thumb-wrap {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--natural-border);
    flex-shrink: 0;
}

.dv-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.dv-thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(250,250,249,0.8);
    font-size: 1.5rem;
}

/* Image preview trong modal */
.dv-image-preview {
    position: relative;
    display: inline-block;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid var(--natural-border);
    background: rgba(250,250,249,1);
}

.dv-image-preview img {
    width: 140px;
    height: 100px;
    object-fit: cover;
    display: block;
}

.dv-remove-preview {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(239,68,68,0.9);
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.dv-remove-preview:hover {
    background: #dc2626;
}

.dv-current-label {
    position: absolute;
    bottom: 4px;
    left: 4px;
    background: rgba(0,0,0,0.5);
    color: white;
    font-size: 9px;
    padding: 2px 8px;
    border-radius: 8px;
    font-weight: 600;
    letter-spacing: 0.05em;
}

@media (max-width: 768px) {
    .dv-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>