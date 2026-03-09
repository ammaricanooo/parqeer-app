# Parqeer Mobile App Development Roadmap

**Project:** Flutter Android Mobile App for Parking Attendants  
**Status:** Pre-development (Ready to start)  
**Timeline:** 4-6 weeks (February-March 2026)  
**Team Size:** 1-2 Flutter developers + 1 backend engineer  
**Target:** Android 8.0+ (minSdkVersion 26)  

---

## 🎯 Project Objectives

### Primary Goals
1. ✅ **MVP (Minimum Viable Product)** in 4-6 weeks
   - Attendants can enter/exit vehicles via mobile
   - Process payments on-device
   - View real-time occupancy
   - Work offline with queue sync

2. ✅ **Feature Parity** with web interface
   - Everything the web app does, available on mobile
   - Better UX for field workers (attendants)
   - Faster transaction processing

3. ✅ **User Adoption** target
   - All 10+ parking attendants using app within 2 weeks of launch
   - Reduce paper receipts by 80%
   - Increase transaction speed by 40%

### Success Metrics
| Metric | Target | Status |
|--------|--------|--------|
| App launches daily | >80% usage | ⏳ |
| Transaction entry speed | <30 seconds | ⏳ |
| Offline sync success | >99% | ⏳ |
| App crashes | <0.1% | ⏳ |
| User satisfaction | >4.5/5 stars | ⏳ |
| Battery usage | <5% per hour | ⏳ |

---

## 📋 Scope Definition

### ✅ IN SCOPE (MVP)
- [x] Authentication (login/logout)
- [x] Vehicle entry (plate, color, area)
- [x] Vehicle exit (select from list, auto-calculate duration)
- [x] Payment processing (amount, change)
- [x] QR code scanning (entry/receipt)
- [x] Real-time occupancy dashboard
- [x] Daily summary (transactions, revenue)
- [x] Offline mode (local queue, auto-sync)
- [x] Push notifications (Firebase)
- [x] Session management (30-day token expiry)

### ❌ OUT OF SCOPE (v1.0)
- [ ] Vehicle images/photos
- [ ] Owner/admin features
- [ ] Vehicle whitelist/blacklist
- [ ] Peak hour rates
- [ ] Analytics dashboard
- [ ] Multiple language support
- [ ] iOS version

### 📋 Phase 2 (Post-launch)
- Blacklist integration
- Slot management (prevent double-parking)
- Peak hour rate adjustment
- Receipt printing integration
- Multi-language support
- Enhanced dashboard analytics

---

## 🏗️ Architecture Overview

```
┌─────────────────────┐
│  Flutter Mobile App │ (Android 8.0+)
├─────────────────────┤
│ - Screens           │
│ - QR Scanner        │
│ - State Management  │
│ - Local Database    │
└──────────┬──────────┘
           │
           │ HTTP/REST (Dio)
           │
┌──────────▼──────────┐
│  Laravel Backend    │ (Existing)
├─────────────────────┤
│ - API Endpoints     │
│ - Authentication    │
│ - Database Ops      │
│ - Activity Logging  │
└─────────────────────┘
```

### Core Technologies

| Component | Technology | Version | Status |
|-----------|-----------|---------|--------|
| **Framework** | Flutter | 3.x | ✅ Ready |
| **Language** | Dart | 3.x | ✅ Ready |
| **State Mgmt** | Provider | 6.0 | ✅ Selected |
| **HTTP Client** | Dio | 5.3 | ✅ Selected |
| **Local Storage** | SQLite + Hive | Latest | ✅ Ready |
| **QR Scanning** | mobile_scanner | 3.5 | ✅ Ready |
| **Notifications** | Firebase FCM | Latest | ✅ Ready |
| **Backend** | Laravel + Sanctum | Existing | ✅ Ready |
| **Database** | SQLite | Existing | ✅ Ready |

---

## 📅 Development Timeline

### Week 1: Foundation & Setup (Days 1-5)

| Day | Task | Deliverable | Owner |
|-----|------|-------------|-------|
| 1-2 | Project setup, dependencies, Flutter config | Runnable app shell | Dev |
| 2-3 | API service (Dio + Retrofit), auth flow | Login working | Dev |
| 3-4 | Auth provider, token management | Persistent login | Dev |
| 4-5 | Home screen skeleton, navigation | Navigation structure | Dev |
| **5** | **Backend:** Create API routes, auth endpoint | `/api/auth/login` working | Backend |

**Deliverables:**
- ✅ Flutter project scaffolded with dependencies
- ✅ Login screen fully functional
- ✅ API communication verified (Postman test)
- ✅ Authentication token stored/retrieved
- ✅ Home screen with user info displayed

**Testing:**
- ✅ Manual testing: Login/logout works
- ✅ Token persistence across app restart
- ✅ API error handling (invalid credentials shows error)

---

### Week 2: Core Features - Entry & Exit (Days 6-10)

| Day | Task | Deliverable | Owner |
|-----|------|-------------|-------|
| 6 | Entry form UI (plate, color, area dropdown) | Entry screen | Dev |
| 6-7 | Vehicle validation (plate format, required fields) | Input validation | Dev |
| 7-8 | Integration with `/api/transactions/batch` | Entry API call | Dev |
| 8-9 | Exit screen (list active vehicles, select to exit) | Exit screen | Dev |
| 9-10 | Duration calculation, payment estimation | Computed values | Dev |
| **10** | **Backend:** Batch sync endpoint, active transactions API | Endpoints tested | Backend |

**Deliverables:**
- ✅ Entry form with area selection dropdown
- ✅ Success/error messages
- ✅ Vehicle plate validation
- ✅ Exit screen lists all active vehicles
- ✅ Duration/cost calculation working

**Testing:**
- ✅ Create entry → verify in API
- ✅ Exit → verify duration correct
- ✅ Occupancy updates on entry/exit

---

### Week 3: QR Scanning & Payment (Days 11-15)

| Day | Task | Deliverable | Owner |
|-----|------|-------------|-------|
| 11 | QR scanner integration (mobile_scanner) | QR screen | Dev |
| 11-12 | QR parsing (extract plate number) | Plate auto-population | Dev |
| 13 | QR receipt generation (qr_flutter) | Receipt with QR | Dev |
| 13-14 | Payment screen (amount → change calculation) | Payment UI | Dev |
| 14-15 | Payment API call, receipt display | Payment workflow | Dev |
| **15** | **Backend:** Payment logging, occupancy endpoint | Endpoints complete | Backend |

**Deliverables:**
- ✅ QR scanner opens from entry form
- ✅ Scanned plate auto-fills field
- ✅ QR code generated for receipt
- ✅ Payment form with change calculation
- ✅ Receipt shows: vehicle, duration, amount, change, timestamp

**Testing:**
- ✅ Scan QR code → plate auto-filled
- ✅ Payment amount validated
- ✅ Change calculation correct
- ✅ Payment state updates API

---

### Week 4: Offline Mode & Polish (Days 16-20)

| Day | Task | Deliverable | Owner |
|-----|------|-------------|-------|
| 16 | Local SQLite database setup (models) | Database schema | Dev |
| 16-17 | Offline queue (Hive for queue management) | Queue structure | Dev |
| 17-18 | Sync logic (batch upload when online) | Offline detection + sync | Dev |
| 18-19 | Offline UI indicators (connection status) | Status badge | Dev |
| 19-20 | Error recovery, retry logic | Resilient sync | Dev |
| **20** | **Backend:** Database migration for device tokens | User columns added | Backend |

**Deliverables:**
- ✅ App works offline (local database)
- ✅ Transactions queued when offline
- ✅ Auto-sync when online detected
- ✅ Visual indicator: "Offline Mode - Syncing..."
- ✅ Retry failed transactions

**Testing:**
- ✅ Enter vehicle while offline
- ✅ Disconnect network mid-transaction
- ✅ Sync succeeds after reconnect
- ✅ Queue clears completely

---

### Week 5: Testing & Refinement (Days 21-25)

| Day | Task | Type | Success Criteria |
|-----|------|------|-----------------|
| 21 | Unit tests (models, formatters) | Unit | >80% code coverage |
| 21-22 | Widget tests (screens, buttons) | Widget | All screens render |
| 22-23 | Integration tests (API + local DB) | Integration | Entry→Exit→Payment flow |
| 23-24 | Manual testing (real device) | Manual | All features work on real device |
| 24 | Bug fixes, optimization | Bug Fix | Critical bugs fixed |
| **25** | **Regression testing:** All endpoints with new schema | Smoke Test | All routes return 200 |

**Testing Checklist:**
- [ ] Login with valid/invalid credentials
- [ ] Enter vehicle (save locally, sync)
- [ ] Multiple vehicles in list
- [ ] Exit & calculate duration
- [ ] Payment with change
- [ ] QR scan → auto-populate
- [ ] Offline mode (disconnect WiFi)
- [ ] Sync after reconnect
- [ ] Session timeout (30 days)
- [ ] Concurrent operations safe
- [ ] Touch device at 2GB storage
- [ ] Battery usage <5%/hour
- [ ] App launch time <3 seconds

**Performance Targets:**
- App startup: <3 seconds
- API response: <2 seconds
- QR scan: <500ms
- Payment calculation: <100ms
- Offline sync: <10 seconds for 50 transactions

---

### Week 6: Build, Sign & Release (Days 26-30)

| Day | Task | Deliverable | Owner |
|-----|------|-------------|-------|
| 26 | Version setting (pubspec.yaml 1.0.0) | Version file | Dev |
| 26-27 | APK signing (keystore creation) | parqeer-key.jks | DevOps/Dev |
| 27 | APK build (release mode) | app-release.apk (~45MB) | Dev |
| 27-28 | Google Play Store setup (account, listing) | Store listing created | Product |
| 28 | Upload APK, complete store listing | App submitted | Product |
| 29 | Review & approval (2-4 hours) | App live in Play Store | Google |
| **30** | Launch & announcement, internal testing | Users downloading | All |

**Store Listing Checklist:**
- [ ] App title: "Parqeer - Parking Attendant"
- [ ] Short description (80 chars)
- [ ] Full description (comprehensive)
- [ ] 5-8 screenshots (all features)
- [ ] Feature graphic (1024x500)
- [ ] Icon (512x512)
- [ ] Content rating (complete)
- [ ] Privacy policy (linked)
- [ ] Category: Business/Productivity
- [ ] Pricing: Free
- [ ] Version released: 1.0.0

**APK Details:**
- Filename: `app-release.apk`
- Size: ~40-50MB
- Min SDK: 26 (Android 8.0)
- Target SDK: 34 (Android 14)
- Signing: Release keystore

---

## 🛠️ Dependencies & Setup

### Required Tools (Pre-development)

```bash
# Flutter SDK (if not installed)
# Download: https://flutter.dev/docs/get-started/install/windows
flutter doctor  # Should show all green

# Android Studio
# Download: https://developer.android.com/studio
# Install: Android SDK 26+ (API level 26+)

# VS Code + Flutter Extension
# Or use Android Studio IDE

# Git (version control)
git --version

# Postman (API testing)
# Download: https://www.postman.com/downloads/
```

### pubspec.yaml Dependencies

```yaml
# Complete dependency list in MOBILE_APP_QUICK_START.md
# Install: flutter pub get
# Build runner: flutter pub run build_runner build
```

---

## 🔧 Backend Requirements

### New Endpoints Needed

| Endpoint | Method | Purpose | Status |
|----------|--------|---------|--------|
| `/api/auth/login` | POST | Attendant login | ✅ [MobileController.php](app/Http/Controllers/MobileController.php) |
| `/api/auth/logout` | POST | Logout & revoke token | ✅ AuthController |
| `/api/transactions/batch` | POST | Sync offline transactions | ✅ MobileController |
| `/api/areas/occupancy` | GET | Real-time occupancy | ✅ MobileController |
| `/api/attendant/daily-summary` | GET | Daily stats for user | ✅ MobileController |
| `/api/attendant/active-transactions` | GET | List vehicles still parked | ✅ MobileController |
| `/api/mobile/register` | POST | Device token for FCM | ✅ MobileController |
| `/api/user` | GET | Current user profile | ✅ Sanctum default |

### Backend Modifications Checklist

- [x] Create `/app/Http/Controllers/MobileController.php`
- [x] Add routes to `routes/api.php`
- [x] Ensure Sanctum configured (token expiry: 30 days)
- [ ] Database migration: add user columns (device_token, app_version)
- [ ] Test all API endpoints with Postman collection
- [ ] Verify error responses (401, 422, 500)
- [ ] Set CORS headers for mobile app domain
- [ ] Configure rate limiting (60 requests/minute)
- [ ] Add database indexes on frequently queried columns

### Database Migrations

```php
// Add to users table migration
Schema::table('users', function (Blueprint $table) {
    $table->string('device_token')->nullable();
    $table->string('last_device_platform')->nullable();
    $table->string('app_version')->nullable();
    $table->timestamp('last_login_at')->nullable();
});

// Run: php artisan migrate
```

---

## 👥 Team Responsibilities

### Mobile Developer(s) - 1.5 FTE
**Week 1-2:**
- Project setup, auth flow, entry/exit screens
- API integration testing

**Week 3-4:**
- QR scanning, payment, offline mode
- UI refinement

**Week 5-6:**
- Testing, bug fixes, APK build
- Play Store submission support

**Daily Tasks:**
- Code commits (git)
- Testing on real device
- Bug tracking (GitHub Issues)
- Sync with backend engineer

### Backend Engineer - 0.5 FTE
**Week 1:**
- Create API routes, auth endpoint
- Verify database queries work

**Week 2:**
- Batch sync endpoint
- Active transactions endpoint

**Week 3-4:**
- Occupancy endpoint
- Payment logging
- Device token management

**Week 5-6:**
- Database migrations
- Regression testing
- Performance optimization

### DevOps/Release Manager - 0.25 FTE
**Week 6:**
- Create signing keystore
- Build release APK
- Play Store account setup
- Monitor first users

### Product Manager - 0.25 FTE
**Full Duration:**
- Requirements clarification
- Feature trade-off decisions
- User feedback collection
- Release announcement

---

## 🚀 Deployment Plan

### Pre-Launch (Week 5-6)

#### Internal Testing (Alpha)
```
1. Install APK on test devices (5-10 devices)
2. Test with real attendants in parking
3. Collect feedback
4. Fix critical bugs
5. Week 5 end: All attendants can test
```

#### Play Store Submission
```
1. Create Google Play Developer Account ($25)
2. Create app listing (complete with screenshots)
3. Upload APK (app-release.apk)
4. Set pricing: Free
5. Submit for review
6. Wait 2-4 hours for approval
7. Monitor for rejection (rare for business apps)
```

### Post-Launch (Week 6+)

#### Monitoring
- Crash reports (Firebase Crashlytics)
- User reviews & ratings
- Daily active users
- Transaction success rate

#### Updates
- Week 1 after launch: Bug fix build
- Week 2+: Feature updates (Phase 2)

---

## 📱 Testing Strategy

### Device Requirements
- **Minimum:** Android 8.0 (Nexus 5X or higher)
- **Recommended:** Android 10+ (recent device)
- **Screen Sizes:** Test on phone (5") and tablet (7")
- **Network:** Test 4G, WiFi, and offline mode

### Test Device Setup
```
Devices to test on:
- Android emulator (Android 12, 13, 14)
- Real device (Samsung Galaxy, Xiaomi, or Oppo)
- Low-end device (2GB RAM, verify app works)
- Old device (Android 8.0, min target)
```

### Test Scenarios

**Authentication Flow**
- [ ] Valid login → Home screen
- [ ] Invalid password → Error message
- [ ] Logout → Login screen
- [ ] Token expiration → Auto-logout
- [ ] Device restart → Stay logged in

**Transaction Entry**
- [ ] New entry → Saved locally & synced
- [ ] Duplicate plate → Error or warning
- [ ] Missing area → Error message
- [ ] Offline entry → Queued for sync
- [ ] Invalid plate format → Validation error

**Transaction Exit**
- [ ] Select from list → Exit processed
- [ ] Duration calculated → Correct minutes/hours
- [ ] Estimated cost shown → Accurate
- [ ] Multiple vehicles → All list correctly
- [ ] Already exited vehicle → Error message

**Payment**
- [ ] Enter paid amount → Change calculated
- [ ] Insufficient payment → Error
- [ ] Exact amount → Change = 0
- [ ] Offline payment → Queued
- [ ] Receipt generated → QR visible, details complete

**QR Scanning**
- [ ] Camera permission → Granted
- [ ] Valid QR → Data parsed
- [ ] Invalid QR → Error
- [ ] Scanned plate → Auto-filled in form
- [ ] Focus vs. scan → Works in bright/dim light

**Offline Mode**
- [ ] Disable WiFi → "Offline" status shown
- [ ] Queue transaction → Saved locally
- [ ] Feature disabled → Clear indication
- [ ] Re-enable WiFi → Auto-sync starts
- [ ] Sync complete → Queue empty, "Online" status

**Performance**
- [ ] App launch → <3 seconds
- [ ] Screen transition → <500ms
- [ ] API call → <2 seconds
- [ ] QR scan → <500ms for detection
- [ ] Offline sync → <10s for 50 transactions
- [ ] Battery → <5% per hour usage
- [ ] Storage → Works with 2GB free

---

## 🐛 Known Issues & Mitigations

| Issue | Severity | Mitigation | Timeline |
|-------|----------|-----------|----------|
| QR scanner crashes on old Android | Medium | Fallback to manual input | Week 4 |
| Firebase init slow on first launch | Low | Cache token locally | Week 4 |
| Large offline queue (100+ txs) | Low | Batch in groups of 50 | Week 5 |
| Screen keyboard overlap on small phones | Low | Test on Nexus 5X (5"), adjust layouts | Week 3 |
| Battery drain if sync loop | High | Add exponential backoff | Week 4 |
| API rate limit exceeded | Medium | Implement local caching | Week 5 |

---

## 📊 Success Criteria

### Functional Requirements (MVP)
- [x] Login with email/password
- [x] View active transactions
- [x] Enter new vehicle (plate, color, area)
- [x] Exit vehicle and auto-calculate parking duration
- [x] Process payment with change calculation
- [x] Scan QR codes
- [x] View real-time occupancy
- [x] View daily summary
- [x] Work offline with auto-sync
- [x] Receive push notifications
- [x] User session expires after 30 days

### Non-Functional Requirements
- Performance: API response <2s, QR scan <500ms
- Reliability: >99% transaction success on sync
- Usability: <30 seconds for complete entry+exit+payment
- Battery: <5% per hour on typical usage
- Storage: Works on 2GB free storage
- Crashes: <0.1% crash rate
- User satisfaction: >4.5/5 stars

### Business Goals
- Reduce paper usage by 80%
- Increase transaction speed (cashier efficiency)
- Enable real-time occupancy visibility
- Improve audit trail (all activities logged)
- Reduce attendant training time (intuitive UI)

---

## 💡 Key Decisions Made

| Decision | Rationale | Impact |
|----------|-----------|--------|
| Flutter (vs React Native) | Faster performance, compile to native, strong typing | Slightly steeper learning curve, but better UX |
| Android only (vs iOS) | Simpler MVP, target market uses Android | Can add iOS later if needed |
| Sanctum auth tokens | Existing in Laravel, no extra config | Token expiry at 30 days, can be reduced |
| SQLite local storage | Built-in Android, no external dependencies | Automatic sync via batch API |
| Provider state mgmt | Simpler than Riverpod, decent performance | Can upgrade to Riverpod later |
| Offline-first approach | Attendants work in areas with poor connectivity | Crucial for reliability |
| QR scanning optional | Entry works with manual input fallback | Reduces dependency on permission issues |

---

## 📞 Communication Plan

### Daily Standups (15 min)
- Time: 10:00 AM daily
- Attendees: Mobile dev, Backend dev, Product lead
- Topics: Blockers, progress, dependencies

### Weekly Reviews (30 min)
- Time: Friday 2:00 PM
- Attendees: All + stakeholders
- Topics: Weekly progress, upcoming risks, user feedback

### Tools
- Git: GitHub/GitLab for code
- Issues: GitHub Issues for bugs/features
- Slack: #parqeer-mobile for real-time chat
- Trello/Jira: Task tracking

---

## 🚨 Risk Management

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|-----------|
| API endpoint delay | Medium | High | Parallel dev with mock API |
| QR camera permission issues | Low | Medium | Fallback to manual input field |
| Offline sync infinite loop | Low | High | Add max retry limit + exponential backoff |
| Play Store rejection | Very Low | High | Review policies early, test thoroughly |
| Battery drain | Medium | Medium | Profile battery usage in Week 4 |
| Data loss on app crash | Low | High | Implement data persistence + recovery |
| Network timeout on large sync | Low | Medium | Chunk batches into <50 transactions |

---

## 📚 Documentation & Resources

### Deliverables
- [x] MOBILE_APP_IMPLEMENTATION.md (50KB comprehensive guide)
- [x] MOBILE_APP_QUICK_START.md (Developer quick reference)
- [x] MobileController.php (Backend API code)
- [x] MOBILE_API_ROUTES.php (Route definitions)
- [x] Parqeer_Mobile_API.postman_collection.json (Postman tests)
- [ ] Code repository with README
- [ ] Deployment guide
- [ ] Troubleshooting FAQ
- [ ] User manual for attendants

### External Resources
- [Flutter Documentation](https://flutter.dev/docs)
- [Dio HTTP Library](https://pub.dev/packages/dio)
- [Firebase Cloud Messaging](https://firebase.google.com/docs/cloud-messaging)
- [Android Development Guide](https://developer.android.com/docs)
- [Play Store Publishing](https://developer.android.com/studio/publish)
- [Sanctum Auth](https://laravel.com/docs/sanctum)

---

## ✅ Pre-Development Checklist

Before February 16 (construction start):

**Infrastructure:**
- [ ] Git repository created (public or private)
- [ ] CI/CD pipeline configured (optional but recommended)
- [ ] Google Play Developer account created ($25)
- [ ] Firebase project created (for notifications)
- [ ] Firebase Dart configuration file downloaded

**Backend:**
- [ ] MobileController.php code reviewed
- [ ] API routes added to routes/api.php
- [ ] Database migration prepared
- [ ] Postman collection imported and tested
- [ ] CORS headers configured
- [ ] Rate limiting configured (60 req/min)

**Development Environment:**
- [ ] Flutter SDK installed & `flutter doctor` green
- [ ] Android Studio with SDK 26+ installed
- [ ] At least 2 test devices available
- [ ] Android emulator configured (Android 12+)
- [ ] VS Code + Flutter extension OR Android Studio IDE

**Team:**
- [ ] Dev(s) assigned and available 100%
- [ ] Backend engineer assigned 50%
- [ ] Product manager assigned 25%
- [ ] First standup scheduled (Feb 16)
- [ ] Slack channel created (#parqeer-mobile)
- [ ] Access to all repositories granted

**Knowledge Transfer:**
- [ ] Team reviewed MOBILE_APP_IMPLEMENTATION.md
- [ ] Team reviewed MOBILE_APP_QUICK_START.md
- [ ] Backend API walkthroughs completed
- [ ] Q&A session for clarifications

---

## 🎉 Success Announcement Template

```
🎉 Parqeer Mobile App v1.0.0 Launched! 🎉

Finally available on Google Play Store!

For parking attendants:
✅ Enter/exit vehicles faster than ever
✅ Process payments instantly
✅ Works offline too!
✅ Track your daily earnings

Download now: 
https://play.google.com/store/apps/details?id=com.parqeer.mobile

Version 1.0.0 Features:
• Quick vehicle entry (plate + location)
• Automatic exit & duration calculation
• Payment processing with change
• QR code scanning
• Real-time occupancy visibility
• Daily earnings summary
• Offline mode with auto-sync
• Push notifications

Next update (v1.1):
• Vehicle blacklist integration
• Parking slot management
• Peak hour rate adjustment

Questions? Support: support@parqeer.com
```

---

## 🔄 Post-Launch Phase 2

### Weeks 7-10: Feature Enhancements
- Implement blacklist checking
- Add slot management (prevent double-parking)
- Peak hour rate adjustments  
- Enhanced receipt printing
- Multi-language support

### Timeline for Phase 2
- **Week 7:** Gather user feedback
- **Week 8-9:** Implement Phase 2 features
- **Week 10:** Release v1.1

### Extended Roadmap (Q2-Q3 2026)
- iOS version (if demand exists)
- Owner/admin dashboard app
- Advanced analytics
- Integration with parking sensor systems

---

**Document Version:** 1.0  
**Last Updated:** February 16, 2026  
**Next Review:** After Week 1 completion  
**Status:** Ready for development kickoff
