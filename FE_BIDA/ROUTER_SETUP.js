// router/index.js - Integration Example
// Thêm các import và route dưới đây vào router của bạn

import { createRouter, createWebHistory } from 'vue-router'

// Import components
import DichVuManagement from '@/components/admin/dichvu/index.vue'
import BillPrinter from '@/components/admin/hoaban/BillPrinter.vue'

const routes = [
  // Existing routes...
  
  // 🎯 Dịch vụ Management
  {
    path: '/admin/dich-vu',
    name: 'DichVuManagement',
    component: DichVuManagement,
    meta: {
      title: 'Quản lý Dịch vụ',
      icon: 'list',
      breadcrumb: [
        { label: 'Admin', path: '/admin' },
        { label: 'Dịch vụ', path: '/admin/dich-vu' }
      ]
    }
  },
  
  // 🖨️ Hóa Đơn Printer
  {
    path: '/admin/hoa-don',
    name: 'BillPrinter',
    component: BillPrinter,
    meta: {
      title: 'In Hóa Đơn',
      icon: 'print',
      breadcrumb: [
        { label: 'Admin', path: '/admin' },
        { label: 'Hóa Đơn', path: '/admin/hoa-don' }
      ]
    }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
