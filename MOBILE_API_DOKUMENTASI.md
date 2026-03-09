# Mobile API Documentation - Parqeer Attendant App

## Status
✅ **UPDATED** - March 8, 2026  
Semua fitur di web app sekarang tersedia di mobile API untuk petugas (attendant)

---

## API Base URL
```
http://localhost:8000/api
```

## Authentication
Semua endpoint memerlukan **Bearer Token** dari login:
```
Authorization: Bearer {token}
```

---

## 📋 Endpoint List

### 1️⃣ AUTHENTICATION

#### Login
```http
POST /auth/login
Content-Type: application/json

{
  "email": "petugas@parqeer.com",
  "password": "password"
}

Response (200):
{
  "success": true,
  "user": { "id": 1, "name": "Budi", "role": "attendant" },
  "token": "7|H3x...",
  "expires_at": "2026-04-08T10:00:00Z"
}
```

#### Logout
```http
POST /auth/logout
Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### 2️⃣ TRANSACTION MANAGEMENT

#### List Transactions (dengan filter)
```http
GET /transactions?status=paid&date=2026-03-08&area_id=1&per_page=20
Authorization: Bearer {token}

Response (200):
{
  "data": [
    {
      "id": 123,
      "plate_number": "B 1234 ABC",
      "vehicle_color": "Merah",
      "area": { "id": 1, "name": "Basement 1" },
      "entry_time": "2026-03-08T10:30:00Z",
      "status": "paid"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 20,
    "total": 45,
    "last_page": 3
  }
}
```

#### Get Transaction Details
```http
GET /transactions/{id}
Authorization: Bearer {token}

Response (200):
{
  "id": 123,
  "plate_number": "B 1234 ABC",
  "vehicle_color": "Merah",
  "vehicle_type": "car",
  "area": "Basement 1",
  "entry_time": "2026-03-08T10:30:00Z",
  "exit_time": "2026-03-08T11:45:00Z",
  "duration_minutes": 75,
  "duration_hours": 2,
  "amount": 30000,
  "paid_amount": 40000,
  "change": 10000,
  "status": "paid"
}
```

#### Create Transaction (Entry)
```http
POST /transactions
Authorization: Bearer {token}
Content-Type: application/json

{
  "plate_number": "B 1234 ABC",
  "vehicle_color": "Merah",
  "vehicle_type": "car",      (optional if vehicle exists, required for new vehicles)
  "area_id": 1
}

Response (201):
{
  "success": true,
  "message": "Vehicle entry recorded successfully",
  "data": {
    "id": 124,
    "plate_number": "B 1234 ABC",
    "area": "Basement 1",
    "entry_time": "2026-03-08T14:00:00Z",
    "amount": null
  }
}
```

**Notes:**
- `vehicle_type`: Optional jika kendaraan sudah pernah masuk sebelumnya (auto-detect)
- `vehicle_type`: Required untuk kendaraan baru (gunakan "car" atau "motorcycle")
- `vehicle_color`: Optional, akan auto-detect dari data sebelumnya jika ada

#### Record Exit
```http
POST /transactions/{id}/exit
Authorization: Bearer {token}

Response (200):
{
  "success": true,
  "message": "Vehicle exit recorded",
  "data": {
    "id": 124,
    "exit_time": "2026-03-08T14:45:00Z",
    "duration_hours": 1,
    "amount": 15000
  }
}
```

#### Record Payment
```http
POST /transactions/{id}/payment
Authorization: Bearer {token}
Content-Type: application/json

{
  "paid_amount": 50000
}

Response (200):
{
  "success": true,
  "message": "Payment recorded successfully",
  "data": {
    "id": 124,
    "amount": 15000,
    "paid_amount": 50000,
    "change": 35000
  }
}
```

---

### 3️⃣ DASHBOARD & SUMMARY

#### Dashboard (Stats Hari Ini)
```http
GET /attendant/dashboard
Authorization: Bearer {token}

Response (200):
{
  "today": {
    "transactions_count": 45,
    "revenue": 450000,
    "vehicles_entered": 25,
    "vehicles_exited": 20,
    "vehicles_paid": 18
  },
  "month": {
    "transactions_count": 850,
    "revenue": 8500000,
    "vehicles_paid": 750
  },
  "active": {
    "count": 5
  }
}
```

#### Daily Summary (dengan date filter)
```http
GET /attendant/daily-summary?date=2026-03-08
Authorization: Bearer {token}

Response (200):
{
  "date": "2026-03-08",
  "transactions_count": 45,
  "revenue": 450000,
  "vehicles_entered": 25,
  "vehicles_exited": 20,
  "vehicles_paid": 18,
  "average_duration": 127,
  "average_payment": 9500
}
```

#### Active Transactions (sedang parkir)
```http
GET /attendant/active-transactions
Authorization: Bearer {token}

Response (200):
{
  "count": 5,
  "transactions": [
    {
      "id": 125,
      "plate_number": "B 5678 DEF",
      "vehicle_color": "Hitam",
      "area": "Basement 1",
      "entry_time": "2026-03-08T13:00:00Z",
      "duration_minutes": 67,
      "duration_hours": 2,
      "estimated_cost": 30000
    }
  ]
}
```

---

### 4️⃣ AREA & RATE DATA

#### Get All Areas
```http
GET /areas
Authorization: Bearer {token}

Response (200):
{
  "data": [
    {
      "id": 1,
      "name": "Basement 1",
      "capacity": 150,
      "occupied": 87,
      "available": 63,
      "percentage": 58,
      "rates": [
        { "id": 1, "vehicle_type": "car", "amount": 15000 }
      ]
    }
  ],
  "count": 2
}
```

#### Get Single Area
```http
GET /areas/{id}
Authorization: Bearer {token}

Response (200):
{
  "id": 1,
  "name": "Basement 1",
  "capacity": 150,
  "occupied": 87,
  "available": 63,
  "percentage": 58,
  "rates": [
    { "id": 1, "vehicle_type": "car", "amount": 15000 },
    { "id": 2, "vehicle_type": "motorcycle", "amount": 5000 }
  ]
}
```

#### Get Area Occupancy (real-time)
```http
GET /areas/occupancy
Authorization: Bearer {token}

Response (200):
{
  "areas": [
    {
      "id": 1,
      "name": "Basement 1",
      "capacity": 150,
      "occupied": 87,
      "available": 63,
      "percentage": 58,
      "status": "available",
      "rates": [ ... ]
    }
  ],
  "timestamp": "2026-03-08T14:30:00Z"
}
```

---

### 5️⃣ VEHICLE VALIDATION

#### Validate Vehicle by Plate
```http
POST /vehicles/validate
Authorization: Bearer {token}
Content-Type: application/json

{
  "plate_number": "B 1234 ABC"
}

Response (200) - Exists:
{
  "exists": true,
  "vehicle": {
    "id": 5,
    "plate_number": "B 1234 ABC",
    "color": "Merah",
    "type": "car"
  }
}

Response (200) - Not Found:
{
  "exists": false,
  "message": "Vehicle not found"
}
```

---

### 6️⃣ USER MANAGEMENT

#### Get User Profile
```http
GET /user
Authorization: Bearer {token}

Response (200):
{
  "id": 1,
  "name": "Budi Susanto",
  "email": "petugas@parqeer.com",
  "role": "attendant",
  "created_at": "2026-02-01T00:00:00Z"
}
```

#### Update User Profile
```http
PUT /user
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Budi Susanto" (optional)
}

Response (200):
{
  "success": true,
  "message": "Profile updated",
  "user": {
    "id": 1,
    "name": "Budi Susanto",
    "email": "petugas@parqeer.com",
    "role": "attendant"
  }
}
```

#### Register Device (Push Notifications)
```http
POST /mobile/register
Authorization: Bearer {token}
Content-Type: application/json

{
  "device_token": "firebase_token_here",
  "platform": "android",
  "app_version": "1.0.0"
}

Response (200):
{
  "success": true,
  "message": "Device registered successfully"
}
```

---

### 7️⃣ BATCH SYNC (Offline Mode)

#### Sync Offline Transactions
```http
POST /transactions/batch
Authorization: Bearer {token}
Content-Type: application/json

{
  "transactions": [
    {
      "type": "entry",
      "plate_number": "B 1111 AAA",
      "vehicle_color": "Putih",
      "vehicle_type": "car",
      "area_id": 1,
      "entry_time": "2026-03-08T10:00:00Z"
    },
    {
      "type": "exit",
      "id": 120,
      "exit_time": "2026-03-08T11:00:00Z"
    },
    {
      "type": "payment",
      "id": 120,
      "paid_amount": 20000
    }
  ]
}

Response (200):
{
  "synced": 3,
  "failed": 0,
  "errors": [],
  "transactions": [
    { "type": "entry", "transaction_id": 126, "status": "success" },
    { "type": "exit", "transaction_id": 120, "status": "success" },
    { "type": "payment", "transaction_id": 120, "status": "success" }
  ]
}
```

---

## 🔗 Perbandingan Web vs Mobile

| Fitur | Web | Mobile |
|-------|-----|--------|
| Login/Logout | ✅ | ✅ |
| Entry Kendaraan | ✅ | ✅ |
| Exit Kendaraan | ✅ | ✅ |
| Payment | ✅ | ✅ |
| View Transactions | ✅ | ✅ |
| Dashboard | ✅ | ✅ |
| Daily Summary | ✅ | ✅ |
| Active Transactions | ✅ | ✅ |
| Area Occupancy | ✅ | ✅ |
| Area & Rates | ✅ | ✅ |
| Vehicle Validation | ✅ | ✅ |
| User Profile | ✅ | ✅ |
| Batch Sync (Offline) | ❌ | ✅ |
| Device Registration | ❌ | ✅ |

---

## 📱 Mobile App Integration

### 1. Login Flow
```dart
// Login dengan email/password
POST /api/auth/login
// Simpan token di SharedPreferences/Hive
// Use token di Authorization header untuk request berikutnya
```

### 2. Entry Flow
```dart
// 1. Get areas
GET /api/areas

// 2. Create entry
POST /api/transactions with plate, color, area_id

// 3. Show entry ticket dengan QR code
```

### 3. Exit/Payment Flow
```dart
// 1. Get active transactions
GET /api/attendant/active-transactions

// 2. Select transaction to exit
POST /api/transactions/{id}/exit

// 3. Process payment
POST /api/transactions/{id}/payment with paid_amount
```

### 4. Dashboard
```dart
// Get dashboard stats
GET /api/attendant/dashboard

// Or get daily summary with specific date
GET /api/attendant/daily-summary?date=2026-03-08
```

### 5. Offline Mode
```dart
// Queue transactions locally when offline
// When back online:
POST /api/transactions/batch with all queued transactions
// Clear local queue on success
```

---

## ⚠️ Error Responses

### 401 - Unauthorized
```json
{
  "message": "Unauthorized"
}
```

### 422 - Validation Error
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "plate_number": ["The plate number field is required."]
  }
}
```

### 404 - Not Found
```json
{
  "message": "Not found"
}
```

---

## 🚀 Testing dengan Postman

Gunakan `Parqeer_Mobile_API.postman_collection.json` yang sudah ada, atau import rute-rute baru:

```bash
# Import ke Postman
1. Open Postman
2. Click Import
3. Raw text atau paste URL
4. Set {{base_url}} = http://localhost:8000
5. Login untuk mendapat {{token}}
6. Test semua endpoint
```

---

## 📝 Rate Limiting
Semua mobile endpoints memiliki throttle: **60 requests per 1 minute**

---

**Document Updated:** March 8, 2026  
**Status:** Complete ✅  
**Semua fitur web sekarang tersedia di mobile API untuk petugas**
