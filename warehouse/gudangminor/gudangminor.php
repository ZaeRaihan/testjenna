<?php
session_start();
require 'functions.php';

// Check jika user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$gudangminor = query("SELECT * FROM gudangminor");

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$totalRecords = count(query("SELECT * FROM gudangminor"));
$totalPages = ceil($totalRecords / $limit);
// Hitung posisi awal pagination
$start = ($page - 1) * $limit;

// Periksa apakah start_date dan end_date diatur dalam parameter GET
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Inisialisasi variabel date_filter
$date_filter = '';

// Bangun klausa WHERE untuk filter tanggal
if (!empty($start_date) && !empty($end_date)) {
    $date_filter = "WHERE tgl_brg_masuk BETWEEN '$start_date' AND '$end_date'";
}

// Periksa apakah query pencarian diatur dalam parameter GET
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Inisialisasi variabel search_filter
$search_filter = '';

// Bangun klausa WHERE untuk filter pencarian
if (!empty($search)) {
    $search = strtolower($search); // Ubah istilah penelusuran menjadi huruf kecil
    $search = str_replace('.', '', $search); // Menghapus tanda titik dari angka format Rupiah
    $search = str_replace('Rp ', '', $search); // Menghapus teks "Rp " dari angka format Rupiah
    
    $search_filter = "WHERE 
            LOWER(kode) LIKE '%$search%' OR
            LOWER(collection) LIKE '%$search%' OR
            LOWER(kategori) LIKE '%$search%' OR
            LOWER(article_name) LIKE '%$search%' OR
            LOWER(size) LIKE '%$search%' OR
            LOWER(stock) LIKE '%$search%' OR
            LOWER(harga) LIKE '%$search%' OR
            LOWER(rak) LIKE '%$search%' OR
            LOWER(lokasi) LIKE '%$search%' OR
            LOWER(umur) LIKE '%$search%'";
}

// Query dengan search filter and date filter
$query = "SELECT * FROM gudangminor $date_filter $search_filter ORDER BY idstock_minor DESC LIMIT $start, $limit";
$gudangminor = query($query);

// Hitung jumlah total records untuk pencarian yang difilter dan rentang tanggal
$totalRecordsSearchDateFilter = count(query("SELECT * FROM gudangminor $date_filter $search_filter"));
$totalPagesSearchDateFilter = ceil($totalRecordsSearchDateFilter / $limit);

// Hitung jumlah total catatan untuk filtered date range
$totalRecordsDateFilter = count(query("SELECT * FROM gudangminor $date_filter"));
$totalPagesDateFilter = ceil($totalRecordsDateFilter / $limit);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- boootstrap -->
    <link href="../../vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="../../vendor/css/bootstrap/bootstrap.css" rel="stylesheet">

    <!-- icon dan fonts -->
    <link href="../../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- tema css -->
    <link href="../../css/tabel.css" rel="stylesheet">
    <title>Gudang Barang Minor</title>
</head>

<body>
    <!------- NAVBAR ------->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">navigation</span> Menu <i class="fa fa-bars"></i>
            </button>
            <a class="navbar-brand">ADMIN WAREHOUSE</a>
        </div>
        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown"
                    href="../logout.php"><?php echo getNama($_SESSION['username']); ?></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li>
                        <form class="" action="../logout.php" onclick="return confirm('yakin ingin logout?');"
                            method="post">
                            <button class="btn btn-default" type="submit" name="keluar"><i class="fa fa-sign-out"></i>
                                Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>

        <!-- menu samping -->
        <div class="navbar-default sidebar" role="navigation" style="font-size : 13.5px" ;>
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li>
                        <a href="../beranda.php">
                            BERANDA
                        </a>
                    </li>
                    <li>
                        <a href="../admin/admin.php">
                            ADMIN
                        </a>
                    </li>
                    <li>
                        <a href="../baranglolosqc/baranglolosqc.php">
                            BARANG LOLOS QC MASUK
                        </a>
                    </li>
                    <li>
                        <a href="../stockgudang/stockgudang.php">
                            STOCK GUDANG
                        </a>
                    </li>
                    <li>
                        <a href="../barangreject/barangreject.php">
                            BARANG REJECT MASUK
                        </a>
                    </li>
                    <li>
                        <a href="../gudangreject/gudangreject.php">
                            GUDANG BARANG REJECT
                        </a>
                    </li>
                    <li>
                        <a href="../barangminor/barangminor.php">
                            BARANG MINOR MASUK
                        </a>
                    </li>
                    <li>
                        <a href="gudangminor.php">
                            GUDANG BARANG MINOR
                        </a>
                    </li>
                    <li>
                        <a href="../barangkeluar/barangkeluar.php">
                            BARANG KELUAR
                        </a>
                    </li>
                    <li>
                        <a href="../barangretur/barangretur.php">
                            BARANG RETUR
                        </a>
                    </li>
                    <li>
                        <a href="../barangpinjam/barangpinjam.php">
                            BARANG PINJAM
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" onclick="return confirm('yakin ingin logout?')">
                            Logout
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <!------- CONTENT ------->
    <div class="content">
        <div class="container-fluid">
            <!------- HEADER ------->
            <h1 class="page-header">Gudang Barang Minor
                <br><br><br>

                <!-- TAMBAH DATA DENGAN MODAL -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-left">
                            <a href="#" class="btn btn-success" data-toggle="modal"
                                data-target="#tambahBarangModal">Tambah
                                Barang</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- Search Form -->
                        <form action="" method="GET" class="form-inline">
                            <label for="search"></label>
                            <input type="text" class="form-control mx-2" id="search" name="search"
                                value="<?php echo $_GET['search'] ?? ''; ?>" placeholder="cari data barang">
                            <button type="submit" class="btn btn-success">Search</button>
                            <a href="gudangminor.php" class="btn btn-warning mx-2">Clear</a>
                        </form>

                    </div>
                </div>


                <div class="row" style="margin-top: 5px;">
                    <div class="col-md-6">
                        <div class="button-group" style="display: flex; align-items: center;">
                            <!-- PRINT -->
                            <!-- <div style="margin-right: 5px;">
                                <button class="btn btn-warning" onclick="printTable()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                            </div> -->
                            <!-- PAGINATION -->
                            <div class="pagination-container">
                                <ul class="pagination pagination-lg">
                                    <?php for ($p = 1; $p <= $totalPagesSearchDateFilter; $p++) : ?>
                                    <li class="<?php if ($p == $page) echo 'active'; ?>">
                                        <a
                                            href="?page=<?php echo $p; ?>&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&search=<?php echo $search; ?>">
                                            <?php echo $p; ?>
                                        </a>
                                    </li>
                                    <?php endfor; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date Filter Form -->
                <!--    <div class="row" style="margin-top: 5px; margin-bottom: 5px">
                    <div class="col-md-12">
                        <form action="" method="GET" class="form-inline">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control mx-2" id="start_date" name="start_date"
                                value="<?php echo $_GET['start_date'] ?? ''; ?>">

                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control mx-2" id="end_date" name="end_date"
                                value="<?php echo $_GET['end_date'] ?? ''; ?>">

                            <button type="submit" class="btn btn-success">Apply Filter</button>
                            <a href="gudangminor.php" class="btn btn-warning mx-2">Clear Filter</a>
                        </form>
                    </div>
                </div> -->



                <!-- Modal -->
                <div class="modal fade" id="tambahBarangModal" tabindex="-1" role="dialog"
                    aria-labelledby="tambahBarangModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title" id="tambahBarangModalLabel">Tambah Barang</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Form untuk menambahkan data barang -->
                                <form action="proses_tambah.php" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="kode">Kode Barang</label>
                                        <input type="text" class="form-control" id="kode" name="kode">
                                    </div>
                                    <div class="form-group">
                                        <label for="collection">Collection</label>
                                        <input type="text" class="form-control" id="collection" name="collection">
                                    </div>
                                    <div class="form-group">
                                        <label for="kategori">Kategori</label>
                                        <select class="form-control" id="kategori" name="kategori" required
                                            onchange="checkLainnya('kategori')">
                                            <option value="" disabled selected>Pilih Kategori Barang</option>
                                            <option value="BAG">Bag</option>
                                            <option value="BROOCH">Brooch</option>
                                            <option value="BUCKET HAT">Bucket Hat</option>
                                            <option value="BUNDLING">Bundling</option>
                                            <option value="CLOTH MASK">Cloth Mask</option>
                                            <option value="DRESS">Dress</option>
                                            <option value="INNER">Inner</option>
                                            <option value="OUTER">Outer</option>
                                            <option value="PANTS">Pants</option>
                                            <option value="PRAYER SET">Prayer Set</option>
                                            <option value="QURAN">Quran</option>
                                            <option value="SANDAL">Sandal</option>
                                            <option value="SCARF">Scarf</option>
                                            <option value="SCRUNCHIE">Scrunchie</option>
                                            <option value="SHIRT">Shirt</option>
                                            <option value="SHOES">Shoes</option>
                                            <option value="SKIRT">Skirt</option>
                                            <option value="TOP">Top</option>
                                            <option value="TUNIC">Tunic</option>
                                            <!-- Opsi kategori lainnya -->
                                            <option value="LAINNYA">Lainnya</option>
                                            <!-- ... (opsi lainnya seperti sebelumnya) ... -->
                                        </select>
                                        <input type="text" class="form-control mt-2" id="kategori_input"
                                            name="kategori_input" style="display: none;"
                                            placeholder="Masukkan Kategori">
                                    </div>
                                    <div class="form-group">
                                        <label for="article_name">Article Name</label>
                                        <input type="text" class="form-control" id="article_name" name="article_name"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="size">Size</label>
                                        <input type="text" class="form-control" id="size" name="size" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Stock</label>
                                        <input type="text" class="form-control" id="stock" name="stock" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Harga</label>
                                        <input type="text" class="form-control" id="harga" name="harga" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="lokasi">Lokasi</label>
                                        <select class="form-control" id="lokasi" name="lokasi" required
                                            onchange="checkLainnya('lokasi')">
                                            <option value="" disabled selected>Pilih Lokasi Barang</option>
                                            <option value="Karton">Karton</option>
                                            <option value="Rak">Rak</option>
                                            <option value="Kontainer">Kontainer</option>
                                            <option value="Karung">Karung</option>
                                            <!-- Opsi lokasi lainnya -->
                                            <option value="LAINNYA">Lainnya</option>
                                            <!-- ... (opsi lainnya seperti sebelumnya) ... -->
                                        </select>
                                        <input type="text" class="form-control mt-2" id="lokasi_input"
                                            name="lokasi_input" style="display: none;" placeholder="Masukkan Lokasi">
                                    </div>
                                    <div class="form-group">
                                        <label for="stock">Rak</label>
                                        <input type="text" class="form-control" id="rak" name="rak" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="umur">Umur</label>
                                        <select class="form-control" id="umur" name="umur" required>
                                            <option value="" disabled selected>Pilih Umur Barang</option>
                                            <option value="0 - 3 BULAN">0 - 3 BULAN</option>
                                            <option value="4 - 6 BULAN">4 - 6 BULAN</option>
                                            <option value="7 - 9 BULAN">7 - 9 BULAN</option>
                                            <option value="10 - 12 BULAN">10 - 12 BULAN</option>
                                            <option value="13 - 18 BULAN">13 - 18 BULAN</option>
                                            <option value="19 - 24 BULAN">19 - 24 BULAN</option>
                                            <option value="> 24 BULAN">> 24 BULAN</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Tambah</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="data-table" class="table table-hover">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Collection</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Article Name</th>
                            <th scope="col">Size</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Harga</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Rak</th>
                            <th scope="col">Umur</th>
                            <th scope="col">Aksi</th>
                        </tr>

                        <!-- Table and Pagination -->
                        <?php $i = $start + 1;?>
                        <!-- Hitung indeks awal saat ini berdasarkan halaman dan limit -->
                        <?php foreach ($gudangminor as $row) : ?>

                        <tr class="data-row">
                            <td><?= $i; ?></td>
                            <td><?= $row["kode"]; ?></td>
                            <td><?= $row["collection"]; ?></td>
                            <td><?= $row["kategori"]; ?></td>
                            <td><?= $row["article_name"]; ?></td>
                            <td><?= $row["size"]; ?></td>
                            <td><?= $row["stock"]; ?></td>
                            <td><?= format_rupiah($row["harga"]); ?></td>
                            <td><?= $row["lokasi"]; ?></td>
                            <td><?= $row["rak"]; ?></td>
                            <td><?= $row["umur"]; ?></td>
                            <td>
                                <div class="btn-group text-center" style="display: flex; justify-content: center;">
                                    <button type="button" class="btn btn-warning delete-button"
                                        data-id="<?= $row["idstock_minor"]; ?>">Hapus</button>
                                    <button type="button" class="btn btn-primary update-button" data-toggle="modal"
                                        data-target="#ubahBarangModal<?= $row["idstock_minor"]; ?>"
                                        data-id="<?= $row["idstock_minor"]; ?>" data-page="<?= $page; ?>">Ubah</button>
                                    <button type="button" class="btn btn-success copy-button"
                                        data-id="<?= $row["idstock_minor"]; ?>">Copy</button>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="ubahBarangModal<?= $row["idstock_minor"]; ?>" tabindex="-1"
                            role="dialog" aria-labelledby="ubahBarangModalLabel<?= $row["idstock_minor"]; ?>"
                            aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title" id="ubahBarangModalLabel<?= $row["idstock_minor"]; ?>">
                                            Ubah Barang</h3>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Form untuk mengubah data barang -->
                                        <form action="proses_ubah.php" method="post" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $row["idstock_minor"]; ?>">
                                            <div class="form-group">
                                                <label for="kode">Kode Barang</label>
                                                <input type="text" class="form-control" id="kode" name="kode"
                                                    value="<?= $row["kode"]; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="collection">Collection</label>
                                                <input type="text" class="form-control" id="collection"
                                                    name="collection" value="<?= $row["collection"]; ?>">
                                            </div>
                                            <div class="form-group">
                                                <label for="kategori">Kategori</label>
                                                <input type="text" class="form-control" id="kategori" name="kategori"
                                                    value="<?= $row["kategori"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="article_name">Article Name</label>
                                                <input type="text" class="form-control" id="article_name"
                                                    name="article_name" value="<?= $row["article_name"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="size">Size</label>
                                                <input type="text" class="form-control" id="size" name="size"
                                                    value="<?= $row["size"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock">Stock</label>
                                                <input type="text" class="form-control" id="stock" name="stock"
                                                    value="<?= $row["stock"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="harga">Harga</label>
                                                <input type="text" class="form-control" id="harga" name="harga"
                                                    value="<?= $row["harga"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="rak">Lokasi</label>
                                                <input type="text" class="form-control" id="lokasi" name="lokasi"
                                                    value="<?= $row["lokasi"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="rak">Rak</label>
                                                <input type="text" class="form-control" id="rak" name="rak"
                                                    value="<?= $row["rak"]; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="umur">Umur</label>
                                                <select class="form-control" id="umur" name="umur" required>
                                                    <option value="" disabled>Pilih Umur Barang</option>
                                                    <option value="0 - 3 BULAN"
                                                        <?= ($row["umur"] == "0 - 3 BULAN") ? "selected" : ""; ?>>
                                                        0 - 3 BULAN
                                                    </option>
                                                    <option value="4 - 6 BULAN"
                                                        <?= ($row["umur"] == "4 - 6 BULAN") ? "selected" : ""; ?>>
                                                        4 - 6 BULAN</option>
                                                    <option value="4 - 6 BULAN"
                                                        <?= ($row["umur"] == "4 - 6 BULAN") ? "selected" : ""; ?>>
                                                        4 - 6 BULAN</option>
                                                    <option value="7 - 9 BULAN"
                                                        <?= ($row["umur"] == "7 - 9 BULAN") ? "selected" : ""; ?>>
                                                        7 - 9 BULAN</option>
                                                    <option value="10 - 12 BULAN"
                                                        <?= ($row["umur"] == "10 - 12 BULAN") ? "selected" : ""; ?>>
                                                        10 - 12 BULAN</option>
                                                    <option value="13 - 18 BULAN"
                                                        <?= ($row["umur"] == "13 - 18 BULAN") ? "selected" : ""; ?>>
                                                        13 - 18 BULAN</option>
                                                    <option value="19 - 24 BULAN"
                                                        <?= ($row["umur"] == "19 - 24 BULAN") ? "selected" : ""; ?>>
                                                        19 - 24 BULAN</option>
                                                    <option value="> 24 BULAN"
                                                        <?= ($row["umur"] == "> 24 BULAN") ? "selected" : ""; ?>>
                                                        > 24 BULAN</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="page" value="<?= $page; ?>">
                                            <button type="submit" class="btn btn-primary">Ubah</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php $i++; ?>
                        <?php endforeach; ?>

                    </table>
                </div>

                <div>
                    <a href="../beranda.php" class="btn btn-success">Kembali</a>
                </div>
        </div>


    </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>©2023 Jenna and Kaia</p>
    </footer>

    <!-- jQuery -->
    <script src="../../vendor/jquery/jquery.min.js"></script>

    <!--include-->
    <script src="../../vendor/css/js/bootstrap.min.js"></script>

    <!-- JavaScript file -->
    <script src="script.js"></script>

    <!-- <script>
    function printTable() {
        const selectedRows = [];
        const checkboxes = document.querySelectorAll('.print-checkbox:checked');

        checkboxes.forEach(checkbox => {
            selectedRows.push(checkbox.getAttribute('data-id'));
        });

        if (selectedRows.length > 0) {
            const selectedRowsStr = selectedRows.join(',');
            const printPageUrl = `print_page.php?selected_rows=${encodeURIComponent(selectedRowsStr)}`;

            // Buka URL Print Page di tab baru
            window.open(printPageUrl, '_blank');
        } else {
            alert('No rows selected for printing.');
        }
    }
    </script> -->
    <script>
    function checkLainnya(field) {
        const dropdown = document.getElementById(field);
        const inputField = document.getElementById(field + "_input");

        if (dropdown.value === "LAINNYA") {
            inputField.style.display = "block";
            inputField.setAttribute("required", "required");
        } else {
            inputField.style.display = "none";
            inputField.removeAttribute("required");
        }
    }
    </script>

</body>

</html>