<template>
    <div>
        <!-- Filter -->
        <div class="td-filter-bar">
            <h3 class="td-title serif">Thực đơn</h3>
            <div class="td-filter-btns">
                <button v-for="tab in filterTabs" :key="tab.value"
                    class="uv-filter-btn"
                    :class="{ active: selectedLoai === tab.value }"
                    @click="filterLoaiDichVu(tab.value)">
                    {{ tab.icon }} {{ tab.label }}
                </button>
            </div>
        </div>

        <!-- Thông báo trạng thái bàn đang chọn -->
        <div v-if="selectedBan" class="td-ban-indicator">
            <div v-if="activeBanHoaDon" class="td-ban-active">
                <i class="fa-solid fa-circle-dot" style="color: #059669;"></i>
                <span>Đang phục vụ: <strong>{{ selectedBan.name }}</strong></span>
                <span class="font-mono" style="font-size: 11px; color: var(--natural-muted);">
                    HĐ #{{ activeBanHoaDon.hoa_don_id }}
                </span>
            </div>
            <div v-else class="td-ban-no-hd">
                <i class="fa-solid fa-circle-exclamation" style="color: #f59e0b;"></i>
                <span><strong>{{ selectedBan.name }}</strong> chưa có hóa đơn. Vui lòng mở bàn trước.</span>
            </div>
        </div>
        <div v-else class="td-ban-indicator td-ban-no-select">
            <i class="fa-solid fa-hand-pointer"></i>
            <span>Chưa chọn bàn. Qua tab <strong>Phòng bàn</strong> để chọn bàn trước.</span>
        </div>

        <!-- Loading -->
        <div v-if="isLoadingMenu" class="td-empty">
            <div class="spinner-border" style="color: var(--natural-primary);" role="status"></div>
            <p class="label-xs" style="margin-top: 16px;">Đang tải thực đơn...</p>
        </div>

        <!-- Empty -->
        <div v-else-if="filteredDichVu.length === 0" class="td-empty">
            <i class="fa-solid fa-utensils" style="font-size: 3rem; color: var(--natural-muted); opacity: 0.15;"></i>
            <h3 class="serif" style="font-style: italic; font-weight: 300; color: var(--natural-muted); margin-top: 16px;">Không có món nào</h3>
            <p class="label-xs">Thử đổi bộ lọc khác</p>
        </div>

        <!-- Menu Grid -->
        <div v-else class="td-grid">
            <button v-for="item in filteredDichVu" :key="item.dich_vu_id" class="td-card" @click="openOrderModal(item)">
                <div class="td-card-media">
                    <img v-if="item.image_url" class="td-card-img" :src="item.image_url" :alt="item.dich_vu_name" />
                    <div v-else class="td-card-emoji">{{ getCategoryEmoji(item.loai_dich_vu) }}</div>
                </div>
                <div class="td-card-body">
                    <h4 class="td-card-name">{{ item.dich_vu_name }}</h4>
                    <p class="td-card-price font-mono">{{ formatPrice(item.price) }}</p>
                    <span class="badge-organic badge-info-organic" style="font-size: 8px;">{{ item.loai_dich_vu || 'Khác' }}</span>
                </div>
                <div class="td-card-overlay">
                    <div class="td-card-overlay-btn">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                </div>
            </button>
        </div>

        <!-- Modal Đặt Món -->
        <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Đặt Món</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" v-if="selectedItem">
                        <!-- Item info -->
                        <div class="td-modal-item">
                            <div class="td-modal-media">
                                <img v-if="selectedItem.image_url" class="td-modal-img" :src="selectedItem.image_url" :alt="selectedItem.dich_vu_name" />
                                <span v-else class="td-modal-emoji">{{ getCategoryEmoji(selectedItem.loai_dich_vu) }}</span>
                            </div>
                            <div>
                                <p style="font-weight: 700; margin: 0;">{{ selectedItem.dich_vu_name }}</p>
                                <p class="font-mono" style="color: var(--natural-secondary); font-weight: 700; margin: 0;">{{ formatPrice(selectedItem.price) }}</p>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-3">
                            <label class="form-label">Số lượng</label>
                            <div class="td-quantity-wrap">
                                <button class="td-qty-btn" @click="quantity > 1 && quantity--">−</button>
                                <input type="number" class="td-qty-input font-mono" v-model.number="quantity" min="1">
                                <button class="td-qty-btn" @click="quantity++">+</button>
                            </div>
                        </div>

                        <!-- Select invoice / Table info -->
                        <div class="mb-3">
                            <label class="form-label">Thêm vào hóa đơn</label>
                            <!-- Nếu đã xác định được hóa đơn của bàn đang chọn -->
                            <div v-if="activeBanHoaDon" class="td-hoadon-selected">
                                <i class="fa-solid fa-check-circle" style="color: #059669;"></i>
                                <span>
                                    <strong>{{ selectedBan.name }}</strong> — HĐ #{{ activeBanHoaDon.hoa_don_id }}
                                </span>
                            </div>
                            <!-- Fallback: dropdown chọn hóa đơn thủ công -->
                            <div v-else>
                                <select class="form-select" v-model="selectedHoaDonId">
                                    <option :value="null" disabled>-- Chọn hóa đơn --</option>
                                    <option v-for="hd in danhSachHoaDon" :key="hd.hoa_don_id" :value="hd.hoa_don_id">
                                        HĐ #{{ hd.hoa_don_id }} — Bàn {{ hd.ban_id }}
                                    </option>
                                </select>
                                <small v-if="danhSachHoaDon.length === 0" style="color: #d97706; margin-top: 8px; display: block;">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Không có hóa đơn nào đang hoạt động. Vui lòng mở bàn trước.
                                </small>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="td-modal-total">
                            <span class="label-xs">Thành tiền</span>
                            <span class="font-mono" style="font-weight: 700; font-size: 1.3rem; color: var(--natural-primary);">{{ formatPrice(selectedItem.price * quantity) }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" @click="confirmOrder" :disabled="isOrdering || (!activeBanHoaDon && !selectedHoaDonId)">
                            {{ isOrdering ? 'Đang xử lý...' : 'Xác nhận đặt' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'
import { Modal } from 'bootstrap'

export default {
    props: {
        // Nhận bàn đang chọn từ indexView.vue (layout cha)
        selectedBan: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            dich_vu: [],
            filteredDichVu: [],
            selectedLoai: 'all',
            selectedItem: null,
            quantity: 1,
            danhSachHoaDon: [],        // Tất cả hóa đơn chưa TT
            activeBanHoaDon: null,     // Hóa đơn của bàn đang chọn (auto-detect)
            selectedHoaDonId: null,    // Fallback khi không có selectedBan
            isLoadingMenu: false,
            isOrdering: false,
            orderModal: null,
            filterTabs: [
                { label: 'Tất cả', value: 'all', icon: '📋' },
                { label: 'Nước uống', value: 'Nước uống', icon: '🥤' },
                { label: 'Đồ ăn nhẹ', value: 'Đồ ăn nhẹ', icon: '🍿' },
                { label: 'Đồ ăn chính', value: 'Đồ ăn chính', icon: '🍗' },
                { label: 'Tráng miệng', value: 'Tráng miệng', icon: '🍰' },
                { label: 'Combo', value: 'Combo', icon: '🎁' },
                { label: 'Khác', value: 'Khác', icon: '📦' },
            ]
        }
    },
    watch: {
        // Khi bàn thay đổi → tìm hóa đơn chưa TT của bàn đó
        selectedBan: {
            immediate: true,
            handler(newBan) {
                if (newBan && newBan.id) {
                    this.findActiveBanHoaDon(newBan.id);
                } else {
                    this.activeBanHoaDon = null;
                }
            }
        }
    },
    mounted() {
        this.getMenuData()
        this.getActiveHoaDon()
    },
    methods: {
        getMenuData() {
            this.isLoadingMenu = true
            axios.get('http://127.0.0.1:8000/api/admin/dich-vu/get-data')
                .then((res) => {
                    this.dich_vu = res.data.data || []
                    this.filteredDichVu = this.dich_vu
                })
                .catch((error) => {
                    console.error("Lỗi:", error)
                })
                .finally(() => {
                    this.isLoadingMenu = false
                })
        },
        // Lấy tất cả hóa đơn chưa thanh toán
        getActiveHoaDon() {
            axios.get('http://127.0.0.1:8000/api/admin/hoa-don/get-data')
                .then((res) => {
                    this.danhSachHoaDon = (res.data.data || []).filter(hd => {
                        const s = hd.status;
                        return s === 'chưa thanh toán' || s === 1 || s === 'unpaid';
                    });
                    // Sau khi có danh sách, tìm HĐ của bàn đang chọn
                    if (this.selectedBan && this.selectedBan.id) {
                        this.findActiveBanHoaDonFromList(this.selectedBan.id);
                    }
                })
                .catch((error) => {
                    console.error("Lỗi:", error)
                })
        },
        // Tìm hóa đơn chưa TT của bàn theo ban_id từ danh sách đã có
        findActiveBanHoaDonFromList(banId) {
            const found = this.danhSachHoaDon.find(hd => hd.ban_id == banId);
            this.activeBanHoaDon = found || null;
        },
        // Gọi API để tìm HĐ chưa TT của bàn (dùng khi selectedBan thay đổi)
        findActiveBanHoaDon(banId) {
            axios.get(`http://127.0.0.1:8000/api/admin/hoa-don/get-bill-by-ban-id?ban_id=${banId}`)
                .then((res) => {
                    const data = res.data.data;
                    if (data && !Array.isArray(data) && data.hoa_don_id) {
                        this.activeBanHoaDon = data;
                    } else if (Array.isArray(data) && data.length > 0) {
                        this.activeBanHoaDon = data[0];
                    } else {
                        this.activeBanHoaDon = null;
                    }
                })
                .catch(() => {
                    this.activeBanHoaDon = null;
                });
        },
        filterLoaiDichVu(loai) {
            this.selectedLoai = loai
            if (loai === 'all') {
                this.filteredDichVu = this.dich_vu
            } else {
                this.filteredDichVu = this.dich_vu.filter(item => item.loai_dich_vu === loai)
            }
        },
        getCategoryEmoji(category) {
            const map = {
                'Nước uống': '🥤', 'Đồ ăn nhẹ': '🍿', 'Đồ ăn chính': '🍗',
                'Tráng miệng': '🍰', 'Combo': '🎁', 'Khác': '📦'
            }
            return map[category] || '📦'
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
        },
        openOrderModal(item) {
            this.selectedItem = item
            this.quantity = 1
            // Nếu không có activeBanHoaDon thì reset selectedHoaDonId để user chọn thủ công
            if (!this.activeBanHoaDon) {
                this.selectedHoaDonId = null;
                // Refresh danh sách hóa đơn
                this.getActiveHoaDon();
            }
            const modal = new Modal(document.getElementById('orderModal'))
            modal.show()
        },
        async confirmOrder() {
            // Ưu tiên dùng hóa đơn của bàn đang chọn, fallback sang dropdown
            const hoaDonId = this.activeBanHoaDon
                ? this.activeBanHoaDon.hoa_don_id
                : this.selectedHoaDonId;

            if (!hoaDonId) {
                alert('Vui lòng chọn bàn hoặc hóa đơn trước!')
                return
            }
            if (this.quantity < 1 || !this.selectedItem?.dich_vu_id) {
                alert('Thông tin không hợp lệ')
                return
            }
            this.isOrdering = true
            try {
                await axios.post('http://127.0.0.1:8000/api/admin/chi-tiet-hoa-don/create-data', {
                    hoa_don_id: hoaDonId,
                    dich_vu_id: this.selectedItem.dich_vu_id,
                    price: this.selectedItem.price,
                    so_luong: parseInt(this.quantity),
                    total: this.selectedItem.price * parseInt(this.quantity)
                })
                alert(`Đã thêm "${this.selectedItem.dich_vu_name}" vào hóa đơn!`)
                const modal = Modal.getInstance(document.getElementById('orderModal'))
                if (modal) modal.hide()
                // Thông báo cho layout cha biết để refresh BanDetail
                this.$emit('order-added')
            } catch (error) {
                console.error("Lỗi:", error)
                alert('Không thể thêm món. Vui lòng thử lại.')
            } finally {
                this.isOrdering = false
            }
        }
    }
}
</script>

<style scoped>
/* Banner trạng thái bàn đang chọn */
.td-ban-indicator {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: var(--radius-md);
    margin-bottom: 20px;
    font-size: 13px;
}

.td-ban-active {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(5, 150, 105, 0.06);
    border: 1px solid rgba(5, 150, 105, 0.2);
    border-radius: var(--radius-md);
    padding: 10px 16px;
    width: 100%;
}

.td-ban-no-hd {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(245, 158, 11, 0.06);
    border: 1px solid rgba(245, 158, 11, 0.25);
    border-radius: var(--radius-md);
    padding: 10px 16px;
    width: 100%;
}

.td-ban-no-select {
    background: rgba(214, 211, 209, 0.3);
    border: 1px solid rgba(214, 211, 209, 0.5);
}

.td-hoadon-selected {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: rgba(5, 150, 105, 0.05);
    border: 1px solid rgba(5, 150, 105, 0.2);
    border-radius: var(--radius-md);
    font-size: 13px;
}

.td-filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.td-title {
    font-size: 1.5rem;
    font-weight: 300;
    font-style: italic;
    color: var(--natural-text);
    margin: 0;
}

.td-filter-btns {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.uv-filter-btn {
    padding: 6px 14px;
    border: 1px solid var(--natural-border);
    border-radius: var(--radius-full);
    background: white;
    color: var(--natural-muted);
    font-family: var(--font-sans);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.uv-filter-btn:hover {
    border-color: var(--natural-primary);
    color: var(--natural-primary);
}

.uv-filter-btn.active {
    background: var(--natural-primary);
    color: white;
    border-color: var(--natural-primary);
}

.td-empty {
    text-align: center;
    padding: 80px 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.td-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
}

.td-card {
    position: relative;
    background-color: var(--natural-surface);
    border: 1px solid var(--natural-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    cursor: pointer;
    transition: all var(--transition-normal);
    text-align: center;
    padding: 0;
    font-family: var(--font-sans);
}

.td-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-4px);
}

.td-card-media {
    background-color: rgba(250, 250, 249, 0.5);
}

.td-card-img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.td-card-emoji {
    font-size: 3rem;
    padding: 24px 16px 8px;
    background-color: rgba(250, 250, 249, 0.5);
}

.td-card-body {
    padding: 12px 16px 20px;
}

.td-card-name {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin: 0 0 4px 0;
    color: var(--natural-text);
}

.td-card-price {
    font-weight: 700;
    color: var(--natural-secondary);
    margin: 0 0 8px 0;
    font-size: 1rem;
}

.td-card-overlay {
    position: absolute;
    inset: 0;
    background-color: rgba(74, 93, 78, 0.85);
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity var(--transition-normal);
    border-radius: calc(var(--radius-lg) - 1px);
}

.td-card:hover .td-card-overlay {
    opacity: 1;
}

.td-card-overlay-btn {
    width: 48px;
    height: 48px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--natural-primary);
    font-size: 20px;
    box-shadow: var(--shadow-lg);
}

.td-modal-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background-color: rgba(250, 250, 249, 1);
    border-radius: var(--radius-md);
    margin-bottom: 20px;
}

.td-modal-media {
    height: 52px;
    width: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.td-modal-img {
    height: 52px;
    width: 52px;
    object-fit: cover;
    border-radius: 14px;
    border: 1px solid rgba(0, 0, 0, 0.06);
    display: block;
}

.td-modal-emoji {
    font-size: 2.5rem;
}

.td-quantity-wrap {
    display: flex;
    align-items: center;
    border: 1px solid var(--natural-border);
    border-radius: var(--radius-md);
    overflow: hidden;
    width: 160px;
}

.td-qty-btn {
    padding: 10px 16px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: var(--natural-primary);
    font-weight: 700;
    transition: background-color var(--transition-fast);
}

.td-qty-btn:hover {
    background-color: rgba(250, 250, 249, 1);
}

.td-qty-input {
    width: 60px;
    text-align: center;
    border: none;
    border-left: 1px solid var(--natural-border);
    border-right: 1px solid var(--natural-border);
    padding: 8px;
    font-size: 16px;
    font-weight: 700;
    outline: none;
}

.td-modal-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background-color: rgba(74, 93, 78, 0.05);
    border-radius: var(--radius-md);
    margin-top: 16px;
}
</style>