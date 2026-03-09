# Parqeer App - Code Review & Refactoring Recommendations

**Date:** February 15, 2026  
**Framework:** Laravel 10 | **Arch:** Role-based MVC with Blade templating

---

## 📋 Executive Summary

The Parqeer parking management system has solid fundamentals with proper role separation (Attendant, Admin, Owner), QR-based entry/exit tracking, and real-time occupancy management. However, there are opportunities to improve code maintainability, reduce duplication, and add features that are common in modern mall parking systems.

---

## 🔴 Critical Code Quality Issues

### 1. **Inconsistent Transaction Status Values**
**Location:** `Transaction.php`  
**Problem:** Model uses enum `'in', 'out', 'paid'` but has unused scopes referencing `'masuk', 'sudah_keluar'`

```php
// Current (inconsistent)
protected $casts = [...]; // uses 'in', 'out', 'paid'
public function scopeParked($query) {
    return $query->where('status', 'masuk'); // ❌ Wrong value
}
```

**Fix:** Use `whereNull('exit_time')` instead for active parking logic

---

### 2. **Heavy Routes File (250+ lines)**
**Location:** `routes/web.php`  
**Problem:** Business logic querying in route closures creates:
- Difficult testing
- Code duplication
- Hard to maintain
- No single responsibility

**Examples:**
```php
// ❌ Bad: Complex queries in routes
Route::get('/owner/laporan', function() {
    $vehicleRecap = \App\Models\Transaction::whereBetween('entry_time', [$from, $to])
        ->join('vehicles', ...)
        ->selectRaw('...')
        ...
})->name('owner.laporan');
```

**Fix:** Create dedicated controller action

---

### 3. **Duplicate Role-Based View Rendering**
**Location:** `TransactionController.php` (lines 145-157, 238-256)

```php
// ❌ Repeated in entryReceipt() AND receipt()
if (Auth::user()->role === 'admin') {
    return view('admin.transaction.entry_receipt', ...);
} elseif (Auth::user()->role === 'owner') {
    return view('owner.transaction.entry_receipt', ...);
}
return view('attendant.transaction.entry_receipt', ...);
```

**Fix:** Create a method to determine view name, or use middleware/middleware-resolved views

---

### 4. **Rate Calculation Logic Mixed in Model & Controller**
**Location:** `TransactionController.php` (line 230+) & `Transaction.php` (line 85+)

```php
// ❌ Duplicated logic
// In controller:
$hours = ceil($minutes / 60);
$amount = $this->rate->amount * $hours;

// In model:
public function processExit($exitTime) {
    $hours = ceil($duration / 60);
    $amount = $this->rate->amount * $hours;
}
```

**Fix:** Create `RateCalculator` service or use model method only

---

### 5. **No Atomic Transactions for Concurrent Operations**
**Location:** `TransactionController.php` store/exit methods

**Problem:** If two attendants record same vehicle entry simultaneously, duplicate records created
- No database transaction wrapping
- Area occupancy could be inconsistent
- Race condition on `updateOccupancy()`

**Fix:** Wrap create/update in `DB::transaction()`

---

### 6. **Area Rate Configuration is Confusing**
**Location:** Business logic in `TransactionController.create()` & `store()`

```php
// ❌ Confusing requirement: "area must have exactly 1 rate"
if ($area->rates->count() !== 1) {
    // Error: multiple rates not allowed
}
```

**Problem:** This design prevents having car + motorcycle rates in same area. For mall parking, this should be normal.

**Fix:** Allow multiple rates per area (by vehicle type), handle in frontend dropdown

---

### 7. **Missing Transaction Rollback on Occupancy Update**
**Location:** `TransactionController.php` line 94-95

```php
$transaction = Transaction::create($validated);
$area->updateOccupancy(); // ← No rollback if fails
```

---

## 🟡 Code Maintenance Issues

### 1. **View Response Determination Should Be Centralized**
```php
// ❌ Appears in 2+ methods, violates DRY
$view = match(Auth::user()->role) {
    'admin' => 'admin.transaction.receipt',
    'owner' => 'owner.transaction.receipt',
    default => 'attendant.transaction.receipt'
};
return view($view, $data);
```

### 2. **Vehicle Search Endpoint Has Weak Input Validation**
```php
// ❌ Allows searches < 2 chars, no pagination
public function searchVehicle(Request $request) {
    if (strlen($search) < 2) return response()->json([]);
    $vehicles = Vehicle::where(...)->limit(10)->get();
}
```

### 3. **Missing Authorization in Some Routes**
```php
// ✓ Good: Uses policy
$this->authorize('create', Transaction::class);

// ❌ Missing: getRate API has no auth check (should verify area access)
Route::get('/transaction/get-rate', [TransactionController::class, 'getRate']);
```

### 4. **LogActivity Usage is Incomplete**
- Auto-logged in entry/exit but **not in payment**
- No index/view to filter activities by date/user
- Could be useful for audit trail

---

## 🟢 Suggested Features for Mall Parking

### **Phase 1: Essential Mall Features** ✅

1. **📋 Tiered Rate Structure (Peak/Off-peak)**
   - Why: Malls profit from encouraging off-peak parking
   - Design: `Rate` model with `start_time`, `end_time`, `day_of_week`
   - Benefit: Drive traffic during slow hours

2. **📍 Parking Slot Management**
   - Why: Attendants need to assign specific spaces
   - Design: `ParkingSlot` model with `area_id`, `slot_number`, `status` (empty/occupied)
   - Benefit: Prevent double-parking, improve security

3. **🚫 Vehicle Blacklist System**
   - Why: Stolen vehicles, unpaid fines, banned users
   - Design: `Blacklist` model with `plate_number`, `reason`, `expires_at`
   - Benefit: Prevent access, security checkpoint

4. **🎟️ Monthly/Subscription Passes**
   - Why: Generate recurring revenue (mall employees, frequent shoppers)
   - Design: `Subscription` model with unlimited parking for period
   - Benefit: Predictable revenue, database faster lookups

5. **🎨 Real-time Occupancy Map**
   - Why: Show which areas are full/available
   - Design: WebSocket or polling API for area.occupied/area.capacity
   - Benefit: Better UX, guides customers

### **Phase 2: Enhanced Features** 🔄

6. **📱 Mobile App Integration**
   - QR code scanning from phone (reduce attendant tasks)
   - Push notifications for entry/exit
   - Mobile ticketing (QR receipt via WhatsApp)

7. **💳 Digital Payment Gateway**
   - Why: Reduce cash handling, safety
   - Design: Integrate Stripe/PayTabs with transaction
   - Benefit: Audit trail, reduces theft

8. **📊 Peak Hour Analytics**
   - Dashboard showing busiest hours/days
   - Capacity utilization trends
   - Revenue forecasting

9. **🎁 Promotional/Discount Engine**
   - First 30 min free
   - Weekend specials
   - Multi-level parking discount

10. **📧 Notifications**
    - SMS/Email on entry (for premium members)
    - Receipt delivery via email/WhatsApp
    - Lost vehicle notifications

### **Phase 3: Advanced** 🚀

11. **🚗 Vehicle Size Categories**
    - Compact (motorcycles/small cars)
    - Standard (sedans)
    - Oversized (SUVs, trucks)
    - Different rates per category

12. **🔐 License Plate Recognition (LPR)**
    - Automated entry/exit detection
    - Reduce attendant errors
    - Integration with camera system

13. **💰 Invoice Management for Corporates**
    - Monthly billing for company employees
    - Expense reports
    - Payment delay options

14. **⏰ Reserved/VIP Parking**
    - Guarantee spaces for premium members
    - Price premium for guarantee
    - Admin dashboard to manage reservations

---

## 📐 Recommended Architecture Improvements

### **1. Create Service Layer for Business Logic**

```php
// app/Services/ParkingService.php
class ParkingService
{
    public function recordEntry(
        Vehicle $vehicle, 
        Area $area, 
        string $entryTime
    ): Transaction {
        // All entry logic here
        // Atomic transaction
        // Occupancy update
        // Log activity
    }

    public function recordExit(Transaction $transaction, string $exitTime): void {
        // All exit logic
        // Calculate duration
        // Calculate amount
        // Update occupancy
    }

    public function calculateParking(
        Carbon $entryTime, 
        Carbon $exitTime, 
        Rate $rate
    ): decimal {
        // Centralized rate calculation
    }
}
```

### **2. Move Route Logic to Controllers**

```php
// Old (in routes/web.php)
Route::get('/owner/laporan', function() {
    $vehicleRecap = Transaction::...->get();
    // 20+ lines of queries
});

// New (in controller)
Route::get('/owner/laporan', [ReportController::class, 'ownerReport'])->name('owner.laporan');

// app/Http/Controllers/ReportController.php
public function ownerReport(Request $request): View {
    $data = $this->reportService->getOwnerLaporan($request->all());
    return view('owner.laporan', $data);
}
```

### **3. Create Middleware for Role-Based Views**

```php
// app/Http/Middleware/SetViewPath.php
class SetViewPath {
    public function handle(Request $request, Closure $next) {
        View::addLocation(resource_path('views/' . auth()->user()->role));
        return $next($request);
    }
}

// Usage (much cleaner)
return view('transaction.receipt'); // Auto-resolves to auth()->user()->role path
```

### **4. Extract Query Builders to Scopes & Query Classes**

```php
// ✓ Better than inline queries
$revenuePerArea = Transaction::paid()
    ->in('area_id', $areaIds)
    ->between('exit_time', $from, $to)
    ->selectRevenue()
    ->get();

// In Transaction.php
public function scopePaid($query) {
    return $query->whereNotNull('exit_time')->where('status', 'paid');
}
```

---

## 🛠️ Immediate Refactoring Tasks (Priority Order)

### **High Priority** (Do First)
- [ ] Fix transaction status inconsistency (scopes)
- [ ] Add DB transactions to entry/exit logic
- [ ] Extract role-view logic to helper/middleware
- [ ] Move owner laporan queries to controller/service
- [ ] Remove confusing "exactly 1 rate per area" requirement

### **Medium Priority**
- [ ] Create `RateCalculator` service
- [ ] Create `ReportService` for analytics queries
- [ ] Add auth check to `getRate` API
- [ ] Centralize vehicle image/file upload
- [ ] Extract complex views into Blade components

### **Low Priority**
- [ ] Add LogActivity filtering UI
- [ ] Create activity audit report (admin only)
- [ ] Add soft deletes for audit compliance

---

## 🎯 Quick Wins (Easy Improvements)

1. **Add middleware for route groups**
   ```php
   // Currently mixed, should separate:
   auth, verified, role:attendant // attendant routes
   auth, verified, role:admin      // admin routes
   auth, verified, role:owner      // owner routes
   ```

2. **Use route model binding consistently**
   ```php
   // Current: Route::get('/transaction/{transaction}/exit', ...)
   // Blade: {{ $transaction->id }}
   
   // Could improve with implicit binding in all routes
   ```

3. **Add `created_by` to transactions for audit trail**
   ```php
   $transaction->user_id //← Already there ✓
   ```

4. **Add `updated_at` logging to LogActivity when critical fields change**

---

## 📊 Suggested New Routes (Phase 1)

```php
// Blacklist management
Route::prefix('admin')->group(function () {
    Route::resource('blacklist', BlacklistController::class);
    Route::post('blacklist/check-vehicle', [BlacklistController::class, 'checkVehicle']);
});

// Subscription management
Route::resource('subscriptions', SubscriptionController::class);
Route::post('subscriptions/{subscription}/verify', [SubscriptionController::class, 'verify']);

// Parking slots
Route::resource('admin.slots', ParkingSlotController::class);
Route::patch('admin.slots/{slot}/status', [ParkingSlotController::class, 'updateStatus']);

// Rate configuration (enhanced)
Route::resource('admin.rates-scheduled', ScheduledRateController::class);

// Real-time API
Route::get('api/areas/occupancy', [AreaController::class, 'occupancyApi']);
```

---

## 🚀 Summary: Top 5 Recommendations

| Priority | Task | Impact | Effort |
|----------|------|--------|--------|
| 1️⃣ | Fix transaction status values & scopes | Prevents bugs | 30min |
| 2️⃣ | Add DB::transaction() wrapping to entry/exit | Data consistency | 1hr |
| 3️⃣ | Create RateCalculator service | DRY, testable | 2hrs |
| 4️⃣ | Move owner laporan to controller | Maintainability | 1.5hrs |
| 5️⃣ | Add blacklist feature (check on entry) | Security | 3hrs |

---

## 🔗 Feature Dependency Graph

```
Core System ✓ (current)
├── Entry/Exit ✓
├── Payment/Change ✓
├── Occupancy ✓
└── Reporting ✓

Next Level:
├─→ Blacklist Check (1-2hr)
├─→ Slot Management (3-4hr)
├─→ Peak-hour Rates (2-3hr)
└─→ Real-time Map (2-3hr)

Revenue Features:
├─→ Monthly Passes (4-5hr)
├─→ VIP Parking (3-4hr)
└─→ Promotions (2-3hr)

Integration:
├─→ SMS Notifications (2hr)
├─→ Payment Gateway (3hr)
└─→ Mobile App (6-8hr)
```

---

## ✅ What's Working Well

- ✓ Clean role separation (Attendant/Admin/Owner)
- ✓ QR-based entry/exit flow
- ✓ Real-time occupancy tracking
- ✓ Proper authorization policies
- ✓ Excel export functionality
- ✓ Multi-role dashboards
- ✓ Payment processing with change
- ✓ Good database indexing
- ✓ Activity logging in place
- ✓ CAPTCHA on login ✓

---

**Generated:** 2026-02-15  
**Version:** 1.0  
**Next Review:** After Phase 1 implementation

