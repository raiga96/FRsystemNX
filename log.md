# Log Kemaskini Sistem Fault Report (FRsystemNX)

Tarikh: **25 Julai 2026**  
Sistem: **FRsystemNX (Fault Report System)**

---

## 1. Modul My Dashboard (`myDashboard.php`)
- **Optimasasi Prestasi SQL Query**:
  - Mengubah kueri penugasan dan laporan aduan peribadi untuk membaca terus nombor FR berindeks dari jadual `assign` dan `action` (menghapuskan carian *wildcard* `LIKE %...%` dan *cross JOIN* yang perlahan).
- **Pembetulan Identiti Pengguna Log Masuk**:
  - Memperbetulkan ralat paparan `Welcome, User` dengan menambah kueri terus `SELECT name FROM user WHERE username = ...` supaya nama penuh pengguna log masuk dipaparkan dengan tepat.
- **Penterjemahan UI/UX (Bahasa Melayu → Bahasa Inggeris)**:
  - Penterjemahan penuh teks banner aluan, status kad KPI, borang penapis `YEAR:`, `ALL YEARS`, butang `LODGE NEW FR` dan `REFRESH`.

---

## 2. Modul User Management (`accManagement.php`)
- **Pembetulan Ralat Sambungan Backend (`includes/user_management_action.php`)**:
  - Membaiki ralat sintaks PHP HTTP 500 (*Failed to connect to server*) pada pensuisan **Active Status** dan **LDAP Mode**.
- **Naik Naik Notifikasi UI**:
  - Mengubah notifikasi toast kepada **SweetAlert Modal (`Swal.fire`)** berdialog mesej Bahasa Inggeris sepenuhnya (`SUCCESS!` / `ERROR`).

---

## 3. Penyesuaian Font Global (Design System)
- **Penukaran Font kepada Poppins**:
  - Mengimport Google Fonts `Poppins` (weights: 300, 400, 500, 600, 700, 800) pada CSS utama (`assets/css/material-dashboard.css`).
  - Mengemaskini pembolehubah CSS `--bs-font-sans-serif` dan `--bs-body-font-family` untuk menggunakan font `Poppins` di seluruh halaman.
  - Memperbetulkan pautan templat utama (`index.php`, `myDashboard.php`, `frList.php`, `frDetail.php`, `accManagement.php`, `login.php`, `addUser.php`, `accProfile.php`).

---

## 4. Modul Halaman Utama Dashboard (`index.php`, `hq_dashboard.php`, `div_dashboard.php`)
- **Penterjemahan UI/UX ke Bahasa Inggeris**:
  - Menterjemah keseluruhan kandungan teks modul Command Center HQ Dashboard dan Division Dashboard ke Bahasa Inggeris (termasuk kad KPI, status aduan *Unassigned*, *In Progress*, *Solved*, kategori *Hardware/Software*, dan senarai *Recent FRs*).

---

## 5. Modul Baharu: Action Taken (`actionManagement.php`)
- **Pembangunan Modul**:
  - Membina modul baharu bagi mengurus dan mencatat tindakan aduan (`action` table).
  - Menyediakan borang *Modal* pop-up untuk merekod tindakan aduan, mengemaskini punca masalah (`causeprob`), status tindakan (`action_status`), status FR (`FR_status`), dan catatan maklum balas pengguna (`Note2User`).
- **Penyepaduan Menu Sidebar**:
  - Menambah pautan menu **Action Taken** (`actionManagement.php`) pada `sidebar.php`.

---

## 6. Modul Baharu: New Assign (`assignManagement.php`)
- **Pembangunan Modul**:
  - Membina modul penugasan aduan baharu untuk pegawai tindakan/staf sokongan (`assign` table).
  - Menapis senarai aduan berasaskan tugasan akaun pengguna log masuk secara spesifik.
- **Mekanisme Lencana Notifikasi (Unread Badge Notification)**:
  - Menambah lencana notifikasi merah `X NEW` pada menu tepi **New Assign** (`sidebar.php`) untuk aduan baharu yang belum dibaca.
  - Rekod aduan baharu ditanda dengan lencana merah **NEW** pada jadual.
  - Menambah pengesan status dibaca (*Read status tracker*) pada `frDetail.php` di mana lencana notifikasi akan **hilang secara automatik** sebaik sahaja aduan ditekan/dibuka oleh pengguna.
- **Pembetulan Ralat PHP 8.1+ Deprecation Notice**:
  - Memperbetulkan amaran `htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated` dengan penegasan pemutus jenis data `(string)`.
- **Penambahbaikan Kueri Lencana**:
  - Menghapuskan padanan carian *wildcard* `LIKE` berasaskan teks lalai `'Pengguna'`/`'User'` untuk memastikan lencana dikira mengikut akaun pengguna secara tepat.

---

## 9. Penyesuaian Halaman My Dashboard untuk Peranan Focal Person (FP) (`myDashboard.php`)
- **Paparan Pengesan KPI Khusus FP**:
  - Bagi pengguna berpenjelmaan peranan **Focal Person (`FP`)**, metrik disesuaikan secara khusus kepada **NEW FR RECEIVED** (bilangan aduan baharu yang diterima di bahagian FP tersebut) dan **PENDING ASSIGN**.
- **Lencana Notifikasi Sidebar (`My Dashboard` Menu)**:
  - Membina penanda lencana merah **`X NEW`** pada pautan **My Dashboard** di `sidebar.php` khusus untuk pengguna peranan **Focal Person (`FP`)**.
  - Lencana ini mengira secara automatik bilangan aduan baharu yang belum di-assign dalam bahagian (*Division*) pengguna FP berkenaan.
- **Penyembunyian Bahagian `REPORTS LODGED BY ME`**:
  - Menyorokkan jadual aduan peribadi (*Reports Lodged by Me*) khas untuk akaun berperanan FP kerana tugas utama Focal Person adalah mengurus dan menugaskan aduan bahagian tersebut kepada staf *Computer Centre* / *ISB*.
