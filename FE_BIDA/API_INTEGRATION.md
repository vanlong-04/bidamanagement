# API Integration Guide

## Overview
This guide shows the expected API responses and request formats for both components.

---

## 1️ Dịch Vụ (Service Management) API

### GET /admin/dich-vu/get-data
**Purpose:** Fetch all services/dishes

**Request:**
```bash
GET http://localhost:8000/api/admin/dich-vu/get-data
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "ten_dich_vu": "Nước Chanh",
      "gia": 25000,
      "loai": "Nước uống",
      "mo_ta": "Nước chanh tươi mát, sinh tố thiên nhiên"
    },
    {
      "id": 2,
      "ten_dich_vu": "Bánh Mì",
      "gia": 15000,
      "loai": "Đồ ăn nhẹ",
      "mo_ta": "Bánh mì nóng vừa nướng, ngon hấp dẫn"
    },
    {
      "id": 3,
      "ten_dich_vu": "Ốc Hấp",
      "gia": 50000,
      "loai": "Đồ ăn chính",
      "mo_ta": "Ốc hấp nước sốt, ngon tuyệt"
    }
  ]
}
```

**Error Response:**
```json
{
  "status": false,
  "message": "Lỗi khi lấy dữ liệu",
  "data": null
}
```

---

### POST /admin/dich-vu/create-data
**Purpose:** Create a new service

**Request:**
```bash
POST http://localhost:8000/api/admin/dich-vu/create-data
Content-Type: application/json

{
  "ten_dich_vu": "Cà Phê Đen",
  "gia": 12000,
  "loai": "Nước uống",
  "mo_ta": "Cà phê đen nguyên chất, đậm đà"
}
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Thêm dịch vụ thành công",
  "data": {
    "id": 4,
    "ten_dich_vu": "Cà Phê Đen",
    "gia": 12000,
    "loai": "Nước uống",
    "mo_ta": "Cà phê đen nguyên chất, đậm đà",
    "created_at": "2024-03-26T14:30:00"
  }
}
```

**Error Response:**
```json
{
  "status": false,
  "message": "Tên dịch vụ đã tồn tại",
  "data": null
}
```

---

### POST /admin/dich-vu/update-data
**Purpose:** Update an existing service

**Request:**
```bash
POST http://localhost:8000/api/admin/dich-vu/update-data
Content-Type: application/json

{
  "id": 1,
  "ten_dich_vu": "Nước Chanh Sạch",
  "gia": 28000,
  "loai": "Nước uống",
  "mo_ta": "Nước chanh sạch sẽ, vệ sinh"
}
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Cập nhật dịch vụ thành công",
  "data": {
    "id": 1,
    "ten_dich_vu": "Nước Chanh Sạch",
    "gia": 28000,
    "loai": "Nước uống",
    "mo_ta": "Nước chanh sạch sẽ, vệ sinh",
    "updated_at": "2024-03-26T14:35:00"
  }
}
```

---

### POST /admin/dich-vu/delete-data
**Purpose:** Delete a service

**Request:**
```bash
POST http://localhost:8000/api/admin/dich-vu/delete-data
Content-Type: application/json

{
  "id": 1
}
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Xóa dịch vụ thành công",
  "data": {
    "id": 1,
    "deleted_at": "2024-03-26T14:40:00"
  }
}
```

**Error Response (Already deleted):**
```json
{
  "status": false,
  "message": "Dịch vụ không tồn tại",
  "data": null
}
```

---

## 2️⃣ Hóa Đơn (Bill/Invoice) API

### GET /admin/ban/get-data
**Purpose:** Fetch all tables/billiard courts

**Request:**
```bash
GET http://localhost:8000/api/admin/ban/get-data
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Success",
  "data": [
    {
      "id": 1,
      "ten_ban": "Bàn 1",
      "trang_thai": "available"
    },
    {
      "id": 2,
      "ten_ban": "Bàn 2",
      "trang_thai": "occupied"
    },
    {
      "id": 5,
      "ten_ban": "Bàn 5",
      "trang_thai": "occupied"
    },
    {
      "id": 3,
      "ten_ban": "Bàn 3",
      "trang_thai": "available"
    }
  ]
}
```

---

### GET /admin/hoa-don/get-bill-by-ban-id
**Purpose:** Fetch bill/invoice details for a specific table

**Request:**
```bash
GET http://localhost:8000/api/admin/hoa-don/get-bill-by-ban-id?ban_id=5
```

**Expected Response:**
```json
{
  "status": true,
  "message": "Success",
  "data": {
    "id": 105,
    "ban_id": 5,
    "tableNumber": 5,
    "gio_vao": "2024-03-26 14:30:00",
    "gio_ra": "2024-03-26 16:45:30",
    "startTime": "2024-03-26 14:30:00",
    "endTime": "2024-03-26 16:45:30",
    "nhan_vien": "Nguyễn Văn A",
    "staffName": "Nguyễn Văn A",
    "items": [
      {
        "id": 201,
        "productId": 1,
        "ten_dich_vu": "Nước Chanh",
        "name": "Nước Chanh",
        "gia": 25000,
        "price": 25000,
        "so_luong": 2,
        "quantity": 2,
        "thanh_tien": 50000,
        "end_time": "2024-03-26 14:45:00"
      },
      {
        "id": 202,
        "productId": 2,
        "ten_dich_vu": "Bánh Mì",
        "name": "Bánh Mì",
        "gia": 15000,
        "price": 15000,
        "so_luong": 1,
        "quantity": 1,
        "thanh_tien": 15000,
        "end_time": "2024-03-26 15:00:00"
      },
      {
        "id": 203,
        "productId": 3,
        "ten_dich_vu": "Ốc Hấp",
        "name": "Ốc Hấp",
        "gia": 50000,
        "price": 50000,
        "so_luong": 1,
        "quantity": 1,
        "thanh_tien": 50000,
        "end_time": "2024-03-26 16:30:00"
      }
    ],
    "tong_tien": 115000,
    "total": 115000,
    "ghi_chu": "Khách VIP, ưu đãi 10%"
  }
}
```

**Response when no bill exists:**
```json
{
  "status": false,
  "message": "Không tìm thấy hóa đơn",
  "data": null
}
```

---

##  Request/Response Format Notes

### 1. Field Naming Flexibility
Component supports both Vietnamese and English field names:

```javascript
// Both work fine:
service.ten_dich_vu  // Vietnamese
service.name         // English

// Both understood:
bill.gia            // Vietnamese for price
bill.price          // English

// Time fields:
bill.gio_vao        // Check-in time (Vietnamese)
bill.startTime      // Alternative naming
```

### 2. Common Issues & Fixes

**Issue:** Response structure different
```javascript
// If your API returns:
{ "services": [...] }

// You might need to adjust in component:
services.value = response.data.services || response.data.data
```

**Issue:** Field names don't match
```javascript
// Map fields in fetchServices():
services.value = response.data.data.map(item => ({
  id: item.dich_vu_id,      // Your field
  name: item.ten_dich_vu,   // Map to expected field
  price: item.gia,
  category: item.loai
}))
```

**Issue:** Date format different
```javascript
// If backend returns:
"2024-03-26T14:30:00.000Z"

// Component auto-converts with formatDateTime()
// But ensure backend returns ISO format
```

---

##  Testing with Postman

### 1. Setup Environment Variables
```json
{
  "base_url": "http://localhost:8000/api",
  "service_id": 1,
  "table_id": 5
}
```

### 2. Test Create Service
```
POST {{base_url}}/admin/dich-vu/create-data

{
  "ten_dich_vu": "Nước Chanh Test",
  "gia": 25000,
  "loai": "Nước uống",
  "mo_ta": "Test description"
}

Tests:
- pm.test("Status should be true", () => {
    pm.expect(pm.response.json().status).to.be.true;
  });
- pm.test("Should return ID", () => {
    pm.expect(pm.response.json().data.id).to.exist;
  });
```

### 3. Test Get Bill
```
GET {{base_url}}/admin/hoa-don/get-bill-by-ban-id?ban_id={{table_id}}

Tests:
- pm.test("Response should contain items", () => {
    pm.expect(pm.response.json().data.items).to.be.an('array');
  });
- pm.test("Items should have required fields", () => {
    let items = pm.response.json().data.items;
    items.forEach(item => {
      pm.expect(item).to.have.property('ten_dich_vu');
      pm.expect(item).to.have.property('gia');
      pm.expect(item).to.have.property('so_luong');
    });
  });
```

---

##  HTTP Headers

### Request Headers (Recommended)
```
Content-Type: application/json
Accept: application/json
X-Requested-With: XMLHttpRequest
```

### Response Headers (Backend should set)
```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE
Access-Control-Allow-Headers: Content-Type
```

---

##  Error Handling

### Expected Error Responses

**Validation Error:**
```json
{
  "status": false,
  "message": "Validation error",
  "errors": {
    "ten_dich_vu": "Tên dịch vụ không được để trống",
    "gia": "Giá phải lớn hơn 0"
  }
}
```

**Unauthorized:**
```json
{
  "status": false,
  "message": "Unauthorized",
  "code": 401
}
```

**Not Found:**
```json
{
  "status": false,
  "message": "Dịch vụ không tồn tại",
  "code": 404
}
```

**Server Error:**
```json
{
  "status": false,
  "message": "Lỗi server nội bộ",
  "code": 500
}
```

---

## 📍 URL Patterns

### Service URLs
```
GET    /admin/dich-vu/get-data
POST   /admin/dich-vu/create-data
POST   /admin/dich-vu/update-data
POST   /admin/dich-vu/delete-data
GET    /admin/dich-vu/{id}              (optional)
PUT    /admin/dich-vu/{id}              (optional)
DELETE /admin/dich-vu/{id}              (optional)
```

### Bill URLs
```
GET    /admin/ban/get-data
GET    /admin/hoa-don/get-bill-by-ban-id?ban_id={id}
GET    /admin/hoa-don/{id}              (optional)
POST   /admin/hoa-don/create-data       (optional)
```

---

##  Data Flow Sequence

### Add Service Flow
```
Component                        Backend
     │
     ├─→ Validate Form ─────────────┐
     │                             │
     ├─ Form Valid?                │
     │   └─ Yes ─→ POST /create ──→│ Validate
     │             │               │ Save DB
     │             │   Status: true│
     │             ←─ Response ────┤
     │             │               │
     ├─ Show Toast (Success)        │
     ├─ Close Modal                 │
     └─ Reload GET /get-data    ─→┐│ Fetch all
                                  ││
                        ←─────────┘│
```

### Get Bill Flow
```
Component                Backend
     │
     ├─ Select Table ─────────────→│ Fetch ban data
     │                             │
     ├ onChange trigger            │
     │                             │
     └─ GET /get-bill-by-ban-id ──→│ Query bill
                                  │ + items
              ←─ Response ────────┤
              │
        Render Receipt
```

---

##  Example Implementation (Laravel)

### Controller Method
```php
// app/Http/Controllers/DichVuController.php
public function getData() {
    $services = DichVu::all();
    
    return response()->json([
        'status' => true,
        'message' => 'Success',
        'data' => $services
    ]);
}

public function createData(Request $request) {
    $validated = $request->validate([
        'ten_dich_vu' => 'required|string|max:255',
        'gia' => 'required|numeric|min:0',
        'loai' => 'required|string',
        'mo_ta' => 'nullable|string'
    ]);
    
    $service = DichVu::create($validated);
    
    return response()->json([
        'status' => true,
        'message' => 'Thêm dịch vụ thành công',
        'data' => $service
    ], 201);
}
```

---

##  Checklist

- [ ] Backend returns correct field names
- [ ] API returns `status` and `message` fields
- [ ] Timestamps in ISO format (YYYY-MM-DD HH:mm:ss)
- [ ] Prices are integers (not strings)
- [ ] Error responses follow format
- [ ] CORS headers properly configured
- [ ] Database relationships properly set up
- [ ] Validation errors clear and helpful
- [ ] Tests pass in Postman
- [ ] Response times < 500ms

---

**API Version**: 1.0  
**Last Updated**: 2024-03-26  
**Status**: Ready for Integration 
