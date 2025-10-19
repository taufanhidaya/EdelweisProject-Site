<?php
if (session_status() === PHP_SESSION_NONE)
  session_start();
?>

<nav class="navbar navbar-expand-lg fixed-top bg-light">
  <div class="container">
    <a class="navbar-brand" href="/"><span style="font-family: 'Brush Script MT', cursive;">Edelweis</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($page === 'home') ? 'active' : '' ?>" href="/">BERANDA</a>
        </li>

        <!-- Dropdown TENTANG -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= (strpos($page, 'tentang') === 0) ? 'active' : '' ?>" href="#"
            data-bs-toggle="dropdown">TENTANG</a>
          <ul class="dropdown-menu">
            <li class="dropdown-submenu">
              <a class="dropdown-item has-submenu" href="#">Profil</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/tentang/profil/visi-misi">Visi & Misi</a></li>
                <li><a class="dropdown-item" href="/tentang/profil/kode-etik">Kode Etik</a></li>
                <li><a class="dropdown-item" href="/tentang/profil/sejarah">Sejarah</a></li>
                <li><a class="dropdown-item" href="/tentang/profil/21-juli">21 Juli</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a class="dropdown-item has-submenu" href="#">Struktur Edelweis</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/tentang/pengurus/struktur">Struktur Organisasi</a></li>
                <li><a class="dropdown-item" href="/tentang/pengurus/ketum">Ketua Umum</a></li>
              </ul>
            </li>
            <li class="dropdown-submenu">
              <a class="dropdown-item has-submenu" href="#">Lain-Lain</a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/tentang/lain-lain/proker">Program Kerja</a></li>
                <li><a class="dropdown-item" href="/tentang/lain-lain/kegiatan">Kegiatan</a></li>
              </ul>
            </li>
          </ul>
        </li>

        <!-- Dropdown DIVISI -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?= (strpos($page, 'divisi') === 0) ? 'active' : '' ?>" href="#"
            data-bs-toggle="dropdown">DIVISI</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/divisi/mountaineering">Mountaineering</a></li>
            <li><a class="dropdown-item" href="/divisi/climbing">Climbing</a></li>
            <li><a class="dropdown-item" href="/divisi/rafting">Rafting</a></li>
            <li><a class="dropdown-item" href="/divisi/ksda">KSDA</a></li>
            <li><a class="dropdown-item" href="/divisi/digitalisasi">Digitalisasi</a></li>
          </ul>
        </li>

        <!-- ✅ Fitur Khusus untuk Anggota yang Login -->
        <?php if (isset($_SESSION['username']) && ($_SESSION['role'] === 'Anggota' || $_SESSION['role'] === 'Admin')): ?>
          <li class="nav-item">
            <a class="nav-link <?= ($page === 'anggota') ? 'active' : '' ?>" href="/anggota">ANGGOTA</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($page === 'arsipan') ? 'active' : '' ?>" href="/arsipan">ARSIPAN</a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- ✅ Status Login -->
      <?php if (isset($_SESSION['username'])): ?>
        <div class="dropdown">
          <a class="btn btn-outline-dark dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <?= htmlspecialchars($_SESSION['username']) ?>
            <small class="text-muted">(<?= htmlspecialchars($_SESSION['role']) ?>)</small>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="/profil">
              <i class="fas fa-user me-2"></i>Profil Saya
            </a></li>
            
            <?php if ($_SESSION['role'] === 'Admin'): ?>
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Panel Admin</h6></li>
              <li><a class="dropdown-item" href="/admin/dashboard">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
              </a></li>
              <li><a class="dropdown-item" href="/admin/users">
                <i class="fas fa-users me-2"></i>Kelola Users
              </a></li>
              <li><a class="dropdown-item" href="/admin/anggota">
                <i class="fas fa-address-book me-2"></i>Data Anggota
              </a></li>
              <li><a class="dropdown-item" href="/admin/kegiatan">
                <i class="fas fa-calendar-alt me-2"></i>Data Kegiatan
              </a></li>
              <li><a class="dropdown-item" href="/admin/isu_lingkungan">
                <i class="fas fa-leaf me-2"></i>Isu Lingkungan
              </a></li>
            <?php endif; ?>

            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="/logout">
              <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="/login" class="btn btn-primary-orange">GET STARTED</a>
      <?php endif; ?>

    </div>
  </div>
</nav>