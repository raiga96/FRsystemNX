# Dokumentasi Database `frs` (Fault Report System)

Database ini digunakan oleh sistem pelaporan & penjejakan aduan/kerosakan (Fault Report System) Jabatan Tanah & Survei Sarawak, untuk sistem seperti TRS/RVS/LAAS/Proacts.

Ia menguruskan kitaran hidup satu laporan kerosakan:

```
Lapor (fr) -> Ditugaskan (assign) -> Tindakan (action) -> Rujuk pihak lain (refer_to)
                                                        -> Lampiran (recatt / upload)
                                                        -> Perbincangan (msgseccm)
```

- **Engine**: MyISAM
- **Charset**: latin1 / latin1_swedish_ci
- **Tiada Foreign Key constraints** dikuatkuasakan oleh DB — semua hubungan antara jadual adalah **logik aplikasi sahaja** (disahkan: tiada satu `ADD CONSTRAINT` / `FOREIGN KEY` dalam keseluruhan dump).

---

## 1. Senarai Jadual & Peranan

| Jadual | Primary Key | Unique Key | Fungsi |
|---|---|---|---|
| `fr` | `Id` (auto-increment) | `Frn` | Jadual **induk** — rekod asal laporan kerosakan |
| `assign` | `AssignId` | - | Penugasan FR kepada staf tertentu |
| `action` | `ActionId` | `frno` | Tindakan yang diambil ke atas satu FR |
| `refer_to` | `ReferId` | - | Rujukan FR ke pihak luar (SAINS/ISB) |
| `recatt` | `attid` | - | Lampiran fail (versi 1) |
| `upload` | `id` | - | Lampiran fail (versi 2 — struktur bertindih dgn `recatt`) |
| `msgseccm` | `MsgId` | - | Papan mesej/perbincangan berkaitan FR |
| `user` | `username` | - | Pengguna sistem |

---

## 2. Struktur & Medan Setiap Jadual

### 2.1 `fr` — Laporan Kerosakan (induk)
| Medan | Jenis | Keterangan |
|---|---|---|
| `Id` | int(14) PK | ID unik rekod |
| `Frn` | varchar(10) UK | **Nombor rujukan FR** — kunci utama yang dirujuk oleh semua jadual lain |
| `frcate` | varchar(25) | Kategori (cth. `Hardware`, `Application/Software`) |
| `frntype` | varchar(11) | Jenis sistem (cth. `TRS`) |
| `equip`, `brand`, `srn` | varchar | Maklumat peralatan (jenis, jenama, no. siri) |
| `HardSLA` | varchar(20) | Kategori SLA perkakasan |
| `occurDate`, `timeoccur` | date/time | Tarikh & masa kejadian |
| `Description` | text | Penerangan aduan |
| `request_by` | varchar(50) | **Nama** pelapor (teks bebas, bukan `username`) |
| `approved_by` | varchar(50) | **Nama** pelulus |
| `approval_status` | varchar(3) | `Yes` / `No` |
| `AppRej_date` | date | Tarikh diluluskan/ditolak |
| `SuptName`, `SuptAuthorize`, `Autho_date` | varchar/date | Kebenaran penyelia |
| `reject_reason` | text | Sebab ditolak |
| `date_add` | datetime | Tarikh rekod dicipta |
| `Oridiv`, `Section` | varchar(15) | Bahagian & seksyen asal pelapor |

### 2.2 `action` — Tindakan ke atas FR
| Medan | Jenis | Keterangan |
|---|---|---|
| `ActionId` | int(14) PK | ID unik |
| `frno` | varchar(10) UK | Rujuk `fr.Frn` |
| `DateReceived`, `TimeReceived` | date/time | Bila FR diterima oleh pegawai tindakan |
| `SPVremark` | text | Catatan penyelia |
| `ActionStart`, `ActionEnd` | date | Tempoh tindakan |
| `action_taken` | text | Penerangan tindakan yang diambil |
| `ActionTakenBy` | varchar(50) | **Nama** pegawai yang bertindak |
| `causeprob` | varchar(30) | Punca masalah (cth. `User Error`, `Data Error`, `Faulty Hardware`, `Program Limitation`, `Unknown`) |
| `action_status` | varchar(4) | `Done` |
| `FR_status` | varchar(5) | `Close` / `Open` |
| `Note2User`, `DateSendToUser`, `TimeSendToUser` | text/date/time | Maklum balas kepada pengguna |
| `DateUserVerified` | datetime | Tarikh pengguna sahkan selesai |
| `UserRemarks` | text | Catatan balasan pengguna |

### 2.3 `assign` — Penugasan
| Medan | Jenis | Keterangan |
|---|---|---|
| `AssignId` | int(14) PK | ID unik |
| `Assfrno` | varchar(10) | Rujuk `fr.Frn` |
| `assign_to` | varchar(50) | **Nama** staf yang ditugaskan |
| `assign_date` | datetime | Tarikh ditugaskan |
| `act_status` | varchar(6) | Status (cth. `assign`) |
| `remarks` | text | Catatan |

### 2.4 `refer_to` — Rujukan Luar
| Medan | Jenis | Keterangan |
|---|---|---|
| `ReferId` | int(14) PK | ID unik |
| `FrRefId` | varchar(10) | Rujuk `fr.Frn` |
| `DateRef`, `TimeRef` | date/time | Tarikh & masa rujukan dibuat |
| `Refcate` | varchar(20) | Kategori rujukan (cth. `SAINS`, `ISB`) |
| `RefWho` | varchar(50) | **Nama** penerima rujukan (cth. `Helpdesk`) |
| `SEaction_status` | varchar(8) | Status (`KIV` / `Done`, default) |
| `SainsDocNo` | varchar(50) | No. docket SAINS helpdesk |

### 2.5 `recatt` — Lampiran (versi 1)
| Medan | Jenis | Keterangan |
|---|---|---|
| `attid` | int PK | ID unik |
| `attname` | varchar(100) | Nama fail |
| `attFrid` | varchar(10) | Rujuk `fr.Frn` |
| `atttype` | varchar(30) | MIME type (cth. `application/pdf`) |
| `attsize` | int | Saiz fail (byte) |
| `fileatt_by` | varchar(50) | **Nama** pemuat naik |

### 2.6 `upload` — Lampiran (versi 2)
| Medan | Jenis | Keterangan |
|---|---|---|
| `id` | int PK | ID unik |
| `name` | varchar(100) | Nama fail |
| `Frid` | varchar(10) | Rujuk `fr.Frn` |
| `type` | varchar(30) | MIME type |
| `size` | int | Saiz fail (byte) |
| `att_by` | varchar(50) | Pemuat naik (nampaknya guna `username`, bukan nama penuh) |

> **Nota:** `recatt` dan `upload` mempunyai struktur yang hampir 100% bertindih. Kemungkinan besar `upload` ialah jadual baharu/gantian yang tidak menggantikan sepenuhnya `recatt` semasa migrasi sistem — perlu disemak sama ada kedua-duanya masih aktif digunakan oleh aplikasi.

### 2.7 `msgseccm` — Mesej/Perbincangan
| Medan | Jenis | Keterangan |
|---|---|---|
| `MsgId` | int(14) PK | ID unik |
| `FrMsgId` | varchar(10) | Rujuk `fr.Frn` |
| `FrmWho` | varchar(50) | **Nama** penghantar mesej |
| `Msg_Contain` | text | Isi mesej |
| `reply_stat` | varchar(3) | `Yes` / `No` |
| `Msg_datetime` | datetime | Tarikh & masa mesej |

### 2.8 `user` — Pengguna Sistem
| Medan | Jenis | Keterangan |
|---|---|---|
| `username` | varchar(30) PK | ID log masuk |
| `password` | varchar(32) | Hash **MD5** (32 aksara hex) |
| `name` | varchar(50) | Nama penuh — **inilah nilai yang disimpan** dalam `request_by`, `ActionTakenBy`, `assign_to`, dsb. di jadual lain |
| `gander` | varchar(1) | Jantina (`M`/`F`/`-`) — *ejaan salah untuk `gender`* |
| `Division` | varchar(15) | Bahagian (cth. `Kuching`, `Headquarters`, `Samarahan`) |
| `brasec` | varchar(15) | Seksyen (cth. `Computer Centre`, `ISB`, `Land`, `Registry`, `Valuation`, `Drawing`, `Survey`) |
| `Role` | varchar(30) | Peranan (`SPV`=Penyelia, `SE`=Support Engineer, `NU`=Normal User, `FP`=Focal Person) |
| `email` | varchar(50) | Emel |
| `tel` | varchar(12) | No. telefon |
| `active` | varchar(1) | `Y`/`N` — status akaun aktif |

---

## 3. Peta Hubungan Antara Jadual (Entity Relationship)

Semua hubungan adalah `1 (fr) -> banyak (jadual anak)`, dipautkan melalui **nombor FR sebagai teks** — nama lajur **berbeza-beza mengikut jadual** (bukan konsisten), dan **tiada FK sebenar**:

| Jadual induk | Lajur induk | Jadual anak | Lajur anak (pautan) |
|---|---|---|---|
| `fr` | `Frn` | `action` | `frno` |
| `fr` | `Frn` | `assign` | `Assfrno` |
| `fr` | `Frn` | `refer_to` | `FrRefId` |
| `fr` | `Frn` | `recatt` | `attFrid` |
| `fr` | `Frn` | `upload` | `Frid` |
| `fr` | `Frn` | `msgseccm` | `FrMsgId` |

Pautan ke `user` **bukan** melalui `username`, sebaliknya melalui **nama penuh teks bebas** (denormalized): `fr.request_by`, `fr.approved_by`, `action.ActionTakenBy`, `assign.assign_to`, `refer_to.RefWho`, `recatt.fileatt_by`, `msgseccm.FrmWho` — semua ini menyimpan nilai seperti `'Henry Sandah'`, bukan `'henrys'`. Ini bermakna:
- Sukar `JOIN` terus ke `user` tanpa risiko *typo mismatch*.
- Jika nama pengguna ditukar di jadual `user`, rekod sejarah lama **tidak akan** ikut terkemas kini (dan tidak akan match lagi).

---

## 4. Kod Status / Enum Penting

| Lajur | Nilai dijumpai |
|---|---|
| `action.action_status` | `Done` |
| `action.FR_status` | `Close`, `Open` |
| `refer_to.SEaction_status` | `KIV`, `Done` |
| `fr.approval_status` / `fr.SuptAuthorize` | `Yes`, `No` |
| `action.causeprob` | `User Error`, `Data Error`, `Faulty Hardware`, `Program Limitation`, `Unknown`, `Procedure`, `Program bugs`, `Setup Error` |
| `user.Role` | `SPV` (Penyelia), `SE` (Support Engineer), `NU` (Normal User), `FP` (Focal Person) |
| `user.active` | `Y`, `N` |

---

## 5. Isu Keselamatan Kritikal (Security Review)

1. **Kata laluan disimpan sebagai MD5** (`password` varchar(32)) — algoritma ini sudah lapuk dan mudah dipecahkan (rainbow table), bukan `bcrypt`/`Argon2`/`password_hash()`.
2. **Kata laluan lemah disahkan wujud dalam data sebenar**:
   - `5f4dcc3b5aa765d61d8327deb882cf99` = MD5(`"password"`)
   - `e10adc3949ba59abbe56e057f20f883e` = MD5(`"123456"`)
3. **153 akaun berkongsi hash MD5 yang sama** (`7c6a180b...`) — kemungkinan besar kata laluan lalai (default) yang tidak pernah ditukar. Jika satu bocor, kesemua 153 akaun terdedah.
4. Sekurang-kurangnya 2 akaun (`helmisz`, `jumahabb`) menyimpan **kata laluan plaintext** terus dalam lajur `password` (bukan hash MD5) — kemungkinan pepijat semasa seeding/import data.
5. **Charset `latin1`** — berisiko rosakkan aksara Bahasa Melayu/simbol khas; disyorkan migrasi ke `utf8mb4`.
6. **Tiada FK constraint** — integriti rujukan (`frno`, nama pengguna) bergantung 100% kepada kod aplikasi PHP; mudah berlaku rekod anak yatim (orphan records) jika ada pepijat.
7. **Rujukan pengguna melalui nama teks bebas** (bukan `username`) — selain risiko integriti data, ia juga meningkatkan risiko *data leakage* jika nama sama digunakan oleh lebih dari satu orang.

---

## 6. Cadangan Penambahbaikan (Ringkas)

- Tukar engine MyISAM → **InnoDB** dan tambah FK constraint sebenar (`fr.Frn` sebagai rujukan `ON DELETE CASCADE` / `RESTRICT`).
- Standard nama lajur pautan FR (satu nama konsisten, cth. `frn`, bukan `frno`/`Assfrno`/`FrRefId`/`attFrid`/`Frid`/`FrMsgId`).
- Tukar semua lajur "nama pegawai" (`ActionTakenBy`, `assign_to`, dll.) kepada **rujukan `username`** (FK ke `user.username`) dan bukan teks nama bebas.
- Naik taraf hash kata laluan kepada `password_hash()` (bcrypt/Argon2id) + paksa reset kata laluan lalai/lemah yang dikenal pasti di atas.
- Migrasi charset ke `utf8mb4`.
- Semak sama ada `recatt` dan `upload` boleh digabungkan menjadi satu jadual lampiran.
