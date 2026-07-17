# Dokumentasi Aliran Sistem `FRsystem` (Fault Report System)

Dokumen ini menghuraikan **aliran kerja sistem (system flow)**, **pemetaan halaman PHP ke jadual database `frs`**, dan **cadangan dashboard statistik**, berdasarkan analisis kod sumber (`FRsystem.rar`) dan struktur database (`frs_1_.sql`) yang telah dianalisis sebelum ini.

Sistem dibina atas PHP prosedural + `mysql_*` (extension lama, sudah *deprecated*/dibuang dari PHP 7+) dan menggunakan `FPDF` untuk jana laporan PDF serta `PHPMailer` untuk notifikasi emel.

---

## 1. Peranan Pengguna (`user.Role`)

| Kod Role | Peranan | Fungsi Utama dalam Sistem |
|---|---|---|
| `NU` | Normal User (Pelapor) | Buka FR baharu, muat naik lampiran, sahkan (verify) FR selesai |
| `SPV` | Penyelia Bahagian | Lulus/tolak FR daripada NU, tugaskan (assign) kepada SE |
| `SUPT` | Pihak Berkuasa Meluluskan (peringkat lebih tinggi) | Kebenaran/`Authorize` FR tertentu sebelum tindakan |
| `SE` | Support Engineer (ISB/HQ) | Ambil tindakan (action), rujuk ke SAINS/ISB/Landsoft, jana laporan siap |
| `FP` | Focal Person (wakil bahagian/daerah) | Pantau & urus FR bahagian, boleh padam rekod FR |
| `Admin` | Pentadbir Sistem | Urus akaun pengguna (create/update/reset password) |

---

## 2. Aliran Sistem (System Flow)

```
┌──────────────────────────────────────────────────────────────────────────┐
│                         PERINGKAT 1 — PELAPORAN                          │
└──────────────────────────────────────────────────────────────────────────┘
 login.php (semak sesi/kata laluan)
        │
        ▼
 Home.php (NU/SPV/SE)  ──atau──  HomeFP.php (FP)
        │
        ▼
 FRform.php / FRform1.php  → Jana No. FR rawak (Frn) → INSERT fr
        │  (lampir bukti)
        ▼
 Attach.php → upload.php → INSERT upload   (lampiran oleh pelapor)
        │
        ▼
 GeneratePreFR.php → cetak/PDF draf FR (status "-", "Solved", "Approved", "Authorized")
        │
        └─ Emel notifikasi ke SPV (FRemailsendapproval.php) via mail.php/PHPMailer

┌──────────────────────────────────────────────────────────────────────────┐
│                    PERINGKAT 2 — KELULUSAN PENYELIA                      │
└──────────────────────────────────────────────────────────────────────────┘
 Emel → login.php?stat=app&frn=xxx  →  FRapproval.php (Role: SPV)
        │  Lulus → INSERT action + INSERT assign, UPDATE fr.approval_status
        │  Tolak → UPDATE fr (reject_reason)
        ▼
 (jika perlu) login.php?stat=auth&frn=xxx → FRsuptAuthorize.php (Role: SUPT)
        │  INSERT action + INSERT assign, UPDATE fr.SuptAuthorize
        ▼
 GenerateRepFR.php → cetak laporan status kelulusan

┌──────────────────────────────────────────────────────────────────────────┐
│                 PERINGKAT 3 — PENUGASAN & TINDAKAN (SE)                  │
└──────────────────────────────────────────────────────────────────────────┘
 Assign.php / FRassign.php → papar senarai & tugaskan FR kepada SE
        │
        ▼
 login.php?stat=recSE&frn=xxx → FRrecSE.php (SE terima FR)
        │  INSERT msgseccm (nota/perbincangan), UPDATE refer_to
        ▼
 login.php?stat=rec&frn=xxx → FRrectification.php (SE ambil tindakan)
        │  UPDATE action (action_taken, causeprob, action_status, FR_status)
        │  UPDATE msgseccm, UPDATE refer_to
        │
        ├─(jika perlu bantuan luar)─▶ FRSainslodge.php → INSERT refer_to, UPDATE action
        │                              (rujuk ke SAINS Helpdesk)
        ├─(jika perlu bantuan ISB)──▶ emailKIV_isb.php → INSERT/UPDATE refer_to, UPDATE action
        ├─(jika perlu Landsoft)─────▶ emailKIV_landsoft.php → INSERT/UPDATE refer_to, UPDATE action
        └─(perbincangan dgn SE lain)▶ msg2se.php → INSERT msgseccm, UPDATE msgseccm/refer_to
        │
        ▼
 AttachRec.php → uploadRec.php → INSERT recatt (lampiran bukti tindakan oleh SE)
        │
        ▼
 GenerateComFR.php → cetak laporan FR "Completed" (Role: SE)

┌──────────────────────────────────────────────────────────────────────────┐
│                 PERINGKAT 4 — PENGESAHAN PENGGUNA (NU)                   │
└──────────────────────────────────────────────────────────────────────────┘
 FRemailuservery.php → emel notis "sila sahkan" kepada NU
        │
        ▼
 login.php?stat=Ve&frn=xxx → FRuserverification.php (NU sahkan selesai)
        │  UPDATE action (DateUserVerified, UserRemarks, FR_status = 'Close')
        ▼
 completed.php  →  senarai FR yang telah ditutup (Close)

┌──────────────────────────────────────────────────────────────────────────┐
│              PERINGKAT 5 — PEMANTAUAN, LAPORAN & PENTADBIRAN             │
└──────────────────────────────────────────────────────────────────────────┘
 Monitorlist.php / Previous.php / FRstatus.php / FRprogressTab.php / FRsearch.php / Search.php
        → paparan status FR (Open/Close/KIV), carian, & jejak sejarah penuh

 FRfocalperson.php (Role: FP)
        → pantau FR bahagian, boleh DELETE (action/fr/recatt/refer_to/upload) — padam rekod

 FRpreview.php → paparan penuh 1 rekod FR (gabungan SEMUA 8 jadual)

 report.php / report1A.php / report1B.php → laporan cetak (FPDF)

 UserRegistration.php / Userprofilelist.php / UserProfileUpdate.php / SearchUser.php / UserUpdate.php
        → urus akaun pengguna (Role: Admin)

 Resetpassword.php / emailResetpassword.php → reset kata laluan (emel + captcha.php)
```

**Ringkasan status FR** (`action.FR_status` / `action.action_status`):
`Open` (baru/belum selesai) → `KIV` (menunggu maklum balas pihak luar) → `Done`/`Close` (selesai & disahkan pengguna).

---

## 3. Pemetaan Halaman → Jadual Database

> **R** = Baca (SELECT/JOIN) &nbsp;·&nbsp; **W** = Tulis (INSERT/UPDATE) &nbsp;·&nbsp; **D** = Padam (DELETE)

| Halaman (PHP) | Fungsi Ringkas | Jadual Dibaca (R) | Jadual Ditulis (W) | Jadual Dipadam (D) |
|---|---|---|---|---|
| `login.php` | Log masuk & router (`stat=` param) | `user` | – | – |
| `Home.php` / `HomeFP.php` | Papan pemuka utama (NU/SPV/SE / FP) | `user` | – | – |
| `FRform.php` / `FRform1.php` | Borang lapor FR baharu + jana No. FR | `fr`, `user` | `fr` | – |
| `FRformAmend.php` | Pinda FR sebelum lulus | `assign`, `fr`, `user` | `fr` | `assign` |
| `Attach.php` → `upload.php` | Muat naik lampiran (oleh pelapor) | – | `upload` | – |
| `GeneratePreFR.php` | PDF draf FR (status Approve/Authorize) | `user` | – | – |
| `FRapproval.php` | Kelulusan Penyelia (SPV) | `fr`, `user` | `fr`, `action`, `assign` | – |
| `FRsuptAuthorize.php` | Kebenaran peringkat SUPT | `fr`, `user` | `fr`, `action`, `assign` | – |
| `GenerateRepFR.php` | PDF laporan status kelulusan | `action`, `fr` | – | – |
| `Assign.php` / `FRassign.php` | Senarai & penugasan FR ke SE | `action`, `user` | – | – |
| `FRrecSE.php` | SE terima FR (mula tindakan) | `action`, `fr`, `msgseccm`, `refer_to`, `user` | `msgseccm`, `refer_to` | – |
| `FRrectification.php` | SE catat tindakan pembetulan | `action`, `fr`, `msgseccm`, `refer_to`, `user` | `action`, `msgseccm`, `refer_to` | – |
| `FRSainslodge.php` | Rujuk kes ke SAINS Helpdesk | `action` | `action`, `refer_to` | – |
| `emailKIV_isb.php` | Rujuk kes ke ISB (KIV) | `action`, `assign`, `fr`, `user` | `action`, `refer_to` | – |
| `emailKIV_landsoft.php` | Rujuk kes ke Landsoft (KIV) | `action`, `assign`, `fr`, `user` | `action`, `refer_to` | – |
| `msg2se.php` | Papan mesej antara SE | `assign`, `fr`, `msgseccm`, `refer_to`, `user` | `msgseccm`, `refer_to` | – |
| `AttachRec.php` → `uploadRec.php` | Muat naik lampiran bukti tindakan (SE) | – | `recatt` | – |
| `GenerateComFR.php` | PDF laporan FR "Completed" | `user` | – | – |
| `FRemailsendapproval.php` | Trigger emel notis kelulusan | `fr`, `upload`, `user` | – | – |
| `FRemailuservery.php` | Trigger emel minta pengesahan pengguna | `action`, `fr`, `recatt`, `refer_to`, `upload`, `user` | – | – |
| `FRuserverification.php` | Pengguna sahkan FR selesai | `action`, `assign`, `fr`, `refer_to`, `user` | `action` | – |
| `completed.php` | Senarai FR telah ditutup | `action` | – | – |
| `Monitorlist.php` / `Previous.php` | Senarai FR aktif / sejarah lampau | `action` | – | – |
| `FRstatus.php` | Senarai status penuh + padam rekod (Admin/FP) | Semua 8 jadual | – | `action`,`assign`,`fr`,`recatt`,`refer_to`,`upload` |
| `FRprogressTab.php` | Papar & padam progress FR | `action`,`assign`,`fr`,`recatt`,`refer_to`,`upload` | – | sama seperti di atas |
| `FRfocalperson.php` | Pemantauan & padam oleh Focal Person | Semua 8 jadual | `assign`,`action`,`fr` | `action`,`fr`,`recatt`,`refer_to`,`upload` |
| `FRpreview.php` | Paparan penuh 1 rekod FR (gabungan) | Semua 8 jadual | – | – |
| `FRstatusTab.php` / `FRstatusTabSE.php` | Tab status ikut peranan (SPV/SE) | `action`, `fr`, `user`, `refer_to` | – | – |
| `FRreportdetail.php` / `FRreportdetail2.php` | Butiran laporan tindakan | `action`, `user` | – | – |
| `FRreport.php` / `report.php` / `report1A.php` / `report1B.php` | Laporan cetak (FPDF) | `user`, `action`, `fr` | – | – |
| `Search.php` / `FRsearch.php` | Carian FR | `action`, `assign`, `fr`, `user` | – | – |
| `attachment.php` | Urus lampiran `upload` (papar/padam) | `upload` | – | `upload` |
| `attachmentRec.php` | Urus lampiran `recatt` (papar/padam) | `recatt` | – | `recatt` |
| `UserRegistration.php` | Daftar akaun pengguna baharu | `user` | `user` | – |
| `Userprofile.php` / `Userprofilelist.php` | Papar profil / senarai pengguna | `user` | – | – |
| `UserProfileUpdate.php` / `UserUpdate.php` | Kemaskini profil pengguna | `user` | `user` | – |
| `SearchUser.php` | Carian pengguna (Admin) | `user` | – | – |
| `Resetpassword.php` / `emailResetpassword.php` | Reset kata laluan | `user` | `user` | – |

### 3.1 Nota Penting Hasil Analisis Kod

- **`upload` vs `recatt` BUKAN jadual berlebihan (duplicate)** seperti diandaikan sebelum ini — ia sebenarnya **dua tujuan berbeza**:
  - `upload` → lampiran dimuat naik oleh **pelapor (NU)** semasa buat FR (`Attach.php`).
  - `recatt` → lampiran bukti tindakan dimuat naik oleh **Support Engineer (SE)** semasa rectification (`AttachRec.php`).
- **`login.php` bertindak sebagai router pusat** — pautan dalam emel notifikasi (`login.php?stat=app&frn=..`, `stat=fp`, `stat=Amd`, `stat=recSE`, `stat=rec`, `stat=auth`, `stat=Ve`) mengarahkan pengguna terus ke halaman tindakan yang berkaitan selepas log masuk. Ini bermakna **aliran sistem sebenar digerakkan oleh emel (email-driven workflow)**, bukan semata-mata navigasi menu.
- Sistem masih menggunakan fungsi **`mysql_*` (bukan `mysqli_*`/PDO)** — sambungan ini telah **dibuang sepenuhnya sejak PHP 7.0** (2015). Kod ini **tidak akan berfungsi** pada versi PHP moden tanpa migrasi penuh ke `mysqli`/PDO — satu isu kritikal untuk *maintainability* dan keselamatan (tiada prepared statement bermakna risiko SQL Injection tinggi berdasarkan corak `$sql = "... '\".$var.\"' ..."` yang digunakan meluas).
- `FRstatus.php`, `FRprogressTab.php`, dan `FRfocalperson.php` mempunyai ksubmitan **DELETE merentasi 5-6 jadual serentak** tanpa transaksi (`BEGIN...COMMIT`) — kerana engine `MyISAM` tidak menyokong transaksi, kegagalan separuh jalan boleh tinggalkan **rekod anak yatim** (cth. `action` terpadam tapi `upload` masih ada).

---

## 4. Aliran Data Terbalik (Jadual → Sumber Halaman)

Untuk rujukan pantas — bagi setiap jadual, halaman mana yang **menulis** ke dalamnya dan halaman mana yang **membaca** daripadanya:

| Jadual | Ditulis oleh (INSERT/UPDATE) | Dibaca oleh (SELECT/paparan) |
|---|---|---|
| `fr` | `FRform.php`, `FRformAmend.php`, `FRapproval.php`, `FRsuptAuthorize.php`, `FRfocalperson.php` | Hampir semua halaman senarai/laporan/carian |
| `action` | `FRapproval.php`, `FRsuptAuthorize.php`, `FRrectification.php`, `FRSainslodge.php`, `emailKIV_isb.php`, `emailKIV_landsoft.php`, `FRuserverification.php`, `FRfocalperson.php` | `Assign.php`, `Monitorlist.php`, `Previous.php`, `completed.php`, `FRreportdetail(2).php`, `FRstatusTab.php`, laporan PDF |
| `assign` | `FRapproval.php`, `FRsuptAuthorize.php`, `FRfocalperson.php` (INSERT) / `FRformAmend.php` (DELETE) | `Search.php`, `FRpreview.php`, `FRstatus.php` |
| `refer_to` | `FRSainslodge.php`, `emailKIV_isb.php`, `emailKIV_landsoft.php`, `FRrecSE.php`, `FRrectification.php`, `msg2se.php` | `FRstatusTabSE.php`, `FRuserverification.php`, `FRpreview.php` |
| `msgseccm` | `FRrecSE.php`, `FRrectification.php`, `msg2se.php` | `FRpreview.php`, `FRrectification.php` (papar sejarah mesej) |
| `upload` | `upload.php` | `attachment.php`, `FRemailsendapproval.php`, `FRemailuservery.php`, `FRpreview.php`, `FRstatus.php` |
| `recatt` | `uploadRec.php` | `attachmentRec.php`, `FRemailuservery.php`, `FRpreview.php` |
| `user` | `UserRegistration.php`, `UserProfileUpdate.php`, `UserUpdate.php`, `Resetpassword.php` | **Semua** halaman (autentikasi sesi + papar nama/peranan) |

---

## 5. Cadangan Dashboard Statistik

Berdasarkan medan yang tersedia dalam database, berikut cadangan **widget dashboard** yang praktikal dan boleh dilaksanakan terus daripada data sedia ada:

### 5.1 Kad Ringkasan (KPI Cards) — atas dashboard
| Metrik | Sumber Data |
|---|---|
| Jumlah FR keseluruhan (tahun semasa) | `COUNT(fr.Id)` |
| FR masih **Open** | `COUNT(action.FR_status='Open')` |
| FR **KIV** (menunggu pihak luar) | `COUNT(action.FR_status='KIV')` |
| FR **Close** (selesai) | `COUNT(action.FR_status='Close')` |
| Peratus FR disahkan pengguna (verified) | `COUNT(action.DateUserVerified IS NOT NULL) / COUNT(action.FR_status='Close')` |
| Purata masa penyelesaian (hari) | `AVG(DATEDIFF(action.DateUserVerified, action.DateReceived))` |

### 5.2 Carta Taburan (Pie/Donut Chart)
- **FR ikut kategori** (`fr.frcate`: Hardware / Application-Software / dll.)
- **FR ikut punca masalah** (`action.causeprob`: User Error, Data Error, Faulty Hardware, Program Limitation, Setup Error, Unknown)
- **FR ikut Bahagian/Seksyen** (`fr.Oridiv`, `fr.Section`)
- **Status kelulusan** (`fr.approval_status`, `fr.SuptAuthorize`)

### 5.3 Carta Trend (Line/Bar Chart — mengikut masa)
- **Bilangan FR dilapor per bulan** (`fr.date_add`) — trend tahunan, kesan kepada perancangan sumber
- **Bilangan FR diselesaikan per bulan** (`action.DateUserVerified`)
- **Perbandingan FR dibuka vs. ditutup per bulan** (untuk kesan *backlog* semakin membesar/mengecil)

### 5.4 Papan Prestasi Staf (Leaderboard / Bar Chart)
- **Beban kerja setiap SE** — `COUNT(action.ActionTakenBy)` group by nama
- **Purata masa tindakan setiap SE** — `AVG(DATEDIFF(ActionEnd, ActionStart))` group by `ActionTakenBy`
- **Bilangan kes dirujuk keluar (SAINS/ISB/Landsoft)** — `COUNT(refer_to.Refcate)` group by kategori rujukan

### 5.5 Jadual/Senarai Amaran (Alert Table)
- **FR melebihi SLA** — bandingkan `action.DateReceived` dengan `fr.HardSLA` (perlu tetapkan ambang hari mengikut kategori SLA) → highlight merah jika melepasi
- **FR "KIV" > 7 hari tanpa kemaskini** — daripada `refer_to.SEaction_status='KIV'` + `refer_to.DateRef`
- **FR menunggu pengesahan pengguna > 14 hari** (`action.DateSendToUser` lama tetapi `DateUserVerified` masih kosong)

### 5.6 Analisis Peralatan (untuk isu Hardware)
- **Top 10 jenis peralatan paling kerap rosak** — `COUNT(fr.equip)` group by `equip`/`brand`
- Berguna untuk keputusan pembelian/naik taraf peralatan ICT jabatan

### 5.7 Peta/Carta ikut Bahagian (Geografi)
- **Bilangan FR ikut Bahagian** (`user.Division`: Kuching, Samarahan, Sibu, Betong, Sarikei, Headquarters, dll.) — jika ada > 10 bahagian, guna carta bar berbanding peta sebenar (data tiada koordinat GPS)

> **Cadangan teknikal:** Dashboard boleh dibina menggunakan **Chart.js** atau **CanvasJS** (sistem sedia ada sudah memuatkan `canvasjs.min.js` dalam folder `script/`) — tinggal bina *endpoint* PHP yang jalankan query `GROUP BY` di atas dan hantar sebagai JSON ke carta *front-end*.

---

## 6. Isu Teknikal Kritikal yang Perlu Diberi Perhatian Segera

1. **`mysql_*` API sudah *removed* dari PHP** — sistem ini **tidak boleh dijalankan** pada PHP moden (7.0+) tanpa migrasi ke `mysqli`/PDO. Ini keutamaan #1 sebelum sebarang penambahbaikan lain.
2. **SQL Injection** — corak `"... WHERE username = '\".$_SESSION['userID'].\"'"` tanpa *prepared statement* ditemui meluas di seluruh sistem.
3. **DELETE merentasi berbilang jadual tanpa transaksi** (kerana engine MyISAM) — risiko data tidak konsisten jika ralat berlaku separuh jalan.
4. Isu kata laluan MD5/plaintext yang telah dilaporkan dalam `database.md` turut memberi kesan langsung kepada `login.php` dan seluruh sistem kebenaran (authorization) peranan.
