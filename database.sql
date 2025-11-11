-- 1. Membuat Database
CREATE DATABASE IF NOT EXISTS `uts_ifb309` CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_general_ci';

-- 2. Menggunakan Database
USE `uts_ifb309`;

-- 3. Membuat Tabel 'data_sensor'
CREATE TABLE IF NOT EXISTS `data_sensor` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `suhu` DECIMAL(5, 2),
  `humidity` DECIMAL(5, 2),
  `lux` INT,
  `timestamp` TIMESTAMP
);

-- 4. Memasukkan Data Sampel (Dummy Data)
INSERT INTO `data_sensor` (`id`, `suhu`, `humidity`, `lux`, `timestamp`) VALUES
(1, 36, 36, 25, '2010-09-18 07:23:48'),
(2, 21, 35, 15, '2011-01-10 10:00:00'),
(3, 28, 30, 20, '2011-03-22 11:30:00'),
(4, 36, 36, 27, '2011-05-02 12:29:34'),
(5, 30, 30, 30, '2012-07-15 14:00:00'),
(6, 25, 35, 18, '2013-11-30 09:15:00');

-- 5. Update ID agar Sesuai Soal 2a
UPDATE data_sensor 
SET id = 101 
WHERE timestamp = '2010-09-18 07:23:48';

UPDATE data_sensor 
SET id = 226 
WHERE timestamp = '2011-05-02 12:29:34';