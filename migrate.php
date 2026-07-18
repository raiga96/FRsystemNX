<?php
// migrate_passwords.php
set_time_limit(0); // Elakkan skrip timeout jika data terlalu banyak

// 1. Konfigurasi sambungan Database (Ubah ikut tetapan anda)
$host    = 'localhost';
$db      = 'frs';
$user    = 'root';
$pass    = 'imdagreat1';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// 2. Ambil semua data user yang passwordnya belum ditukar ke Bcrypt
// String Bcrypt sentiasa bermula dengan '$2y$' atau '$2a$'
$stmt = $pdo->query("SELECT uid, password FROM user WHERE password NOT LIKE '$2y$%'");
$users = $stmt->fetchAll();

echo "Menjumpai " . count($users) . " pengguna yang memerlukan migrasi password.<br><br>";

// Prepare kenyataan UPDATE sedia ada untuk prestasi pantas
$updateStmt = $pdo->prepare("UPDATE user SET password = :new_password WHERE uid = :uid");

$successCount = 0;

// 3. Gelung (Loop) untuk menukar setiap password secara unik
foreach ($users as $userRow) {
    $uid = $userRow['uid'];
    $oldPassword = $userRow['password'];
    
    // Abaikan jika password kosong (elakkan security bypass)
    if (empty($oldPassword)) {
        continue;
    }

    // Guna fungsi rasmi PHP password_hash dengan algoritma BCRYPT
    $newBcryptPassword = password_hash($oldPassword, PASSWORD_BCRYPT, ['cost' => 10]);

    // Kemas kini ke database
    $updateStmt->execute([
        'new_password' => $newBcryptPassword,
        'uid'          => $uid
    ]);

    $successCount++;
}

echo "🎉 Migrasi Selesai! Sebanyak $successCount password telah berjaya ditukar ke format password_hash (Bcrypt) secara selamat.";
?>