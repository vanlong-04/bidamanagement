import { createRouter, createWebHistory } from "vue-router"; // cài vue-router: npm install vue-router@next --save

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    component: () => import('../components/login/index.vue'),
  },
  {
    path: '/user',
    component: () => import('../layout/wrapper/indexView.vue'),
    meta: { layout: 'user', requiresAuth: true },
    children: [
      {
        path: 'dat-ban',
        components: {
          default: () => import('../components/user_view/user/index.vue'),
          detail: () => import('../components/user_view/user/BanDetail.vue')
        }
      },
      {
        path: 'thuc-don',
        components: {
          default: () => import('../components/user_view/user/indexThucDon.vue'),
          detail: () => import('../components/user_view/user/BanDetail.vue')
        }
      },
    ]
  },
  {
    path: '/admin',
    component: () => import('../layout/wrapper/index.vue'),
    meta: { layout: 'admin', requiresAuth: true },
    children: [
      {
        path: 'dashboard',
        component: () => import('../components/Test/index.vue')
      },
      // Routes của Sơn - Quản lý Bàn
      {
        path: 'ban',
        component: () => import('../components/admin/ban/index.vue')
      },
      {
        path: 'dat-ban',
        component: () => import('../components/admin/datban/index.vue')
      },
      {
        path: 'dich-vu',
        component: () => import('../components/admin/dichvu/index.vue')
      },
      {
        // Trang quản lý hóa đơn (danh sách CRUD)
        path: 'hoa-don',
        component: () => import('../components/admin/hoaban/index.vue')
      },
      {
        // Trang in hóa đơn theo bàn (bill printer)
        path: 'in-hoa-don',
        component: () => import('../components/admin/hoaban/BillPrinter.vue')
      },
      {
        path: 'doanh-thu',
        component: () => import('../components/admin/doanhthu/index.vue')
      },
      {
        // Trang quản lý nhân viên (mới)
        path: 'nhan-vien',
        component: () => import('../components/admin/nhanvien/index.vue')
      },
      {
        // Quản lý chấm công
        path: 'cham-cong',
        component: () => import('../components/admin/chamcong/index.vue')
      },
      {
        // Quản lý khách hàng / VIP
        path: 'khach-hang',
        component: () => import('../components/admin/khachhang/index.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

// ===== AUTH GUARD =====
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  if (requiresAuth) {
    // Kiểm tra localStorage hoặc sessionStorage
    const user = localStorage.getItem('user') || sessionStorage.getItem('user');
    if (!user) {
      // Chưa đăng nhập → chuyển về login
      next({ path: '/login' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;