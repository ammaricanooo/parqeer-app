# Parqeer - Feature Priority & Implementation Roadmap

**System:** Shopping Mall Parking Management  
**Version:** 1.0 (Current)  
**Last Updated:** 2026-02-15

---

## 🎯 Feature Priority Matrix

### Phase 1: ESSENTIAL (Next 2-4 weeks)
**Impact:** 🔴🔴🔴 | **Effort:** 🟠🟠 | **Revenue:** 💰💰💰

These features fix critical operational gaps in a real mall environment.

#### 1️⃣ **Parking Slot Management** [CRITICAL]
**Why:** Essential for preventing double-parking and improving security  
**What:** Assign vehicles to specific slot numbers  
**Implementation:**
```
Models:
  - ParkingSlot (area_id, slot_number, floor, status)
  - Transaction: add parking_slot_id

Views:
  - Attendant: Select available slot during entry
  - Admin: Visual lot map (occupancy heatmap)
  - Owner: Capacity trends by slot type

API:
  - GET /api/areas/{id}/available-slots
  - PATCH /api/slots/{id}/status
```

**Effort:** 3-4 days  
**Revenue Impact:** Better service = customer retention  

---

#### 2️⃣ **Vehicle Blacklist System** [HIGH]
**Why:** Prevent stolen vehicles, enforce payment, security control  
**What:** Block known problematic vehicles at entry  
**Implementation:**
```
Models:
  - Blacklist (plate_number, reason, expires_at, severity)

Controllers:
  - BlacklistController (CRUD + check endpoint)

Views:
  - Admin: Blacklist management UI
  - Attendant: Auto-alert on entry if blacklisted

Logic:
  - Check plate on entry → warn attendant
  - Log blocked attempts → security audit
  - Auto-expire after date (e.g., 30 days for unpaid)
```

**Effort:** 1-2 days  
**Revenue Impact:** Reduce losses, enforce payment  

---

#### 3️⃣ **Peak Hour Rate Adjustment** [IMPORTANT]
**Why:** Optimize revenue, manage capacity during busy periods  
**What:** Different rates based on time of day/week  
**Implementation:**
```
Models:
  - ScheduledRate(area_id, vehicle_type, start_time, end_time, day_of_week, amount)

Controllers:
  - RateCalculator service enhancement

Views:
  - Admin: Schedule UI (show effective rate for each time)

Calculation:
  - Entry time + area → fetch applicable rate
  - Show customer estimated cost based on current rate
```

**Effort:** 2-3 days  
**Revenue Impact:** +15-25% revenue during peak hours  

---

#### 4️⃣ **Occupancy Alerts & Capacity Dashboard** [IMPORTANT]
**Why:** Guide customers, optimize operations  
**What:** Real-time "Area Full" warnings  
**Implementation:**
```
Views:
  - Admin: Dashboard showing occupancy % by area
    - Red: 90%+ (full)
    - Yellow: 70-90% (getting full)
    - Green: <70% (available)
  
  - Entrance display: Which areas have space
  
  - Attendant: Can't select full area

Logic:
  - updateOccupancy() ✓ (already implemented)
  - Add: getAvailableCapacity() query
```

**Effort:** 1-2 days  
**Revenue Impact:** Better customer experience  

---

### Phase 2: REVENUE ENHANCING (Weeks 5-8)
**Impact:** 🔴🔴 | **Effort:** 🟠🟠🟠 | **Revenue:** 💰💰💰💰

These features create new revenue streams.

#### 5️⃣ **Monthly/Season Pass (Subscription)** [HIGH REVENUE]
**What:** Unlimited parking for employees/frequent visitors  
**Implementation:**
```
Models:
  - Subscription (user_id, vehicle_id, start_date, end_date, amount, status)

Logic:
  - Check vehicle: If in active subscription → free entry/exit
  - Admin: Issue subscription with QR code
  - Owner: Track subscription revenue separately

Report:
  - Recurring vs casual revenue comparison
```

**Effort:** 3-4 days  
**Revenue Model:** Rp 500K/month × 100+ employees = Rp 50M+/month recurring  

---

#### 6️⃣ **VIP/Premium Parking** [MODERATE REVENUE]
**What:** Reserved spaces with premium pricing  
**Implementation:**
```
Models:
  - ParkingSlot: add is_premium boolean
  - Rate: add premium_multiplier (e.g., 1.5x)

Logic:
  - Premium slots cost 50% more
  - Visual differentiation on map
  - Admin can toggle premium status

Use Cases:
  - Disabled accessible parking
  - Close-to-mall entrance spaces
  - Premium customer service
```

**Effort:** 1-2 days  
**Revenue Model:** 10-20 premium slots × 1.5x rate = +25% revenue  

---

#### 7️⃣ **Corporate Invoice & Deferred Payment** [B2B Revenue]
**What:** Monthly billing for company employees  
**Implementation:**
```
Models:
  - CorporateAccount (company_name, employee_count)
  - CorporateTransaction (corporate_id, transactions[], invoice_date, status)

Views:
  - Admin: Issue invoices, manage accounts
  - Owner: Revenue tracking by customer type

Logic:
  - Mark transactions as "corporate" on entry
  - Group by company, generate monthly invoice
  - Track payment status
```

**Effort:** 4-5 days  
**Revenue Impact:** Recurring corporate contracts  

---

#### 8️⃣ **Time-Based Promotions** [CUSTOMER ACQUISITION]
**What:** Free first hour, weekend discounts, loyalty rewards  
**Implementation:**
```
Models:
  - Promotion (code, type, discount%, active_days, expiry)

Logic:
  - On exit: Check if promotion applies
  - Calculate: (base_cost - discount) × quantity_hours
  - Log promotion usage

Types:
  - Free first 30 min (target mall shoppers)
  - Weekend 50% off (increase traffic)
  - Monday-Thursday: 20% off (off-peak)
  - Loyalty: 10% after 10 visits
```

**Effort:** 2-3 days  
**Revenue Impact:** +20% traffic, repeat customers  

---

### Phase 3: CUSTOMER EXPERIENCE (Weeks 9-12)
**Impact:** 🔴 | **Effort:** 🟠🟠🟠 | **Revenue:** 💰

These features improve satisfaction and reduce friction.

#### 9️⃣ **Mobile Receipt & Notifications** [UX IMPROVEMENT]
**What:** SMS/Email/WhatsApp receipts and alerts  
**Implementation:**
```
Services:
  - SmsService (using Twilio/Nexmo)
  - EmailService

Triggers:
  - Entry: "Vehicle entered at Basement 1, Rp 50K/hr"
  - Exit: Generate receipt QR, send via SMS
  - Payment: Confirmation + change amount

Requirements:
  - Phone number capture at entry
  - Opt-in system (GDPR compliance)
```

**Effort:** 3-4 days (includes gateway integration)  
**Impact:** Reduced complaints, higher transparency  

---

#### 🔟 **Real-Time Parking Map** [CUSTOMER EXPERIENCE]
**What:** Visual representation of lot occupancy  
**Implementation:**
```
Frontend:
  - SVG/Canvas: Draw lot map
  - Color coding: Green (empty), Yellow (taken), Red (reserved)
  - Update via WebSocket every 10 sec

Backend:
  - WebSocket channel: "parking.lot.{area_id}"
  - Publish: currentOccupancy when transaction entry/exit

View:
  - Public kiosk display
  - Mobile app (Phase 4)
  - Admin dashboard
```

**Effort:** 4-5 days  
**Impact:** Better customer decision-making  

---

### Phase 4: ADVANCED INTEGRATION (Months 4-6)
**Impact:** 🔴 | **Effort:** 🔴 | **Revenue:** 💰💰

#### 1️⃣1️⃣ **License Plate Recognition (LPR)** [AUTOMATION]
**What:** Auto entry/exit recording via camera  
**Implementation:**
```
Hardware:
  - CCTV with LPR capability
  - API integration

Workflow:
  - Camera detects plate → API call to Parqeer
  - auto-create entry/exit transactions
  - Attendant verifies/corrects

Reduces:
  - Attendant workload
  - Human errors
  - Queue times

Effort:** 6-8 weeks (depends on LPR vendor)  

---

#### 1️⃣2️⃣ **Payment Gateway Integration** [CASHLESS]
**What:** Accept cards, mobile wallets (not just cash)  
**Implementation:**
```
Gateways:
  - Stripe / Xendit / PayTabs

Workflow:
  - Entry tag or QR
  - At exit: Offer card payment
  - Real-time account settlement

Benefits:
  - Reduced cash handling
  - Better audit trail
  - Remote payment option
```

**Effort:** 3-4 days (per vendor)  

---

---

## 📊 Implementation Timeline

```
Week 1-4: Phase 1 (Slots, Blacklist, Peak Rates, Alerts)
Week 5-8: Phase 2 (Subscriptions, Premium, Corporate, Promos)
Week 9-12: Phase 3 (Notifications, Real-time Map)
Month 4-6: Phase 4 (LPR, Payment Gateways)
```

---

## 💰 Revenue Projection

| Feature | Monthly Revenue | Implementation |
|---------|-----------------|-----------------|
| Current System | Rp 50M | Existing |
| + Peak Rates (25% ↑) | +Rp 12.5M | Week 3-4 |
| + Subscriptions (100 users) | +Rp 50M | Week 6-7 |
| + Premium Parking (20 slots) | +Rp 5M | Week 7 |
| + Corporate Accounts (5 × Rp 2M) | +Rp 10M | Week 8 |
| + Promotions (attract 30% new traffic) | +Rp 7.5M | Week 8 |
| **TOTAL** | **~Rp 135M** | **By Week 8** |

**ROI:** Implementation costs ~Rp 5-10M for development = Payback in < 1 month

---

## ⚡ Quick Wins (< 1 day each)

- [ ] Add `is_premium` flag to Area
- [ ] Create SimpleBlacklist model + check endpoint
- [ ] Add `reason` field to LogActivity (already logged, just expand)
- [ ] Create occupancy threshold alerts (email to admin)
- [ ] Add promotion code to Transaction model
- [ ] Create API endpoint: `GET /api/areas/occupancy` (JSON)

---

## 🛡️ Security & Compliance

All features should include:
- ✓ Role-based access control (Admin/Owner/Attendant)
- ✓ Activity logging (for audit)
- ✓ Data validation (prevent injection)
- ✓ Rate limiting (prevent API abuse)
- ✓ GDPR compliance (phone/email storage)

---

## 📝 Notes

1. **Start with Slot Management** - foundation for all visual features
2. **Blacklist should be available immediately** - security priority
3. **Peak rates can be implemented in parallel** - revenue quick win
4. **Run load tests before Phase 3** - WebSocket can be resource-intensive
5. **Consider batch processing for invoices** - Queue large report generation

---

**Next Steps:** Start Phase 1 implementation based on business priority  
**Owner Sign-off Needed:** Subscriptions pricing, Peak rate percentages, Promotion strategy
