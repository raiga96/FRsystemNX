# Spesifikasi Pembetulan & Penambahan Dashboard `FRsystem`

Dokumen ini menyediakan **query SQL cadangan** untuk membetulkan ketidakkonsistenan data pada dashboard sedia ada, serta **spesifikasi penuh widget tambahan** yang dicadangkan dalam semakan sebelum ini.

> ⚠️ **Amaran penting sebelum guna dokumen ini**
> Query di bawah dibina berdasarkan skema jadual `frs` (`fr`, `action`, `assign`, `refer_to`, `recatt`, `upload`, `msgseccm`, `user`) yang telah kita analisis daripada `frs_1_.sql` (data 2011–2012). Dashboard yang anda tunjukkan memaparkan **data 2026** dan medan seperti `Critical`, `Urgent`, `New`, `Ontime`/`Delayed` **tidak wujud** dalam skema asal yang saya analisis. Ini bermakna sistem produksi sebenar berkemungkinan telah **menambah lajur/jadual baharu** (cth. `priority`, `sla_target`) yang saya tiada akses kepadanya. **Sila sahkan struktur jadual terkini** (`SHOW CREATE TABLE fr; SHOW CREATE TABLE action;`) sebelum guna query di bawah secara terus — saya tandakan dengan jelas bahagian mana yang perlu medan baharu.

---

## 1. Pembetulan Kad "Fault Reports" (New / In Progress / Solved)

**Isu asal**: kad atas tunjuk `Solved = 0`, kad bawah tunjuk `Solved = 527` — dua sumber data berbeza untuk konsep yang sama.

**Cadangan**: guna SATU sumber kebenaran (single source of truth) — jadual `action.FR_status`, bukan campuran logik lain.

```sql
-- Jumlah keseluruhan FR tahun semasa
SELECT COUNT(*) AS total_fr
FROM fr
WHERE YEAR(date_add) = 2026;

-- New: FR yang belum ada rekod 'action' langsung (belum ditugaskan/diambil tindakan)
SELECT COUNT(*) AS new_fr
FROM fr
WHERE YEAR(fr.date_add) = 2026
  AND NOT EXISTS (
      SELECT 1 FROM action WHERE action.frno = fr.Frn
  );

-- In Progress: ada rekod action, tapi FR_status masih 'Open' atau 'KIV'
SELECT COUNT(*) AS in_progress_fr
FROM fr
INNER JOIN action ON action.frno = fr.Frn
WHERE YEAR(fr.date_add) = 2026
  AND action.FR_status IN ('Open', 'KIV');

-- Solved: FR_status = 'Close' DAN pengguna sudah sahkan (DateUserVerified diisi)
SELECT COUNT(*) AS solved_fr
FROM fr
INNER JOIN action ON action.frno = fr.Frn
WHERE YEAR(fr.date_add) = 2026
  AND action.FR_status = 'Close'
  AND action.DateUserVerified IS NOT NULL
  AND action.DateUserVerified != '0000-00-00 00:00:00';
```

**Semak**: `New + In Progress + Solved` MESTI menyamai `total_fr` (atau hampir — baki adalah kes `Rejected`). Guna formula ini sebagai *unit test* automatik pada setiap deploy dashboard.

---

## 2. Pembetulan Kad "In Progress" (Percentage / Pending / Unassigned / Others)

```sql
-- Peratusan In Progress berbanding jumlah keseluruhan
SET @total = (SELECT COUNT(*) FROM fr WHERE YEAR(date_add) = 2026);
SET @in_progress = (SELECT COUNT(*) FROM fr f
                     INNER JOIN action a ON a.frno = f.Frn
                     WHERE YEAR(f.date_add) = 2026 AND a.FR_status IN ('Open','KIV'));
SELECT ROUND((@in_progress / @total) * 100, 2) AS pct_in_progress;

-- Pending: FR sudah ditugaskan (ada rekod assign) tapi tindakan belum mula (action.ActionStart kosong)
SELECT COUNT(*) AS pending_fr
FROM fr f
INNER JOIN assign asg ON asg.Assfrno = f.Frn
LEFT JOIN action a ON a.frno = f.Frn
WHERE YEAR(f.date_add) = 2026
  AND (a.ActionStart IS NULL OR a.ActionStart = '0000-00-00');

-- Unassigned: FR lulus tapi tiada rekod dalam jadual assign langsung
SELECT COUNT(*) AS unassigned_fr
FROM fr f
WHERE YEAR(f.date_add) = 2026
  AND f.approval_status = 'Yes'
  AND NOT EXISTS (SELECT 1 FROM assign WHERE assign.Assfrno = f.Frn);
```

> `Others` (0%) — perlu takrifan perniagaan yang jelas daripada pemilik sistem (cth. kes yang ditolak/`Rejected` tapi masih dikira "in progress"?). Tanpa takrifan, cadangkan buang kategori ini dahulu daripada UI berbanding papar "0%" yang mengelirukan.

---

## 3. Pembetulan Kad "Users" (Active / Inactive)

**Isu asal**: Active=0, Inactive=0, tapi Total Staff=1,390 — jumlah tak match.

Ingat semasa analisis `frs_1_.sql`, lajur `user.active` ada **3 kemungkinan nilai**: `'Y'`, `'N'`, dan **string kosong `''`** (bukan NULL). Jika query asal hanya `WHERE active = 'Y'` vs `WHERE active = 'N'`, semua rekod `active=''` akan **hilang** daripada kedua-dua kiraan — punca 0+0 tak match 1,390.

```sql
SELECT
  SUM(CASE WHEN active = 'Y' THEN 1 ELSE 0 END) AS active_users,
  SUM(CASE WHEN active = 'N' THEN 1 ELSE 0 END) AS inactive_users,
  SUM(CASE WHEN active NOT IN ('Y','N') OR active IS NULL THEN 1 ELSE 0 END) AS status_unknown,
  COUNT(*) AS total_staff
FROM user;
```

Tambah kad ke-3 kecil "Status tidak ditetapkan" (`status_unknown`) supaya jumlah sentiasa padan — atau lebih baik, jalankan **data cleanup**: kemaskini semua `active=''` kepada `'Y'` atau `'N'` mengikut dasar organisasi, supaya lajur ini sentiasa `NOT NULL DEFAULT 'Y'`.

---

## 4. Kad "Critical" / "Urgent" — Memerlukan Medan Baharu

Skema asal (`fr`, `action`) **tiada lajur tahap keutamaan/severity**. Medan sedia ada yang paling hampir ialah `fr.HardSLA` (varchar bebas teks, cth. `'Others'` — bukan enum berstruktur).

**Cadangan** — tambah lajur baharu supaya data ini benar dan boleh dipercayai, bukan sentiasa "0":

```sql
ALTER TABLE fr
  ADD COLUMN priority ENUM('Low','Normal','Urgent','Critical') NOT NULL DEFAULT 'Normal' AFTER frcate;
```

Kemudian borang `Data Entry` (`FRform.php`) **mesti** ada medan pilihan keutamaan semasa pelapor buat FR baharu (atau ditetapkan oleh SPV semasa kelulusan di `FRapproval.php`), dan query dashboard:

```sql
SELECT
  SUM(CASE WHEN priority = 'Critical' THEN 1 ELSE 0 END) AS critical_count,
  SUM(CASE WHEN priority = 'Urgent'   THEN 1 ELSE 0 END) AS urgent_count
FROM fr
WHERE YEAR(date_add) = 2026
  AND Frn NOT IN (SELECT frno FROM action WHERE FR_status = 'Close');
```

**Jangan biarkan kad ini kekal papar "0" tanpa medan sumber sebenar** — ia memberi gambaran palsu kepada pengurusan bahawa tiada kes kritikal, sedangkan sebenarnya data tidak pernah dikumpul.

---

## 5. Kad "KPI" (Ontime / Delayed) — Perlu Takrifan SLA

`fr.HardSLA` sedia ada tapi nilainya teks bebas (`'Others'`), bukan bilangan hari. Cadangan tambah jadual rujukan SLA:

```sql
CREATE TABLE sla_target (
  frcate VARCHAR(25) PRIMARY KEY,
  sla_days INT NOT NULL
);
INSERT INTO sla_target (frcate, sla_days) VALUES
  ('Hardware', 3),
  ('Application/Software', 5);
-- (isi mengikut dasar SLA jabatan sebenar)
```

```sql
SELECT
  SUM(CASE WHEN DATEDIFF(a.DateUserVerified, a.DateReceived) <= s.sla_days THEN 1 ELSE 0 END) AS ontime,
  SUM(CASE WHEN DATEDIFF(a.DateUserVerified, a.DateReceived) > s.sla_days THEN 1 ELSE 0 END) AS delayed
FROM action a
INNER JOIN fr f ON f.Frn = a.frno
INNER JOIN sla_target s ON s.frcate = f.frcate
WHERE a.FR_status = 'Close'
  AND a.DateUserVerified IS NOT NULL
  AND YEAR(f.date_add) = 2026;
```

---

## 6. Pembetulan Kad "FR Category" — Label "overall total" Mengelirukan

**Isu asal**: jumlah `567` pada kad ini **sama persis** dengan "567 Pending" di tengah donut chart — tapi label kad tulis "overall total (2026)". Ini nampak seperti kad sebenarnya masih tertapis `WHERE status='Pending'`, bukan jumlah keseluruhan sebenar.

```sql
-- BETUL: overall total tanpa sebarang penapis status
SELECT
  SUM(CASE WHEN frcate = 'Application/Software' THEN 1 ELSE 0 END) AS software,
  SUM(CASE WHEN frcate = 'Hardware' THEN 1 ELSE 0 END) AS hardware,
  SUM(CASE WHEN frcate NOT IN ('Application/Software','Hardware') THEN 1 ELSE 0 END) AS others,
  COUNT(*) AS overall_total
FROM fr
WHERE YEAR(date_add) = 2026;
```

Jika hasil `overall_total` masih 567 (bukan 1,050), sahkan sekali lagi sama ada label patut jadi **"Pending total"**, bukan **"overall total"** — pembetulan boleh jadi di label UI, bukan query, bergantung niat sebenar business.

---

## 7. Pembetulan Donut Chart "Ticket In Progress"

**Isu asal**: legenda (38+4+4+179=225) tak sepadan dengan 567 di tengah, dan segmen 33.9%/19.2% terlalu besar untuk nilai "4".

```sql
-- Unassigned
SELECT COUNT(*) AS unassigned
FROM fr f
WHERE f.approval_status = 'Yes'
  AND NOT EXISTS (SELECT 1 FROM assign WHERE assign.Assfrno = f.Frn);

-- Lodge to SAINS (dalam proses rujukan, belum 'Done')
SELECT COUNT(DISTINCT FrRefId) AS lodge_to_sains
FROM refer_to
WHERE Refcate = 'SAINS' AND SEaction_status != 'Done';

-- Technical Review (dirujuk ke ISB, belum 'Done')
SELECT COUNT(DISTINCT FrRefId) AS technical_review
FROM refer_to
WHERE Refcate = 'ISB' AND SEaction_status != 'Done';

-- Pending Closed (tindakan sudah 'Done' tapi pengguna belum sahkan)
SELECT COUNT(*) AS pending_closed
FROM action
WHERE action_status = 'Done'
  AND (DateUserVerified IS NULL OR DateUserVerified = '0000-00-00 00:00:00');
```

**Semak wajib**: `unassigned + lodge_to_sains + technical_review + pending_closed` MESTI = jumlah di tengah donut (cth. 567). Jika masih tak match selepas guna query di atas, kemungkinan ada **kategori ke-5/ke-6** yang perlu ditambah dalam legenda (cth. "Rejected", "Awaiting user info") — jangan biar baki tersembunyi.

---

## 8. Spesifikasi Widget Tambahan (Cadangan Baharu)

| # | Widget | Jenis Carta | Query Ringkas | Kedudukan Dicadang |
|---|---|---|---|---|
| 1 | Trend FR dibuka vs ditutup per bulan | Line chart (2 garis) | `GROUP BY MONTH(date_add)` vs `GROUP BY MONTH(DateUserVerified)` | Baris penuh di bawah donut chart |
| 2 | Beban kerja setiap SE | Bar chart mendatar | `COUNT(*) GROUP BY ActionTakenBy ORDER BY COUNT DESC LIMIT 10` | Ganti/tambah sebelah "Users" |
| 3 | Purata masa penyelesaian (hari) mengikut kategori | KPI + bar chart | `AVG(DATEDIFF(DateUserVerified, DateReceived)) GROUP BY frcate` | Bawah kad KPI Ontime/Delayed |
| 4 | FR ikut Bahagian (Division) | Bar chart menegak | `COUNT(*) GROUP BY Oridiv ORDER BY COUNT DESC` | Baris baharu bawah FR Category |
| 5 | Top 10 peralatan paling kerap rosak | Bar chart mendatar | `COUNT(*) GROUP BY equip, brand ORDER BY COUNT DESC LIMIT 10` | Sub-tab "Hardware" dalam Reports |
| 6 | Senarai amaran — FR terlalu lama tunggu pengesahan pengguna | Jadual (table) | `WHERE DateSendToUser < NOW() - INTERVAL 14 DAY AND DateUserVerified IS NULL` | Panel kanan, atas KPI |

### Contoh Query Widget #1 (Trend Bulanan)

```sql
SELECT
  DATE_FORMAT(f.date_add, '%Y-%m') AS bulan,
  COUNT(DISTINCT f.Id) AS fr_dibuka,
  (SELECT COUNT(DISTINCT a.ActionId)
     FROM action a
     WHERE DATE_FORMAT(a.DateUserVerified, '%Y-%m') = DATE_FORMAT(f.date_add, '%Y-%m')
       AND a.FR_status = 'Close') AS fr_ditutup
FROM fr f
WHERE YEAR(f.date_add) = 2026
GROUP BY bulan
ORDER BY bulan;
```

### Contoh Query Widget #2 (Beban Kerja SE)

```sql
SELECT
  a.ActionTakenBy AS nama_se,
  COUNT(*) AS jumlah_kes,
  SUM(CASE WHEN a.FR_status = 'Close' THEN 1 ELSE 0 END) AS diselesaikan,
  ROUND(AVG(DATEDIFF(a.ActionEnd, a.ActionStart)), 1) AS purata_hari_tindakan
FROM action a
WHERE YEAR(a.DateReceived) = 2026
GROUP BY a.ActionTakenBy
ORDER BY jumlah_kes DESC
LIMIT 10;
```

> **Nota**: query ini guna `ActionTakenBy` (lajur teks nama bebas, bukan `username`) — seperti yang dilaporkan dalam `database.md`, ini berisiko *typo mismatch* (cth. `"Henry Sandah"` vs `"henry sandah "` dengan ruang tambahan tersembunyi). Sebelum jalankan laporan prestasi rasmi, cadangkan **bersihkan data** (`TRIM()`, standard nama) atau — lebih baik — migrasi lajur ini kepada rujukan `user.username` sebenar.

---

## 9. Senarai Semak Sebelum Deploy

- [ ] Sahkan struktur jadual sebenar (`SHOW CREATE TABLE`) — pastikan lajur `priority`, `sla_target` (atau setara) wujud sebelum guna Seksyen 4 & 5
- [ ] Jalankan ujian jumlah: `New + In Progress + Solved (+ Rejected) = Total`
- [ ] Jalankan ujian jumlah: donut chart legenda = nombor tengah donut
- [ ] Bersihkan `user.active` — tiada lagi nilai kosong `''`
- [ ] Tukar library carta daripada AnyChart Trial → Chart.js/CanvasJS (elak watermark & kos lesen)
- [ ] Tambah *drill-down* — setiap kad/segmen boleh diklik terus ke `FR List` dengan filter automatik
- [ ] Tambah *filter* Tahun & Division di bahagian atas dashboard
