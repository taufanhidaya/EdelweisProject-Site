<?php
session_start();
require_once __DIR__ . '/../config/connect.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: /profil");
    exit;
}

// Ambil data user dan anggota berdasarkan no_registrasi
$no_registrasi = $_SESSION['no_registrasi'];
$query = "
    SELECT a.*, u.username, j.nm_jurusan 
    FROM anggota a 
    JOIN users u ON a.no_registrasi = u.no_registrasi 
    LEFT JOIN jurusan j ON a.id_jurusan = j.id_jurusan 
    WHERE a.no_registrasi = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $no_registrasi);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Jika data tidak ditemukan
if (!$user_data) {
    die("Data profil tidak ditemukan.");
}

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nm_anggota = $_POST['nm_anggota'];
    $gender = $_POST['gender'];
    $no_hp = $_POST['no_hp'];
    $id_jurusan = $_POST['id_jurusan'];
    $th_in_out = $_POST['th_in_out'];
    $alamat = $_POST['alamat'];
    $status = $_POST['status'];
    
    // Update data anggota
    $update_anggota = $conn->prepare("
        UPDATE anggota 
        SET nm_anggota=?, gender=?, no_hp=?, id_jurusan=?, th_in_out=?, alamat=?, status=? 
        WHERE no_registrasi=?
    ");
    $update_anggota->bind_param(
        "sssissss", 
        $nm_anggota, $gender, $no_hp, $id_jurusan, $th_in_out, $alamat, $status, $no_registrasi
    );
    
    // Update username jika diubah
    if (!empty($_POST['username'])) {
        $new_username = $_POST['username'];
        $update_user = $conn->prepare("UPDATE users SET username=? WHERE no_registrasi=?");
        $update_user->bind_param("ss", $new_username, $no_registrasi);
        $update_user->execute();
    }
    
    // Update password jika diubah
    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_pass = $conn->prepare("UPDATE users SET password=? WHERE no_registrasi=?");
        $update_pass->bind_param("ss", $new_password, $no_registrasi);
        $update_pass->execute();
    }
    
    if ($update_anggota->execute()) {
        $success_message = "Profil berhasil diperbarui!";
        // Refresh data
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
    } else {
        $error_message = "Terjadi kesalahan saat memperbarui profil: " . $conn->error;
    }
}

// Ambil daftar jurusan untuk dropdown
$jurusan_query = $conn->query("SELECT * FROM jurusan");
$jurusan_list = [];
while ($row = $jurusan_query->fetch_assoc()) {
    $jurusan_list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Anggota</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .profile-card {
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 25px;
      margin-top: 30px;
    }
    .profile-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid #eee;
      padding-bottom: 15px;
    }
    .profile-title {
      color: #333;
      font-weight: 600;
    }
    .profile-info {
      margin-bottom: 15px;
    }
    .profile-info label {
      font-weight: 500;
      color: #555;
      width: 150px;
      display: inline-block;
    }
    .profile-info span {
      color: #333;
    }
    .bg-option {
      width: 60px;
      height: 60px;
      display: inline-block;
      margin: 5px;
      border-radius: 8px;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .bg-option:hover {
      transform: scale(1.1);
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="profile-card">
          <div class="profile-header">
            <h2 class="profile-title">PROFIL ANGGOTA</h2>
            <div class="dropdown">
              <button class="btn btn-light" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#bgModal">Ubah Background</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </div>
          </div>

          <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $success_message ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= $error_message ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <div class="row">
            <div class="col-md-6">
              <div class="profile-info">
                <label>Username:</label>
                <span><?= htmlspecialchars($user_data['username'] ?? '') ?></span>
              </div>
              <div class="profile-info">
                <label>Nama Anggota:</label>
                <span><?= htmlspecialchars($user_data['nm_anggota'] ?? '') ?></span>
              </div>
              <div class="profile-info">
                <label>Jurusan:</label>
                <span><?= htmlspecialchars($user_data['nm_jurusan'] ?? '') ?></span>
              </div>
              <div class="profile-info">
                <label>No. Registrasi:</label>
                <span><?= htmlspecialchars($user_data['no_registrasi'] ?? '') ?></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="profile-info">
                <label>Gender:</label>
                <span><?= htmlspecialchars($user_data['gender'] ?? '') ?></span>
              </div>
              <div class="profile-info">
                <label>Status:</label>
                <span><?= htmlspecialchars($user_data['status'] ?? '') ?></span>
              </div>
              <div class="profile-info">
                <label>Tahun Masuk/Keluar:</label>
                <span><?= htmlspecialchars($user_data['th_in_out'] ?? '') ?></span>
              </div>
              <div class="profile-info">
                <label>Alamat:</label>
                <span><?= htmlspecialchars($user_data['alamat'] ?? '') ?></span>
              </div>
            </div>
          </div>

          <div class="text-center mt-4">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
              <i class="fas fa-edit me-2"></i>Edit Profil
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit Profil -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Profil</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="username" name="username" 
                         value="<?= htmlspecialchars($user_data['username'] ?? '') ?>">
                </div>
                <div class="mb-3">
                  <label for="nm_anggota" class="form-label">Nama Anggota</label>
                  <input type="text" class="form-control" id="nm_anggota" name="nm_anggota" 
                         value="<?= htmlspecialchars($user_data['nm_anggota'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                  <label for="gender" class="form-label">Gender</label>
                  <select class="form-select" id="gender" name="gender">
                    <option value="Pria" <?= ($user_data['gender'] ?? '') == 'Pria' ? 'selected' : '' ?>>Pria</option>
                    <option value="Wanita" <?= ($user_data['gender'] ?? '') == 'Wanita' ? 'selected' : '' ?>>Wanita</option>
                    <option value="-" <?= ($user_data['gender'] ?? '') == '-' ? 'selected' : '' ?>>-</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="no_hp" class="form-label">No. HP</label>
                  <input type="text" class="form-control" id="no_hp" name="no_hp" 
                         value="<?= htmlspecialchars($user_data['no_hp'] ?? '') ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="id_jurusan" class="form-label">Jurusan</label>
                  <select class="form-select" id="id_jurusan" name="id_jurusan">
                    <?php foreach ($jurusan_list as $jurusan): ?>
                      <option value="<?= $jurusan['id_jurusan'] ?>" 
                        <?= ($user_data['id_jurusan'] ?? '') == $jurusan['id_jurusan'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jurusan['nm_jurusan']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="th_in_out" class="form-label">Tahun Masuk/Keluar</label>
                  <input type="text" class="form-control" id="th_in_out" name="th_in_out" 
                         value="<?= htmlspecialchars($user_data['th_in_out'] ?? '') ?>">
                </div>
                <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" id="status" name="status">
                    <option value="Aktif" <?= ($user_data['status'] ?? '') == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label">Password Baru (kosongkan jika tidak diubah)</label>
                  <input type="password" class="form-control" id="password" name="password">
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="alamat" class="form-label">Alamat</label>
              <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($user_data['alamat'] ?? '') ?></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Background Options -->
  <div class="modal fade" id="bgModal" tabindex="-1" aria-labelledby="bgModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bgModalLabel">Pilih Background</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <div class="bg-option" style="background: linear-gradient(135deg, #FF4500, #800000);" 
                 onclick="changeBackground('linear-gradient(135deg, #FF4500, #800000)')"></div>
            <div class="bg-option" style="background: linear-gradient(135deg, #1e90ff, #00008b);" 
                 onclick="changeBackground('linear-gradient(135deg, #1e90ff, #00008b)')"></div>
            <div class="bg-option" style="background: linear-gradient(135deg, #32cd32, #006400);" 
                 onclick="changeBackground('linear-gradient(135deg, #32cd32, #006400)')"></div>
            <div class="bg-option" style="background: linear-gradient(135deg, #9370db, #4b0082);" 
                 onclick="changeBackground('linear-gradient(135deg, #9370db, #4b0082)')"></div>
            <div class="bg-option" style="background: linear-gradient(135deg, #ffd700, #ff8c00);" 
                 onclick="changeBackground('linear-gradient(135deg, #ffd700, #ff8c00)')"></div>
            <div class="bg-option" style="background: linear-gradient(135deg, #20b2aa, #008080);" 
                 onclick="changeBackground('linear-gradient(135deg, #20b2aa, #008080)')"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function changeBackground(bgValue) {
      document.body.style.background = bgValue;
      // Simpan pilihan background di localStorage
      try {
        localStorage.setItem('background', bgValue);
      } catch (e) {
        console.log('LocalStorage tidak tersedia');
      }
      // Tutup modal
      var bgModal = bootstrap.Modal.getInstance(document.getElementById('bgModal'));
      bgModal.hide();
    }
    
    // Terapkan background yang dipilih sebelumnya saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
      try {
        const savedBackground = localStorage.getItem('background');
        if (savedBackground) {
          document.body.style.background = savedBackground;
        }
      } catch (e) {
        console.log('LocalStorage tidak tersedia');
      }
    });
  </script>
</body>
</html>