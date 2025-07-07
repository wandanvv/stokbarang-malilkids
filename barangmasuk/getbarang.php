<?php
require '../function.php';

$search = $_GET['term'];

$query = mysqli_query($conn, "SELECT idbarang, namabarang FROM stok WHERE namabarang LIKE '%$search%'");
$data = array();
while($row = mysqli_fetch_assoc($query)){
    $data[] = [
        "label" => $row['namabarang'],
        "value" => $row['idbarang']
    ];
}

echo json_encode($data);
?>
