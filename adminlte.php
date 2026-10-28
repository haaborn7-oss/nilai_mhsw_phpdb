
<?php
require_once __DIR__ . "/koneksi.php";

// Notifikasi tambah, edit, dan hapus
$tipe = $_GET['tipe'] ?? '';
$pesan = $_GET['msg'] ?? '';

$tipe_valid = ['success', 'danger', 'warning', 'info'];
$pesan_valid = [
    'Data nilai berhasil ditambahkan.',
    'Data nilai berhasil diperbarui.',
    'Data nilai berhasil dihapus.',
    'NIM sudah terdaftar.',
    'Data mahasiswa tidak ditemukan.'
];

if (
    !in_array($tipe, $tipe_valid, true) ||
    !in_array($pesan, $pesan_valid, true)
) {
    $tipe = '';
    $pesan = '';
}

// Pencarian, filter, sort, dan pagination
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$predikat_filter = mysqli_real_escape_string($conn, $_GET['predikat'] ?? 'all');

$sort = $_GET['sort'] ?? '';
$order = ['asc' => 'nilai_akhir ASC', 'desc' => 'nilai_akhir DESC'][$sort] ?? 'nim ASC';
$sort_berikut = $sort == 'desc' ? 'asc' : 'desc';

$per_page = (int)($_GET['per_page'] ?? 7);
$per_page = in_array($per_page, [5, 7, 10, 25, 50]) ? $per_page : 7;

$where = "WHERE nama_lengkap LIKE '%$search%'";
$where .= $predikat_filter != 'all' ? " AND predikat = '$predikat_filter'" : '';

$total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM nilaimhsw $where");
$total = (int)mysqli_fetch_assoc($total_query)['total'];

$total_halaman = max(1, (int)ceil($total / $per_page));
$page = min(max(1, (int)($_GET['page'] ?? 1)), $total_halaman);
$offset = ($page - 1) * $per_page;

$result = mysqli_query(
    $conn,
    "SELECT * FROM nilaimhsw $where ORDER BY $order LIMIT $per_page OFFSET $offset"
);

$dari = $total ? $offset + 1 : 0;
$sampai = $offset + mysqli_num_rows($result);

// Mempertahankan parameter pencarian, filter, dan pengurutan
function link_ke($ubah) {
    $parameter = $_GET;
    unset($parameter['status'], $parameter['tipe'], $parameter['msg']);

    return '?' . http_build_query(array_merge($parameter, $ubah));
}
?>

<!doctype html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 4 | Rekap Nilai Mahasiswa</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css" crossorigin="anonymous">
</head>

<body class="bg-body-tertiary p-3">
<main class="app-main m-0 p-0">

    <div class="app-content-header mb-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="mb-0 fs-3">Rekap Nilai Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                        <li class="breadcrumb-item active">Nilai</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <!-- Notifikasi -->
            <?php if ($pesan && $tipe) { ?>
                <div class="alert alert-<?php echo htmlspecialchars($tipe); ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($pesan); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <div class="card mb-4">
                <form method="GET" action="">
                    <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                    <input type="hidden" name="per_page" value="<?php echo $per_page; ?>">

                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-4">
                                <h3 class="card-title">Direktori Nilai</h3>
                            </div>

                            <div class="col-12 col-md-8">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <div class="input-group input-group-sm w-auto">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="search" name="search" class="form-control"
                                               placeholder="Cari berdasarkan nama"
                                               value="<?php echo htmlspecialchars($search); ?>"
                                               style="width: 200px">
                                    </div>

                                    <select name="predikat" class="form-select form-select-sm w-auto"
                                            onchange="this.form.submit()">
                                        <option value="all">Semua Predikat</option>
                                        <?php
                                        foreach (['Sangat Memuaskan', 'Memuaskan', 'Cukup', 'Kurang', 'Tidak Lulus'] as $p) {
                                        ?>
                                            <option value="<?php echo htmlspecialchars($p); ?>"
                                                <?php echo $predikat_filter == $p ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($p); ?>
                                            </option>
                                        <?php } ?>
                                    </select>

                                    <button type="button" class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal" data-bs-target="#modal-add">
                                        <i class="bi bi-person-plus-fill me-1"></i>
                                        Tambah Data Nilai
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <?php if (mysqli_num_rows($result) > 0) { ?>
                            <table class="table table-hover align-middle m-0">
                                <thead>
                                    <tr>
                                        <th>NIM</th>
                                        <th>Nama Lengkap</th>
                                        <th>
                                            <a href="<?php echo htmlspecialchars(link_ke(['sort' => $sort_berikut, 'page' => 1])); ?>"
                                               class="text-reset text-decoration-none">
                                                Nilai Akhir
                                                <i class="bi bi-arrow-down <?php echo $sort == 'desc' ? 'opacity-25' : ''; ?>"></i>
                                                <i class="bi bi-arrow-up <?php echo $sort == 'asc' ? 'opacity-25' : ''; ?>"></i>
                                            </a>
                                        </th>
                                        <th>Nilai Huruf</th>
                                        <th>Predikat</th>
                                        <th>Tgl Dibuat</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['nim']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg"
                                                     alt="" class="rounded-circle me-2"
                                                     style="width:32px;height:32px;object-fit:cover">
                                                <span class="fw-medium">
                                                    <?php echo htmlspecialchars($row['nama_lengkap']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['nilai_akhir']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nilai_huruf']); ?></td>
                                        <td><?php echo htmlspecialchars($row['predikat']); ?></td>
                                        <td><?php echo htmlspecialchars($row['entry_date']); ?></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <!-- Tombol Edit -->
                                                <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modal-edit-<?php echo htmlspecialchars($row['nim']); ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <!-- Tombol Hapus menggunakan POST -->
                                                <form action="hapus_nilai.php" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah anda yakin ingin menghapus data ini?');">
                                                    <input type="hidden" name="nim"
                                                           value="<?php echo htmlspecialchars($row['nim']); ?>">
                                                    <button type="submit" class="btn btn-outline-danger" aria-label="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade"
                                         id="modal-edit-<?php echo htmlspecialchars($row['nim']); ?>"
                                         tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="update_nilai.php" method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Data Nilai</h5>
                                                        <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label">NIM</label>
                                                            <input type="text" class="form-control" name="nim"
                                                                   value="<?php echo htmlspecialchars($row['nim']); ?>" readonly>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nama Lengkap</label>
                                                            <input type="text" class="form-control" name="nama_lengkap"
                                                                   value="<?php echo htmlspecialchars($row['nama_lengkap']); ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Nilai Akhir</label>
                                                            <input type="number" min="0" max="100" step="0.01"
                                                                   class="form-control" name="nilai_akhir"
                                                                   value="<?php echo htmlspecialchars($row['nilai_akhir']); ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Update Data</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="p-4 text-center">0 results - Data not found!</div>
                        <?php } ?>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="fs-7 text-body-secondary">
                            Menampilkan <?php echo $dari; ?> sampai <?php echo $sampai; ?> dari <?php echo $total; ?> nilai
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <form method="GET" action="" class="d-flex align-items-center gap-2 m-0">
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <input type="hidden" name="predikat" value="<?php echo htmlspecialchars($predikat_filter); ?>">
                                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">

                                <select name="per_page" class="form-select form-select-sm w-auto"
                                        onchange="this.form.submit()">
                                    <?php foreach ([5, 7, 10, 25, 50] as $pp) { ?>
                                        <option value="<?php echo $pp; ?>"
                                            <?php echo $pp == $per_page ? 'selected' : ''; ?>>
                                            <?php echo $pp; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="fs-7 text-body-secondary">baris</span>
                            </form>

                            <ul class="pagination pagination-sm m-0">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                       href="<?php echo htmlspecialchars(link_ke(['page' => max(1, $page - 1)])); ?>">
                                        &laquo;
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link"
                                           href="<?php echo htmlspecialchars(link_ke(['page' => $i])); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php } ?>

                                <li class="page-item <?php echo $page >= $total_halaman ? 'disabled' : ''; ?>">
                                    <a class="page-link"
                                       href="<?php echo htmlspecialchars(link_ke(['page' => min($total_halaman, $page + 1)])); ?>">
                                        &raquo;
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Data -->
    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="olah_nilai.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Data Nilai</h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label">NIM</label>
                            <input type="text" class="form-control" name="nim"
                                   minlength="10" maxlength="10" pattern="[0-9]{10}"
                                   placeholder="Masukkan 10 digit NIM" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap"
                                   placeholder="Masukkan Nama Lengkap" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nilai Akhir</label>
                            <input type="number" min="0" max="100" step="0.01"
                                   class="form-control" name="nilai_akhir"
                                   placeholder="Contoh: 85.50" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid px-3 text-body-secondary fs-7 pb-3 d-flex justify-content-between">
        <span>Hak cipta &copy; pada Prodi TI - 2026. Seluruh hak cipta dilindungi.</span>
        <span>Pemrograman Web - Semester 5 - Desain menggunakan AdminLTE v4</span>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js" crossorigin="anonymous"></script>
</body>
</html>
