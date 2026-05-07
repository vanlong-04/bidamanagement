<template>
  <div class="page-section">
    <header class="page-header">
      <div>
        <h2 class="page-title">Dashboard Thống kê</h2>
        <p class="page-subtitle">Tổng hợp doanh thu và lượt đặt bàn hôm nay.</p>
      </div>
      <button class="btn-organic btn-ghost-organic" @click="refreshData" :disabled="isLoading">
        <i class="fa-solid fa-rotate-right"></i> Làm mới
      </button>
    </header>

    <div class="stats-grid">
      <div class="stat-card">
        <p class="stat-label">Doanh thu hôm nay</p>
        <p class="stat-value">{{ formatMoney(stats.today_revenue) }}</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Đặt bàn tổng</p>
        <p class="stat-value">{{ stats.total_bookings }}</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Đã xác nhận</p>
        <p class="stat-value">{{ stats.confirmed_bookings }}</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Bàn đang sử dụng</p>
        <p class="stat-value">{{ stats.active_tables }}</p>
      </div>
    </div>

    <div class="card-organic card-organic-xl">
      <div v-if="isLoading" class="loading-block">
        <div class="spinner-border" role="status"></div>
      </div>
      <div v-else>
        <div class="summary-grid">
          <div class="summary-item">
            <span>Chờ xác nhận</span>
            <strong>{{ stats.pending_bookings }}</strong>
          </div>
          <div class="summary-item">
            <span>Đã hủy</span>
            <strong>{{ stats.cancelled_bookings }}</strong>
          </div>
          <div class="summary-item">
            <span>Bàn đã đặt</span>
            <strong>{{ stats.reserved_tables }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      isLoading: false,
      stats: {
        total_bookings: 0,
        confirmed_bookings: 0,
        pending_bookings: 0,
        cancelled_bookings: 0,
        today_revenue: 0,
        active_tables: 0,
        reserved_tables: 0,
      }
    };
  },
  mounted() {
    this.loadStats();
  },
  methods: {
    loadStats() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/admin/dashboard/stats')
        .then(res => {
          this.stats = res.data.data || this.stats;
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    refreshData() {
      this.loadStats();
    },
    formatMoney(value) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value || 0);
    }
  }
};
</script>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 18px;
  margin-bottom: 24px;
}
.stat-card {
  padding: 24px;
  border-radius: var(--radius-lg);
  background: var(--natural-surface);
}
.stat-label {
  margin-bottom: 12px;
  color: var(--natural-muted);
}
.stat-value {
  font-size: 2rem;
  font-weight: 700;
}
.summary-grid {
  display: grid;
  gap: 16px;
  padding: 24px;
}
.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  border-radius: var(--radius-md);
  background: rgba(245, 245, 244, 1);
}
.loading-block {
  padding: 48px;
  text-align: center;
}
@media (max-width: 900px) {
  .stats-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 600px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
