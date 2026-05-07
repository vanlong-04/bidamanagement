<template>
  <div class="page-section">
    <header class="page-header">
      <div>
        <h2 class="page-title">Đặt bàn trước</h2>
        <p class="page-subtitle">Tạo và quản lý lịch đặt bàn cho khách hàng.</p>
      </div>
      <button class="btn-organic btn-primary-organic" @click="openModal">
        <i class="fa-solid fa-calendar-plus"></i> Đặt bàn mới
      </button>
    </header>

    <div class="card-organic card-organic-xl">
      <div class="filter-bar">
        <div class="search-organic-wrap">
          <i class="fa-solid fa-magnifying-glass search-icon"></i>
          <input
            type="text"
            class="input-organic"
            placeholder="Tìm tên khách hoặc SĐT"
            v-model="searchQuery"
          />
        </div>
        <select class="select-organic" v-model="statusFilter">
          <option value="all">Tất cả trạng thái</option>
          <option value="pending">Chờ xác nhận</option>
          <option value="confirmed">Đã xác nhận</option>
          <option value="completed">Đã nhận bàn</option>
          <option value="cancelled">Đã hủy</option>
        </select>
      </div>

      <div class="summary-bar">
        <div class="summary-card">
          <span>Tổng lượt đặt</span>
          <strong>{{ bookingCounts.total }}</strong>
        </div>
        <div class="summary-card">
          <span>Đang chờ</span>
          <strong>{{ bookingCounts.pending }}</strong>
        </div>
        <div class="summary-card">
          <span>Đã xác nhận</span>
          <strong>{{ bookingCounts.confirmed }}</strong>
        </div>
        <div class="summary-card">
          <span>Đã hủy</span>
          <strong>{{ bookingCounts.cancelled }}</strong>
        </div>
      </div>

      <div v-if="isLoading" class="loading-block">
        <div class="spinner-border" role="status"></div>
      </div>

      <div v-else>
        <table class="table-organic" v-if="filteredBookings.length > 0">
          <thead>
            <tr>
              <th>#</th>
              <th>Khách hàng</th>
              <th>SĐT</th>
              <th>Thời gian</th>
              <th>Bàn</th>
              <th>Ghi chú</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(booking, index) in filteredBookings" :key="booking.id">
              <td>{{ index + 1 }}</td>
              <td>{{ booking.ten_khach_hang }}</td>
              <td>{{ booking.so_dien_thoai }}</td>
              <td>{{ formatDateTime(booking.thoi_gian_dat) }}</td>
              <td>
                <span v-if="booking.ban" class="badge-organic badge-info-organic">
                  {{ booking.ban.ban_name }}
                </span>
                <span v-else>Chưa gán bàn</span>
              </td>
              <td>
                <span class="note-text">{{ booking.ghi_chu || 'Không có' }}</span>
              </td>
              <td>
                <span :class="statusClass(booking.status)" class="badge-organic">
                  {{ statusText(booking.status) }}
                </span>
              </td>
              <td>
                <button
                  class="btn-organic btn-ghost-organic"
                  @click="updateStatus(booking.id, 'confirmed')"
                  :disabled="booking.status === 'confirmed'"
                >
                  Xác nhận
                </button>
                <button
                  class="btn-organic btn-danger-organic"
                  @click="deleteBooking(booking.id)"
                >
                  Xóa
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div v-else class="empty-state">
          <p>Hiện chưa có lịch đặt bàn phù hợp.</p>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalDatBan" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-organic">
          <div class="modal-header">
            <h5 class="modal-title">Đặt bàn mới</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="form-grid">
              <label>
                Tên khách hàng
                <input type="text" class="input-organic" v-model="form.ten_khach_hang" />
              </label>
              <label>
                Số điện thoại
                <input type="text" class="input-organic" v-model="form.so_dien_thoai" />
              </label>
              <label>
                Thời gian đặt
                <input type="datetime-local" class="input-organic" v-model="form.thoi_gian_dat" />
              </label>
              <label>
                Loại bàn
                <select class="select-organic" v-model="form.loai_ban">
                  <option :value="1">Bida Lỗ</option>
                  <option :value="2">Bida Phăng</option>
                  <option :value="3">Bida Lỗ VIP</option>
                  <option :value="4">Bida Phăng VIP</option>
                </select>
              </label>
              <label>
                Bàn cụ thể
                <select class="select-organic" v-model="form.ban_id">
                  <option :value="null">Chọn bàn</option>
                  <option v-for="ban in tableList" :key="ban.ban_id" :value="ban.ban_id">
                    {{ ban.ban_name }} - {{ ban.loai_ban_label }} ({{ ban.status_label || ban.status }})
                  </option>
                </select>
              </label>
              <label class="col-12">
                Ghi chú
                <textarea class="input-organic" rows="3" v-model="form.ghi_chu"></textarea>
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn-organic btn-ghost-organic" data-bs-dismiss="modal">Huỷ</button>
            <button type="button" class="btn-organic btn-primary-organic" @click="saveBooking" :disabled="isSaving">
              {{ isSaving ? 'Đang lưu...' : 'Lưu đặt bàn' }}
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
      bookings: [],
      tableList: [],
      searchQuery: '',
      statusFilter: 'all',
      modal: null,
      form: {
        ten_khach_hang: '',
        so_dien_thoai: '',
        thoi_gian_dat: '',
        loai_ban: 1,
        ban_id: null,
        so_luong_nguoi: 1,
        ghi_chu: ''
      },
      formErrors: {}
    };
  },
  computed: {
    filteredBookings() {
      let list = this.bookings;
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(item =>
          item.ten_khach_hang.toLowerCase().includes(q) ||
          item.so_dien_thoai.includes(q)
        );
      }
      if (this.statusFilter !== 'all') {
        list = list.filter(item => item.status === this.statusFilter);
      }
      return list;
    },
    bookingCounts() {
      return {
        total: this.bookings.length,
        pending: this.bookings.filter(b => b.status === 'pending').length,
        confirmed: this.bookings.filter(b => b.status === 'confirmed').length,
        cancelled: this.bookings.filter(b => b.status === 'cancelled').length,
      };
    }
  },
  mounted() {
    this.loadBookings();
    this.loadTables();
    this.modal = new Modal(document.getElementById('modalDatBan'));
  },
  methods: {
    resetForm() {
      this.form = {
        ten_khach_hang: '',
        so_dien_thoai: '',
        thoi_gian_dat: new Date().toISOString().slice(0, 16),
        loai_ban: 1,
        ban_id: null,
        so_luong_nguoi: 1,
        ghi_chu: ''
      };
      this.formErrors = {};
    },
    loadBookings() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/admin/dat-ban/get-data')
        .then(res => {
          this.bookings = res.data.data || [];
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    loadTables() {
      axios.get('http://127.0.0.1:8000/api/admin/ban/get-data')
        .then(res => {
          this.tableList = res.data.data || [];
        });
    },
    openModal() {
      this.resetForm();
      this.modal.show();
    },
    saveBooking() {
      if (!this.form.ten_khach_hang || !this.form.so_dien_thoai || !this.form.thoi_gian_dat) {
        this.formErrors = {
          ten_khach_hang: !this.form.ten_khach_hang ? 'Tên khách hàng bắt buộc' : '',
          so_dien_thoai: !this.form.so_dien_thoai ? 'Số điện thoại bắt buộc' : '',
          thoi_gian_dat: !this.form.thoi_gian_dat ? 'Thời gian đặt bắt buộc' : ''
        };
        return;
      }
      this.isSaving = true;
      axios.post('http://127.0.0.1:8000/api/admin/dat-ban/create-data', this.form)
        .then(() => {
          this.loadBookings();
          this.loadTables();
          this.resetForm();
          this.modal.hide();
        })
        .catch(error => {
          this.handleApiError(error);
        })
        .finally(() => {
          this.isSaving = false;
        });
    },
    updateStatus(id, status) {
      axios.post('http://127.0.0.1:8000/api/admin/dat-ban/update-status', { id, status })
        .then(() => {
          this.loadBookings();
          this.loadTables();
        });
    },
    deleteBooking(id) {
      if (!confirm('Xác nhận xóa lịch đặt bàn này?')) return;
      axios.post('http://127.0.0.1:8000/api/admin/dat-ban/delete-data', { id })
        .then(() => {
          this.loadBookings();
          this.loadTables();
        })
        .catch(error => {
          this.handleApiError(error);
        });
    },
    formatDateTime(value) {
      return new Date(value).toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    statusText(status) {
      const map = {
        pending: 'Chờ xác nhận',
        confirmed: 'Đã xác nhận',
        completed: 'Đã nhận bàn',
        cancelled: 'Đã hủy'
      };
      return map[status] || status;
    },
    statusClass(status) {
      const map = {
        pending: 'badge-warning-organic',
        confirmed: 'badge-info-organic',
        completed: 'badge-success-organic',
        cancelled: 'badge-danger-organic'
      };
      return map[status] || 'badge-organic';
    },
    handleApiError(error) {
      const message = error?.response?.data?.message || 'Lỗi kết nối server, vui lòng thử lại';
      alert(message);
    }
  }
};
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  align-items: center;
  margin-bottom: 24px;
}
.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 18px;
  padding: 20px 24px;
}
.loading-block {
  text-align: center;
  padding: 60px 0;
}
.empty-state {
  padding: 40px;
  text-align: center;
  color: var(--natural-muted);
}
.form-grid {
  display: grid;
  gap: 16px;
}
.form-grid label {
  display: flex;
  flex-direction: column;
  gap: 8px;
  font-weight: 500;
}
.summary-bar {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  padding: 16px 24px;
}
.summary-card {
  padding: 18px 20px;
  background: var(--natural-surface);
  border: 1px solid var(--natural-border);
  border-radius: var(--radius-lg);
}
.summary-card span {
  display: block;
  margin-bottom: 8px;
  color: var(--natural-muted);
}
.summary-card strong {
  font-size: 1.45rem;
  font-weight: 700;
}
@media (max-width: 900px) {
  .summary-bar {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 600px) {
  .summary-bar {
    grid-template-columns: 1fr;
  }
}
