<?php
// index.php - Router utama dengan Authentication
// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: tampilkan error di dev (aktifkan saat debugging)
// ini jangan diaktifkan di production
// ini hanya contoh; uncomment jika butuh debugging lokal
// ini_set('display_errors', 1); error_reporting(E_ALL);

// Logout handler (tetap sederhana)
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /");
    exit;
}

// Ambil parameter `page`, default 'home'
$page = isset($_GET['page']) ? trim($_GET['page'], '/') : 'home';

// Sanitasi dasar: izinkan huruf, angka, dash, underscore, slash, & (untuk query yang mungkin dipakai)
$page = preg_replace('/[^a-z0-9_\-\/&]/i', '', $page);

// OPTIONAL: hindari directory traversal eksplisit
if (strpos($page, '..') !== false) {
    http_response_code(400);
    echo "<h1>400 - Bad Request</h1>";
    exit;
}

/**
 * AUTHENTICATION LOGIC
 * Fungsi untuk mengecek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['id_users']) && !empty($_SESSION['id_users']);
}

/**
 * Fungsi untuk mengecek apakah user adalah admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

/**
 * Fungsi untuk mengecek apakah user adalah anggota (sudah login)
 */
function isAnggota() {
    return isLoggedIn() && isset($_SESSION['role']) && ($_SESSION['role'] === 'Anggota' || $_SESSION['role'] === 'Admin');
}

/**
 * Redirect ke login page
 */
function redirectToLogin($returnUrl = '') {
    $loginUrl = '/login';
    if ($returnUrl) {
        $loginUrl .= '?return=' . urlencode($returnUrl);
    }
    header("Location: " . $loginUrl);
    exit;
}

/**
 * Redirect ke home page
 */
function redirectToHome() {
    header("Location: /");
    exit;
}

/**
 * ROUTES: whitelist mapping dengan level akses
 * Keys = route (yang akan dipakai di ?page=...)
 * Values = array dengan:
 *   - 'path' => path file relatif
 *   - 'auth' => level authentication ('public', 'anggota', 'admin')
 */
$allowed = [
    // PUBLIC ROUTES (dapat diakses tanpa login)
    'home'                        => ['path' => 'page/home.php', 'auth' => 'public'],
    'login'                       => ['path' => 'login.php', 'auth' => 'public'],
    'artikel'                     => ['path' => 'page/artikel.php', 'auth' => 'public'],
    // 'register'                    => ['path' => 'page/register.php', 'auth' => 'public'],
    
    'tentang/pengurus/ketum'      => ['path' => 'page/tentang/pengurus/ketum.php', 'auth' => 'public'],
    'tentang/pengurus/struktur'   => ['path' => 'page/tentang/pengurus/struktur.php', 'auth' => 'public'],

    'tentang/profil/21-juli'      => ['path' => 'page/tentang/profil/21juli.php', 'auth' => 'public'],
    'tentang/profil/kode-etik'    => ['path' => 'page/tentang/profil/kodeEtik.php', 'auth' => 'public'],
    'tentang/profil/sejarah'      => ['path' => 'page/tentang/profil/sejarah.php', 'auth' => 'public'],
    'tentang/profil/visi-misi'    => ['path' => 'page/tentang/profil/visi&misi.php', 'auth' => 'public'],
    'tentang/profil/kegiatan'     => ['path' => 'page/tentang/profil/kegiatan.php', 'auth' => 'Anggota'],
    'tentang/profil/proker'       => ['path' => 'page/tentang/profil/proker.php', 'auth' => 'Anggota'],

    'arsip/administrasi'          => ['path' => 'page/arsip/administrasi.php', 'auth' => 'Anggota'],
    'arsip/galery'                => ['path' => 'page/arsip/galery.php', 'auth' => 'Anggota'],

    'tentang/lain-lain/proker'    => ['path' => 'page/tentang/lain-lain/proker.php', 'auth' => 'public'],
    'tentang/lain-lain/kegiatan'  => ['path' => 'page/tentang/lain-lain/kegiatan.php', 'auth' => 'public'],

    'divisi/climbing'             => ['path' => 'page/divisi/climbing.php', 'auth' => 'public'],
    'divisi/digitalisasi'         => ['path' => 'page/divisi/digitalisasi.php', 'auth' => 'public'],
    'divisi/ksda'                 => ['path' => 'page/divisi/ksda.php', 'auth' => 'public'],
    'divisi/mountaineering'       => ['path' => 'page/divisi/mountaineering.php', 'auth' => 'public'],
    'divisi/rafting'              => ['path' => 'page/divisi/rafting.php', 'auth' => 'public'],

    // ANGGOTA ROUTES (perlu login sebagai Anggota atau Admin)
    'anggota'                     => ['path' => 'page/anggota.php', 'auth' => 'Anggota'],
    'arsipan'                     => ['path' => 'page/arsipan.php', 'auth' => 'Anggota'],
    'profil'                      => ['path' => 'page/profil.php', 'auth' => 'Anggota'],

    // ADMIN ROUTES (perlu login sebagai admin)
    'admin/dashboard'             => ['path' => 'admin/sections/dashboard.php', 'auth' => 'Admin'],
    'admin/users'                 => ['path' => 'admin/sections/users.php', 'auth' => 'Admin'],

    // CONTENT SECTION
    'admin/anggota'               => ['path' => 'admin/sections/content/anggota.php', 'auth' => 'Admin'],
    'admin/kegiatan'              => ['path' => 'admin/sections/content/kegiatan.php', 'auth' => 'Admin'],
    'admin/isu_lingkungan'        => ['path' => 'admin/sections/content/isulingkungan.php', 'auth' => 'Admin'],
    'admin/arsip_data'            => ['path' => 'admin/sections/content/dokumen.php', 'auth' => 'Admin'],

    // SERVICE SECTION
    'admin/upload_kegiatan'       => ['path' => 'admin/sections/service/uploadKegiatan.php', 'auth' => 'Admin'],
    'admin/upload_anggota'        => ['path' => 'admin/sections/service/uploadAnggota.php', 'auth' => 'Admin'],
    'admin/upload_isu_lingkungan' => ['path' => 'admin/sections/service/uploadIsuling.php', 'auth' => 'Admin'],

    // ADMIN OTHER
    'admin/team'                  => ['path' => 'admin/sections/team.php', 'auth' => 'Admin'],
    'admin/setting'               => ['path' => 'admin/sections/setting.php', 'auth' => 'Admin'],
    'admin/referensi_jurusan'     => ['path' => 'admin/referensi_jurusan.php', 'auth' => 'Admin'],
];

/**
 * Jika route tidak ada di whitelist -> redirect ke home
 */
if (!array_key_exists($page, $allowed)) {
    redirectToHome();
}

/**
 * AUTHENTICATION CHECK
 * Cek apakah user memiliki akses ke halaman ini
 */
$route_config = $allowed[$page];
$required_auth = $route_config['auth'];

switch ($required_auth) {
    case 'admin':
        if (!isAdmin()) {
            // Siapapun yang BUKAN admin (termasuk anggota dan public) tidak bisa akses
            if (!isLoggedIn()) {
                // Belum login sama sekali, redirect ke login
                redirectToLogin($page);
            } else {
                // Sudah login tapi bukan admin (anggota/role lain), redirect ke home
                redirectToHome();
            }
        }
        break;
        
    case 'anggota':
        if (!isAnggota()) {
            if (!isLoggedIn()) {
                // Belum login, redirect ke login
                redirectToLogin($page);
            } else {
                // Login tapi bukan anggota/admin, redirect ke home
                redirectToHome();
            }
        }
        break;
        
    case 'public':
    default:
        // Tidak perlu autentikasi, semua boleh akses
        break;
}

/**
 * Resolve target path and safety check
 */
$target_rel = $route_config['path'];
$target = __DIR__ . '/' . $target_rel;

// Pastikan file benar-benar ada
if (!file_exists($target)) {
    error_log("[Router] Missing file for route '{$page}': {$target}");
    redirectToHome();
}

// Optional: extra safety — pastikan target berada di dalam project root (prevent traversal)
$realTarget = realpath($target);
$root = realpath(__DIR__);
if ($realTarget === false || strpos($realTarget, $root) !== 0) {
    error_log("[Router] Unsafe include attempt: {$target}");
    redirectToHome();
}

/**
 * Capture output dari section yang diminta
 */
ob_start();
require_once $realTarget;
$content = ob_get_clean();

/**
 * Routes yang berdiri sendiri (tanpa template)
 */
$standalone_routes = [
    'login' => true,
    // Tambahkan route lain yang perlu berdiri sendiri
    // 'register' => true,
];

/**
 * Admin layout behavior dengan raw routes
 */
$admin_raw = [
    // Tambahkan route admin yang perlu dijalankan raw (tanpa template)
    // Contoh: 'admin/api/users' => true
];

$is_admin_route = (stripos($page, 'admin/') === 0);
$is_raw_admin = array_key_exists($page, $admin_raw);
$is_standalone = array_key_exists($page, $standalone_routes);

/**
 * Render content berdasarkan tipe route
 */
if ($is_standalone) {
    // Route yang berdiri sendiri (seperti login)
    echo $content;
    exit;
}

if ($is_admin_route && !$is_raw_admin) {
    // Admin route dengan template
    $mainA = __DIR__ . '/admin/mainA.php';
    if (!file_exists($mainA)) {
        error_log("[Router] Missing admin template: {$mainA}");
        redirectToHome();
    }
    require_once $mainA;
    exit;
}

if ($is_admin_route && $is_raw_admin) {
    // Admin raw output (API endpoint, dll)
    echo $content;
    exit;
}

// Public route dengan template
$mainPublic = __DIR__ . '/main.php';
if (!file_exists($mainPublic)) {
    echo $content;
    exit;
}

require_once $mainPublic;
exit;