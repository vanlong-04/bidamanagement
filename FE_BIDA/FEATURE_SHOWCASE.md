# 🎨 Feature Showcase & Design Documentation

## 📊 Component Comparison

| Feature | Dịch Vụ | Hóa Đơn |
|---------|--------|--------|
| Modern UI | ✅ | ✅ |
| Dark Mode Ready | ✅ | ✅ |
| Responsive | ✅ | ✅ |
| Print Optimized | ❌ | ✅ |
| PDF Export | 🔄 | 🔄 |
| Form Validation | ✅ | ❌ |
| Search/Filter | ✅ | ✅ |
| CRUD Operations | ✅ | ❌ |
| Real-time Updates | ✅ | ✅ |
| Toast Notifications | ✅ | ✅ |

---

## 1️⃣ Quản lý Dịch vụ - Feature Breakdown

### 📋 Grid Card Interface
```
┌─────────────────────────────┐
│   🥤 IMAGE PLACEHOLDER      │  ← 160px height
├─────────────────────────────┤
│ Nước Chanh                   │  ← Service Name
│ Nước uống (badge)            │  ← Category Badge
├─────────────────────────────┤
│ Giá: 25.000đ                 │  ← Formatted Price
│ Nước chanh tươi mát...       │  ← Truncated Description
├─────────────────────────────┤
│ [✏️ Sửa]  [🗑️ Xóa]         │  ← Action Buttons
└─────────────────────────────┘
```

### 🔍 Search Features
- **Real-time search**: Từng ký tự gõ vào liền tìm kiếm
- **Multi-field search**: Tìm theo tên or loại
- **Case-insensitive**: "nước" = "Nước" = "NƯỚC"
- **Partial match**: "cha" tìm được "Chanh"

### ➕ Add Service Modal
```
Modal Form Fields:
1. Tên Dịch vụ (*)
   - Validation: Required, max 100 chars
   
2. Loại Dịch vụ (*)
   - Dropdown: Nước uống, Đồ ăn nhẹ, Đồ ăn chính, Tráng miệng, Khác
   
3. Giá (VNĐ) (*)
   - Validation: Required, > 0, number only
   
4. Mô tả (optional)
   - Textarea: Max 500 chars
```

### 💾 CRUD Operations

**Create:**
- POST to `/admin/dich-vu/create-data`
- Payload: `{ ten_dich_vu, loai, gia, mo_ta }`

**Update:**
- POST to `/admin/dich-vu/update-data`
- Payload: `{ id, ten_dich_vu, loai, gia, mo_ta }`

**Delete:**
- Confirmation dialog bắt buộc
- POST to `/admin/dich-vu/delete-data`
- Payload: `{ id }`

### 🎯 User Interactions

| Action | Trigger | Feedback |
|--------|---------|----------|
| Add Service | Click "Thêm Dịch Vụ" | Modal opens |
| Edit Service | Click pencil icon | Modal with data |
| Delete Service | Click trash icon | Confirmation |
| Search | Type in search | Grid updates live |
| Hover Card | Mouse over | Card lifts w/ shadow |

### 🔔 Notification System
```
Success Toast:
✓ Thêm dịch vụ thành công
✓ Cập nhật dịch vụ thành công  
✓ Xóa dịch vụ thành công

Error Toast:
✗ Lỗi khi tải dữ liệu dịch vụ
✗ Có lỗi xảy ra
```

### 🎨 Color Scheme - Dịch Vụ

| Element | Color | Usage |
|---------|-------|-------|
| Primary | #3b82f6 | Buttons, accents |
| Primary Dark | #1e40af | Button hover |
| Primary Light | #dbeafe | Edit button background |
| Success | #10b981 | Toast success |
| Danger | #ef4444 | Delete button, errors |
| Background | #f9fafb | Page background |
| Card | #ffffff | Service cards |
| Text | #111827 | Main text |
| Secondary | #6b7280 | Helper text |

---

## 2️⃣ In Hóa Đơn - Feature Breakdown

### 🖨️ Receipt Template

**Header:**
```
    🎱 QUÁN BIDA VIP
    Giải trí, thư giãn, chất lượng hàng đầu
           [HÓA ĐƠN]
```

**Info Section:**
```
Bàn: 5
Ngày vào: 26/03/2024 14:30:00
Ngày ra: 26/03/2024 16:45:00
Nhân viên: Nguyễn Văn A
```

**Items Table:**
```
┌───────────────┬────┬──────┬─────────┐
│ Sản phẩm      │ SL │ Giá  │ Tổng    │
├───────────────┼────┼──────┼─────────┤
│ Nước chanh    │ 2  │25.000│50.000đ  │
│ Bánh mì       │ 1  │15.000│15.000đ  │
└───────────────┴────┴──────┴─────────┘
```

**Summary:**
```
Tổng cộng: 65.000đ
════════════════════════
Số tiền thanh toán: 65.000đ
════════════════════════
```

**Footer:**
```
Cảm ơn quý khách đã ghé thăm
26/03/2024 16:46
Dùng lại dịch vụ của chúng tôi
```

### 🎯 Print Optimization

**Thermal 80mm:**
- Width: 80mm (320px at 96dpi)
- Auto-height: Dùng `size: 80mm auto`
- Margins: 5mm all sides
- Font: Monospace (Courier New)
- Format: Receipt style

**A4 Paper:**
- Automatic page breaks
- Preserves formatting
- Multiple pages if needed

**Print Trigger:**
```javascript
// Using native window.print()
window.print()

// Browser print dialog handles:
- Paper size selection
- Color/B&W
- Scaling
- Margins
```

### 🎯 Controller Logic

**Table Selection:**
1. Load danh sách bàn from `/admin/ban/get-data`
2. Populate dropdown with bàn IDs
3. On change → Load bill data

**Bill Loading:**
1. Fetch from `/admin/hoa-don/get-bill-by-ban-id?ban_id=X`
2. Parse response → Store in `billData`
3. Render template with formatted data

**Calculations:**
```javascript
// Total = Σ(price × quantity)
calculateTotal() {
  return billData.items.reduce((sum, item) => {
    return sum + (item.price * item.quantity)
  }, 0)
}
```

### 💳 Data Flow

```
┌──────────────────────┐
│ Chọn Bàn (Dropdown)  │
└──────────┬───────────┘
           │
           ↓
┌──────────────────────────────────┐
│ GET /admin/ban/get-data          │
│ Response: [{ id, ... }, ...]     │
└──────────┬─────────────────────────┘
           │
           │ onChange selectedTableId
           ↓
┌────────────────────────────────────────────┐
│ GET /admin/hoa-don/get-bill-by-ban-id?ban_id=X
│ Response: { tableNumber, items[], ... }    │
└──────────┬────────────────────────────────┘
           │
           ↓
┌──────────────────────┐
│ Render Receipt       │
│ Calculate Total      │
│ Display on Screen    │
└─────────┬──────────┘
          │
          ├─→ Print: window.print()
          └─→ PDF: html2pdf (future)
```

### 🎨 Print CSS Features

```css
/* Hide on print */
@media print {
  .bill-controls { display: none; }
  .toast-notification { display: none; }
}

/* Paper size definition */
@page {
  size: 80mm auto;        /* Thermal or custom */
  margin: 0;              /* No margins */
}

/* Print-specific styles */
body { margin: 0; padding: 0; }
.bill-template.print-mode { 
  max-width: 100%;
  margin: 0;
  border-radius: 0;
}
```

### 📱 Responsive Print

| Device | Size | Optimization |
|--------|------|--------------|
| Thermal | 80mm | Native |
| Mobile | 320px | Scales down |
| Tablet | 600px | Optimized |
| A4 | 210mm | Standard |

---

## 🎨 Design System

### Typography
```
Headings:
- H1: 32px / 700 (Desktop)
- H2: 20px / 700 (Modal)
- H3: 18px / 600 (Card titles)
- Body: 14px / 400 (Default)
- Small: 12px / 500 (Labels)
```

### Spacing Scale
```
4px   - Micro spacing
8px   - Component padding
12px  - Element spacing
16px  - Section padding
20px  - Card padding
24px  - Modal padding
32px  - Section margin
40px  - Page margin
```

### Shadow System
```
--box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1)
              0 1px 2px rgba(0, 0, 0, 0.06);
              
--box-shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07)
                 0 2px 4px rgba(0, 0, 0, 0.05);
```

### Border Radius
```
Small: 6px  (form inputs, badges)
Lg:    8px  (buttons)
XL:    12px (cards)
2XL:   16px (modals)
```

### Transition
```
--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1)
```

---

## 🔄 State Management

### Component States

**Dịch Vụ:**
- `isLoading` - Loading danh sách
- `isSubmitting` - Submit form
- `showModal` - Modal visibility
- `isEditMode` - Add vs Edit mode
- `searchQuery` - Search input
- `services` - Service list
- `formData` - Form fields
- `formErrors` - Validation errors

**Hóa Đơn:**
- `isLoadingBill` - Loading bill
- `isPrinting` - Print mode
- `selectedTableId` - Selected table
- `billData` - Bill details
- `tableIds` - Available tables

### Form States

```
INITIAL → INPUT → VALIDATE → SUBMIT → LOADING → SUCCESS/ERROR → RESET
```

---

## ♿ Accessibility Features

- ✅ Semantic HTML (form, button, input)
- ✅ ARIA labels on icons
- ✅ Keyboard navigation (Tab, Enter)
- ✅ Focus indicators
- ✅ Error message associations
- ✅ Sufficient color contrast

---

## 📈 Performance Considerations

- ✅ Lazy computed properties
- ✅ Efficient list rendering
- ✅ Debounced search
- ✅ Minimal re-renders
- ✅ CSS animations (GPU-accelerated)

---

## 🚀 Future Enhancements

1. **Dark Mode**: Toggle theme
2. **PDF Export**: html2pdf integration
3. **Email Receipt**: Send to customer
4. **Multi-language**: i18n support
5. **Statistics**: Sales dashboard
6. **Bulk Operations**: Batch actions
7. **Favorites**: Save frequently used items
8. **History**: Undo/Redo functionality

---

## 🧪 Testing Checklist

- [ ] Add service with valid data
- [ ] Add service with invalid data (validation)
- [ ] Edit service and verify update
- [ ] Delete service with confirmation
- [ ] Search filters correctly
- [ ] Modal closes on cancel
- [ ] Notifications appear/disappear
- [ ] Print button triggers print dialog
- [ ] Print layout looks correct on 80mm
- [ ] Responsive on mobile/tablet
- [ ] All animations smooth
- [ ] No console errors
- [ ] API calls working
- [ ] Error handling works

---

**Design Version**: 1.0  
**Last Updated**: 2024-03-26  
**Status**: Production Ready ✅
