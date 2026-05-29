CREATE DATABASE IF NOT EXISTS tiket;
USE tiket;

CREATE TABLE `user` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100),
    no_hp VARCHAR(20),
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tempat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    lokasi VARCHAR(200),
    gambar VARCHAR(150) DEFAULT 'default.jpg',
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tiket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_tempat INT NOT NULL,
    jenis VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    deskripsi VARCHAR(200),
    stok INT DEFAULT 100,
    FOREIGN KEY (id_tempat) REFERENCES tempat(id) ON DELETE CASCADE
);

CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_booking VARCHAR(20) NOT NULL UNIQUE,
    id_user INT NOT NULL,
    id_tiket INT NOT NULL,
    tanggal_kunjungan DATE NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    total_harga DECIMAL(12,2) NOT NULL,
    metode_bayar ENUM('transfer_bca','transfer_mandiri','transfer_bni','kartu_kredit','kartu_debit') NOT NULL,
    status ENUM('pending','lunas','dibatalkan') DEFAULT 'pending',
    catatan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES `user`(id) ON DELETE CASCADE,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);

CREATE TABLE pengumuman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    id_admin INT NOT NULL,
    waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_admin) REFERENCES `user`(id) ON DELETE CASCADE
);
