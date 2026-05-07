# Quick Start Guide

## 5-Minute Setup

### Step 1: Update Router (2 min)
Edit `src/router/index.js`:

```javascript
import DichVuManagement from '@/components/admin/dichvu/index.vue'
import BillPrinter from '@/components/admin/hoaban/BillPrinter.vue'

// Add these routes:
{
  path: '/admin/dich-vu',
  component: DichVuManagement,
  meta: { title: 'Quản lý Dịch vụ' }
},
{
  path: '/admin/hoa-don',
  component: BillPrinter,
  meta: { title: 'In Hóa Đơn' }
}
```

### Step 2: Configure API URL (1 min)
Create `.env.local`:

```
VITE_API_URL=http://localhost:8000/api
```

Or update `.env.example` and copy to `.env.local`

### Step 3: Navigation (1 min)
Add links to your navigation menu:

```vue
<router-link to="/admin/dich-vu">Quản lý Dịch vụ</router-link>
<router-link to="/admin/hoa-don">In Hóa Đơn</router-link>
```

### Step 4: Start Dev Server (1 min)
```bash
npm run dev
```

### Step 5: Test (1 min)
- Open http://localhost:5173/admin/dich-vu
- Try adding a service
- The component should work! 

---

## Feature Checklist

### Dịch Vụ Component
- [ ] Can see list of services
- [ ] Can add new service
- [ ] Can edit service
- [ ] Can delete service (with confirmation)
- [ ] Search works
- [ ] Mobile responsive

### Bill Component
- [ ] Can select table from dropdown
- [ ] Bill data loads correctly
- [ ] Print button works
- [ ] Receipt template displays correctly
- [ ] Responsive on mobile

---

## 🔧 API Setup (Backend)

### Required Endpoints

Make sure your Laravel backend has these routes:

```php
// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    // Dịch vụ
    Route::get('/admin/dich-vu/get-data', [DichVuController::class, 'getData']);
    Route::post('/admin/dich-vu/create-data', [DichVuController::class, 'createData']);
    Route::post('/admin/dich-vu/update-data', [DichVuController::class, 'updateData']);
    Route::post('/admin/dich-vu/delete-data', [DichVuController::class, 'deleteData']);
    
    // Bàn billiard
    Route::get('/admin/ban/get-data', [BanController::class, 'getData']);
    
    // Hóa đơn
    Route::get('/admin/hoa-don/get-bill-by-ban-id', [HoaDonController::class, 'getBillByBanId']);
});
```

### Backend Response Format

Each endpoint must return:
```json
{
  "status": true,
  "message": "Success",
  "data": {...}
}
```

---

## Common Issues & Fixes

### Issue 1: "404 Not Found" on API calls
**Solution:**
- Verify backend is running on port 8000
- Check `.env.local` has correct URL
- Test API in Postman
- Check CORS settings in backend

### Issue 2: Blank page after clicking component
**Solution:**
- Open browser DevTools (F12)
- Check Console tab for errors
- Check Network tab for failed requests
- Verify router path is correct

### Issue 3: Form not submitting
**Solution:**
- Check form validation errors (red messages)
- Open Console to see any JS errors
- Verify API endpoint in Network tab
- Check Laravel validation rules

### Issue 4: Print layout broken
**Solution:**
- Test print preview (Ctrl+Shift+P)
- Check page size settings (80mm or A4)
- Adjust @media print CSS if needed
- Try in different browser

---

## 📱 Mobile Testing

### Test on Device
```bash
# Get your machine IP
ipconfig getifaddr en0    # Mac
ipconfig                  # Windows

# Then navigate to:
http://YOUR_IP:5173/admin/dich-vu
```

### Test in Browser DevTools
- Press F12 → Device Toolbar
- Select iPhone/Android
- Test all interactions

---

## Debugging Tips

### Vue DevTools
1. Install Vue DevTools extension
2. Open DevTools (F12)
3. Go to Vue tab
4. Inspect component state
5. Check computed/data values

### Network Inspection
1. DevTools → Network tab
2. Perform action (e.g., add service)
3. Look for API calls
4. Check response in Response tab
5. Verify status code (200, 201, etc.)

### Console Logging
All components have console.log for debugging:
```javascript
// Add custom logging if needed:
console.log('Service added:', response.data)
```

---

## Performance Tips

### Optimize Bundle
```bash
npm run build
# Check dist/ folder size
```

### Improve Load Time
- Lazy load router
- Use code splitting
- Optimize images
- Minimize CSS/JS

### API Performance
- Add pagination (optional)
- Implement caching
- Use proper indexes in DB
- Monitor response times

---

## Customization Examples

### Change Primary Color
Edit component `<style scoped>`:
```css
:root {
  --primary-color: #ef4444;   /* Red instead of blue */
  --primary-dark: #dc2626;
}
```

### Change Receipt Logo
Edit BillPrinter.vue:
```vue
<h2 class="business-name"> PIZZA HUT</h2>
<p class="business-tagline">Đồ ăn nhanh, chất lượng</p>
```

### Add Fields to Form
Edit DichVuManagement.vue:
```vue
<!-- Add in modal form -->
<div class="form-group">
  <label>Mã SKU</label>
  <input v-model="formData.sku" type="text">
</div>

<!-- Add to formData initialization -->
formData.value = {
  name: '',
  category: '',
  price: 0,
  description: '',
  sku: ''  // New field
}
```

---

## Backup & Deployment

### Before Deployment
- [ ] Test all features in production APIs
- [ ] Update .env.local with production URL
- [ ] Run build: `npm run build`
- [ ] Test dist/ locally
- [ ] Backup current version

### After Deployment
- [ ] Verify components load
- [ ] Test CRUD operations
- [ ] Test print functionality
- [ ] Monitor error logs
- [ ] Get user feedback

---

## Support Resources

### Documentation Files
- `SETUP_GUIDE.md` - Full setup instructions
- `FEATURE_SHOWCASE.md` - Feature details
- `API_INTEGRATION.md` - API specifications
- `ROUTER_SETUP.js` - Router example

### Testing Tools
- Postman - API testing
- Browser DevTools - Frontend debugging
- Vue DevTools - Component inspection
- Network tab - Request monitoring

### Learning Resources
- Vue 3 Docs: https://vuejs.org
- Axios Docs: https://axios-http.com
- Bootstrap: https://getbootstrap.com
- Tailwind: https://tailwindcss.com

---

## First Steps

1. **Copy components** to your project 
2. **Update router** with new routes 
3. **Set API URL** in .env.local 
4. **Test in browser** with npm run dev 
5. **Verify API calls** work 
6. **Customize colors** if needed 
7. **Deploy to production** 

---

## You're Ready!

Your modern, professional billiard management system is ready to go. 

**Need help?**
- Check the documentation files
- Inspect DevTools console
- Verify API connections
- Test in Postman

**Happy coding! **

---

**Version:** 1.0  
**Updated:** 2024-03-26  
**Status:** Production Ready 
