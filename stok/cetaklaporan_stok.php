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
        body {
            font-family: 'Poppins', sans-serif;
            background: white;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0077b6;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .text-blue { color: #0077b6 !important; }
        .text-orange { color: #ff8800 !important; }
        
        .company-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .report-title {
            font-size: 24px;
            font-weight: 600;
            color: #0077b6;
            margin: 10px 0;
        }
        
        .report-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #0077b6;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .info-label {
            font-weight: 600;
            color: #0077b6;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #0077b6, #005f8a);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .summary-card.success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .summary-card .number {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
        }
        
        .report-table th {
            background: #0077b6;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #005f8a;
        }
        
        .report-table td {
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        .report-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .report-table tbody tr:hover {
            background: #e9f5fc;
        }
        
        .print-footer {
            margin-top: 40px;
            text-align: right;
            font-size: 14px;
        }
        
        .signature-area {
            margin-top: 60px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            margin-left: 50px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 60px auto 10px auto;
        }
        
        /* Print specific styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-header {
                break-inside: avoid;
            }
            
            .summary-cards {
                break-inside: avoid;
            }
            
            .report-table {
                break-inside: auto;
            }
            
            .report-table thead {
                display: table-header-group;
            }
            
            .report-table tbody tr {
                break-inside: avoid;
            }
        }
        
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn {
            padding: 8px 16px;
            margin: 0 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #0077b6;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Laporan
        </button>
        <a href="stok.php" class="btn btn-secondary">
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
        // Format tanggal Indonesia
        function formatTanggal(date) {
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            const d = new Date(date);
            return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
        }
    </script>
</body>
</html>