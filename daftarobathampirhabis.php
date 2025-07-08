<?php
session_start();
require_once('conf/conf.php');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
$tanggal = mktime(date("m"), date("d"), date("Y"));
date_default_timezone_set('Asia/Jakarta');
$jam = date("H:i");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Obat Hampir Habis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/5hb7x1z5l5e5l5e5l5e5l5e5l5e5l5e5l5e5l" crossorigin="anonymous"></script>
</head>

<body class="bg-success">
    <main class="mt-3">
        <div class="container-md container-xl">
            <div class="card  bg-white">
                <div class="card-body">
                    <?php
                    $setting = mysqli_fetch_array(bukaquery("SELECT setting.nama_instansi, setting.alamat_instansi, setting.kabupaten, setting.propinsi, setting.kontak, setting.email, setting.logo FROM setting"));
                    echo "<div class='d-flex justify-content-start align-items-center'>
                            <div>
                                <img class='img-fluid img-thumbnail' src='data:image/jpeg;base64," . base64_encode($setting['logo']) . "' width='90' height='90' alt='Logo' />
                            </div>
                            <div class='mx-4'>
                                <h3 class='text-black'>Daftar Obat Hampir Habis</h3>
                                <p class='mb-1'>Daftar obat dibawah adalah stok depo Apotek/Farmasi (AP) yang juga digunakan dokter saat peresepan.</p>
                                <p class='mb-1'>" . date("d-M-Y", $tanggal) . " " . $jam . "</p>
                            </div>  
                        </div>";
                    ?>
                    <table class="table table-bordered mt-4 table-hover text-center">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode Obat</th>
                                <th>Nama Obat</th>
                                <th>Jenis Obat</th>
                                <th>Stok Minimal</th>
                                <th>Stok Saat Ini</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody
                            <?php
                            $no = 1;
                            $query = bukaquery("SELECT databarang.kode_brng, 
                                                    databarang.nama_brng,
                                                    jenis.nama AS nama_jenis,
                                                    databarang.kode_sat,
                                                    databarang.letak_barang,
                                                    industrifarmasi.nama_industri,
                                                    databarang.stokminimal,
                                                    gudangbarang.stok,
                                                    CASE
                                                        WHEN gudangbarang.stok = 0 THEN 'Habis!'
                                                        WHEN gudangbarang.stok < databarang.stokminimal THEN 'Hampir Habis'
                                                        WHEN gudangbarang.stok <= (databarang.stokminimal + 2) THEN 'Mendekati Habis'
                                                    END AS keterangan
                                                FROM databarang 
                                                INNER JOIN jenis ON databarang.kdjns = jenis.kdjns
                                                INNER JOIN industrifarmasi ON industrifarmasi.kode_industri = databarang.kode_industri
                                                INNER JOIN gudangbarang ON databarang.kode_brng = gudangbarang.kode_brng 
                                                WHERE databarang.status = '1' 
                                                AND gudangbarang.no_batch = '' 
                                                AND gudangbarang.no_faktur = '' 
                                                AND gudangbarang.kd_bangsal = 'AP'
                                                AND (
                                                    gudangbarang.stok = 0 OR 
                                                    gudangbarang.stok < databarang.stokminimal OR 
                                                    gudangbarang.stok <= (databarang.stokminimal + 2)
                                                )
                                                ORDER BY gudangbarang.stok ASC");

                            while ($data = mysqli_fetch_array($query)) {
                                echo "<tr>
                                        <td>" . $no . "</td>
                                        <td>" . $data['kode_brng'] . "</td>
                                        <td>" . $data['nama_brng'] . "</td>
                                        <td>" . $data['nama_jenis'] . "</td>
                                        <td>" . $data['stokminimal'] . "</td>
                                        <td>" . $data['stok'] . "</td>
                                        <td class='fw-bold " . ($data['keterangan'] == 'Habis!' ? 'text-danger' : ($data['keterangan'] == 'Hampir Habis' ? 'text-warning' : 'text-success')) . "'>" . $data['keterangan'] . "</td>
                                    </tr>";
                                $no++;
                            }
                            ?>
                            </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>

</html>