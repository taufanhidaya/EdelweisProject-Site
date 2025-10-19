<section class="content-section bg-secondary text-white py-5">
    <div class="container mt-5">
        <h2 class="section-title mb-4">Daftar Anggota UKM-PA Edelweis</h2>

        <?php
        include "config/connect.php";
        $role = $_SESSION['role'] ?? '';

        // Search functionality
        $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
        $search_query = '';
        if (!empty($search)) {
            $search_query = " WHERE nm_anggota LIKE '%$search%' OR no_registrasi LIKE '%$search%' OR gender LIKE '%$search%' OR alamat LIKE '%$search%' OR status LIKE '%$search%'";
        }

        // Query untuk mengambil semua data anggota dengan search
        $result = mysqli_query($conn, "SELECT * FROM anggota $search_query ORDER BY no_registrasi");

        // Fungsi untuk mengkonversi angka romawi ke integer
        function roman_to_int($roman)
        {
            $romans = ['I' => 1, 'V' => 5, 'X' => 10, 'L' => 50, 'C' => 100, 'D' => 500, 'M' => 1000];
            $result = 0;
            $prev = 0;
            for ($i = strlen($roman) - 1; $i >= 0; $i--) {
                $current = $romans[$roman[$i]] ?? 0;
                if ($current < $prev) {
                    $result -= $current;
                } else {
                    $result += $current;
                }
                $prev = $current;
            }
            return $result;
        }

        // Fungsi untuk mengekstrak angkatan dari no_registrasi
        function extract_angkatan($no_registrasi) {
            // Jika no_registrasi mengandung "PENDIRI" (case insensitive)
            if (stripos($no_registrasi, 'PENDIRI') !== false) {
                return 'PENDIRI';
            }
            
            // Ekstrak angka romawi dari format seperti "E. 106/XXIII/PNL"
            if (preg_match('/\/([IVXLCDM]+)\//', $no_registrasi, $matches)) {
                $roman = $matches[1];
                $angkatan_number = roman_to_int($roman);
                return "ANGKATAN " . $angkatan_number;
            }
            
            // Jika tidak sesuai format, masukkan ke "LAINNYA"
            return 'LAINNYA';
        }

        // Kelompokkan data berdasarkan angkatan
        $grouped_data = [];
        $total_records = 0;

        while ($row = mysqli_fetch_assoc($result)) {
            // Pastikan status selalu ada, dengan default 'Aktif'
            $row['status'] = $row['status'] ?? 'Aktif';
            
            $angkatan = extract_angkatan($row['no_registrasi']);
            $grouped_data[$angkatan][] = $row;
            $total_records++;
        }

        // Urutkan grup: PENDIRI pertama, kemudian angkatan berdasarkan nomor
        uksort($grouped_data, function($a, $b) {
            if ($a == 'PENDIRI') return -1;
            if ($b == 'PENDIRI') return 1;
            if ($a == 'LAINNYA') return 1;
            if ($b == 'LAINNYA') return -1;
            
            // Ekstrak nomor angkatan untuk sorting
            $num_a = (int) str_replace('ANGKATAN ', '', $a);
            $num_b = (int) str_replace('ANGKATAN ', '', $b);
            
            return $num_a - $num_b;
        });

        // Search and Filter Form
        echo '<div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Cari nama, no registrasi, gender, alamat, atau status..." value="' . htmlspecialchars($search) . '">
                        <button class="btn btn-primary" type="submit">Cari</button>';

                        if (!empty($search)) {
                            echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn btn-secondary">Reset</a>';
                        }
                    
                        echo '      
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-0 mt-2"><strong>Total Data: ' . $total_records . '</strong></p>
            </div>
        </div>';

        // Tampilkan data berdasarkan kelompok angkatan
        if (!empty($grouped_data)) {
            $overall_counter = 1;
            
            foreach ($grouped_data as $angkatan => $members) {
                // Header untuk setiap angkatan
                echo "<div class='mb-4'>
                    <div class='card border-warning'>
                        <div class='card-header bg-warning text-dark'>
                            <h5 class='mb-0'>
                                <i class='bi bi-people-fill me-2'></i>$angkatan 
                                <span class='badge bg-dark ms-2'>" . count($members) . " Anggota</span>
                            </h5>
                        </div>
                        <div class='card-body p-0'>
                            <div class='table-responsive'>
                                <table class='table table-hover align-middle mb-0'>
                                    <thead style='background-color: #f8f9fa;'>
                                        <tr class='text-nowrap'>
                                            <th class='text-center'>No</th>
                                            <th>Foto</th>
                                            <th>Nama Anggota</th>
                                            <th>No. Registrasi</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Jurusan</th>
                                            <th>Tahun Masuk/Keluar</th>
                                            <th>Alamat</th>
                                            <th>Status</th>";

                // Hanya tampilkan kolom Aksi untuk admin
                if ($role === 'admin') {
                    echo "<th>Aksi</th>";
                }

                echo "</tr>
                                    </thead>
                                    <tbody>";

                foreach ($members as $row) {
                    // Tambahkan warna background jika status Di Pecat
                    $rowClass = ($row['status'] == 'Di pecat') ? 'style="background-color: #ffe6e6;"' : '';

                    echo "<tr $rowClass>
                        <td class='text-center'>" . $overall_counter++ . "</td>
                        <td>";

                    if (!empty($row['media_foto'])) {
                        echo "<img src='/media/img/anggota/" . htmlentities($row['media_foto'] ?? '') . "' alt='Foto'
                              style='width: 80px; height: 80px; object-fit: cover; border: 2px solid #FFD700;'>";
                    } else {
                        if (strtolower($row['gender'] ?? '') == "pria") {
                            echo "<img src='/media/img/Avatar.jpeg' alt='Default Boy'
                                  style='width: 80px; height: 80px; object-fit: cover; border: 2px solid #FFD700;'>";
                        } else {
                            echo "<img src='/media/img/Avatar.jpeg' alt='Default Girl'
                                  style='width: 80px; height: 80px; object-fit: cover; border: 2px solid #FFD700;'>";
                        }
                    }

                    echo "</td>
                        <td class='text-start'>" . htmlentities($row['nm_anggota'] ?? '') . "</td>
                        <td>" . htmlentities($row['no_registrasi'] ?? '') . "</td>
                        <td>" . htmlentities($row['gender'] ?? '') . "</td>
                        <td>" . htmlentities($row['nm_jurusan'] ?? '') . "</td>
                        <td>" . htmlentities($row['th_in_out'] ?? '') . "</td>
                        <td class='text-start'>" . htmlentities($row['alamat'] ?? '') . "</td>
                        <td>";

                    // Tampilkan status dengan badge berwarna
                    if ($row['status'] == 'Aktif') {
                        echo "<span class='badge bg-success'>Aktif</span>";
                    } else if ($row['status'] == 'Tidak Aktif') {
                        echo "<span class='badge bg-warning text-dark'>Tidak Aktif</span>";
                    } else {
                        echo "<span class='badge bg-danger'>Di Pecat</span>";
                    }

                    echo "</td>";

                    // Hanya tampilkan kolom aksi untuk admin
                    if ($role === 'admin') {
                        echo "<td>
            <button type='button' 
                class='btn btn-warning btn-sm me-1 btn-edit-anggota' 
                data-id='{$row['id_anggota']}'>
                <i class='bi bi-pencil-square'></i>
            </button>
            <a href='proses/hapus_anggota.php?id={$row['id_anggota']}' 
                class='btn btn-danger btn-sm me-1' 
                onclick='return confirm(\"Apakah Anda yakin ingin menghapus anggota ini?\")'>
                <i class='bi bi-trash'></i>
            </a>
            
            <div class='dropdown d-inline-block'>
                <button class='btn btn-secondary btn-sm dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                    <i class='bi bi-sliders'></i>
                </button>
                <ul class='dropdown-menu'>";

                        // Only show status options that are different from current status
                        if ($row['status'] != 'Aktif') {
                            echo "<li><a class='dropdown-item text-success' href='proses/ubah_status.php?id={$row['id_anggota']}&status=Aktif' 
                        onclick='return confirm(\"Ubah status menjadi Aktif?\")'>
                        <i class='bi bi-check-circle-fill me-2'></i>Aktif
                    </a></li>";
                        }

                        if ($row['status'] != 'Tidak Aktif') {
                            echo "<li><a class='dropdown-item text-warning' href='proses/ubah_status.php?id={$row['id_anggota']}&status=Tidak Aktif' 
                        onclick='return confirm(\"Ubah status menjadi Tidak Aktif?\")'>
                        <i class='bi bi-dash-circle-fill me-2'></i>Tidak Aktif
                    </a></li>";
                        }

                        if ($row['status'] != 'Di pecat') {
                            echo "<li><a class='dropdown-item text-danger' href='proses/ubah_status.php?id={$row['id_anggota']}&status=Di pecat' 
                        onclick='return confirm(\"Ubah status menjadi Di Pecat?\")'>
                        <i class='bi bi-x-circle-fill me-2'></i>Di Pecat
                    </a></li>";
                        }

                        echo "</ul>
            </div>
        </td>";
                    }

                    echo "</tr>";
                }
                echo "</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='alert alert-info text-center'>
                <i class='bi bi-info-circle'></i> 
                Tidak ada data anggota yang ditemukan.
              </div>";
        }
        ?>
    </div>
</section>