# Flutter Mobile App - Quick Start Guide

**Status:** Ready for development  
**Framework:** Flutter 3.x (Android 8.0+)  
**Target Users:** Parking Attendants  
**Development Estimated:** 4-6 weeks MVP  

---

## 📱 Quick Setup (For Developers)

### Prerequisites
```bash
# Install Flutter (if not already installed)
# Windows: Download from https://flutter.dev/docs/get-started/install/windows

# Verify installation
flutter doctor
  # Should show green checkmarks for:
  # - Flutter
  # - Dart
  # - Android Studio
  # - Chrome
  # - VS Code Flutter plugin
```

### Step 1: Create Project
```bash
cd c:\application\parqeer-app

# Create new Flutter project
flutter create --org com.parqeer parqeer_mobile

cd parqeer_mobile
```

### Step 2: Update pubspec.yaml

Copy this content into `pubspec.yaml` under `dependencies:`:

```yaml
flutter:
  sdk: flutter

# HTTP & API
dio: ^5.3.0
retrofit: ^4.0.0
json_serializable: ^6.6.0
json_annotation: ^4.8.0

# State Management
provider: ^6.0.0
riverpod: ^2.4.0
flutter_riverpod: ^2.4.0

# QR Code
qr_flutter: ^4.0.0
mobile_scanner: ^3.5.0

# Local Storage
sqflite: ^2.3.0
hive: ^2.2.0
hive_flutter: ^1.1.0
shared_preferences: ^2.2.0

# UI
google_fonts: ^6.1.0
intl: ^0.19.0

# Image & Camera
image_picker: ^1.0.0
camera: ^0.10.0

# Notifications
firebase_messaging: ^14.6.0
firebase_core: ^2.23.0

# Logging
logger: ^1.4.0

# Connectivity
connectivity_plus: ^5.0.0

dev_dependencies:
  flutter_test:
    sdk: flutter
  build_runner: ^2.4.0
  retrofit_generator: ^7.0.0
  json_serializable: ^6.6.0
```

### Step 3: Install Dependencies
```bash
flutter pub get
flutter pub run build_runner build  # For code generation
```

### Step 4: Configure Android

**File:** `android/app/build.gradle`

```gradle
android {
    compileSdkVersion 34  // Update if older
    
    defaultConfig {
        targetSdkVersion 34
        minSdkVersion 26  // Android 8.0
        versionCode 1
        versionName "1.0.0"
    }
}

dependencies {
    implementation 'com.google.firebase:firebase-messaging'
}
```

**File:** `android/app/src/main/AndroidManifest.xml`

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android">
    <!-- Permissions -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.CAMERA" />
    <uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
    <uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />
    
    <application>
        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:configChanges="orientation|keyboardHidden|keyboard|screenSize|smallestScreenSize|locale|layoutDirection|fontScale|screenLayout|density|uiMode">
        </activity>
    </application>
</manifest>
```

### Step 5: Create Project Structure

```
parqeer_mobile/lib/
├── main.dart
├── config/
│   ├── api_config.dart
│   └── constants.dart
├── models/
│   ├── transaction.dart
│   ├── area.dart
│   └── user.dart
├── providers/
│   ├── auth_provider.dart
│   ├── transaction_provider.dart
│   └── offline_provider.dart
├── services/
│   ├── api_service.dart
│   ├── storage_service.dart
│   └── qr_service.dart
├── screens/
│   ├── login_screen.dart
│   ├── home_screen.dart
│   ├── entry_screen.dart
│   ├── exit_screen.dart
│   ├── payment_screen.dart
│   └── dashboard_screen.dart
├── widgets/
│   ├── qr_scanner_widget.dart
│   ├── transaction_card.dart
│   └── custom_button.dart
└── utils/
    ├── validators.dart
    └── formatters.dart
```

Create directories:
```bash
mkdir -p lib/{config,models,providers,services,screens,widgets,utils}
```

---

## 🔌 Backend Configuration

### Update Laravel Routes

**File:** `routes/api.php`

Add the mobile API routes (see `MOBILE_API_ROUTES.php` for complete code):

```php
Route::post('/auth/login', [AuthController::class, 'mobileLogin']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/transactions/batch', [MobileController::class, 'batchSync']);
    Route::get('/areas/occupancy', [MobileController::class, 'occupancy']);
    Route::get('/attendant/daily-summary', [MobileController::class, 'dailySummary']);
});
```

### Create Mobile Controller

The complete controller code is in `app/Http/Controllers/MobileController.php`

Key endpoints:
- `POST /api/auth/login` — Authenticate attendant
- `POST /api/transactions/batch` — Sync offline transactions
- `GET /api/areas/occupancy` — Get real-time occupancy
- `GET /api/attendant/daily-summary` — Daily stats

### Database Migrations

Add columns for mobile support:

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('device_token')->nullable();
    $table->string('last_device_platform')->nullable();
    $table->string('app_version')->nullable();
});
```

Run: `php artisan migrate`

---

## 🔑 API Authentication

The mobile app uses **Laravel Sanctum** for token-based authentication.

**Login Request:**
```dart
POST /api/auth/login
{
  "email": "petugas@parqeer.com",
  "password": "password"
}

Response (200):
{
  "success": true,
  "user": {
    "id": 1,
    "name": "Budi Susanto",
    "role": "attendant"
  },
  "token": "7|H3x9wzL8k2mJ4vL5nP2q9R8tU1x4yZ7c9A0bD3eF6",
  "expires_at": "2026-03-17T10:30:00Z"
}
```

**All subsequent requests include:**
```
Authorization: Bearer 7|H3x9wzL8k2mJ4vL5nP2q9R8tU1x4yZ7c9A0bD3eF6
```

---

## 📦 Core Features Implementation Order

### Phase 1: Authentication & Setup (Days 1-3)
- [ ] Login screen
- [ ] API service (Dio + Retrofit)
- [ ] Auth provider (token storage)
- [ ] Home/dashboard screen

### Phase 2: Entry & Exit (Days 4-7)
- [ ] Entry form (plate, color, area selection)
- [ ] Vehicle number input with validation
- [ ] Exit screen (simple selection from active list)
- [ ] Occupancy display

### Phase 3: QR Scanning (Days 8-10)
- [ ] QR scanner integration
- [ ] QR generation for receipts
- [ ] Scan to auto-populate plate number
- [ ] Receipt display

### Phase 4: Payment (Days 11-14)
- [ ] Payment screen (amount, change calculation)
- [ ] Payment method selection
- [ ] Receipt printing/sharing
- [ ] Payment history

### Phase 5: Offline Mode (Days 15-19)
- [ ] Local SQLite database setup
- [ ] Queue failed transactions
- [ ] Auto-sync when online
- [ ] Offline state indicator

### Phase 6: Testing & Polish (Days 20-30)
- [ ] Unit tests
- [ ] Widget tests
- [ ] Integration tests
- [ ] Bug fixes & optimization
- [ ] APK build & signing

---

## 🧪 Testing Checklist

### Device/Emulator Setup

**Android Emulator:**
```bash
# If Flutter SDK not in PATH:
flutter devices

# Create emulator if needed
flutter emulator --create --name parqeer

# Start emulator
flutter emulators --launch parqeer

# Or use Android Studio AVD Manager
```

**Real Device:**
- Enable USB debugging
- Connect via USB
- `flutter devices` should show device

### Run App
```bash
# Debug mode (for development)
flutter run

# Release mode (faster performance)
flutter run --release

# Build APK
flutter build apk --release
```

**Test Scenarios:**
- [ ] Login with valid credentials
- [ ] Login with invalid credentials (error shown)
- [ ] Enter vehicle (save locally, sync to API)
- [ ] Exit vehicle (calculate duration)
- [ ] Process payment (calculate change)
- [ ] QR code scan (populate fields)
- [ ] Offline mode (queue transactions, sync later)
- [ ] Session timeout (after 30 days, auto-logout)
- [ ] Network loss (graceful fallback, retry)

---

## 🔐 Security Checklist

- [ ] API calls use HTTPS only (prod)
- [ ] Auth token stored securely (SharedPreferences or Hive with encryption)
- [ ] Token expires after 30 days
- [ ] Logout revokes token
- [ ] QR codes expire after 5 minutes
- [ ] Rate limiting on API calls (60/minute)
- [ ] Input validation on all forms
- [ ] No logs of sensitive data (payment amounts, passwords)

---

## 📱 APK Build & Upload

### Generate Signing Key (One time)
```bash
keytool -genkey -v -keystore ~/parqeer-key.jks \
  -keyalg RSA -keysize 2048 -validity 10000 \
  -alias parqeer-key

# This creates parqeer-key.jks
# Password example: Parqeer123!@#
```

### Sign APK for Release
```bash
# Create signing configuration
# File: android/key.properties

storePassword=Parqeer123!@#
keyPassword=Parqeer123!@#
keyAlias=parqeer-key
storeFile=/path/to/parqeer-key.jks

# Build signed APK
flutter build apk --release

# Output: build/app/outputs/flutter-apk/app-release.apk
# Size: ~40-50MB
```

### Google Play Store Upload
1. Create Play Store account ($25 one-time fee)
2. Create app entry (Parqeer Mobile)
3. Fill store listing:
   - Title: "Parqeer - Parking Attendant"
   - Description: Parking management for attendants
   - Screenshots: 5-8 screenshots of app
   - Icon: 512x512 PNG
4. Content rating: Complete questionnaire
5. Upload APK: `build/app/outputs/flutter-apk/app-release.apk`
6. Set pricing: Free
7. Submit for review (2-4 hours approval)

---

## 🐛 Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| `Target SDK version too low` | Update `android/app/build.gradle` minSdkVersion to 26 |
| `Gradle build fails` | Run `flutter clean` then `flutter pub get` |
| `QR scanner not working` | Check camera permissions granted in manifest |
| `API calls timeout` | Check backend is running (http://localhost:8000) |
| `Offline sync stuck` | Check database migration ran, restart app |
| `APK too large (>100MB)` | Enable `minifyEnabled true` in build.gradle |

---

## 📊 Development Timeline (4-6 weeks)

| Week | Tasks | Status |
|------|-------|--------|
| **Week 1** | Setup, auth, API integration, home screen | ⏳ |
| **Week 2** | Entry/exit screens, occupancy, basic UI | ⏳ |
| **Week 3** | QR scanning, payment calculation, receipts | ⏳ |
| **Week 4** | Offline mode, local database, sync logic | ⏳ |
| **Week 5** | Testing (unit, widget, integration), bug fixes | ⏳ |
| **Week 6** | APK build, signing, Play Store release prep | ⏳ |

---

## 💰 Development Costs (Estimate)

| Item | Cost |
|------|------|
| Flutter Developer (4-6 weeks @ Rp 400K/day) | Rp 20-30M |
| Backend API development | Rp 5-10M |
| Testing & QA | Rp 3-5M |
| Google Play Store account (one-time) | $25 |
| **Total** | **Rp 28-45M + $25** |

---

## 📚 Resources

- [Flutter Official Docs](https://flutter.dev/docs)
- [Dio HTTP Client](https://pub.dev/packages/dio)
- [Mobile Scanner (QR)](https://pub.dev/packages/mobile_scanner)
- [Provider State Management](https://pub.dev/packages/provider)
- [SQLite with Flutter](https://pub.dev/packages/sqflite)
- [Firebase Messaging](https://firebase.flutter.dev/docs/messaging/overview)
- [Android Emulator Docs](https://developer.android.com/studio/run/emulator)

---

## ✅ Handoff Checklist

Before handing off to development team:

- [ ] Backend API endpoints tested (Postman collection created)
- [ ] Database migrations prepared and tested
- [ ] Flutter SDK installed and `flutter doctor` shows all green
- [ ] Android Studio with latest SDK (API 34) installed
- [ ] Project structure scaffolded
- [ ] Initial dependencies in pubspec.yaml
- [ ] Main.dart skeleton created
- [ ] Git repository initialized with `.gitignore`
- [ ] Design mockups/wireframes provided to team
- [ ] API documentation generated
- [ ] Developer environment tested on 2+ machines

---

**Next Steps:**  
1. Assign Flutter developer to this project
2. Schedule kickoff meeting to review MOBILE_APP_IMPLEMENTATION.md
3. Start with Week 1 tasks (setup & auth)
4. Use this guide as daily reference

📞 **Questions?** Refer to MOBILE_APP_IMPLEMENTATION.md for comprehensive details.
