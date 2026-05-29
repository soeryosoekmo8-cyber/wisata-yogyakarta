CREATE TABLE IF NOT EXISTS `admin` (
  `id`         INT NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `nama`       VARCHAR(150) NOT NULL,
  `role`       ENUM('admin','superadmin') DEFAULT 'admin',
  `status`     ENUM('aktif','nonaktif') DEFAULT 'aktif',
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`username`, `password`, `nama`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin'),
('superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'superadmin');

CREATE TABLE IF NOT EXISTS `kategori` (
  `id`        INT NOT NULL AUTO_INCREMENT,
  `nama`      VARCHAR(100) NOT NULL,
  `ikon`      VARCHAR(50) DEFAULT 'fa-map-marker-alt',
  `deskripsi` VARCHAR(255),
  `warna`     VARCHAR(20) DEFAULT '#007bff',
  `status`    ENUM('aktif','nonaktif') DEFAULT 'aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori` (`nama`, `ikon`, `deskripsi`, `warna`) VALUES
('Wisata Alam',    'fa-mountain',     'Keindahan alam, pegunungan, pantai, dan gua',            '#28a745'),
('Wisata Budaya',  'fa-landmark',     'Candi, keraton, dan warisan budaya bersejarah',           '#fd7e14'),
('Wisata Kuliner', 'fa-utensils',     'Makanan khas dan pengalaman kuliner Yogyakarta',          '#dc3545'),
('Wisata Belanja', 'fa-shopping-bag', 'Pasar, mal, dan pusat oleh-oleh khas Yogyakarta',         '#6f42c1'),
('Wisata Religi',  'fa-mosque',       'Masjid, gereja, dan tempat ibadah bersejarah',            '#20c997');

CREATE TABLE IF NOT EXISTS `wisata` (
  `id`              INT NOT NULL AUTO_INCREMENT,
  `nama`            VARCHAR(200) NOT NULL,
  `kategori_id`     INT NOT NULL,
  `lokasi`          VARCHAR(255) NOT NULL,
  `deskripsi`       TEXT NOT NULL,
  `harga_tiket`     VARCHAR(100) DEFAULT 'Gratis',
  `harga_tiket_min` INT DEFAULT 0,
  `harga_tiket_max` INT DEFAULT 0,
  `is_gratis`       TINYINT(1) DEFAULT 0,
  `jam_buka`        VARCHAR(100) DEFAULT '24 Jam',
  `gambar`          VARCHAR(255) DEFAULT 'default.jpg',
  `maps_url`        TEXT,
  `latitude`        DECIMAL(10,8),
  `longitude`       DECIMAL(11,8),
  `telepon`         VARCHAR(20),
  `website`         VARCHAR(255),
  `rating`          DECIMAL(2,1) DEFAULT 0.0,
  `views`           INT DEFAULT 0,
  `status`          ENUM('aktif','nonaktif') DEFAULT 'aktif',
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_kategori` FOREIGN KEY (`kategori_id`)
    REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `wisata` (`nama`, `kategori_id`, `lokasi`, `deskripsi`, `harga_tiket`, `harga_tiket_min`, `harga_tiket_max`, `is_gratis`, `jam_buka`, `gambar`, `rating`, `latitude`, `longitude`, `telepon`, `website`) VALUES
('Candi Borobudur',     2, 'Magelang (dekat Yogyakarta)', 'Candi Buddha terbesar di dunia yang dibangun pada abad ke-8-9 Masehi. Merupakan Situs Warisan Dunia UNESCO dengan arsitektur yang menakjubkan dan relief yang menceritakan kisah Buddha.',        'Rp 50.000 (domestik)',  50000, 50000,  0, '06.00 - 17.00 WIB', 'borobudur.jpg',    5.0, -7.60788, 110.20367, '0293-788266', 'https://borobudurpark.com'),
('Candi Prambanan',     2, 'Sleman, Yogyakarta',          'Kompleks candi Hindu terbesar di Indonesia dan Asia Tenggara, didedikasikan untuk Trimurti (Brahma, Wisnu, Siwa). Dibangun pada abad ke-9 dan merupakan Situs Warisan Dunia UNESCO.',               'Rp 50.000 (domestik)',  50000, 50000,  0, '06.00 - 17.00 WIB', 'prambanan.jpg',    4.9, -7.75208, 110.49143, '0274-496401', 'https://borobudurpark.com'),
('Keraton Yogyakarta',  2, 'Kraton, Kota Yogyakarta',    'Istana resmi Kesultanan Ngayogyakarta Hadiningrat yang masih aktif dihuni oleh Sultan. Merupakan pusat kebudayaan Jawa dengan arsitektur tradisional yang indah dan koleksi benda-benda bersejarah.', 'Rp 15.000',             15000, 15000,  0, '08.00 - 14.00 WIB', 'keraton.jpg',      4.7, -7.80540, 110.36403, '0274-373721', NULL),
('Pantai Parangtritis', 1, 'Bantul, Yogyakarta',          'Pantai paling terkenal di Yogyakarta dengan pemandangan matahari terbenam yang spektakuler. Dikenal dengan legenda Nyi Roro Kidul dan keindahan ombak lautnya yang menawan.',                        'Rp 10.000',             10000, 10000,  0, '24 Jam',            'parangtritis.jpg', 4.6, -8.02535, 110.32691, NULL,          NULL),
('Gunung Merapi',       1, 'Sleman, Yogyakarta',          'Gunung berapi paling aktif di Indonesia dengan pemandangan alam yang memukau. Tersedia wisata jeep untuk menjelajahi kawasan sekitar gunung dan melihat sisa-sisa letusan.',                          'Rp 50.000 (Jeep Tour)', 50000, 300000, 0, '05.00 - 17.00 WIB', 'merapi.jpg',       4.8, -7.54088, 110.44629, NULL,          NULL),
('Malioboro',           3, 'Kota Yogyakarta',             'Jalan ikonik Yogyakarta yang terkenal dengan deretan pedagang kaki lima, toko batik, kerajinan tangan, dan kuliner khas. Pusat oleh-oleh dan pengalaman budaya Jawa yang autentik.',                  'Gratis',                0,     0,      1, '24 Jam',            'malioboro.jpg',    4.7, -7.79306, 110.36583, NULL,          NULL),
('Taman Sari',          2, 'Kraton, Kota Yogyakarta',    'Bekas taman istana Keraton Yogyakarta yang dibangun pada 1758. Memiliki kolam pemandian kerajaan, lorong bawah tanah, dan menara pandang dengan arsitektur unik perpaduan Jawa dan Eropa.',             'Rp 15.000',             15000, 15000,  0, '09.00 - 15.00 WIB', 'tamansari.jpg',    4.6, -7.81000, 110.35833, NULL,          NULL),
('Goa Pindul',          1, 'Gunungkidul, Yogyakarta',    'Wisata cave tubing menyusuri gua dengan sungai bawah tanah yang menakjubkan. Gua sepanjang 350 meter ini memiliki stalaktit dan stalagmit yang indah dengan pencahayaan alami.',                       'Rp 35.000',             35000, 35000,  0, '08.00 - 16.00 WIB', 'goaping.jpg',      4.8, -7.94444, 110.60583, NULL,          NULL);

CREATE TABLE IF NOT EXISTS `galeri_wisata` (
  `id`         INT NOT NULL AUTO_INCREMENT,
  `wisata_id`  INT NOT NULL,
  `gambar`     VARCHAR(255) NOT NULL,
  `keterangan` VARCHAR(200),
  `urutan`     INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `wisata_id` (`wisata_id`),
  CONSTRAINT `fk_galeri_wisata` FOREIGN KEY (`wisata_id`)
    REFERENCES `wisata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `fasilitas` (
  `id`        INT NOT NULL AUTO_INCREMENT,
  `wisata_id` INT NOT NULL,
  `nama`      VARCHAR(100) NOT NULL,
  `ikon`      VARCHAR(50) DEFAULT 'fa-check',
  `tersedia`  TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `wisata_id` (`wisata_id`),
  CONSTRAINT `fk_fasilitas_wisata` FOREIGN KEY (`wisata_id`)
    REFERENCES `wisata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fasilitas` (`wisata_id`, `nama`, `ikon`, `tersedia`) VALUES
(1, 'Toilet Umum',   'fa-restroom', 1),
(1, 'Area Parkir',   'fa-parking',  1),
(1, 'Mushola',       'fa-mosque',   1),
(1, 'Restoran',      'fa-utensils', 1),
(1, 'Toko Souvenir', 'fa-store',    1),
(1, 'WiFi',          'fa-wifi',     0);

CREATE TABLE IF NOT EXISTS `komentar` (
  `id`         INT NOT NULL AUTO_INCREMENT,
  `wisata_id`  INT NOT NULL,
  `nama`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150),
  `komentar`   TEXT NOT NULL,
  `rating`     TINYINT(1) DEFAULT 5,
  `status`     ENUM('pending','approved','rejected') DEFAULT 'pending',
  `is_spam`    TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `wisata_id` (`wisata_id`),
  CONSTRAINT `fk_wisata_komentar` FOREIGN KEY (`wisata_id`)
    REFERENCES `wisata` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE OR REPLACE VIEW `v_rating_wisata` AS
SELECT
  w.id                                        AS wisata_id,
  w.nama                                      AS nama_wisata,
  COUNT(k.id)                                 AS total_ulasan,
  IFNULL(ROUND(AVG(k.rating), 1), 0)          AS rating_avg
FROM `wisata` w
LEFT JOIN `komentar` k
  ON k.wisata_id = w.id AND k.status = 'approved'
GROUP BY w.id, w.nama;

CREATE OR REPLACE VIEW `v_wisata_populer` AS
SELECT
  w.id,
  w.nama,
  k.nama  AS kategori,
  w.lokasi,
  w.views,
  w.rating,
  w.gambar,
  w.status
FROM `wisata` w
JOIN `kategori` k ON k.id = w.kategori_id
WHERE w.status = 'aktif'
ORDER BY w.views DESC;