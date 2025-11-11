<?php
/*
 * API Backend untuk Soal 2a
 * Menghasilkan JSON data sensor dari database uts_ifb309
 */

// --- 1. Konfigurasi & Koneksi Database (PDO) ---
$host = '127.0.0.1'; // atau 'localhost'
$db   = 'uts_ifb309';
$user = 'root';      // User default Laragon
$pass = '';          // Password default Laragon (kosong)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Mengembalikan data sebagai associative array
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Jika koneksi gagal, tampilkan error
     header('Content-Type: application/json');
     http_response_code(500); // Server Error
     echo json_encode(['error' => 'Koneksi database gagal: ' . $e->getMessage()]);
     exit;
}

// --- 2. Logika Pengambilan Data ---

$result = [];

try {
    // Query 1: Ambil statistik dasar (max, min, avg) DAN max humidity
    // Kita butuh max humidity untuk query selanjutnya
    $stmt_stats = $pdo->query("SELECT 
                                MAX(suhu) as suhumax, 
                                MIN(suhu) as suhumin, 
                                AVG(suhu) as suhurata,
                                MAX(humidity) as humidmax 
                              FROM data_sensor");
    $stats = $stmt_stats->fetch();

    // Simpan nilai max untuk query berikutnya
    $max_suhu = $stats['suhumax'];
    $max_humid = $stats['humidmax'];

    // Format nilai statistik sesuai contoh (int dan float 2 desimal)
    $result['suhumax'] = (int)$stats['suhumax'];
    $result['suhumin'] = (int)$stats['suhumin'];
    $result['suhurata'] = round((float)$stats['suhurata'], 2);


    // Query 2 (Versi BARU dengan SIGNED)
    $stmt_max_data = $pdo->prepare("SELECT 
                                    id as idx, 
                                    CAST(suhu AS SIGNED) as suhun, 
                                    CAST(humidity AS SIGNED) as humid, 
                                    lux as kecerahan, 
                                    timestamp 
                                  FROM data_sensor 
                                  WHERE suhu = ? AND humidity = ?");
    $stmt_max_data->execute([$max_suhu, $max_humid]);
    $nilai_suhu_max_humid_max = $stmt_max_data->fetchAll();

    // Masukkan ke hasil
    $result['nilai_suhu_max_humid_max'] = $nilai_suhu_max_humid_max;


    // Proses 3: Ambil 'month_year_max' dari hasil Query 2
    // Kita lakukan ini di PHP agar lebih mudah
    $month_year_max = [];
    foreach ($nilai_suhu_max_humid_max as $row) {
        // Ubah string '2010-09-18 07:23:48' menjadi objek DateTime
        $date = new DateTime($row['timestamp']);
        
        // Format menjadi '9-2010' (n = bulan tanpa 0, Y = tahun 4 digit)
        $month_year_max[] = [
            'month_year' => $date->format('n-Y')
        ];
    }
    
    // Masukkan ke hasil
    $result['month_year_max'] = $month_year_max;


} catch (\PDOException $e) {
    // Jika query gagal, tampilkan error
    header('Content-Type: application/json');
    http_response_code(500); // Server Error
    echo json_encode(['error' => 'Query database gagal: ' . $e->getMessage()]);
    exit;
}

// --- 4. Tampilkan Hasil sebagai JSON ---

// Set header agar browser tahu ini adalah file JSON
header('Content-Type: application/json');

// Cetak array $result sebagai string JSON
// JSON_PRETTY_PRINT membuat outputnya rapi dan mudah dibaca
echo json_encode($result, JSON_PRETTY_PRINT);

?>