# Parqeer Parking System - Flowcharts & DFD

## 🎯 Flowchart Petugas (Attendant)

```mermaid
flowchart TD
    A([Mulai]) --> B[Login dengan Email & Password]
    B --> C{Login Berhasil?}
    C -->|Tidak| D[Tampilkan Error]
    D --> B
    C -->|Ya| E[Tampilkan Dashboard]
    E --> F{Pilih Aksi}
    
    F -->|Masuk Kendaraan| G[Input Data Kendaraan]
    G --> G1[Nomor Plat]
    G1 --> G2[Warna Kendaraan]
    G2 --> G3[Pilih Area Parkir]
    G3 --> G4{Scan QR?}
    G4 -->|Ya| G5[Buka Camera QR Scanner]
    G5 --> G6[Baca Data dari QR]
    G6 --> G7[Isi Otomatis Plat]
    G4 -->|Tidak| G7
    G7 --> H[Simpan Data Masuk ke Database]
    H --> H1[Update Okupansi Area]
    H1 --> I[Tampilkan Konfirmasi & Jam Masuk]
    I --> E
    
    F -->|Keluar Kendaraan| J[Pilih Kendaraan dari List Aktif]
    J --> K[Hitung Durasi Parkir]
    K --> L[Hitung Tarif Berdasarkan Durasi]
    L --> M[Tampilkan Jumlah Bayar]
    M --> N[Simpan Data Keluar]
    N --> O[Update Okupansi Area]
    O --> P[Lanjut ke Proses Pembayaran?]
    
    P -->|Ya| Q[Masukkan Jumlah Bayar]
    Q --> Q1{Cukup?}
    Q1 -->|Tidak| Q2[Tampilkan Error & Hitung Ulang]
    Q2 --> Q
    Q1 -->|Ya| R[Hitung Kembalian]
    R --> S[Cetak/Tampilkan Resi]
    S --> S1[Simpan Status Pembayaran]
    S1 --> E
    
    P -->|Tidak| T[Simpan Status Belum Bayar]
    T --> E
    
    F -->|Lihat Ringkasan Harian| U[Tampilkan Dashboard]
    U --> U1[Total Transaksi Hari Ini]
    U1 --> U2[Total Revenue]
    U2 --> U3[Kendaraan Masuk/Keluar]
    U3 --> U4[Kendaraan Belum Bayar]
    U4 --> E
    
    F -->|Lihat Okupansi| V[Tampilkan Status Semua Area]
    V --> V1[Kapasitas vs Terisi]
    V1 --> V2[Warna: Hijau/Kuning/Merah]
    V2 --> E
    
    F -->|Logout| W[Hapus Token Session]
    W --> X([Berakhir])
```

---

## 🔧 Flowchart Admin

```mermaid
flowchart TD
    A([Mulai]) --> B[Login dengan Email & Password]
    B --> C{Login Berhasil?}
    C -->|Tidak| D[Tampilkan Error]
    D --> B
    C -->|Ya| E{Role Admin?}
    E -->|Tidak| F[Tolak Akses]
    F --> X([Berakhir])
    E -->|Ya| G[Tampilkan Menu Admin]
    
    G --> H{Pilih Aksi}
    
    H -->|Kelola Area| I[Tampilkan List Area]
    I --> I1{Pilih}
    I1 -->|Tambah| I2[Form Input Area Baru]
    I2 --> I3[Nama Area, Kapasitas]
    I3 --> I4[Simpan ke Database]
    I4 --> G
    I1 -->|Edit| I5[Form Edit Area]
    I5 --> I6[Update Kapasitas/Nama]
    I6 --> I4
    I1 -->|Hapus| I7[Konfirmasi Hapus]
    I7 --> I8[Hapus dari Database]
    I8 --> G
    
    H -->|Kelola Tarif| J[Tampilkan List Tarif]
    J --> J1{Pilih}
    J1 -->|Tambah| J2[Pilih Area & Tipe Kendaraan]
    J2 --> J3[Input Tarif per Jam]
    J3 --> J4[Simpan ke Database]
    J4 --> G
    J1 -->|Edit| J5[Ubah Tarif]
    J5 --> J4
    J1 -->|Hapus| J6[Konfirmasi Hapus]
    J6 --> J4
    
    H -->|Kelola User| K[Tampilkan List User]
    K --> K1{Pilih}
    K1 -->|Tambah| K2[Form Registrasi User]
    K2 --> K3[Nama, Email, Role, Password]
    K3 --> K4[Validasi Email Unik]
    K4 --> K5[Simpan User ke Database]
    K5 --> G
    K1 -->|Edit| K6[Ubah Role/Status User]
    K6 --> K5
    K1 -->|Hapus| K7[Konfirmasi Hapus]
    K7 --> K5
    
    H -->|Lihat Log Aktivitas| L[Tampilkan Log Semua Transaksi]
    L --> L1[Filter: Tanggal, User, Area]
    L1 --> L2[Tampilkan: Entry, Exit, Payment]
    L2 --> G
    
    H -->|Export Data| M[Pilih Range Tanggal]
    M --> M1[Pilih Format: Excel/PDF]
    M1 --> M2[Generate Report]
    M2 --> M3[Download File]
    M3 --> G
    
    H -->|Logout| N[Hapus Token Session]
    N --> X([Berakhir])
```

---

## 👔 Flowchart Owner

```mermaid
flowchart TD
    A([Mulai]) --> B[Login dengan Email & Password]
    B --> C{Login Berhasil?}
    C -->|Tidak| D[Tampilkan Error]
    D --> B
    C -->|Ya| E{Role Owner?}
    E -->|Tidak| F[Tolak Akses]
    F --> X([Berakhir])
    E -->|Ya| G[Tampilkan Dashboard Owner]
    
    G --> H{Pilih Aksi}
    
    H -->|Lihat Laporan Ringkas| I[Tampilkan Dashboard]
    I --> I1[Total Revenue Hari Ini]
    I1 --> I2[Total Kendaraan Masuk]
    I2 --> I3[Total Kendaraan Bayar]
    I3 --> I4[Occupancy Rate per Area]
    I4 --> G
    
    H -->|Lihat Laporan Detail| J[Pilih Jenis Laporan]
    J --> J1{Pilih}
    J1 -->|Revenue Harian| J2[Filter Tanggal]
    J2 --> J3[Tampilkan Revenue per Area]
    J3 --> J4[Chart: Revenue Trend]
    J4 --> G
    
    J1 -->|Rekap Kendaraan| J5[Filter Tanggal]
    J5 --> J6[Tampilkan: Masuk/Keluar per Area]
    J6 --> J7[Tabel Detail Transaksi]
    J7 --> G
    
    J1 -->|Status Pembayaran| J8[Filter Tunggakan]
    J8 --> J9[Tampilkan Kendaraan Belum Bayar]
    J9 --> J10[List Plat & Durasi]
    J10 --> G
    
    H -->|Export Laporan| K[Pilih Jenis Laporan]
    K --> K1[Pilih Range Tanggal]
    K1 --> K2[Pilih Format: Excel/PDF]
    K2 --> K3[Generate & Download]
    K3 --> G
    
    H -->|Lihat Log Aktivitas| L[Filter: Tanggal, Tipe Aktivitas]
    L --> L1[Tampilkan Event Log]
    L1 --> L2[Entry, Exit, Payment, User Actions]
    L2 --> G
    
    H -->|Lihat Statistik| M[Tampilkan Statistik Bulanan]
    M --> M1[Revenue Trend]
    M1 --> M2[Vehicle Distribution]
    M2 --> M3[Peak Hours]
    M3 --> M4[Area Utilization]
    M4 --> G
    
    H -->|Logout| N[Hapus Token Session]
    N --> X([Berakhir])
```

---

## 📊 DFD Level 0 (Context Diagram)

```mermaid
graph TB
    subgraph External_Entities["EXTERNAL ENTITIES"]
        E1["👤 Petugas<br/>(Attendant)"]
        E2["👨‍💼 Admin"]
        E3["👨‍💻 Owner"]
        E4["🚗 Vehicle Owner"]
    end
    
    subgraph System["⭐ PARKING MANAGEMENT SYSTEM"]
        S["Parqeer<br/>Parking<br/>System"]
    end
    
    subgraph Data_Store["🗄️ DATA STORE"]
        DB["database.sqlite<br/>- users<br/>- vehicles<br/>- areas<br/>- rates<br/>- transactions<br/>- log_activities"]
    end
    
    %% Petugas flows
    E1 -->|1. Login (email, password)| S
    S -->|1.1 Display Dashboard| E1
    E1 -->|2. Input Entry (plat, warna, area)| S
    S -->|2.1 QR Code Receipt| E1
    E1 -->|3. Input Exit & Payment| S
    S -->|3.1 Confirmation & Receipt| E1
    E1 -->|4. View Occupancy| S
    S -->|4.1 Display Occupancy| E1
    
    %% Admin flows
    E2 -->|5. Login (email, password)| S
    S -->|5.1 Display Admin Menu| E2
    E2 -->|6. Manage Area/Rate/User| S
    S -->|6.1 Confirmation| E2
    E2 -->|7. View Reports & Logs| S
    S -->|7.1 Display Data| E2
    
    %% Owner flows
    E3 -->|8. Login (email, password)| S
    S -->|8.1 Display Dashboard| E3
    E3 -->|9. Request Reports| S
    S -->|9.1 Display Analytics| E3
    E3 -->|10. Request Export| S
    S -->|10.1 Download File| E3
    
    %% Vehicle Owner (external info)
    E4 -->|11. Check Parking Status| S
    S -->|11.1 Info Kendaraan| E4
    
    %% System to Database
    S <-->|Read/Write Data| DB
    
    style S fill:#4CAF50,color:#fff,stroke:#2E7D32,stroke-width:3px
    style DB fill:#2196F3,color:#fff,stroke:#1565C0,stroke-width:2px
    style E1 fill:#FF9800,color:#fff
    style E2 fill:#9C27B0,color:#fff
    style E3 fill:#F44336,color:#fff
    style E4 fill:#00BCD4,color:#fff
```

---

## 📋 DFD Level 0 - Data Flow Summary

### Input Data (External → System)
| No | Source | Data | Destination |
|----|---------|-----------------------|----------|
| 1 | Petugas | Credentials (email, password) | Login Module |
| 2 | Petugas | Entry Data (plat, warna, area, waktu) | Transaction Entry |
| 3 | Petugas | Exit & Payment (tx_id, amount) | Transaction Exit/Payment |
| 4 | Admin | User Management (create/update/delete) | User Module |
| 5 | Admin | Area Management (create/update/delete) | Area Module |
| 6 | Admin | Rate Management (create/update/delete) | Rate Module |
| 7 | Owner | Report Request (type, date_range) | Report Module |
| 8 | Owner | Export Request (format: excel/pdf) | Export Module |

### Output Data (System → External)
| No | Destination | Data | Source |
|----|---------|-----------------------|----------|
| 1.1 | Petugas | Dashboard (occupancy, active tx) | System |
| 2.1 | Petugas | QR Receipt (tx_id, vehicle, time, rate) | Report Generator |
| 3.1 | Petugas | Confirmation & Receipt | Transaction Module |
| 4.1 | Petugas | Occupancy Real-time | Occupancy Monitor |
| 5.1 | Admin | Admin Menu & Navigation | System |
| 6.1 | Admin | Form Confirmation | Validation Module |
| 7.1 | Admin | Reports & Logs | Report Module |
| 8.1 | Owner | Dashboard Analytics | Analytics Module |
| 9.1 | Owner | Detailed Reports (revenue, vehicles, status) | Report Module |
| 10.1 | Owner | Exported File (Excel/PDF) | Export Module |
| 11.1 | Vehicle Owner | Vehicle Status Info | Information Module |

### Database Operations
| Operation | Data | Module |
|-----------|------|--------|
| CREATE | user, vehicle, area, rate, transaction, log_activity | All modules |
| READ | user (auth), area (display), rate (calculation), transaction (list/detail) | All modules |
| UPDATE | transaction (exit, payment status), area (occupancy), user (profile) | Transaction, Area modules |
| DELETE | user, area, rate (soft delete) | Admin modules |

---

## 🔄 Data Flow Diagram Level 1 - Entry Process (Detail)

```mermaid
graph TB
    subgraph Input["INPUT"]
        I1["Petugas<br/>Input Data"]
    end
    
    subgraph Validation["VALIDATION"]
        V1["Validasi Email/<br/>Password"]
        V2["Validasi Format<br/>Plat Nomor"]
        V3["Validasi Area<br/>Tersedia"]
    end
    
    subgraph Processing["PROCESSING"]
        P1["Create Vehicle<br/>Record"]
        P2["Create Trans<br/>Record"]
        P3["Update Ocupancy<br/>Area"]
        P4["Log Activity<br/>Entry"]
    end
    
    subgraph Output["OUTPUT"]
        O1["Display<br/>Confirmation"]
        O2["Generate QR<br/>Receipt"]
        O3["Update<br/>Dashboard"]
    end
    
    subgraph Storage["STORAGE"]
        DB1["users"]
        DB2["vehicles"]
        DB3["areas"]
        DB4["transactions"]
        DB5["log_activities"]
    end
    
    I1 -->|credentials| V1
    V1 -->|valid| P1
    I1 -->|entry data<br/>plat, warna, area| V2
    V2 -->|valid| V3
    V3 -->|available| P1
    
    P1 -->|store| DB2
    P2 -->|store| DB4
    P3 -->|update| DB3
    P4 -->|store| DB5
    
    P2 -->|check rate| DB3
    P3 -->|tx_id| O2
    P2 -->|tx_id, amount| O1
    
    O1 --> O3
    O2 --> O3
    
    V1 -->|invalid| E1["❌ Error:<br/>Auth Failed"]
    V2 -->|invalid| E2["❌ Error:<br/>Invalid Plate"]
    V3 -->|full| E3["❌ Error:<br/>Area Full"]
    
    style Input fill:#FFB300,color:#000
    style Validation fill:#2196F3,color:#fff
    style Processing fill:#4CAF50,color:#fff
    style Output fill:#9C27B0,color:#fff
    style Storage fill:#607D8B,color:#fff
```

---

## 📌 Key Points

### Petugas Role
✅ Hanya bisa: Entry, Exit, Payment  
✅ Lihat: Dashboard pribadi, Occupancy  
❌ Tidak bisa: Manage users, Manage areas, Delete data  

### Admin Role
✅ Hanya bisa: Manage areas, rates, users, logs  
✅ Lihat: Semua reports, activities  
❌ Tidak bisa: Delete completed transactions, Change owner account  

### Owner Role
✅ Hanya bisa: View reports, export data  
✅ Lihat: Dashboard, Analytics, Revenue trends  
❌ Tidak bisa: Modify data, Manage users  

### Data Integrity Guarantees
🔒 All transactions are atomic (all-or-nothing)  
🔒 Occupancy always accurate (updated on entry/exit)  
🔒 Authorization checks on every action  
🔒 Complete audit trail (log_activities)  
🔒 Payment validation (amount >= cost)  

---

**Document Generated:** March 2, 2026  
**System Version:** v1.0  
**Status:** Ready for reference
