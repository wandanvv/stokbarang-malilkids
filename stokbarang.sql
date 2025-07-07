-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jul 2025 pada 17.19
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stokbarang`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `namakategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `namakategori`) VALUES
(1, 'Gendongan'),
(2, 'Baby Blanket  & Swaddle'),
(5, 'Baby Stuff'),
(6, 'Nursing Stuff'),
(7, 'Hampers Bag');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keluar`
--

CREATE TABLE `keluar` (
  `idkeluar` int(11) NOT NULL,
  `idbarang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `keluar`
--

INSERT INTO `keluar` (`idkeluar`, `idbarang`, `jumlah`, `tanggal`, `keterangan`) VALUES
(2, 4, 12, '2025-06-26 03:11:07', 'shope'),
(3, 4, 15, '2025-07-06 05:04:25', 'shope'),
(4, 4, 15, '2025-07-06 05:04:25', 'shope'),
(5, 2, 10, '2025-07-06 05:14:18', 'shopee'),
(6, 2, 10, '2025-07-06 05:14:18', 'shopee');

-- --------------------------------------------------------

--
-- Struktur dari tabel `masuk`
--

CREATE TABLE `masuk` (
  `idmasuk` int(11) NOT NULL,
  `idbarang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp(),
  `penerima` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `masuk`
--

INSERT INTO `masuk` (`idmasuk`, `idbarang`, `jumlah`, `tanggal`, `penerima`) VALUES
(1, 0, 0, '2025-04-28 08:17:47', 'wanda'),
(4, 2, 50, '2025-04-28 08:39:30', 'wanda'),
(5, 4, 12, '2025-05-01 12:29:07', 'wanda'),
(6, 5, 15, '2025-07-06 05:13:24', 'wanda'),
(8, 5, 30, '2025-07-06 05:14:51', 'wanda'),
(9, 5, 30, '2025-07-06 05:14:51', 'wanda');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok`
--

CREATE TABLE `stok` (
  `idbarang` int(11) NOT NULL,
  `namabarang` varchar(50) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `stok` int(11) NOT NULL,
  `foto` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stok`
--

INSERT INTO `stok` (`idbarang`, `namabarang`, `kategori`, `stok`, `foto`) VALUES
(1, 'geos premium', 'geos', 25, 'ex'),
(2, 'Blanket Minky Cream x Mocca', 'Baby Blanket  &amp; Swaddle', 55, 'ttd.png'),
(4, 'Geos SP Waffle Black', 'Gendongan', 82, 'b44bb78b504039e53336ca4a833ce86d.png'),
(5, 'Geos SP Waffle Denim', 'Gendongan', 145, '6e3ae22537072a06f313cf4240cc9b88.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`iduser`, `email`, `password`) VALUES
(1, 'wandacantik@gmail.com', '$2y$10$vCZE4d5MgEPIIgHDnvBAe.AMcAfaDSvgHK0axWRfo/JfPLNT4KIgy'),
(2, 'wandanvv@gmail.com', '4aa502101e76120a49780020b623e0ce'),
(3, 'ridwangabriel@gmail.com', 'b59c67bf196a4758191e42f76670ceba');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keluar`
--
ALTER TABLE `keluar`
  ADD PRIMARY KEY (`idkeluar`);

--
-- Indeks untuk tabel `masuk`
--
ALTER TABLE `masuk`
  ADD PRIMARY KEY (`idmasuk`);

--
-- Indeks untuk tabel `stok`
--
ALTER TABLE `stok`
  ADD PRIMARY KEY (`idbarang`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `keluar`
--
ALTER TABLE `keluar`
  MODIFY `idkeluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `masuk`
--
ALTER TABLE `masuk`
  MODIFY `idmasuk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `stok`
--
ALTER TABLE `stok`
  MODIFY `idbarang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
