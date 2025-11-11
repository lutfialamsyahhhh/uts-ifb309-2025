<?php
/*
 * API Backend untuk Soal 2a
 */

// --- 1. Konfigurasi & Koneksi Database (PDO) ---
$host = '127.0.0.1';
$db   = 'uts_ifb309';
$user = 'root';
$pass = '';
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
     header('Content-Type: application/json');
     http_response_code(500);
     echo json_encode(['error' => 'Koneksi database gagal: ' . $e->getMessage()]);
     exit;
}

// --- 2. Logika Pengambilan Data ---

$result = [];

try {
    $stmt_stats = $pdo->query("SELECT 
                                MAX(suhu) as suhumax, 
                                MIN(suhu) as suhumin, 
                                AVG(suhu) as suhurata,
                                MAX(humidity) as humidmax 
                              FROM data_sensor");
    $stats = $stmt_stats->fetch();

    $max_suhu = $stats['suhumax'];
    $max_humid = $stats['humidmax'];

    $result['suhumax'] = (int)$stats['suhumax'];
    $result['suhumin'] = (int)$stats['suhumin'];
    $result['suhurata'] = round((float)$stats['suhurata'], 2);


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

    $result['nilai_suhu_max_humid_max'] = $nilai_suhu_max_humid_max;


    // Proses 3: Ambil 'month_year_max' dari hasil Query 2
    $month_year_max = [];
    foreach ($nilai_suhu_max_humid_max as $row) {
        $date = new DateTime($row['timestamp']);
        
        $month_year_max[] = [
            'month_year' => $date->format('n-Y')
        ];
    }
    
    $result['month_year_max'] = $month_year_max;


} catch (\PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Query database gagal: ' . $e->getMessage()]);
    exit;
}

// --- 4. Tampilkan Hasil sebagai JSON ---

header('Content-Type: application/json');

echo json_encode($result, JSON_PRETTY_PRINT);

?>