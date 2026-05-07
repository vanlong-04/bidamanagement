<template>
    <div>
        <!-- Header -->
        <header class="page-header">
            <div>
                <h2 class="page-title">Lịch sử <strong>Hóa đơn</strong></h2>
                <p class="page-subtitle">Xem và in hóa đơn cho từng bàn.</p>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <select class="select-organic" style="min-width: 180px;" v-model="selectedTableId" @change="loadBillData">
                    <option value="">-- Chọn bàn --</option>
                    <option v-for="table in tableList" :key="table.ban_id" :value="table.ban_id">
                        Bàn {{ table.ban_name || table.ban_id }}
                    </option>
                </select>
                <button v-if="billData" class="btn-organic btn-ghost-organic" @click="printBill">
                    <i class="fa-solid fa-print"></i> In
                </button>
                <button v-if="billData" class="btn-organic btn-primary-organic" @click="exportPDF">
                    <i class="fa-solid fa-file-pdf"></i> Xuất PDF
                </button>
            </div>
        </header>

        <!-- Content -->
        <div class="card-organic card-organic-xl" style="padding: 40px;">
            <!-- Loading -->
            <div v-if="isLoadingBill" class="bill-placeholder">
                <div class="spinner-border" style="color: var(--natural-primary);" role="status"></div>
                <p class="label-xs" style="margin-top: 16px;">Đang tải hóa đơn...</p>
            </div>

            <!-- No selection -->
            <div v-else-if="!billData && !noBillMessage" class="bill-placeholder">
                <i class="fa-solid fa-file-invoice" style="font-size: 3rem; color: var(--natural-muted); opacity: 0.15;"></i>
                <h3 class="serif" style="font-style: italic; font-weight: 300; color: var(--natural-primary); margin-top: 16px;">Chọn một bàn để xem hóa đơn</h3>
                <p class="label-xs">Chọn số bàn từ danh sách bên trên</p>
            </div>

            <!-- No bill -->
            <div v-else-if="noBillMessage" class="bill-placeholder">
                <i class="fa-solid fa-inbox" style="font-size: 3rem; color: var(--natural-muted); opacity: 0.15;"></i>
                <h3 class="serif" style="font-style: italic; font-weight: 300; color: var(--natural-muted); margin-top: 16px;">{{ noBillMessage }}</h3>
                <p class="label-xs">Bàn này hiện không có hóa đơn chưa thanh toán</p>
            </div>

            <!-- Bill Content -->
            <div v-else>
                <div ref="billContent" class="bill-receipt">
                    <!-- Header -->
                    <div class="bill-receipt-header">
                        <h3 class="serif" style="font-size: 2rem; font-weight: 300; font-style: italic; color: var(--natural-primary); margin-bottom: 4px;">🎱 Bida Vip</h3>
                        <p class="label-xs" style="letter-spacing: 0.2em;">Giải trí • Thư giãn • Chất lượng</p>
                        <div style="margin-top: 12px;">
                            <span class="badge-organic" style="background: var(--natural-primary); color: white;">HÓA ĐƠN THANH TOÁN</span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bill-info-grid">
                        <div>
                            <p class="label-xs">Bàn</p>
                            <p class="bill-info-value">{{ getTableName(billData.ban_id) }}</p>
                            <p class="label-xs" style="margin-top: 6px; color: var(--natural-primary);">
                                {{ getTableTypeLabel(billData) }} - {{ formatPriceShort(getTableHourlyRate(billData)) }}/giờ
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <p class="label-xs">Nhân viên</p>
                            <p class="bill-info-value">{{ billData.nhan_vien_id || 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="bill-info-grid">
                        <div>
                            <p class="label-xs">Giờ vào</p>
                            <p class="bill-info-value font-mono">{{ formatDateTime(billData.start_time) }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p class="label-xs">Giờ ra</p>
                            <p class="bill-info-value font-mono">{{ billData.end_time ? formatDateTime(billData.end_time) : 'Đang chơi' }}</p>
                        </div>
                    </div>

                    <!-- Items -->
                    <table class="table-organic" style="margin: 24px 0;">
                        <thead>
                            <tr>
                                <th style="padding: 12px 16px;">Sản phẩm</th>
                                <th style="padding: 12px 16px; text-align: center;">SL</th>
                                <th style="padding: 12px 16px; text-align: right;">Đơn giá</th>
                                <th style="padding: 12px 16px; text-align: right;">T.Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="billData.chi_tiet_hoa_dons && billData.chi_tiet_hoa_dons.length > 0"
                                v-for="item in billData.chi_tiet_hoa_dons" :key="item.chi_tiet_hoa_don_id">
                                <td style="padding: 12px 16px; font-weight: 600;">{{ item.dich_vu?.dich_vu_name || 'N/A' }}</td>
                                <td style="padding: 12px 16px; text-align: center;" class="font-mono">{{ item.quantity || item.so_luong }}</td>
                                <td style="padding: 12px 16px; text-align: right;" class="font-mono">{{ formatPriceShort(item.price) }}</td>
                                <td style="padding: 12px 16px; text-align: right; font-weight: 700;" class="font-mono">{{ formatPriceShort(item.price * (item.quantity || item.so_luong || 1)) }}</td>
                            </tr>
                            <tr v-if="!billData.chi_tiet_hoa_dons || billData.chi_tiet_hoa_dons.length === 0">
                                <td colspan="4" style="text-align: center; padding: 32px; color: var(--natural-muted); font-style: italic;">Không có sản phẩm</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Summary -->
                    <div class="bill-summary">
                        <div class="bill-summary-row">
                            <span class="label-xs">Tổng dịch vụ & đồ uống</span>
                            <span class="font-mono" style="font-weight: 600;">{{ formatPrice(calculateServiceTotal()) }}</span>
                        </div>
                        <div class="bill-summary-row" v-if="billData.total_hours">
                            <span class="label-xs">Thời gian chơi: {{ billData.total_hours }} giờ</span>
                            <span class="font-mono" style="font-weight: 600; color: var(--natural-muted);">{{ formatPrice(getTableCharge(billData)) }}</span>
                        </div>
                        <div class="bill-summary-total">
                            <span class="serif" style="font-style: italic; font-weight: 300; font-size: 1.5rem;">Tổng cộng</span>
                            <span class="font-mono" style="font-weight: 700; font-size: 1.5rem; color: var(--natural-primary);">{{ formatPrice(calculateGrandTotal()) }}</span>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bill-receipt-footer">
                        <p style="font-weight: 700; margin-bottom: 4px;">Cảm ơn quý khách! 🙏</p>
                        <p class="label-xs">{{ getCurrentDateTime() }}</p>
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
            isLoadingBill: false,
            selectedTableId: '',
            billData: null,
            tableList: [],
            noBillMessage: ''
        }
    },
    mounted() {
        this.loadTableList()
    },
    methods: {
        async loadTableList() {
            try {
                const res = await axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
                this.tableList = res.data.data || []
            } catch (error) {
                console.error('Error loading tables:', error)
            }
        },
        getTableName(banId) {
            const table = this.tableList.find(t => t.ban_id == banId)
            return table ? table.ban_name : `Bàn ${banId}`
        },
        getTableTypeLabel(bill) {
            const loaiBan = Number(bill?.ban?.loai_ban ?? this.tableList.find(t => t.ban_id == bill?.ban_id)?.loai_ban)
            return loaiBan === 1 ? 'Thường' : 'VIP'
        },
        getTableHourlyRate(bill) {
            const rate = Number(bill?.ban?.hourly_rate)
            if (rate > 0) return rate
            const table = this.tableList.find(t => t.ban_id == bill?.ban_id)
            return Number(table?.hourly_rate || (Number(table?.loai_ban) === 1 ? 50000 : 70000))
        },
        getTableCharge(bill) {
            const savedCharge = Number(bill?.charge || 0)
            if (savedCharge > 0) return savedCharge
            const hours = Number(bill?.total_hours || 0)
            return Math.round(hours * this.getTableHourlyRate(bill))
        },
        async loadBillData() {
            if (!this.selectedTableId) {
                this.billData = null
                this.noBillMessage = ''
                return
            }
            this.isLoadingBill = true
            this.noBillMessage = ''
            try {
                const res = await axios.get(
                    `http://127.0.0.1:8000/api/admin/hoa-don/get-bill-by-ban-id?ban_id=${this.selectedTableId}`
                )
                const data = res.data.data
                if (data && ((Array.isArray(data) && data.length > 0) || (!Array.isArray(data) && data))) {
                    this.billData = Array.isArray(data) ? data[0] : data
                } else {
                    this.billData = null
                    this.noBillMessage = `Bàn ${this.getTableName(this.selectedTableId)} không có hóa đơn`
                }
            } catch (error) {
                console.error('Error:', error)
                this.billData = null
            } finally {
                this.isLoadingBill = false
            }
        },
        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0)
        },
        formatPriceShort(price) {
            return new Intl.NumberFormat('vi-VN').format(price || 0) + 'đ'
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
        getCurrentDateTime() {
            return new Date().toLocaleString('vi-VN')
        },
        calculateServiceTotal() {
            if (!this.billData?.chi_tiet_hoa_dons) return 0
            return this.billData.chi_tiet_hoa_dons.reduce((sum, item) => {
                return sum + (item.price * (item.quantity || item.so_luong || 1))
            }, 0)
        },
        calculateGrandTotal() {
            // total_amount đã lưu tổng tất cả (bao gồm dịch vụ) khi thanh toán
            // Nếu chưa thanh toán, tính từ chi tiết hóa đơn
            if (this.billData?.total_amount && Number(this.billData.total_amount) > 0) {
                return Number(this.billData.total_amount);
            }
            return this.calculateServiceTotal() + this.getTableCharge(this.billData);
        },
        printBill() {
            window.print()
        },
        async exportPDF() {
            try {
                const html2pdf = (await import('html2pdf.js')).default
                const element = this.$refs.billContent
                await html2pdf().set({
                    margin: [10, 10, 10, 10],
                    filename: `hoa-don-${this.getTableName(this.selectedTableId)}-${Date.now()}.pdf`,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
                }).from(element).save()
                alert('Xuất PDF thành công!')
            } catch (error) {
                console.error('PDF error:', error)
                alert('Lỗi khi xuất PDF')
            }
        }
    }
}
</script>

<style scoped>
.bill-placeholder {
    text-align: center;
    padding: 80px 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.bill-receipt {
    max-width: 520px;
    margin: 0 auto;
    padding: 36px;
    border: 1px solid var(--natural-border);
    border-radius: var(--radius-lg);
    background: white;
}

.bill-receipt-header {
    text-align: center;
    padding-bottom: 24px;
    margin-bottom: 24px;
    border-bottom: 1px solid rgba(245, 245, 244, 1);
}

.bill-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.bill-info-value {
    font-weight: 700;
    margin: 4px 0 0 0;
    font-size: 14px;
}

.bill-summary {
    border-top: 1px solid rgba(245, 245, 244, 1);
    padding-top: 16px;
}

.bill-summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.bill-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    margin-top: 8px;
    border-top: 1px solid rgba(214, 211, 209, 1);
}

.bill-receipt-footer {
    text-align: center;
    border-top: 1px solid rgba(245, 245, 244, 1);
    margin-top: 24px;
    padding-top: 24px;
}

@media print {
    .page-header { display: none !important; }
    .bill-receipt { border: none; max-width: 100%; }
}
</style>
