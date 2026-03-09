# Parqeer Mobile App - Implementation Guide

**Platform:** Android (Flutter)  
**Target Users:** Parking Attendants (Petugas Parkir)  
**Status:** Planning Phase  
**Estimated Timeline:** 4-6 weeks MVP

---

## 🏗️ Architecture Overview

### Frontend Stack
- **Framework:** Flutter (Dart)
- **State Management:** Provider / Riverpod
- **Local Database:** SQLite / Hive
- **HTTP Client:** Dio
- **QR/Barcode:** mobile_scanner
- **Camera:** image_picker
- **Push Notifications:** Firebase Cloud Messaging
- **Storage:** SharedPreferences (simple), Hive (complex)

### Backend Connection
- RESTful API (Laravel backend - already existing)
- Authentication: Token-based (Bearer token)
- Real-time sync: Optional WebSocket for live occupancy

### Device Requirements
- Android 8.0+ (min SDK 26)
- Camera access (QR scanning)
- GPS access (location tracking)
- Notifications permission

---

## 📱 Main Features for Attendants

### 1. **Login & Authentication**
```
Flow:
  1. Username/Password login
  2. CAPTCHA verification (already in backend)
  3. Token stored locally
  4. Auto-login with refresh token
```

### 2. **Vehicle Entry Recording**
```
Screen: Entry Form
  - Scan QR / Manual plate number input
  - Vehicle color input
  - Area selection (dropdown with occupancy %)
  - Entry time (auto-filled, can adjust)
  - Camera preview
  - Submit → Generate QR receipt

Benefits:
  - Faster than web form
  - Offline mode (queue if no internet)
  - Real-time area occupancy
```

### 3. **Vehicle Exit Recording**
```
Screen: Current Parked Vehicles List
  - Search by plate number / scan QR
  - Show entry time + duration
  - Show current estimated cost
  - Tap to record exit
  - Submit exit time
  - Proceed to payment

Real-time duration calculation:
  - Updates every second
  - Shows Rp amount dynamically
```

### 4. **Payment Processing**
```
Screen: Payment Form
  - Display: Parking cost
  - Input: Amount paid (cash)
  - Auto-calculate: Change
  - Print/Preview: Receipt with QR
  - Save locally for offline processing
```

### 5. **QR Scanner**
```
Features:
  - Real-time camera feed
  - Barcode detection (mobile_scanner)
  - Auto-submit on valid QR
  - Manual override option
  - Flashlight toggle
  - History of scanned codes

Use cases:
  - Entry: Scan vehicle QR from previous receipt
  - Exit: Scan to quickly find vehicle
```

### 6. **Dashboard (At-a-Glance)**
```
Screen: Attendant Dashboard
  - Today's transactions count
  - Current parked vehicles in area
  - Total revenue today
  - Current time
  - Network status indicator
  - Sync status (online/offline)

Notifications:
  - Area becoming full
  - Vehicle over-parked (alert for unusual durations)
```

### 7. **Offline Mode**
```
Automatically enabled when:
  - No internet connection
  - Device is in offline area

Features:
  - Queue transactions locally
  - Sync when connection restored
  - Conflict resolution (server wins)
  - Show "Pending sync" badge

Limitations:
  - Can't fetch fresh rates
  - Can't verify vehicle history
  - Show last-known area occupancy
```

### 8. **Settings & Profile**
```
Screen: Settings
  - Change password
  - Logout
  - App version
  - Check for updates
  - Toggle notifications
  - Toggle dark mode
```

---

## 📊 Screen Flow (Wireframe)

```
Login Screen
    ↓ (authentication: username/password/captcha)
    ↓
Dashboard (Attendant Home)
├── Entry Recording Quick Button
│   ↓
│   Entry Form
│   ├── QR Scanner ← Start here for fast entry
│   ├── Manual Plate Input
│   ├── Color & Area Select
│   └── Submit → Receipt Preview
│
├── Exit/Payment Quick Button
│   ↓
│   Vehicle List (or Scanner)
│   ├── Search Plate
│   ├── Scan QR
│   └── Tap Vehicle
│       ↓
│       Exit Confirmation
│       ↓
│       Payment Form
│       ├── Show Cost (real-time)
│       ├── Input Cash Amount
│       ├── Calculate Change
│       └── Print Receipt
│
└── Settings
    ├── Change Password
    ├── Logout
    └── App Settings
```

---

## 🔌 Backend API Requirements

All endpoints already exist (✓) or need to be created (✗)

### Existing Endpoints ✓
```
POST /login                              ✓ Auth
GET /attendant/transaction               ✓ List active
GET /attendant/transaction/search/vehicle ✓ Search
GET /attendant/transaction/get-rate      ✓ Rate calculation
POST /attendant/transaction              ✓ Create entry
POST /attendant/transaction/{id}/exit    ✓ Record exit
POST /attendant/transaction/{id}/pay     ✓ Process payment
GET /api/transaction/{id}/current-amount ✓ Real-time cost
```

### New Endpoints Needed ✗

#### 1. **Offline Transaction Queue** [NEW]
```
POST /api/transactions/batch
Content: [
  { plate, area_id, entry_time, status: 'pending' },
  { id, exit_time, status: 'pending' },
  { id, paid_amount, change, status: 'pending' }
]
Returns: { synced: 5, failed: 0, conflicts: [] }
```

#### 2. **Real-time Area Occupancy** [NEW]
```
GET /api/areas/occupancy
Returns: {
  "areas": [
    { "id": 1, "name": "B1", "occupied": 45, "capacity": 100, "percentage": 45 },
    { "id": 2, "name": "B2", "occupied": 89, "capacity": 100, "percentage": 89 }
  ]
}
```

#### 3. **Mobile Device Registration** [OPTIONAL]
```
POST /api/mobile/register
Body: { user_id, device_token, platform: "android", app_version }
Purpose: Push notifications, usage tracking
```

#### 4. **Attendant Activity Summary** [OPTIONAL]
```
GET /api/attendant/daily-summary
Returns: {
  "date": "2026-02-16",
  "transactions_count": 45,
  "revenue": 450000,
  "vehicles_entered": 25,
  "vehicles_exited": 20,
  "average_duration": 120
}
```

---

## 🛠️ Setup Instructions

### Step 1: Install Flutter
```bash
# Download Flutter SDK (3.x+)
# https://flutter.dev/docs/get-started/install

# Verify installation
flutter doctor

# Expected: Android SDK ✓, Android toolchain ✓
```

### Step 2: Create Flutter Project
```bash
flutter create parqeer_mobile --org com.parqeer

cd parqeer_mobile
```

### Step 3: Project Structure
```
parqeer_mobile/
├── lib/
│   ├── main.dart                 # Entry point
│   ├── models/
│   │   ├── transaction.dart
│   │   ├── area.dart
│   │   ├── user.dart
│   │   └── payment.dart
│   ├── services/
│   │   ├── api_service.dart      # HTTP client
│   │   ├── auth_service.dart     # Login/token
│   │   ├── transaction_service.dart
│   │   ├── storage_service.dart  # Local storage
│   │   └── sync_service.dart     # Offline sync
│   ├── providers/                # State management
│   │   ├── auth_provider.dart
│   │   ├── transaction_provider.dart
│   │   ├── area_provider.dart
│   │   └── sync_provider.dart
│   ├── screens/
│   │   ├── login_screen.dart
│   │   ├── dashboard_screen.dart
│   │   ├── entry_screen.dart
│   │   ├── exit_screen.dart
│   │   ├── payment_screen.dart
│   │   ├── scanner_screen.dart
│   │   ├── settings_screen.dart
│   │   └── vehicle_list_screen.dart
│   ├── widgets/
│   │   ├── app_drawer.dart
│   │   ├── transaction_card.dart
│   │   ├── occupancy_card.dart
│   │   └── common_widgets.dart
│   └── utils/
│       ├── constants.dart
│       ├── validators.dart
│       └── extensions.dart
├── pubspec.yaml                  # Dependencies
└── android/
    └── app/
        └── build.gradle          # Android config
```

### Step 4: Dependencies (pubspec.yaml)
```yaml
dependencies:
  # UI & Navigation
  cupertino_icons: ^1.0.0
  get: ^4.6.0  # or: navigation: ^2.0.0

  # State Management
  provider: ^6.0.0
  # OR riverpod: ^2.0.0

  # HTTP & API
  dio: ^5.0.0
  http: ^1.0.0

  # Local Storage
  shared_preferences: ^2.0.0
  hive: ^2.0.0
  hive_flutter: ^1.0.0

  # QR & Camera
  mobile_scanner: ^2.0.0
  image_picker: ^0.8.0
  camera: ^0.10.0

  # Time & Date
  intl: ^0.18.0

  # Notifications
  firebase_core: ^2.0.0
  firebase_messaging: ^14.0.0

  # Logging
  logger: ^2.0.0

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_linter:
  build_runner: ^2.0.0
```

### Step 5: Android Config (android/app/build.gradle)
```gradle
android {
    compileSdkVersion 33
    
    defaultConfig {
        minSdkVersion 26        # Android 8.0+
        targetSdkVersion 33
        versionCode 1
        versionName "1.0.0"
    }
}

// Add permissions to android/app/AndroidManifest.xml
<uses-permission android:name="android.permission.CAMERA" />
<uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
<uses-permission android:name="android.permission.INTERNET" />
<uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
```

---

## 💻 Code Templates

### Template 1: API Service
```dart
// lib/services/api_service.dart
class ApiService {
  static const String BASE_URL = 'https://your-domain.com/api';
  final Dio _dio;
  String? _token;

  ApiService() : _dio = Dio(BaseOptions(
    baseUrl: BASE_URL,
    connectTimeout: Duration(seconds: 10),
    receiveTimeout: Duration(seconds: 10),
  )) {
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) {
          if (_token != null) {
            options.headers['Authorization'] = 'Bearer $_token';
          }
          return handler.next(options);
        },
      ),
    );
  }

  // Login
  Future<AuthResponse> login(String username, String password, String captcha) async {
    try {
      final response = await _dio.post('/login', data: {
        'username': username,
        'password': password,
        'captcha': captcha,
      });
      _token = response.data['token'];
      return AuthResponse.fromJson(response.data);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // Create transaction (entry)
  Future<Transaction> createTransaction(TransactionData data) async {
    try {
      final response = await _dio.post('/attendant/transaction', data: data.toJson());
      return Transaction.fromJson(response.data['data']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // Record exit
  Future<Transaction> recordExit(int transactionId, String exitTime) async {
    try {
      final response = await _dio.post(
        '/attendant/transaction/$transactionId/exit',
        data: { 'exit_time': exitTime }
      );
      return Transaction.fromJson(response.data['data']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // Process payment
  Future<Payment> recordPayment(int transactionId, double paidAmount) async {
    try {
      final response = await _dio.post(
        '/attendant/transaction/$transactionId/pay',
        data: { 'paid_amount': paidAmount }
      );
      return Payment.fromJson(response.data['data']);
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  // Get occupancy
  Future<List<Area>> getOccupancy() async {
    try {
      final response = await _dio.get('/api/areas/occupancy');
      return (response.data['areas'] as List)
          .map((a) => Area.fromJson(a))
          .toList();
    } on DioException catch (e) {
      throw _handleError(e);
    }
  }

  String _handleError(DioException e) {
    if (e.response?.statusCode == 401) return 'Unauthorized - Please login again';
    if (e.message?.contains('Connection') ?? false) return 'No internet connection';
    return e.message ?? 'Unknown error';
  }
}
```

### Template 2: Auth Provider
```dart
// lib/providers/auth_provider.dart
class AuthProvider with ChangeNotifier {
  final ApiService _apiService;
  User? _user;
  String? _token;
  bool _isLoading = false;

  User? get user => _user;
  bool get isAuthenticated => _token != null;
  bool get isLoading => _isLoading;

  AuthProvider(this._apiService);

  Future<void> login(String username, String password, String captcha) async {
    _isLoading = true;
    notifyListeners();

    try {
      final response = await _apiService.login(username, password, captcha);
      _token = response.token;
      _user = response.user;
      await _saveToken(_token!);
      notifyListeners();
    } catch (e) {
      throw Exception('Login failed: $e');
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> logout() async {
    _token = null;
    _user = null;
    await _clearToken();
    notifyListeners();
  }

  Future<void> _saveToken(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('auth_token', token);
  }

  Future<void> _clearToken() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }
}
```

### Template 3: Transaction Entry Screen
```dart
// lib/screens/entry_screen.dart
class EntryScreen extends StatefulWidget {
  @override
  State<EntryScreen> createState() => _EntryScreenState();
}

class _EntryScreenState extends State<EntryScreen> {
  final _plateController = TextEditingController();
  final _colorController = TextEditingController();
  String? _selectedAreaId;
  bool _isLoading = false;

  Future<void> _selectByQR() async {
    // Use mobile_scanner for QR
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => ScannerScreen()),
    );
    if (result != null) {
      _plateController.text = result;
    }
  }

  Future<void> _submitEntry() async {
    if (_plateController.text.isEmpty || _selectedAreaId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Isi semua field'))
      );
      return;
    }

    setState(() => _isLoading = true);

    try {
      final transaction = await Provider.of<TransactionProvider>(context, listen: false)
          .createTransaction(
        plateNumber: _plateController.text,
        color: _colorController.text,
        areaId: _selectedAreaId!,
            entryTime: DateTime.now(),
      );

      if (mounted) {
        // Show receipt & QR
        Navigator.pushNamed(
          context,
          '/receipt',
          arguments: transaction,
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'))
      );
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Kendaraan Masuk')),
      body: SingleChildScrollView(
        padding: EdgeInsets.all(16),
        child: Column(
          children: [
            // QR Scanner Button (prominent)
            ElevatedButton.icon(
              onPressed: _selectByQR,
              icon: Icon(Icons.qr_code_scanner),
              label: Text('Scan QR / Barcode'),
              style: ElevatedButton.styleFrom(
                padding: EdgeInsets.symmetric(vertical: 16, horizontal: 32),
                backgroundColor: Colors.blue,
              ),
            ),
            SizedBox(height: 20),
            Divider(),
            SizedBox(height: 20),
            Text('ATAU Masukkan Manual', style: TextStyle(fontWeight: FontWeight.bold)),
            SizedBox(height: 20),

            // Plate input
            TextField(
              controller: _plateController,
              decoration: InputDecoration(
                labelText: 'Plat Nomor',
                hintText: 'B 1234 ABC',
                border: OutlineInputBorder(),
              ),
              textCapitalization: TextCapitalization.characters,
            ),
            SizedBox(height: 16),

            // Color input
            TextField(
              controller: _colorController,
              decoration: InputDecoration(
                labelText: 'Warna Kendaraan',
                border: OutlineInputBorder(),
              ),
            ),
            SizedBox(height: 16),

            // Area dropdown
            Consumer<AreaProvider>(
              builder: (context, areaProvider, _) {
                return DropdownButtonFormField(
                  value: _selectedAreaId,
                  decoration: InputDecoration(
                    labelText: 'Area Parkir',
                    border: OutlineInputBorder(),
                  ),
                  items: areaProvider.availableAreas.map((area) {
                    return DropdownMenuItem(
                      value: area.id.toString(),
                      child: Text('${area.name} (${area.percentage}% penuh)'),
                    );
                  }).toList(),
                  onChanged: (value) {
                    setState(() => _selectedAreaId = value);
                  },
                );
              },
            ),
            SizedBox(height: 32),

            // Submit button
            ElevatedButton(
              onPressed: _isLoading ? null : _submitEntry,
              child: _isLoading
                  ? SizedBox(
                      height: 20,
                      width: 20,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    )
                  : Text('Catat Kendaraan Masuk'),
              style: ElevatedButton.styleFrom(
                minimumSize: Size(double.infinity, 48),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
```

---

## 🔌 Integration: Laravel Backend Modifications Needed

### Add to `routes/api.php`:
```php
// Mobile app endpoints
Route::middleware(['auth:sanctum'])->group(function () {
    // Batch sync for offline transactions
    Route::post('/transactions/batch', [TransactionController::class, 'batchSync']);
    
    // Real-time occupancy
    Route::get('/areas/occupancy', [AreaController::class, 'occupancy']);
    
    // Mobile device registration
    Route::post('/mobile/register', [DeviceController::class, 'register']);
    
    // Attendant daily summary
    Route::get('/attendant/daily-summary', [TransactionController::class, 'dailySummary']);
});
```

### Create `app/Http/Controllers/MobileController.php`:
```php
<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Area;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function batchSync(Request $request)
    {
        $transactions = $request->validate([
            'transactions' => 'required|array',
            'transactions.*.type' => 'required|in:entry,exit,payment',
        ]);

        $results = [
            'synced' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($transactions['transactions'] as $tx) {
            try {
                if ($tx['type'] === 'entry') {
                    Transaction::create([...]);
                } elseif ($tx['type'] === 'exit') {
                    $transaction = Transaction::find($tx['id']);
                    $transaction->processExit($tx['exit_time']);
                } elseif ($tx['type'] === 'payment') {
                    $transaction = Transaction::find($tx['id']);
                    $transaction->paid_amount = $tx['paid_amount'];
                    $transaction->change = $tx['change'];
                    $transaction->status = 'paid';
                    $transaction->save();
                }
                $results['synced']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = $e->getMessage();
            }
        }

        return response()->json($results);
    }

    public function occupancy()
    {
        $areas = Area::with('rates')
            ->select('id', 'name', 'capacity')
            ->withCount(['transactions' => function ($q) {
                $q->whereNull('exit_time');
            }])
            ->get()
            ->map(fn($area) => [
                'id' => $area->id,
                'name' => $area->name,
                'capacity' => $area->capacity,
                'occupied' => $area->transactions_count,
                'percentage' => round(($area->transactions_count / $area->capacity) * 100),
                'available' => $area->capacity - $area->transactions_count,
            ]);

        return response()->json(['areas' => $areas]);
    }
}
```

---

## 📦 Build & Deployment

### Development Build
```bash
# Run on emulator/device
flutter run

# With hot reload enabled
flutter run --hot
```

### Release Build
```bash
# Build APK (shareable, but not for Play Store)
flutter build apk --release
# Output: build/app/outputs/flutter-app.apk

# Build AAB (for Google Play Store)
flutter build appbundle --release
# Output: build/app/outputs/bundle/release/app-release.aab
```

### Signing APK (for distribution)
```bash
# Create keystore (one-time)
keytool -genkey -v -keystore ~/parqeer-key.jks \
  -keyalg RSA -keysize 2048 -validity 10000 \
  -alias parqeer

# Sign APK
jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 \
  -keystore ~/parqeer-key.jks \
  build/app/outputs/flutter-app.apk parqeer

# Zipalign (optional, recommended)
zipalign -v 4 build/app/outputs/flutter-app.apk \
  parqeer-app-v1.0.apk
```

---

## 📱 Publishing on Google Play Store

1. **Create Google Play Developer Account** (25 USD one-time)
2. **Create App on Store**
   - Name: Parqeer Attendant
   - Category: Productivity
   - Content rating questionnaire

3. **Prepare Assets**
   - App icon (192x192)
   - Screenshots (2-5)
   - Description (short & long)
   - Privacy policy (URL)

4. **Upload Release**
   - Upload AAB bundle
   - Set version code/name
   - Fill release notes
   - Submit for review (1-24 hours)

5. **Track Reviews & Crashes**
   - Use Firebase Crashlytics
   - Monitor user reviews
   - Push updates for bugs

---

## 🧪 Testing Plan

### Unit Tests
```dart
// test/services/api_service_test.dart
void main() {
  test('Login returns AuthResponse', () async {
    final apiService = ApiService();
    final response = await apiService.login('user', 'pass', '1234');
    expect(response.token, isNotEmpty);
  });
}
```

### Widget Tests
```dart
// test/screens/entry_screen_test.dart
void main() {
  testWidgets('Entry form shows validation error', (tester) async {
    await tester.pumpWidget(MyApp());
    await tester.tap(find.byType(ElevatedButton));
    await tester.pump();
    expect(find.byType(SnackBar), findsOneWidget);
  });
}
```

### Integration Tests
```dart
// test_driver/app_test.dart
void main() {
  testWidgets('Full login & entry flow', (tester) async {
    // ...complete user journey
  });
}
```

Run tests:
```bash
flutter test
flutter drive --target=test_driver/app.dart
```

---

## 🔐 Security Considerations

✅ **Implement:**
- [ ] SSL/TLS for all API calls
- [ ] Token encryption in local storage (Hive)
- [ ] Timeout auto-logout (5 min)
- [ ] Device binding (prevent token theft)
- [ ] Offline PIN for sensitive ops
- [ ] Data encryption at rest
- [ ] ProGuard obfuscation for release build

---

## 📊 Timeline Estimate

| Phase | Duration | Tasks |
|-------|----------|-------|
| **Setup** | 2-3 days | Flutter project, dependencies, API client |
| **Core Screens** | 1 week | Login, Entry, Exit, Payment screens |
| **QR Scanner** | 3-4 days | Camera integration, barcode detection |
| **Offline Mode** | 4-5 days | Local sync, conflict resolution |
| **Testing** | 1 week | UI, API, integration tests |
| **Polish & Publish** | 3-4 days | Icons, app store assets, signing |
| **MVP Total** | 4-6 weeks | Complete working app |
| **Phase 2** | +2 weeks | Dark mode, notifications, advanced features |

---

## 💰 Cost Estimate

| Item | Cost | Notes |
|------|------|-------|
| Development | Rp 20-50M | 1-2 developers, 4-6 weeks |
| Google Play Developer Account | $25 | One-time |
| Firebase (optional) | Free-$29/mo | Crashlytics, messaging |
| CI/CD Setup (optional) | Free-$100/mo | GitHub Actions, CodeMagic |
| **Total Initial** | ~Rp 20-50M+ | Recurring minimal |

---

## 🚀 Next Steps

1. ✅ Set up Flutter development environment
2. ✅ Create project structure
3. ✅ Build API client & authentication
4. ✅ Implement entry/exit screens
5. ✅ Add QR scanning
6. ✅ Test thoroughly
7. ✅ Prepare for Google Play
8. ✅ Deploy & monitor

---

**Ready to start?** Run: `flutter create parqeer_mobile --org com.parqeer`

Questions? See the templates above for reference implementations.
