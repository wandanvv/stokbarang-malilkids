<?php
require '../function.php';
require '../cek.php';

// Tangkap parameter filter
$filter_kategori = isset($_GET['filter_kategori']) ? $_GET['filter_kategori'] : '';

// Query berdasarkan filter
if($filter_kategori && $filter_kategori != ''){
    $query = "SELECT * FROM stok WHERE kategori='$filter_kategori' ORDER BY namabarang ASC";
    $title_filter = "Kategori: " . $filter_kategori;
} else {
    $query = "SELECT * FROM stok ORDER BY namabarang ASC";
    $title_filter = "Semua Kategori";
}

$result = mysqli_query($conn, $query);
$total_items = mysqli_num_rows($result);

// Hitung total stok
$total_stok = 0;
$data_items = [];

while($row = mysqli_fetch_array($result)){
    $data_items[] = $row;
    $total_stok += $row['stok'];
}

// Tanggal cetak
$tanggal_cetak = date('d F Y, H:i:s');
$bulan_indo = array(
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
);
$tanggal_cetak = strtr($tanggal_cetak, $bulan_indo);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stock Barang - Malilkids</title>
    <link rel="stylesheet" href="../css/bootstrap.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet" />
    <style>
        /* Print Styles */
        @media print {
            .no-print { display: none !important; }
            body { font-family: 'Arial', sans-serif; }
            .print-header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
            .report-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .report-table th, .report-table td { border: 1px solid #333; padding: 8px; text-align: center; }
            .report-table th { background-color: #f0f0f0; }
        }
        
        /* Screen Styles */
        body { font-family: 'Poppins', sans-serif; margin: 20px; }
        .print-controls { margin-bottom: 20px; }
        .btn { padding: 8px 16px; margin-right: 10px; text-decoration: none; display: inline-block; border-radius: 4px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        
        .print-header { text-align: center; margin-bottom: 30px; }
        .company-name { font-size: 28px; font-weight: bold; }
        .text-blue { color: #007bff; }
        .text-orange { color: #ff6b35; }
        .company-info { font-size: 14px; color: #666; margin: 10px 0; }
        .report-title { font-size: 18px; font-weight: bold; margin-top: 15px; }
        
        .report-info { margin-bottom: 20px; }
        .info-row { margin: 5px 0; }
        .info-label { font-weight: bold; }
        
        .summary-cards { display: flex; gap: 20px; margin-bottom: 20px; }
        .summary-card { flex: 1; padding: 15px; border: 1px solid #ddd; border-radius: 8px; text-align: center; }
        .summary-card.success { background-color: #d4edda; border-color: #c3e6cb; }
        .summary-card h3 { margin: 0 0 10px 0; font-size: 14px; }
        .number { font-size: 24px; font-weight: bold; }
        
        .report-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .report-table th, .report-table td { border: 1px solid #333; padding: 8px; text-align: center; }
        .report-table th { background-color: #f8f9fa; font-weight: bold; }
        
        .print-footer { margin-top: 30px; }
        .signature-area { margin-top: 50px; text-align: right; }
        .signature-box { display: inline-block; text-align: center; min-width: 200px; }
        .signature-line { border-top: 1px solid #333; margin: 50px 0 10px 0; }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Laporan
        </button>
        <a href="../stok.php" class="btn btn-secondary">
            ← Kembali
        </a>
    </div>

    <!-- Print Header -->
    <div class="print-header">
        <div class="company-name">
            <span class="text-blue">mal</span><span class="text-orange">il</span><span class="text-blue">kids</span>
        </div>
        <div class="company-info">
            Sistem Manajemen Inventori Toko Malilkids
        </div>
        <div class="report-title">LAPORAN STOCK BARANG</div>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <div class="info-row">
            <span class="info-label">Filter Kategori:</span>
            <span><?php echo $title_filter; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span><?php echo $tanggal_cetak; ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Dicetak Oleh:</span>
            <span><?php echo $_SESSION['username']; ?></span>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Jenis Barang</h3>
            <div class="number"><?php echo $total_items; ?></div>
        </div>
        <div class="summary-card success">
            <h3>Total Stok Keseluruhan</h3>
            <div class="number"><?php echo number_format($total_stok); ?></div>
        </div>
    </div>

    <!-- Report Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 8%">No</th>
                <th style="width: 45%">Nama Barang</th>
                <th style="width: 30%">Kategori</th>
                <th style="width: 17%">Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach($data_items as $item) {
                $nama_barang = htmlspecialchars($item['namabarang']);
                $kategori = htmlspecialchars($item['kategori']);
                $stok = $item['stok'];
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td style="text-align: left; padding-left: 15px;"><?php echo $nama_barang; ?></td>
                <td><?php echo $kategori; ?></td>
                <td>
                    <strong><?php echo number_format($stok); ?></strong>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <!-- Print Footer -->
    <div class="print-footer">
        <p><strong>Ringkasan:</strong></p>
        <p>Total <?php echo $total_items; ?> jenis barang dengan total stok <?php echo number_format($total_stok); ?> unit.</p>
    </div>

    <!-- Signature Area -->
    <div class="signature-area">
        <div class="signature-box">
            <div>Bandung, <?php echo date('d F Y'); ?></div>
            <div style="margin-top: 10px;">Penanggung Jawab</div>
            <div class="signature-line"></div>
            <div>(<?php echo $_SESSION['username']; ?>)</div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>