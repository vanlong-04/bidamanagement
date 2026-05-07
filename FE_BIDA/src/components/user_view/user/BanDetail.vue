<template>
    <div class="bd-panel" v-if="selectedBan">

        <!-- HEADER: Tên bàn + Đồng hồ -->
        <div class="bd-header">
            <div class="bd-header-info">
                <h2 class="bd-table-name serif">{{ selectedBan.name }}</h2>
                <p class="label-xs" style="margin: 4px 0 0 0;">
                    <span v-if="hoaDon">HĐ #{{ hoaDon.hoa_don_id }}</span>
                    <span v-else style="color: #f59e0b;">Đang tải hóa đơn...</span>
                </p>
            </div>
            <!-- Đồng hồ tính giờ -->
            <div v-if="hoaDon" class="bd-clock-wrap">
                <div class="bd-clock font-mono">{{ elapsedDisplay }}</div>
                <p class="bd-clock-label">{{ formatPrice(tableTimeFee) }}</p>
            </div>
            <div v-else class="bd-clock-wrap bd-clock-empty">
                <div class="bd-clock font-mono">00:00:00</div>
                <p class="bd-clock-label">Chờ HĐ...</p>
            </div>
            <!-- Countdown for expected end time -->
            <div v-if="hoaDon && hoaDon.expected_end_time" class="bd-clock-wrap bd-countdown" :class="{ 'bd-warning': remainingSeconds < 600 }">
                <div class="bd-clock font-mono">{{ remainingDisplay }}</div>
                <p class="bd-clock-label">Còn lại</p>
            </div>
            <!-- Booking Indicator for Status 3 -->
            <div v-if="!hoaDon && selectedBan.status === 3" class="bd-clock-wrap" style="background: #d97706;">
                <div class="bd-clock font-mono">ĐÃ ĐẶT</div>
                <p class="bd-clock-label">{{ formatTimeShort(selectedBan.activeBooking?.thoi_gian_dat) }}</p>
            </div>
        </div>

        <!-- START TIME INFO -->
        <div v-if="hoaDon" class="bd-start-info">
            <i class="fa-solid fa-play-circle" style="color: #059669;"></i>
            <span>Bắt đầu: <strong class="font-mono">{{ formatTime(hoaDon.start_time) }}</strong></span>
            <span v-if="hoaDon.discount_amount > 0" class="bd-rate-badge" style="background: #fef3c7; color: #d97706;">
                <i class="fa-solid fa-bolt"></i> Happy Hour -{{ formatPrice(hoaDon.discount_amount) }}
            </span>
            <span class="bd-rate-badge">{{ tableTypeLabel }} - {{ formatPrice(hourlyRate) }}/giờ</span>
        </div>

        <!-- LOADING -->
        <div v-if="isLoading" class="bd-empty">
            <div class="spinner-border" style="color: var(--natural-primary);"></div>
        </div>

        <!-- CONTENT: Danh sách dịch vụ -->
        <div v-else class="bd-content">
            <div v-if="chiTietHoaDon.length === 0" class="bd-empty">
                <i class="fa-solid fa-receipt" style="font-size:2.5rem; color:var(--natural-muted); opacity:0.15;"></i>
                <p class="serif" style="font-style:italic; color:var(--natural-muted); margin-top:12px;">Chưa có dịch vụ nào</p>
                <p class="label-xs">Qua tab <strong>Thực đơn</strong> để thêm</p>
            </div>
            <div v-else>
                <p class="label-xs" style="padding:4px 4px 12px; letter-spacing:0.3em;">DỊCH VỤ ĐÃ GỌI</p>
                <div v-for="item in chiTietHoaDon" :key="item.chi_tiet_hoa_don_id" class="bd-item">
                    <div class="bd-item-info">
                        <p class="bd-item-name">{{ item.dich_vu_name || 'Dịch vụ #' + item.dich_vu_id }}</p>
                        <p class="label-xs" style="margin:2px 0 0 0;">{{ formatPrice(item.price) }}/cái</p>
                    </div>
                    <div class="bd-item-controls">
                        <button class="bd-qty-btn" @click="changeQty(item, -1)"><i class="fa-solid fa-minus"></i></button>
                        <span class="font-mono bd-qty-val">{{ item.quantity }}</span>
                        <button class="bd-qty-btn" @click="changeQty(item, 1)"><i class="fa-solid fa-plus"></i></button>
                    </div>
                    <div class="bd-item-right">
                        <span class="font-mono bd-item-total">{{ formatPrice(item.price * item.quantity) }}</span>
                        <button class="bd-del-btn" @click="deleteItem(item)"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOOKING INFO SECTION (When Status 3 and no Bill) -->
        <div v-if="!hoaDon && selectedBan.status === 3" class="bd-content" style="background: rgba(217,119,6,0.03); border-top: 1px solid rgba(217,119,6,0.1);">
            <div class="text-center py-4">
                <i class="fa-solid fa-calendar-check" style="font-size: 3rem; color: #d97706; opacity: 0.8;"></i>
                <h4 class="serif mt-3" style="color: #d97706; font-style: italic;">Thông tin đặt bàn</h4>
                <div class="mt-4 text-start bg-white p-3 rounded shadow-sm">
                    <div class="mb-2">
                        <span class="label-xs d-block text-muted">KHÁCH HÀNG</span>
                        <span style="font-weight: 700; font-size: 1.1rem;">{{ selectedBan.activeBooking?.ten_khach_hang }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="label-xs d-block text-muted">SỐ ĐIỆN THOẠI</span>
                        <span class="font-mono">{{ selectedBan.activeBooking?.so_dien_thoai }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="label-xs d-block text-muted">THỜI GIAN</span>
                        <span style="font-weight: 600;">{{ formatDateTimeDisplay(selectedBan.activeBooking?.thoi_gian_dat) }}</span>
                    </div>
                    <div v-if="selectedBan.activeBooking?.ghi_chu">
                        <span class="label-xs d-block text-muted">GHI CHÚ</span>
                        <p class="mb-0 small">{{ selectedBan.activeBooking?.ghi_chu }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER: Tổng tiền -->
        <div class="bd-footer" v-if="hoaDon">
            <div class="bd-total-row">
                <span class="label-xs">Dịch vụ ({{ chiTietHoaDon.length }} món)</span>
                <span class="font-mono" style="font-weight:600;">{{ formatPrice(serviceTotal) }}</span>
            </div>
            <div class="bd-total-row bd-total-time">
                <span class="label-xs"><i class="fa-solid fa-clock" style="margin-right:6px;"></i>Phí bàn ({{ elapsedDisplay }})</span>
                <span class="font-mono" style="font-weight:700; color:var(--natural-primary);">{{ formatPrice(tableTimeFee) }}</span>
            </div>
            <div class="bd-total-grand">
                <span class="serif" style="font-style:italic; font-weight:300; font-size:1.2rem;">Tổng cộng</span>
                <span class="font-mono" style="font-weight:700; font-size:1.3rem; color:var(--natural-primary);">{{ formatPrice(grandTotal) }}</span>
            </div>
            <!-- Nút Thanh toán -->
            <button class="bd-pay-btn" @click="handlePayBill">
                <i class="fa-solid fa-credit-card"></i>
                THANH TOÁN
            </button>
            <!-- Nút Gộp Bàn -->
            <button class="bd-merge-btn" @click="openMergeModal">
                <i class="fa-solid fa-code-merge"></i>
                GỘP BÀN
            </button>
            <!-- Nút Gia Hạn -->
            <button class="bd-extend-btn" @click="openExtendModal">
                <i class="fa-solid fa-clock-rotate-left"></i>
                GIA HẠN GIỜ
            </button>
            <!-- Nút In Hóa Đơn -->
            <button class="bd-print-btn" @click="openPrintModal">
                <i class="fa-solid fa-print"></i>
                XEM & IN HÓA ĐƠN
            </button>
        </div>

        <!-- FOOTER: For Booked status -->
        <div class="bd-footer" v-if="!hoaDon && selectedBan.status === 3">
            <button class="bd-pay-btn" style="background: #d97706;" @click="handleCheckInBooking">
                <i class="fa-solid fa-play"></i>
                XÁC NHẬN NHẬN BÀN
            </button>
            <button class="bd-print-btn" style="border-color: #ef4444; color: #ef4444;" @click="handleCancelBooking">
                <i class="fa-solid fa-calendar-xmark"></i>
                HỦY ĐẶT BÀN
            </button>
        </div>

        <!-- MODAL: In Hóa Đơn Receipt -->
        <div class="modal fade" id="printBillModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom:1px solid var(--natural-border);">
                        <h5 class="modal-title serif" style="font-style:italic; color:var(--natural-primary); font-size:1.2rem;">Hóa Đơn — {{ selectedBan.name }}</h5>
                        <div style="display:flex; gap:10px; align-items:center;">
                            <button class="btn-organic btn-ghost-organic" style="padding:8px 18px; font-size:11px;" @click="printReceipt">
                                <i class="fa-solid fa-print"></i> In
                            </button>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                    <div class="modal-body" style="padding:32px;" id="printReceiptArea">
                        <!-- Header receipt -->
                        <div class="receipt-header">
                            <h2 class="serif" style="font-size:2rem; font-weight:300; font-style:italic; color:var(--natural-primary); margin:0;">🎱 Bida Vip</h2>
                            <p class="label-xs" style="letter-spacing:0.2em; margin:4px 0 12px;">Giải trí • Thư giãn • Chất lượng</p>
                            <span class="badge" style="background:var(--natural-primary); color:white; padding:4px 14px; border-radius:20px; font-size:10px; font-weight:700; letter-spacing:0.15em;">HÓA ĐƠN THANH TOÁN</span>
                        </div>

                        <!-- Info grid -->
                        <div class="receipt-info-grid">
                            <div>
                                <p class="label-xs">Bàn</p>
                                <p style="font-weight:700; font-size:1.1rem; margin:0;">{{ selectedBan.name }}</p>
                            </div>
                            <div style="text-align:right;">
                                <p class="label-xs">Số hóa đơn</p>
                                <p class="font-mono" style="font-weight:700; font-size:1.1rem; margin:0; color:var(--natural-primary);">#{{ hoaDon?.hoa_don_id }}</p>
                            </div>
                        </div>
                        <div class="receipt-info-grid">
                            <div>
                                <p class="label-xs">Giờ vào</p>
                                <p class="font-mono" style="font-weight:600; margin:0;">{{ formatTimeDisplay(hoaDon?.start_time) }}</p>
                            </div>
                            <div style="text-align:right;">
                                <p class="label-xs">Giờ ra</p>
                                <p class="font-mono" style="font-weight:600; margin:0;">{{ formatTimeDisplay(null) }} (Hiện tại)</p>
                            </div>
                        </div>
                        <hr style="border-color:var(--natural-border); margin:20px 0;">

                        <!-- Items table -->
                        <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
                            <thead>
                                <tr style="border-bottom:2px solid var(--natural-border);">
                                    <th style="padding:10px 8px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:0.1em; color:var(--natural-muted);">Sản phẩm</th>
                                    <th style="padding:10px 8px; text-align:center; font-size:11px; text-transform:uppercase; letter-spacing:0.1em; color:var(--natural-muted);">SL</th>
                                    <th style="padding:10px 8px; text-align:right; font-size:11px; text-transform:uppercase; letter-spacing:0.1em; color:var(--natural-muted);">Giá</th>
                                    <th style="padding:10px 8px; text-align:right; font-size:11px; text-transform:uppercase; letter-spacing:0.1em; color:var(--natural-muted);">T.Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="chiTietHoaDon.length === 0">
                                    <td colspan="4" style="padding:24px; text-align:center; color:var(--natural-muted); font-style:italic;">Chưa có dịch vụ nào</td>
                                </tr>
                                <tr v-for="item in chiTietHoaDon" :key="item.chi_tiet_hoa_don_id" style="border-bottom:1px solid rgba(245,245,244,1);">
                                    <td style="padding:12px 8px; font-weight:600;">{{ item.dich_vu_name }}</td>
                                    <td style="padding:12px 8px; text-align:center;" class="font-mono">{{ item.quantity }}</td>
                                    <td style="padding:12px 8px; text-align:right;" class="font-mono">{{ formatPrice(item.price) }}</td>
                                    <td style="padding:12px 8px; text-align:right; font-weight:700;" class="font-mono">{{ formatPrice(item.price * item.quantity) }}</td>
                                </tr>
                                <!-- Dòng phí bàn -->
                                <tr style="border-bottom:1px solid rgba(245,245,244,1);">
                                    <td style="padding:12px 8px; font-weight:600;"><i class="fa-solid fa-clock" style="margin-right:6px; color:var(--natural-primary);"></i>Phí bàn ({{ elapsedDisplay }})</td>
                                    <td style="padding:12px 8px; text-align:center;" class="font-mono">1</td>
                                    <td style="padding:12px 8px; text-align:right;" class="font-mono">{{ formatPrice(hourlyRate) }}/giờ</td>
                                    <td style="padding:12px 8px; text-align:right; font-weight:700; color:var(--natural-primary);" class="font-mono">{{ formatPrice(tableTimeFee) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Summary -->
                        <div style="background:rgba(74,93,78,0.04); border-radius:12px; padding:20px;">
                            <div style="display:flex; justify-content:space-between; padding:6px 0; font-size:13px;">
                                <span style="color:var(--natural-muted);">Dịch vụ &amp; đồ uống</span>
                                <span class="font-mono" style="font-weight:600;">{{ formatPrice(serviceTotal) }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; padding:6px 0; font-size:13px;">
                                <span style="color:var(--natural-muted);">Phí bàn ({{ elapsedHoursDisplay }} giờ)</span>
                                <span class="font-mono" style="font-weight:600; color:var(--natural-primary);">{{ formatPrice(tableTimeFee) }}</span>
                            </div>
                            <div v-if="vipDiscount > 0" style="display:flex; justify-content:space-between; padding:6px 0; font-size:13px; color:#d97706; font-weight:600;">
                                <span>Chiết khấu VIP (10%)</span>
                                <span class="font-mono">-{{ formatPrice(vipDiscount) }}</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; padding-top:14px; margin-top:8px; border-top:2px solid var(--natural-border);">
                                <span class="serif" style="font-style:italic; font-weight:300; font-size:1.3rem;">Tổng cộng</span>
                                <span class="font-mono" style="font-weight:700; font-size:1.5rem; color:var(--natural-primary);">{{ formatPrice(finalTotal) }}</span>
                            </div>
                        </div>

                        <!-- Footer receipt -->
                        <div style="text-align:center; margin-top:28px; padding-top:20px; border-top:1px dashed var(--natural-border);">
                            <div style="margin-bottom: 16px;">
                                <p class="label-xs" style="margin-bottom: 8px; color: var(--natural-primary);">QUÉT MÃ ĐỂ THANH TOÁN</p>
                                <img :src="'https://img.vietqr.io/image/MB-123456789-compact2.png?amount=' + finalTotal + '&addInfo=Thanh toan hoa don ' + (hoaDon?.hoa_don_id || '') + '&accountName=BIDA VIP'" alt="QR Thanh toán" style="width: 150px; height: 150px; object-fit: contain; border-radius: 8px; border: 1px solid var(--natural-border); padding: 4px;">
                            </div>
                            <p class="label-xs" style="letter-spacing:0.2em;">Cảm ơn quý khách đã sử dụng dịch vụ!</p>
                            <p style="font-size:12px; color:var(--natural-muted); margin-top:4px;">BIDA VIP • Hẹn gặp lại</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Gộp Bàn -->
        <div class="modal fade" id="mergeBillModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="border-bottom:1px solid var(--natural-border);">
                        <h5 class="modal-title serif" style="font-style:italic; color:#d97706;">Gộp Bàn: {{ selectedBan.name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding:24px;">
                        <p class="label-xs" style="margin-bottom: 8px;">CHỌN CÁC BÀN MUỐN GỘP VÀO ĐÂY</p>
                        <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--natural-border); border-radius: var(--radius-md); padding: 10px;">
                            <div v-if="activeTables.length === 0" style="color: var(--natural-muted); font-size: 13px; text-align: center; padding: 10px;">
                                Không có bàn nào khác đang mở.
                            </div>
                            <div v-for="ban in activeTables" :key="ban.ban_id" style="padding: 8px 0; border-bottom: 1px solid rgba(245,245,244,1);">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; margin: 0;">
                                    <input type="checkbox" :value="ban.ban_id" v-model="mergeTargetBanIdList" style="width: 16px; height: 16px; accent-color: #d97706;">
                                    {{ ban.ban_name }} ({{ ban.loai_ban_label || (ban.loai_ban == 2 ? 'VIP' : 'Thường') }})
                                </label>
                            </div>
                        </div>
                        <p style="font-size: 13px; color: var(--natural-muted); margin-top: 16px;">
                            <i class="fa-solid fa-circle-info"></i> Tất cả bàn được chọn sẽ được dồn vào hoá đơn tổng của bàn <strong>{{ selectedBan.name }}</strong>. Các bàn gốc sẽ chuyển thành bàn trống.
                        </p>
                    </div>
                    <div class="modal-footer" style="border-top:none; padding:16px 24px;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:var(--radius-md);">Hủy</button>
                        <button type="button" class="btn btn-warning" @click="confirmMerge" :disabled="mergeTargetBanIdList.length === 0 || isMerging" style="background:#d97706; color:white; border:none; border-radius:var(--radius-md); font-weight:600;">
                            <span v-if="isMerging"><i class="fa-solid fa-spinner fa-spin"></i> Đang gộp...</span>
                            <span v-else>Xác nhận Gộp</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Gia Hạn Giờ -->
        <div class="modal fade" id="extendTimeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title serif">Gia hạn thời gian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="label-xs">CHỌN THỜI GIAN MUỐN THÊM</p>
                        <div class="d-grid gap-2 mt-3">
                            <button class="btn btn-organic" @click="extendTime(30)">+ 30 Phút</button>
                            <button class="btn btn-organic" @click="extendTime(60)">+ 1 Giờ</button>
                            <button class="btn btn-organic" @click="extendTime(120)">+ 2 Giờ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OFFCANVAS: Thanh toán -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasPayBill">
            <div class="offcanvas-header" style="border-bottom:1px solid var(--natural-border); padding:24px 28px;">
                <h5 class="offcanvas-title serif" style="font-style:italic; color:var(--natural-primary); font-size:1.3rem;">
                    Thanh toán — {{ selectedBan.name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body" style="padding:24px;">
                <!-- Danh sách dịch vụ -->
                <div class="bd-pay-section">
                    <p class="label-xs" style="letter-spacing:0.2em; margin-bottom:12px;">DỊCH VỤ</p>
                    <div v-if="chiTietHoaDon.length === 0" style="color:var(--natural-muted); font-style:italic; font-size:13px;">Không có dịch vụ</div>
                    <div v-for="item in chiTietHoaDon" :key="item.chi_tiet_hoa_don_id" class="bd-pay-item">
                        <span style="flex:1;">{{ item.dich_vu_name }}</span>
                        <span class="font-mono">×{{ item.quantity }}</span>
                        <span class="font-mono" style="font-weight:600; min-width:90px; text-align:right;">{{ formatPrice(item.price * item.quantity) }}</span>
                    </div>
                </div>

                <!-- Phí bàn -->
                <div class="bd-pay-section">
                    <p class="label-xs" style="letter-spacing:0.2em; margin-bottom:12px;">PHÍ BÀN</p>
                    <div class="bd-pay-item">
                        <span style="flex:1;">Thời gian ({{ elapsedDisplay }})</span>
                        <span class="font-mono" style="font-weight:600; color:var(--natural-primary);">{{ formatPrice(tableTimeFee) }}</span>
                    </div>
                    <div style="font-size:12px; color:var(--natural-muted); margin-top:4px;">
                        {{ formatPrice(hourlyRate) }}/giờ × {{ elapsedHoursDisplay }} giờ
                    </div>
                </div>

                <!-- Khách hàng VIP -->
                <div class="bd-pay-section">
                    <p class="label-xs" style="letter-spacing:0.2em; margin-bottom:8px;">THÀNH VIÊN / VIP</p>
                    <div style="display:flex; gap:8px;">
                        <input type="text" v-model="searchPhone" placeholder="Nhập SĐT khách hàng" class="form-control" style="font-size:13px; border-radius:var(--radius-md);" />
                        <button class="btn btn-secondary" @click="searchVIP" :disabled="isSearchingVIP" style="font-size:13px; border-radius:var(--radius-md);">Tìm</button>
                    </div>
                    <div v-if="foundKhachHang" style="margin-top:8px; padding:10px; border-radius:var(--radius-md); background:rgba(217,119,6,0.1); color:#d97706; font-size:13px; font-weight:600;">
                        <i :class="foundKhachHang.hang_thanh_vien === 'VIP' ? 'fa-solid fa-crown' : 'fa-solid fa-user'"></i> {{ foundKhachHang.ten_khach_hang }} ({{ foundKhachHang.hang_thanh_vien }})
                    </div>
                </div>

                <!-- Tổng kết -->
                <div class="bd-pay-totals">
                    <div class="bd-pay-row"><span class="label-xs">Dịch vụ</span><span class="font-mono" style="font-weight:700;">{{ formatPrice(serviceTotal) }}</span></div>
                    <div class="bd-pay-row"><span class="label-xs">Phí bàn</span><span class="font-mono" style="font-weight:700; color:var(--natural-primary);">{{ formatPrice(tableTimeFee) }}</span></div>
                    <div v-if="vipDiscount > 0" class="bd-pay-row" style="color:#d97706;">
                        <span class="label-xs">Chiết khấu VIP (10%)</span>
                        <span class="font-mono" style="font-weight:700;">-{{ formatPrice(vipDiscount) }}</span>
                    </div>
                    <div class="bd-pay-total-main">
                        <span class="serif" style="font-style:italic; font-weight:300; font-size:1.2rem;">Khách trả</span>
                        <span class="font-mono" style="font-weight:700; font-size:1.6rem; color:var(--natural-primary);">{{ formatPrice(finalTotal) }}</span>
                    </div>
                </div>

                <button class="bd-confirm-btn" @click="confirmPayBill" :disabled="isPaying">
                    <span v-if="isPaying"><i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...</span>
                    <span v-else><i class="fa-solid fa-check-circle"></i> Xác nhận thanh toán</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Offcanvas, Modal } from 'bootstrap';

export default {
    props: {
        selectedBan: { type: Object, required: true }
    },
    data() {
        return {
            hoaDon: null,
            chiTietHoaDon: [],
            isLoading: false,
            isPaying: false,
            tableTimer: null,
            elapsedSeconds: 0,
            activeTables: [],
            mergeTargetBanIdList: [],
            isMerging: false,
            searchPhone: '',
            foundKhachHang: null,
            isSearchingVIP: false,
            remainingSeconds: 0,
            hasNotified: false
        };
    },
    computed: {
        tableTypeLabel() {
            if (this.selectedBan?.loai_ban_label) return this.selectedBan.loai_ban_label;
            return Number(this.selectedBan?.loai_ban) === 2 ? 'VIP' : 'Thường';
        },
        hourlyRate() {
            if (Number(this.selectedBan?.hourly_rate) > 0) {
                return Number(this.selectedBan.hourly_rate);
            }
            return Number(this.selectedBan?.loai_ban) === 2 ? 70000 : 50000;
        },
        elapsedDisplay() {
            const h = Math.floor(this.elapsedSeconds / 3600);
            const m = Math.floor((this.elapsedSeconds % 3600) / 60);
            const s = this.elapsedSeconds % 60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        },
        remainingDisplay() {
            if (this.remainingSeconds <= 0) return "HẾT GIỜ";
            const h = Math.floor(this.remainingSeconds / 3600);
            const m = Math.floor((this.remainingSeconds % 3600) / 60);
            const s = this.remainingSeconds % 60;
            return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        },
        elapsedHours() {
            return this.elapsedSeconds / 3600;
        },
        // Số phút đã chơi, làm tròn lên (chơi 1 phút 1 giây = tính 2 phút)
        elapsedMinutes() {
            return Math.ceil(this.elapsedSeconds / 60);
        },
        elapsedHoursDisplay() {
            // Hiển thị theo phút thực tế (VD: 1 giờ 20 phút → 1.33 giờ)
            return (this.elapsedMinutes / 60).toFixed(2);
        },
        tableTimeFee() {
            // Tính tiền chính xác theo từng phút: phút × (giá/60)
            // Làm tròn đến 100đ cho gọn
            return Math.round((this.elapsedMinutes / 60) * this.hourlyRate / 100) * 100;
        },
        serviceTotal() {
            return this.chiTietHoaDon.reduce((s, item) =>
                s + Number(item.price || 0) * Number(item.quantity || 1), 0);
        },
        grandTotal() {
            return this.serviceTotal + this.tableTimeFee;
        },
        vipDiscount() {
            if (this.foundKhachHang && this.foundKhachHang.hang_thanh_vien === 'VIP') {
                return this.grandTotal * 0.1;
            }
            return 0;
        },
        finalTotal() {
            return this.grandTotal - this.vipDiscount;
        }
    },
    watch: {
        'selectedBan.id': {
            immediate: true,
            handler(newId) {
                if (newId) {
                    this.stopTimer();
                    this.hoaDon = null;
                    this.chiTietHoaDon = [];
                    this.elapsedSeconds = 0;
                    this.searchPhone = '';
                    this.foundKhachHang = null;
                    this.getHoaDonDetail();
                }
            }
        },
        'selectedBan._refresh'(v) {
            if (v && this.hoaDon) this.getChiTietHoaDon();
        }
    },
    beforeUnmount() {
        this.stopTimer();
    },
    methods: {
        getHoaDonDetail() {
            if (!this.selectedBan) return;
            this.isLoading = true;
            axios.get(`http://127.0.0.1:8000/api/admin/hoa-don/get-bill-by-ban-id?ban_id=${this.selectedBan.id}`)
                .then((res) => {
                    const data = res.data.data;
                    if (data && !Array.isArray(data) && data.hoa_don_id) {
                        this.hoaDon = data;
                    } else if (Array.isArray(data) && data.length > 0) {
                        this.hoaDon = data[0];
                    } else {
                        // Chưa có HĐ → thử lại sau 1s (bàn vừa được mở)
                        this.hoaDon = null;
                        setTimeout(() => this.getHoaDonDetail(), 1000);
                        return;
                    }
                    this.getChiTietHoaDon();
                    this.startTimer();
                })
                .catch(console.error)
                .finally(() => { this.isLoading = false; });
        },

        getChiTietHoaDon() {
            if (!this.hoaDon) return;
            axios.get(`http://127.0.0.1:8000/api/admin/chi-tiet-hoa-don/get-data?hoa_don_id=${this.hoaDon.hoa_don_id}`)
                .then((res) => {
                    this.chiTietHoaDon = (res.data.data || []).map(item => ({
                        ...item,
                        quantity: item.quantity || item.so_luong || 1,
                        dich_vu_name: item.dich_vu?.dich_vu_name || item.dich_vu_name || `Dịch vụ #${item.dich_vu_id}`,
                        price: Number(item.price || 0)
                    }));
                })
                .catch(console.error);
        },

        startTimer() {
            if (!this.hoaDon?.start_time) return;
            this.stopTimer();
            const start = new Date(this.hoaDon.start_time);
            const tick = () => {
                this.elapsedSeconds = Math.max(0, Math.floor((new Date() - start) / 1000));
                
                if (this.hoaDon?.expected_end_time) {
                    const end = new Date(this.hoaDon.expected_end_time);
                    this.remainingSeconds = Math.max(0, Math.floor((end - new Date()) / 1000));
                    
                    // Notify when 10 minutes left
                    if (this.remainingSeconds > 0 && this.remainingSeconds <= 600 && !this.hasNotified) {
                        alert(`Bàn ${this.selectedBan.name} sắp hết thời gian chơi (còn lại < 10 phút)!`);
                        this.hasNotified = true;
                    }
                    if (this.remainingSeconds > 600) {
                        this.hasNotified = false; // Reset if time extended
                    }
                }
            };
            tick();
            this.tableTimer = setInterval(tick, 1000);
        },

        stopTimer() {
            if (this.tableTimer) { clearInterval(this.tableTimer); this.tableTimer = null; }
        },

        changeQty(item, delta) {
            const newQty = item.quantity + delta;
            if (newQty < 1) return;
            item.quantity = newQty;
            axios.post('http://127.0.0.1:8000/api/admin/chi-tiet-hoa-don/update-data', {
                chi_tiet_hoa_don_id: item.chi_tiet_hoa_don_id,
                so_luong: newQty,
                price: item.price
            }).catch(() => { item.quantity -= delta; });
        },

        deleteItem(item) {
            if (!confirm(`Xóa "${item.dich_vu_name}" khỏi hóa đơn?`)) return;
            axios.post('http://127.0.0.1:8000/api/admin/chi-tiet-hoa-don/delete-data', {
                chi_tiet_hoa_don_id: item.chi_tiet_hoa_don_id
            }).then(() => this.getChiTietHoaDon())
              .catch(() => alert('Lỗi khi xóa!'));
        },

        handlePayBill() {
            const oc = new Offcanvas(document.getElementById('offcanvasPayBill'));
            oc.show();
        },

        confirmPayBill() {
            this.isPaying = true;
            this.stopTimer();
            const now = this.getNow();

            axios.post('http://127.0.0.1:8000/api/admin/hoa-don/update-data', {
                hoa_don_id: this.hoaDon.hoa_don_id,
                end_time: now,
                total_hours: Math.round(this.elapsedHours * 100) / 100,
                total_amount: this.finalTotal,
                status: 'đã thanh toán',
                payment_method: 'cash'
            })
            .then(() => axios.post('http://127.0.0.1:8000/api/admin/hoa-don/update-status', {
                ban_id: this.selectedBan.id,
                status: 1
            }))
            .then(() => {
                const oc = Offcanvas.getInstance(document.getElementById('offcanvasPayBill'));
                if (oc) oc.hide();
                this.hoaDon = null;
                this.chiTietHoaDon = [];
                this.elapsedSeconds = 0;
                this.$emit('reloadBan');
                // Quay lại trang bàn của nhân viên sau khi thanh toán thành công
                this.$router.push('/user/dat-ban');
            })
            .catch((err) => {
                this.startTimer();
                alert('Lỗi khi thanh toán!');
                console.error(err);
            })
            .finally(() => { this.isPaying = false; });
        },

        formatPrice(price) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price || 0);
        },
        formatTime(dt) {
            if (!dt) return '';
            return new Date(dt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        },
        formatTimeDisplay(dt) {
            const d = dt ? new Date(dt) : new Date();
            return d.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) +
                   ' ' + d.toLocaleDateString('vi-VN');
        },
        formatDateTimeDisplay(dt) {
            if (!dt) return '';
            return new Date(dt).toLocaleString('vi-VN', { 
                hour: '2-digit', minute: '2-digit',
                day: '2-digit', month: '2-digit', year: 'numeric'
            });
        },
        formatTimeShort(dt) {
            if (!dt) return '';
            return new Date(dt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        },
        openPrintModal() {
            const modal = new Modal(document.getElementById('printBillModal'));
            modal.show();
        },
        printReceipt() {
            const content = document.getElementById('printReceiptArea').innerHTML;
            const printWin = window.open('', '_blank', 'width=700,height=900');
            printWin.document.write(`
                <html><head><title>Hóa Đơn - ${this.selectedBan.name}</title>
                <style>
                    body { font-family: 'Segoe UI', sans-serif; padding: 32px; color: #1c1917; }
                    .font-mono { font-family: monospace; }
                    .label-xs { font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: #78716c; }
                    .serif { font-family: Georgia, serif; }
                    .receipt-header { text-align: center; margin-bottom: 24px; }
                    .receipt-info-grid { display: grid; grid-template-columns: 1fr 1fr; margin: 12px 0; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 10px 8px; }
                    @media print { body { padding: 16px; } }
                </style></head>
                <body>${content}</body></html>
            `);
            printWin.document.close();
            printWin.focus();
            setTimeout(() => { printWin.print(); }, 300);
        },
        getNow() {
            const pad = n => n < 10 ? '0' + n : n;
            const d = new Date();
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        },
        openMergeModal() {
            axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
                .then(res => {
                    if(res.data.data) {
                        this.activeTables = res.data.data.filter(b => b.status === 2 && b.ban_id !== this.selectedBan.id);
                    }
                    this.mergeTargetBanIdList = [];
                    const modal = new Modal(document.getElementById('mergeBillModal'));
                    modal.show();
                })
                .catch(err => alert('Lỗi tải danh sách bàn!'));
        },
        confirmMerge() {
            if (this.mergeTargetBanIdList.length === 0) return;
            this.isMerging = true;
            axios.post('http://127.0.0.1:8000/api/admin/hoa-don/gop-ban', {
                ban_id_from_list: this.mergeTargetBanIdList,
                ban_id_to: this.selectedBan.id
            })
            .then(res => {
                alert('Gộp bàn thành công!');
                const modalEl = document.getElementById('mergeBillModal');
                const modal = Modal.getInstance(modalEl);
                if (modal) modal.hide();
                this.$emit('reloadBan');
                this.$router.push('/user/dat-ban');
            })
            .catch(err => {
                alert(err.response?.data?.message || 'Lỗi gộp bàn!');
            })
            .finally(() => { this.isMerging = false; });
        },
        searchVIP() {
            if (!this.searchPhone) return;
            this.isSearchingVIP = true;
            axios.get(`http://127.0.0.1:8000/api/admin/khach-hang/search?phone=${this.searchPhone}`)
                .then(res => {
                    if (res.data.data) {
                        this.foundKhachHang = res.data.data;
                    } else {
                        alert('Không tìm thấy khách hàng với SĐT này.');
                        this.foundKhachHang = null;
                    }
                })
                .catch(err => {
                    alert('Lỗi tra cứu khách hàng!');
                })
                .finally(() => { this.isSearchingVIP = false; });
        },
        openExtendModal() {
            const modal = new Modal(document.getElementById('extendTimeModal'));
            modal.show();
        },
        extendTime(mins) {
            axios.post('http://127.0.0.1:8000/api/admin/hoa-don/extend-time', {
                hoa_don_id: this.hoaDon.hoa_don_id,
                minutes: mins
            })
            .then(res => {
                alert(res.data.message);
                this.hoaDon.expected_end_time = res.data.expected_end_time;
                const modal = Modal.getInstance(document.getElementById('extendTimeModal'));
                if (modal) modal.hide();
            })
            .catch(err => {
                alert('Lỗi gia hạn thời gian!');
            });
        },
        handleCheckInBooking() {
            if (!this.selectedBan.activeBooking) return;
            if (confirm(`Xác nhận khách "${this.selectedBan.activeBooking.ten_khach_hang}" đã đến nhận bàn?`)) {
                axios.post('http://127.0.0.1:8000/api/admin/dat-ban/update-status', {
                    id: this.selectedBan.activeBooking.id,
                    status: 'completed'
                })
                .then(() => {
                    alert('Đã xác nhận nhận bàn. Vui lòng mở bàn để bắt đầu tính giờ.');
                    this.$emit('reloadBan');
                })
                .catch(err => alert('Lỗi khi nhận bàn!'));
            }
        },
        handleCancelBooking() {
            if (!this.selectedBan.activeBooking) return;
            if (confirm(`Bạn có chắc chắn muốn hủy lịch đặt bàn của khách "${this.selectedBan.activeBooking.ten_khach_hang}"?`)) {
                axios.post('http://127.0.0.1:8000/api/admin/dat-ban/update-status', {
                    id: this.selectedBan.activeBooking.id,
                    status: 'cancelled'
                })
                .then(() => {
                    alert('Đã hủy lịch đặt bàn.');
                    this.$emit('reloadBan');
                })
                .catch(err => alert('Lỗi khi hủy đặt bàn!'));
            }
        }
    }
};
</script>

<style scoped>
.bd-panel { display: flex; flex-direction: column; height: 100%; font-family: var(--font-sans); }

/* Header */
.bd-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 24px 28px; border-bottom: 1px solid var(--natural-border);
    background: linear-gradient(135deg, rgba(74,93,78,0.04) 0%, transparent 100%);
}
.bd-table-name { font-size: 1.6rem; font-weight: 300; font-style: italic; color: var(--natural-primary); margin: 0; }

/* Clock */
.bd-clock-wrap { text-align: center; background: var(--natural-primary); border-radius: var(--radius-lg); padding: 12px 20px; }
.bd-clock-empty { background: var(--natural-muted); opacity: 0.5; }
.bd-clock { font-size: 1.4rem; font-weight: 700; color: white; letter-spacing: 0.05em; }
.bd-clock-label { font-size: 11px; color: rgba(255,255,255,0.8); margin: 4px 0 0 0; font-weight: 600; }

/* Start info */
.bd-start-info {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 28px; background: rgba(5,150,105,0.04);
    border-bottom: 1px solid rgba(5,150,105,0.1); font-size: 13px;
}
.bd-rate-badge {
    margin-left: auto; background: rgba(74,93,78,0.1); color: var(--natural-primary);
    padding: 2px 10px; border-radius: var(--radius-full); font-size: 11px; font-weight: 700;
}

/* Content */
.bd-content { flex: 1; overflow-y: auto; padding: 20px 28px; }
.bd-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 40px; text-align: center; height: 100%; }

/* Items */
.bd-item {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 0; border-bottom: 1px solid var(--natural-border);
}
.bd-item:last-child { border-bottom: none; }
.bd-item-info { flex: 1; min-width: 0; }
.bd-item-name { font-size: 14px; font-weight: 600; color: var(--natural-text); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bd-item-controls { display: flex; align-items: center; gap: 4px; background: var(--natural-bg); border-radius: var(--radius-md); padding: 4px; }
.bd-qty-btn { width: 28px; height: 28px; border-radius: var(--radius-sm); border: none; background: white; color: var(--natural-primary); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all var(--transition-fast); box-shadow: var(--shadow-sm); }
.bd-qty-btn:hover { background: var(--natural-primary); color: white; }
.bd-qty-val { width: 32px; text-align: center; font-size: 14px; font-weight: 700; }
.bd-item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
.bd-item-total { font-size: 13px; font-weight: 700; color: var(--natural-primary); }
.bd-del-btn { width: 24px; height: 24px; border-radius: 50%; border: none; background: rgba(239,68,68,0.08); color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; transition: all var(--transition-fast); }
.bd-del-btn:hover { background: #ef4444; color: white; }

/* Footer */
.bd-footer { padding: 20px 28px; border-top: 1px solid var(--natural-border); background: var(--natural-surface); }
.bd-total-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; }
.bd-total-time { background: rgba(74,93,78,0.04); padding: 8px 12px; border-radius: var(--radius-md); margin: 4px 0; }
.bd-total-grand {
    display: flex; justify-content: space-between; align-items: center;
    padding: 16px; background: rgba(74,93,78,0.06); border-radius: var(--radius-lg);
    margin: 12px 0;
}
.bd-pay-btn {
    width: 100%; padding: 16px;
    background: var(--natural-primary); color: white; border: none;
    border-radius: var(--radius-lg); font-family: var(--font-sans); font-size: 11px;
    font-weight: 700; letter-spacing: 0.15em; cursor: pointer;
    box-shadow: 0 6px 20px var(--natural-primary-shadow);
    transition: all var(--transition-normal); display: flex; align-items: center; justify-content: center; gap: 10px;
}
.bd-pay-btn:hover { background: var(--natural-primary-hover); transform: translateY(-1px); }

/* Offcanvas */
.bd-pay-section { margin-bottom: 24px; }
.bd-pay-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(245,245,244,1); font-size: 13px; }
.bd-pay-totals { background: rgba(250,250,249,1); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; }
.bd-pay-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; }
.bd-pay-total-main { display: flex; justify-content: space-between; align-items: center; padding-top: 16px; margin-top: 12px; border-top: 2px solid var(--natural-border); }
.bd-confirm-btn {
    width: 100%; padding: 18px;
    background: #059669; color: white; border: none; border-radius: var(--radius-lg);
    font-family: var(--font-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 0.15em; cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 10px;
    box-shadow: 0 6px 20px rgba(5,150,105,0.25); transition: all var(--transition-normal);
}
.bd-confirm-btn:hover { background: #047857; transform: translateY(-1px); }
.bd-confirm-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

/* Nút In Hóa Đơn */
.bd-print-btn {
    width: 100%; padding: 13px;
    background: transparent; color: var(--natural-primary);
    border: 2px solid var(--natural-primary); border-radius: var(--radius-lg);
    font-family: var(--font-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 0.15em; cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 10px; margin-top: 10px;
    transition: all var(--transition-normal);
}
.bd-print-btn:hover { background: var(--natural-primary); color: white; transform: translateY(-1px); }

/* Nút Gộp Bàn */
.bd-merge-btn {
    width: 100%; padding: 13px;
    background: transparent; color: #d97706;
    border: 2px solid #d97706; border-radius: var(--radius-lg);
    font-family: var(--font-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 0.15em; cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 10px; margin-top: 10px;
    transition: all var(--transition-normal);
}
.bd-merge-btn:hover { background: #d97706; color: white; transform: translateY(-1px); }

/* Receipt styles */
.receipt-header { text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid var(--natural-border); }
.receipt-info-grid { display: grid; grid-template-columns: 1fr 1fr; margin: 12px 0; gap: 8px; }

/* Countdown */
.bd-countdown { background: #0ea5e9; margin-top: 8px; }
.bd-warning { background: #ef4444; animation: pulse-bg 1s infinite; }
@keyframes pulse-bg {
    0% { opacity: 1; }
    50% { opacity: 0.8; }
    100% { opacity: 1; }
}
.bd-extend-btn {
    width: 100%; padding: 13px;
    background: transparent; color: #0ea5e9;
    border: 2px solid #0ea5e9; border-radius: var(--radius-lg);
    font-family: var(--font-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 0.15em; cursor: pointer; display: flex; align-items: center;
    justify-content: center; gap: 10px; margin-top: 10px;
    transition: all var(--transition-normal);
}
.bd-extend-btn:hover { background: #0ea5e9; color: white; transform: translateY(-1px); }
</style>