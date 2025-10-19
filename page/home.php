<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

// Include database connection and helper functions from upload script
include 'config/connect.php';

// Helper functions untuk path (dari kode upload)
function base_web_prefix()
{
    return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}

function web_path($rel)
{
    $prefix = base_web_prefix();
    return $prefix . '/' . ltrim(str_replace('\\', '/', $rel), '/');
}

function fs_path($rel)
{
    $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
    $prefix = base_web_prefix();
    $rel = ltrim(str_replace('\\', '/', $rel), '/');
    return $docroot . $prefix . '/' . $rel;
}

// Helper function untuk memotong teks
function truncate_text($text, $length = 120)
{
    if (strlen($text) <= $length)
        return $text;
    return substr($text, 0, $length) . '...';
}

// Ambil data kegiatan dari database (Persiapan dan Terlaksana)
$kegiatan_list = [];
$query_kegiatan = "SELECT k.*, d.nm_divisi, p.periode AS nm_periode 
                   FROM kegiatan k 
                   LEFT JOIN divisi d ON k.id_divisi = d.id_divisi 
                   LEFT JOIN periode p ON k.id_periode = p.id_periode 
                   WHERE k.status IN ('Persiapan', 'Terlaksana')
                   ORDER BY 
                     CASE WHEN k.status = 'Persiapan' THEN 1 ELSE 2 END,
                     k.tgl_mulai DESC 
                   LIMIT 6";
$result_kegiatan = $conn->query($query_kegiatan);
if ($result_kegiatan) {
    while ($row = $result_kegiatan->fetch_assoc()) {
        $kegiatan_list[] = $row;
    }
}

// Ambil data berita/isu lingkungan dari database
$isu_lingkungan_list = [];
$query_isu = "SELECT * FROM berita 
               ORDER BY tgl_upload DESC 
               LIMIT 6";
$result_isu = $conn->query($query_isu);
if ($result_isu) {
    while ($row = $result_isu->fetch_assoc()) {
        $isu_lingkungan_list[] = $row;
    }
}

$showAdminModal = isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && !isset($_SESSION['akses_admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akses_admin']) && $_SESSION['role'] === 'admin') {
    $_SESSION['akses_admin'] = $_POST['akses_admin'];
    header("Location: /home");
    exit;
}
?>

<style>
    /* Kegiatan Card Styles */
    .kegiatan-card {
        position: relative;
        height: 400px;
        border-radius: 15px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .kegiatan-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .kegiatan-image {
        position: relative;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .kegiatan-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .default-image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 4rem;
        color: white;
        opacity: 0.7;
    }

    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom,
                rgba(0, 0, 0, 0.1) 0%,
                rgba(0, 0, 0, 0.4) 50%,
                rgba(0, 0, 0, 0.8) 100%);
    }

    .kegiatan-content {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 30px 25px;
        color: white;
        z-index: 2;
    }

    .kegiatan-date {
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        display: inline-block;
        margin-bottom: 15px;
    }

    .kegiatan-date.persiapan {
        background: #28a745;
        /* Hijau untuk kegiatan yang akan datang */
    }

    .kegiatan-date.terlaksana {
        background: var(--primary-orange);
        /* Orange untuk kegiatan yang sudah terlaksana */
    }

    .kegiatan-title {
        font-size: 1.4rem;
        font-weight: bold;
        margin-bottom: 10px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        line-height: 1.3;
    }

    .kegiatan-preview {
        font-size: 0.95rem;
        margin-bottom: 15px;
        opacity: 0.9;
        line-height: 1.4;
    }

    .btn-read-more {
        background: transparent;
        border: 2px solid white;
        color: white;
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-read-more:hover {
        background: white;
        color: var(--dark-gray);
        transform: scale(1.05);
    }

    /* Environmental Issues Card Styles */
    .isu-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        /* Added for positioning corner link */
    }

    .isu-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    /* Corner Link Styles */
    .corner-link {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: var(--primary-orange);
        text-decoration: none;
        transition: all 0.3s ease;
        z-index: 10;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .corner-link:hover {
        background: var(--primary-orange);
        color: white;
        transform: scale(1.1);
        text-decoration: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    /* Article Type Badge */
    .article-type-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
        z-index: 5;
    }

    .article-type-badge.external {
        background: rgba(40, 167, 69, 0.9);
    }

    .article-type-badge.internal {
        background: rgba(255, 107, 53, 0.9);
    }

    .isu-image {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
    }

    .isu-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .isu-card:hover .isu-image img {
        transform: scale(1.1);
    }

    .isu-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        opacity: 0.8;
    }

    .isu-content {
        padding: 25px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .isu-date {
        color: #999;
        font-size: 0.85rem;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .isu-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--dark-gray);
        margin-bottom: 15px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .isu-preview {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
        flex-grow: 1;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .isu-tags {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .isu-tag {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Button Action Area */
    .isu-actions {
        margin-top: auto;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-read-more-isu {
        background: linear-gradient(135deg, var(--primary-orange), #e63946);
        color: white;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        justify-content: center;
        border: none;
        cursor: pointer;
    }

    .btn-read-more-isu:hover {
        background: linear-gradient(135deg, #e63946, #dc3545);
        color: white;
        text-decoration: none;
        transform: translateX(5px);
    }

    .btn-read-more-isu i {
        transition: transform 0.3s ease;
    }

    .btn-read-more-isu:hover i {
        transform: translateX(3px);
    }

    /* External Link Button Variant */
    .btn-external-link {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .btn-external-link:hover {
        background: linear-gradient(135deg, #20c997, #17a2b8);
        transform: scale(1.02);
    }

    /* Default Feature Card Styles (fallback) */
    .feature-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-orange), #e63946);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 2rem;
        color: white;
    }

    .feature-card h3 {
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--dark-gray);
        margin-bottom: 15px;
    }

    .feature-card p {
        color: #666;
        line-height: 1.6;
        margin: 0;
    }

    /* No Issues Styles */
    .no-issues-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 300px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        border: 2px dashed #dee2e6;
    }

    .no-issues-content {
        text-align: center;
        max-width: 500px;
        padding: 40px 20px;
    }

    .no-issues-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #6c757d, #495057);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 2.5rem;
        color: white;
        opacity: 0.8;
    }

    .no-issues-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--dark-gray);
        margin-bottom: 15px;
    }

    .no-issues-text {
        font-size: 1rem;
        color: #666;
        line-height: 1.6;
    }

    /* Modal Detail Styles */
    .kegiatan-detail {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .detail-header {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .detail-image {
        width: 100%;
        max-height: 300px;
        object-fit: cover;
        border-radius: 10px;
    }

    .detail-info {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        font-size: 0.9rem;
    }

    .info-item {
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-item i {
        color: var(--primary-orange);
    }

    .detail-description {
        line-height: 1.6;
        color: #555;
    }

    .detail-video {
        text-align: center;
        margin-top: 20px;
    }

    .detail-video video {
        width: 100%;
        max-height: 400px;
        border-radius: 10px;
    }

    /* No Kegiatan Styles */
    .no-kegiatan-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        border: 2px dashed #dee2e6;
    }

    .no-kegiatan-content {
        text-align: center;
        max-width: 600px;
        padding: 40px 20px;
    }

    .no-kegiatan-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #6c757d, #495057);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 3rem;
        color: white;
        opacity: 0.8;
    }

    .no-kegiatan-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: var(--dark-gray);
        margin-bottom: 20px;
    }

    .no-kegiatan-text {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .no-kegiatan-info {
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }

    .info-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: white;
        padding: 15px 25px;
        border-radius: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        font-size: 0.95rem;
        color: #555;
        max-width: 400px;
        width: 100%;
    }

    .info-box i {
        color: var(--primary-orange);
        font-size: 1.1rem;
        width: 20px;
        text-align: center;
    }

    /* Responsive untuk no kegiatan */
    @media (max-width: 768px) {
        .no-kegiatan-container {
            min-height: 350px;
        }

        .no-kegiatan-icon {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
            margin-bottom: 25px;
        }

        .no-kegiatan-title {
            font-size: 1.5rem;
        }

        .no-kegiatan-text {
            font-size: 1rem;
        }

        .info-box {
            padding: 12px 20px;
            font-size: 0.9rem;
        }

        .isu-card {
            margin-bottom: 20px;
        }

        .isu-image {
            height: 180px;
        }

        .isu-content {
            padding: 20px;
        }

        .isu-title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1.1rem;
        }

        .hero-buttons {
            flex-direction: column;
            align-items: center;
        }

        .kegiatan-card {
            height: 350px;
        }

        .kegiatan-content {
            padding: 20px;
        }

        .kegiatan-title {
            font-size: 1.2rem;
        }

        .kegiatan-preview {
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 2rem;
        }
    }
</style>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="hero-content">
        <h1>DARI ALAM KAMI BELAJAR!</h1>
        <p>Alam mengajarkan kami kebijaksanaan, kerasnya medan membentuk kami menjadi tangguh. Setiap suka dan duka,
            mengikat kami dalam persaudaraan.</p>
        <div class="hero-buttons">
            <a href="#video" class="btn-watch-video">
                <i class="fas fa-play"></i> Watch Video
            </a>
        </div>
    </div>
</section>

<!-- Berita Kegiatan Section -->
<section id="about" class="content-section">
    <div class="container">
        <h2 class="section-title">Berita Kegiatan</h2>

        <?php if (!empty($kegiatan_list)): ?>
            <div class="row" id="kegiatan-container">
                <?php foreach ($kegiatan_list as $index => $kegiatan): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="kegiatan-card" data-kegiatan-id="<?= $kegiatan['id_kegiatan'] ?>">
                            <!-- Background Image -->
                            <div class="kegiatan-image">
                                <?php if (!empty($kegiatan['media_foto']) && file_exists(fs_path($kegiatan['media_foto']))): ?>
                                    <img src="<?= htmlspecialchars(web_path($kegiatan['media_foto'])) ?>"
                                        alt="<?= htmlspecialchars($kegiatan['nm_kegiatan']) ?>">
                                <?php else: ?>
                                    <div class="default-image">
                                        <i class="fas fa-mountain"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="image-overlay"></div>
                                <div class="kegiatan-content">
                                    <div class="kegiatan-date <?= strtolower($kegiatan['status']) ?>">
                                        <?php
                                        $dateText = $kegiatan['tgl_kegiatan'] ?: date('d/M/Y', strtotime($kegiatan['tgl_mulai']));
                                        if ($kegiatan['status'] === 'Persiapan') {
                                            echo "Akan Datang - " . $dateText;
                                        } else {
                                            echo $dateText;
                                        }
                                        ?>
                                    </div>
                                    <h3 class="kegiatan-title"><?= htmlspecialchars($kegiatan['nm_kegiatan']) ?></h3>
                                    <p class="kegiatan-preview">
                                        <?php
                                        $defaultDesc = $kegiatan['status'] === 'Persiapan'
                                            ? 'Kegiatan ' . $kegiatan['nm_kegiatan'] . ' akan segera dilaksanakan. Pantau terus informasi terbaru!'
                                            : 'Kegiatan ' . $kegiatan['nm_kegiatan'] . ' telah dilaksanakan dengan sukses.';
                                        echo htmlspecialchars(truncate_text($kegiatan['deskripsi'] ?: $defaultDesc));
                                        ?>
                                    </p>
                                    <button class="btn-read-more" onclick="expandKegiatan(<?= $kegiatan['id_kegiatan'] ?>)">
                                        <?= $kegiatan['status'] === 'Persiapan' ? 'Info Selengkapnya' : 'Baca Selengkapnya' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Tampilan jika tidak ada kegiatan -->
            <div class="row">
                <div class="col-12">
                    <div class="no-kegiatan-container">
                        <div class="no-kegiatan-content">
                            <div class="no-kegiatan-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h3 class="no-kegiatan-title">Belum Ada Kegiatan Pada Periode Ini</h3>
                            <p class="no-kegiatan-text">
                                Saat ini belum ada kegiatan yang terjadwal atau telah dilaksanakan.
                                Pantau terus website kami untuk mendapatkan informasi terbaru tentang kegiatan-kegiatan
                                UKM-PA Edelweis.
                            </p>
                            <div class="no-kegiatan-info">
                                <div class="info-box">
                                    <i class="fas fa-bell"></i>
                                    <span>Notifikasi kegiatan akan diupdate secara berkala</span>
                                </div>
                                <div class="info-box">
                                    <i class="fas fa-calendar-plus"></i>
                                    <span>Kegiatan baru sedang dalam tahap perencanaan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Berita/Isu Lingkungan -->
<section id="services" class="content-section" style="background: white;">
    <div class="container">
        <h2 class="section-title">ARTIKEL</h2>

        <?php if (!empty($isu_lingkungan_list)): ?>
            <div class="row">
                <?php foreach ($isu_lingkungan_list as $index => $berita): ?>
                    <?php
                    // Determine if it's an external link or internal article
                    $isExternalLink = !empty($berita['link_eksternal']) && filter_var($berita['link_eksternal'], FILTER_VALIDATE_URL);
                    $hasCornerLink = !empty($berita['link_pojok']); // Field untuk link pojok kanan atas
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="isu-card">
                            <!-- Article Type Badge -->
                            <div class="article-type-badge <?= $isExternalLink ? 'external' : 'internal' ?>">
                                <?= $isExternalLink ? 'Eksternal' : 'Artikel' ?>
                            </div>

                            <!-- Corner Link (if exists) -->
                            <?php if ($hasCornerLink && filter_var($berita['link_pojok'], FILTER_VALIDATE_URL)): ?>
                                <a href="<?= htmlspecialchars($berita['link_pojok']) ?>" class="corner-link" target="_blank"
                                    rel="noopener noreferrer" title="Link Terkait"
                                    onclick="trackLinkClick('corner', <?= $berita['id_berita'] ?>)">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            <?php endif; ?>

                            <!-- Image Section -->
                            <div class="isu-image">
                                <?php if (!empty($berita['media_foto']) && file_exists(fs_path($berita['media_foto']))): ?>
                                    <img src="<?= htmlspecialchars(web_path($berita['media_foto'])) ?>"
                                        alt="<?= htmlspecialchars($berita['judul_berita']) ?>">
                                <?php else: ?>
                                    <div class="isu-image-placeholder">
                                        <i class="fas fa-leaf"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content Section -->
                            <div class="isu-content">
                                <div class="isu-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= date('d M Y', strtotime($berita['tgl_upload'])) ?>
                                </div>

                                <h3 class="isu-title"><?= htmlspecialchars($berita['judul_berita']) ?></h3>

                                <p class="isu-preview">
                                    <?= htmlspecialchars(truncate_text($berita['deskripsi'] ?? 'Baca artikel lengkap untuk mengetahui informasi lebih detail.', 150)) ?>
                                </p>

                                <?php if (!empty($berita['sumber_berita'])): ?>
                                    <div class="isu-tags">
                                        <span class="isu-tag"><?= htmlspecialchars($berita['sumber_berita']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <div class="isu-actions">
                                    <?php if ($isExternalLink): ?>
                                        <!-- External Link Button -->
                                        <a href="<?= htmlspecialchars($berita['link_eksternal']) ?>"
                                            class="btn-read-more-isu btn-external-link" target="_blank" rel="noopener noreferrer"
                                            onclick="trackLinkClick('external', <?= $berita['id_berita'] ?>)">
                                            Buka Link <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    <?php else: ?>
                                        <!-- Internal Article Button -->
                                        <a href="artikel=<?= $berita['id_berita'] ?>" class="btn-read-more-isu"
                                            onclick="trackLinkClick('internal', <?= $berita['id_berita'] ?>)">
                                            Selengkapnya <i class="fas fa-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Tampilan jika tidak ada isu lingkungan -->
            <div class="row">
                <div class="col-12">
                    <div class="no-issues-container">
                        <div class="no-issues-content">
                            <div class="no-issues-icon">
                                <i class="fas fa-leaf"></i>
                            </div>
                            <h3 class="no-issues-title">Belum Ada Berita</h3>
                            <p class="no-issues-text">
                                Saat ini belum ada berita yang dipublikasikan.
                                Pantau terus untuk mendapatkan informasi terbaru tentang isu-isu lingkungan dan berita
                                terkini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section>
    <div class="container">
        <div class="section-title">Pengumuman</div>

        <div class="row">
            <div class="col-12">
                <div class="no-kegiatan-container">
                    <div class="no-kegiatan-content">
                        <div class="no-kegiatan-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3 class="no-kegiatan-title">Belum Ada Pengumuman Saat ini!!!</h3>
                        <p class="no-kegiatan-text">
                            Saat ini belum ada pengumuman yang tersedia.
                            Pantau terus website kami untuk mendapatkan informasi terbaru seputar kegiatan-kegiatan di
                            UKM-PA <span class="fw-bold" style="font-family: 'Brush Script MT', cursive; font-size: 1.6rem;">Edelweis</span>.
                        </p>
                        <div class="no-kegiatan-info">
                            <div class="info-box">
                                <i class="fas fa-bell"></i>
                                <span>Notifikasi kegiatan akan diupdate secara berkala</span>
                            </div>
                            <div class="info-box">
                                <i class="fas fa-calendar-plus"></i>
                                <span>Kegiatan baru sedang dalam tahap perencanaan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal untuk Detail Kegiatan dan Berita -->
<div class="modal fade" id="kegiatanDetailModal" tabindex="-1" aria-labelledby="kegiatanDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kegiatanDetailLabel">Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="kegiatanDetailContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<?php if ($showAdminModal): ?>
    <div class="modal fade" id="adminChoiceModal" tabindex="-1" aria-labelledby="adminChoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Selamat Datang Admin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Anda login sebagai <strong>Admin</strong>. Silakan pilih mode tampilan:
                </div>
                <div class="modal-footer">
                    <a href="/admin/dashboard" class="btn btn-warning">Masuk Dashboard</a>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="akses_admin" value="publik">
                        <button type="submit" class="btn btn-secondary">Tampilan Publik</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Data kegiatan dan berita untuk JavaScript
    const kegiatanData = <?= json_encode($kegiatan_list) ?>;
    const beritaData = <?= json_encode($isu_lingkungan_list) ?>;

    // Function to handle article click (if needed for modal preview)
    function showBeritaPreview(beritaId) {
        const berita = beritaData.find(b => b.id_berita == beritaId);
        if (!berita) return;

        // Check if it's an external link
        if (berita.link_eksternal && isValidUrl(berita.link_eksternal)) {
            // Open external link in new tab
            window.open(berita.link_eksternal, '_blank', 'noopener,noreferrer');
            return;
        }

        // For internal articles, redirect to article page
        window.location.href = `/page/artikel?id=${berita.id_berita}`;
    }

    function showBeritaDetail(beritaId) {
        const berita = beritaData.find(b => b.id_berita == beritaId);
        if (!berita) return;

        const modalContent = document.getElementById('kegiatanDetailContent');
        const modalTitle = document.getElementById('kegiatanDetailLabel');

        modalTitle.textContent = berita.judul_berita;

        let content = `
        <div class="kegiatan-detail">
            <div class="detail-header">
                ${berita.media_foto ? `
                    <img src="${getWebPath(berita.media_foto)}" alt="${berita.judul_berita}" class="detail-image">
                ` : ''}
                
                <div class="detail-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>${formatDate(berita.tgl_upload)}</span>
                    </div>
                    ${berita.sumber_berita ? `
                        <div class="info-item">
                            <i class="fas fa-newspaper"></i>
                            <span>Sumber: ${berita.sumber_berita}</span>
                        </div>
                    ` : ''}
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span>Berita Lingkungan</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-description">
                <p>${berita.deskripsi || 'Informasi detail tentang berita ini akan segera diperbarui.'}</p>
            </div>
            
            ${berita.link_eksternal ? `
                <div class="mt-3">
                    <a href="${berita.link_eksternal}" target="_blank" rel="noopener noreferrer" class="btn btn-success">
                        <i class="fas fa-external-link-alt me-2"></i>Baca Sumber Asli
                    </a>
                </div>
            ` : ''}
            
            ${berita.link_pojok ? `
                <div class="mt-2">
                    <a href="${berita.link_pojok}" target="_blank" rel="noopener noreferrer" class="btn btn-info">
                        <i class="fas fa-link me-2"></i>Link Referensi
                    </a>
                </div>
            ` : ''}
        </div>
    `;

        modalContent.innerHTML = content;

        const modal = new bootstrap.Modal(document.getElementById('kegiatanDetailModal'));
        modal.show();
    }

    function expandKegiatan(kegiatanId) {
        const kegiatan = kegiatanData.find(k => k.id_kegiatan == kegiatanId);
        if (!kegiatan) return;

        const modalContent = document.getElementById('kegiatanDetailContent');
        const modalTitle = document.getElementById('kegiatanDetailLabel');

        modalTitle.textContent = kegiatan.nm_kegiatan;

        let content = `
        <div class="kegiatan-detail">
            <div class="detail-header">
                ${kegiatan.media_foto ? `
                    <img src="${getWebPath(kegiatan.media_foto)}" alt="${kegiatan.nm_kegiatan}" class="detail-image">
                ` : ''}
                
                <div class="detail-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>${kegiatan.tgl_kegiatan || formatDate(kegiatan.tgl_mulai)}</span>
                    </div>
                    ${kegiatan.lokasi ? `
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${kegiatan.lokasi}</span>
                        </div>
                    ` : ''}
                    ${kegiatan.nm_divisi ? `
                        <div class="info-item">
                            <i class="fas fa-users"></i>
                            <span>${kegiatan.nm_divisi}</span>
                        </div>
                    ` : ''}
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span>${kegiatan.status}</span>
                    </div>
                </div>
            </div>
            
            <div class="detail-description">
                <p>${kegiatan.deskripsi || getDefaultDescription(kegiatan)}</p>
            </div>
            
            ${kegiatan.media_video && kegiatan.status === 'Terlaksana' ? `
                <div class="detail-video">
                    <h6 class="mb-3">Video Kegiatan</h6>
                    <video controls class="w-100">
                        <source src="${getWebPath(kegiatan.media_video)}" type="video/mp4">
                        Browser Anda tidak mendukung pemutar video.
                    </video>
                </div>
            ` : (kegiatan.status === 'Persiapan' ? `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Kegiatan Akan Datang:</strong> Dokumentasi foto dan video akan tersedia setelah kegiatan terlaksana.
                </div>
            ` : '')}
        </div>
    `;

        modalContent.innerHTML = content;

        const modal = new bootstrap.Modal(document.getElementById('kegiatanDetailModal'));
        modal.show();
    }

    function getDefaultDescription(kegiatan) {
        if (kegiatan.status === 'Persiapan') {
            return 'Kegiatan ' + kegiatan.nm_kegiatan + ' akan segera dilaksanakan. Kami sedang melakukan persiapan untuk memastikan kegiatan berjalan dengan lancar. Pantau terus informasi terbaru!';
        } else {
            return 'Kegiatan ' + kegiatan.nm_kegiatan + ' telah dilaksanakan dengan sukses oleh UKM-PA Edelweis.';
        }
    }

    function getWebPath(relativePath) {
        // Fungsi helper untuk mendapatkan web path
        const basePrefix = '<?= base_web_prefix() ?>';
        return basePrefix + '/' + relativePath.replace(/^\/+/, '');
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }

    // Helper function to validate URL
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Function to track link clicks (for analytics)
    function trackLinkClick(type, beritaId) {
        // Add your analytics tracking here
        console.log(`${type} link clicked for article ID: ${beritaId}`);

        // Example: Google Analytics event tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'click', {
                'event_category': 'Article',
                'event_label': type,
                'value': beritaId
            });
        }

        // Example: Facebook Pixel tracking
        if (typeof fbq !== 'undefined') {
            fbq('track', 'ViewContent', {
                content_type: 'article',
                content_ids: [beritaId],
                content_category: type
            });
        }
    }

    // Add event listeners for enhanced functionality
    document.addEventListener('DOMContentLoaded', function () {
        // Add hover effects for cards
        const cards = document.querySelectorAll('.isu-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function () {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Lazy loading for images
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    });

    <?php if ($showAdminModal): ?>
        // Admin modal script
        const adminModal = new bootstrap.Modal(document.getElementById('adminChoiceModal'));
        window.addEventListener('load', () => {
            adminModal.show();
        });
    <?php endif; ?>
</script>