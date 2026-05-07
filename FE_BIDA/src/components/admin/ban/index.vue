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
    </div>
</template>
<script>
export default {
    data() {
        return {}
    }
}
</script>
