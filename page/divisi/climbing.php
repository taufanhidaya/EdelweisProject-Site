<?php
include 'config/connect.php';

// Helper functions dari file admin
function base_web_prefix() {
  return rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
}
function web_path($rel) {
  $prefix = base_web_prefix();
  return $prefix . '/' . ltrim(str_replace('\\','/',$rel), '/');
}
function fs_path($rel) {
  $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
  $prefix = base_web_prefix();
  $rel = ltrim(str_replace('\\','/',$rel), '/');
  return $docroot . $prefix . '/' . $rel;
}

// Ambil kegiatan CLIMBING (bisa berdasarkan jenis_kegiatan atau divisi tertentu)
// Asumsi: kegiatan CLIMBING bisa difilter berdasarkan nama kegiatan yang mengandung 'gunung' atau 'climbing' atau divisi CLIMBING
$query = "SELECT k.*, d.nm_divisi, p.periode AS nm_periode 
          FROM kegiatan k 
          LEFT JOIN divisi d ON k.id_divisi = d.id_divisi 
          LEFT JOIN periode p ON k.id_periode = p.id_periode 
          WHERE (k.nm_kegiatan LIKE '%rafting%' OR k.nm_kegiatan LIKE '%arung jeram%' OR k.nm_kegiatan LIKE '%sungai%'OR d.nm_divisi LIKE '%rafting%' OR d.nm_divisi LIKE '%arung jeram%')
          AND k.status = 'Terlaksana'
          ORDER BY k.tgl_mulai DESC";

$result = $conn->query($query);
$kegiatan_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $kegiatan_list[] = $row;
    }
}

// Ambil satu kegiatan untuk hero image (yang terbaru)
$hero_kegiatan = !empty($kegiatan_list) ? $kegiatan_list[0] : null;
?>
<!-- page/CLIMBING.php -->
<section class="content-section bg-secondary " style="min-height: 100vh;">
    <!-- Hero Section dengan slide carousel -->
    <div id="heroCarousel" class="carousel slide hero-section mt-5" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <?php if (count($kegiatan_list) > 0): ?>
                <?php foreach ($kegiatan_list as $index => $kegiatan): ?>
                    <?php 
                    $bg_image = 'assets/img/default-mountain.jpg';
                    if (!empty($kegiatan['media_foto']) && file_exists(fs_path($kegiatan['media_foto']))) {
                        $bg_image = web_path($kegiatan['media_foto']);
                    }
                    ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <div class="hero-slide" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?= $bg_image ?>'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem 1rem;">
                            <div class="hero-content">
                                <h1 class="hero-title">CLIMBING</h1>
                                <p class="hero-subtitle">Mari jelajahi jauh ke atas puncak dan belajar lebih banyak</p>
                                <div class="hero-kegiatan-info">
                                    <h3 class="kegiatan-title"><?= htmlspecialchars($kegiatan['nm_kegiatan']) ?></h3>
                                    <?php if ($kegiatan['tgl_kegiatan']): ?>
                                        <p class="kegiatan-date">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?= htmlspecialchars($kegiatan['tgl_kegiatan']) ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($kegiatan['lokasi']): ?>
                                        <p class="kegiatan-location">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?= htmlspecialchars($kegiatan['lokasi']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="carousel-item active">
                    <div class="hero-slide" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/img/default-mountain.jpg'); background-size: cover; background-position: center; min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem 1rem;">
                        <div class="hero-content">
                            <h1 class="hero-title">CLIMBING</h1>
                            <p class="hero-subtitle">Kegiatan panjat tebing yang berfokus pada keterampilan teknis, keamanan, dan ketahanan fisik maupun mental.</p>
                            <div class="hero-kegiatan-info">
                                <p class="kegiatan-coming-soon">
                                    <i class="fas fa-mountain"></i> 
                                    Kegiatan CLIMBING akan segera hadir!
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Carousel controls -->
        <?php if (count($kegiatan_list) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            
            <!-- Carousel indicators -->
            <div class="carousel-indicators">
                <?php foreach ($kegiatan_list as $index => $kegiatan): ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>" 
                            <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> 
                            aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Content Section -->
    <div class="container bg-secondary mt-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="content-card" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h2 style="color: #333; font-size: 1.8rem; font-weight: bold; margin-bottom: 1.5rem;">APA SIH CLIMBING DI EDELWEIS ?</h2>
                    <div style="height: 3px; width: 50px; background: var(--primary-orange); margin-bottom: 2rem;"></div>
                    <p style="color: #666; line-height: 1.8; font-size: 1rem; text-align: justify;">
                        Climbing di UKM-PA Edelweis adalah kegiatan panjat tebing yang mengajarkan keterampilan teknis dalam menaklukkan tebing alam maupun dinding buatan. Fokus kami adalah pada teknik panjat yang aman, penggunaan peralatan yang benar, dan etika memanjat.
                    </p>
                    <p style="color: #666; line-height: 1.8; font-size: 1rem; text-align: justify; margin-top: 1.5rem;">
                        Melalui latihan panjat tebing, bouldering, hingga ekspedisi panjat alam, anggota akan dilatih ketahanan fisik, mental, serta kemampuan teknis yang mendukung dunia mountaineering.
                    </p>
                    
                    <?php if (count($kegiatan_list) > 0): ?>
                        <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                            <h4 style="color: #333; margin-bottom: 1rem;">Statistik Kegiatan</h4>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-orange);"><?= count($kegiatan_list) ?></div>
                                    <div style="font-size: 0.9rem; color: #666;">Total Kegiatan</div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-orange);">
                                        <?= count(array_filter($kegiatan_list, function($k) { return !empty($k['media_foto']); })) ?>
                                    </div>
                                    <div style="font-size: 0.9rem; color: #666;">Dokumentasi Foto</div>
                                </div>
                                <div class="col-4">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--primary-orange);">
                                        <?= count(array_filter($kegiatan_list, function($k) { return !empty($k['media_video']); })) ?>
                                    </div>
                                    <div style="font-size: 0.9rem; color: #666;">Dokumentasi Video</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="sidebar-card" style="background: #f8f9fa; padding: 2rem; border-radius: 10px; height: 100%;">
                    <?php if ($hero_kegiatan && !empty($hero_kegiatan['media_foto']) && file_exists(fs_path($hero_kegiatan['media_foto']))): ?>
                        <div style="border-radius: 8px; overflow: hidden; margin-bottom: 1.5rem;">
                            <img src="<?= web_path($hero_kegiatan['media_foto']) ?>" 
                                 alt="<?= htmlspecialchars($hero_kegiatan['nm_kegiatan']) ?>" 
                                 style="width: 100%; height: 200px; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div style="background: #e9ecef; height: 200px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6c757d; margin-bottom: 1.5rem;">
                            <i class="fas fa-mountain" style="font-size: 3rem;"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h4 style="color: #333; margin-bottom: 1rem;">Info Kegiatan Terbaru</h4>
                    <?php if ($hero_kegiatan): ?>
                        <p style="color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0.5rem;">
                            <strong><?= htmlspecialchars($hero_kegiatan['nm_kegiatan']) ?></strong>
                        </p>
                        <?php if ($hero_kegiatan['lokasi']): ?>
                            <p style="color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0.5rem;">
                                <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem; color: var(--primary-orange);"></i>
                                <?= htmlspecialchars($hero_kegiatan['lokasi']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($hero_kegiatan['tgl_kegiatan']): ?>
                            <p style="color: #666; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0.5rem;">
                                <i class="fas fa-calendar" style="margin-right: 0.5rem; color: var(--primary-orange);"></i>
                                <?= htmlspecialchars($hero_kegiatan['tgl_kegiatan']) ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($hero_kegiatan['deskripsi']): ?>
                            <p style="color: #666; font-size: 0.9rem; line-height: 1.6;">
                                <?= htmlspecialchars(substr($hero_kegiatan['deskripsi'], 0, 150)) ?><?= strlen($hero_kegiatan['deskripsi']) > 150 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="color: #666; font-size: 0.9rem; line-height: 1.6;">
                            Belum ada kegiatan CLIMBING yang tersedia. Pantau terus untuk update kegiatan terbaru!
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Additional Content Sections -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="feature-card" style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="color: #333; margin-bottom: 1.5rem;">Kegiatan Climbing/Panjat Tebing</h3>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="activity-item" style="text-align: center;">
                                <div class="activity-icon" style="background: var(--primary-orange); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="bi bi-lightning"></i>
                                </div>
                                <h5 style="color: #333;">Teknik Panjat</h5>
                                <p style="color: #666; font-size: 0.9rem;">mempelajari bouldering, lead climbing, dan top rope.</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="activity-item" style="text-align: center;">
                                <div class="activity-icon" style="background: var(--primary-orange); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <h5 style="color: #333;">Penggunaan Peralatan</h5>
                                <p style="color: #666; font-size: 0.9rem;">memahami harness, carabiner, tali, dan sistem pengaman.
</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="activity-item" style="text-align: center;">
                                <div class="activity-icon" style="background: var(--primary-orange); color: white; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                                    <i class="bi bi-heart-pulse"></i>
                                </div>
                                <h5 style="color: #333;">Fisik & Mental</h5>
                                <p style="color: #666; font-size: 0.9rem;">melatih kekuatan, daya tahan, dan keberanian.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gallery Section - Data Dinamis -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 style="color: white; margin-bottom: 2rem; text-align: center;">Galeri Kegiatan</h3>
                
                <?php if (count($kegiatan_list) > 0): ?>
                    <div class="gallery-container">
                        <div class="row">
                            <?php foreach ($kegiatan_list as $index => $kegiatan): ?>
                                <?php 
                                // Tentukan gambar yang akan digunakan
                                $bg_image = 'path/to/default-image.jpg';
                                if (!empty($kegiatan['media_foto']) && file_exists(fs_path($kegiatan['media_foto']))) {
                                    $bg_image = web_path($kegiatan['media_foto']);
                                }
                                ?>
                                <div class="col-md-4 mb-4">
                                    <div class="gallery-card" onclick="openModal('modal<?= $index + 1 ?>')" 
                                         style="position: relative; cursor: pointer; border-radius: 10px; overflow: hidden; height: 250px; background: url('<?= $bg_image ?>') center/cover;">
                                        <div class="card-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                            <div class="card-content" style="text-align: center; color: white; opacity: 0; transform: translateY(20px); transition: all 0.3s ease;">
                                                <h5 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($kegiatan['nm_kegiatan']) ?></h5>
                                                <p style="font-size: 0.9rem; margin-bottom: 1rem;">
                                                    <?php if ($kegiatan['deskripsi']): ?>
                                                        <?= htmlspecialchars(substr($kegiatan['deskripsi'], 0, 100)) ?><?= strlen($kegiatan['deskripsi']) > 100 ? '...' : '' ?>
                                                    <?php else: ?>
                                                        Kegiatan CLIMBING yang mengajarkan teknik pendakian dan survival skills.
                                                    <?php endif; ?>
                                                </p>
                                                <span style="background: var(--primary-orange); padding: 0.3rem 1rem; border-radius: 20px; font-size: 0.8rem;">Lihat Selengkapnya</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; color: white; padding: 3rem;">
                        <i class="fas fa-mountain" style="font-size: 4rem; margin-bottom: 1rem; opacity: 0.7;"></i>
                        <h4>Belum Ada Kegiatan</h4>
                        <p>Galeri kegiatan CLIMBING akan muncul di sini setelah ada kegiatan yang terlaksana.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal Templates - Data Dinamis -->
    <?php foreach ($kegiatan_list as $index => $kegiatan): ?>
        <div id="modal<?= $index + 1 ?>" class="modal-overlay">
            <div class="modal-content" style="background: white; max-width: 800px; width: 90%; border-radius: 10px; position: relative; margin: 2rem auto;">
                <span class="close-modal" onclick="closeModal('modal<?= $index + 1 ?>')" style="position: absolute; top: 15px; right: 20px; font-size: 30px; cursor: pointer; color: #666; z-index: 1001;">&times;</span>
                <div class="row" style="margin: 0;">
                    <div class="col-lg-6" style="padding: 2rem;">
                        <h2 style="color: #333; margin-bottom: 1rem;"><?= htmlspecialchars($kegiatan['nm_kegiatan']) ?></h2>
                        <div style="height: 3px; width: 50px; background: var(--primary-orange); margin-bottom: 1.5rem;"></div>
                        
                        <?php if ($kegiatan['deskripsi']): ?>
                            <p style="color: #666; line-height: 1.8; text-align: justify;">
                                <?= nl2br(htmlspecialchars($kegiatan['deskripsi'])) ?>
                            </p>
                        <?php else: ?>
                            <p style="color: #666; line-height: 1.8; text-align: justify;">
                                Kegiatan <?= htmlspecialchars($kegiatan['nm_kegiatan']) ?> merupakan bagian dari program CLIMBING 
                                UKM-PA Edelweis yang bertujuan untuk meningkatkan kemampuan anggota dalam teknik pendakian dan survival.
                            </p>
                        <?php endif; ?>

                        <h5 style="color: var(--primary-orange); margin-top: 1.5rem;">Detail Kegiatan:</h5>
                        <ul style="color: #666;">
                            <?php if ($kegiatan['tgl_kegiatan']): ?>
                                <li>Tanggal: <?= htmlspecialchars($kegiatan['tgl_kegiatan']) ?></li>
                            <?php endif; ?>
                            <?php if ($kegiatan['lokasi']): ?>
                                <li>Lokasi: <?= htmlspecialchars($kegiatan['lokasi']) ?></li>
                            <?php endif; ?>
                            <?php if ($kegiatan['nm_divisi']): ?>
                                <li>Divisi: <?= htmlspecialchars($kegiatan['nm_divisi']) ?></li>
                            <?php endif; ?>
                            <?php if ($kegiatan['nm_periode']): ?>
                                <li>Periode: <?= htmlspecialchars($kegiatan['nm_periode']) ?></li>
                            <?php endif; ?>
                            <li>Status: <?= htmlspecialchars($kegiatan['status']) ?></li>
                        </ul>
                        
                        <?php if (!empty($kegiatan['media_video']) && file_exists(fs_path($kegiatan['media_video']))): ?>
                            <div style="margin-top: 1.5rem;">
                                <button onclick="playVideo('<?= web_path($kegiatan['media_video']) ?>')" class="btn" style="background: var(--primary-orange); color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px;">
                                    <i class="fas fa-play" style="margin-right: 0.5rem;"></i>Tonton Video
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-lg-6" style="padding: 2rem; background: #f8f9fa;">
                        <?php if (!empty($kegiatan['media_foto']) && file_exists(fs_path($kegiatan['media_foto']))): ?>
                            <div style="border-radius: 8px; overflow: hidden; margin-bottom: 1rem;">
                                <img src="<?= web_path($kegiatan['media_foto']) ?>" 
                                     alt="<?= htmlspecialchars($kegiatan['nm_kegiatan']) ?>" 
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div style="background: #e9ecef; height: 200px; border-radius: 8px; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-mountain" style="font-size: 4rem; color: #6c757d;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <h5 style="color: #333; margin-bottom: 1rem;">Informasi Lengkap</h5>
                        <div style="background: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                            <strong>Jenis:</strong> <?= htmlspecialchars($kegiatan['jenis_kegiatan']) ?><br>
                            <?php if ($kegiatan['tgl_mulai']): ?>
                                <strong>Mulai:</strong> <?= date('d F Y', strtotime($kegiatan['tgl_mulai'])) ?><br>
                            <?php endif; ?>
                            <?php if ($kegiatan['tgl_selesai'] && $kegiatan['tgl_selesai'] !== $kegiatan['tgl_mulai']): ?>
                                <strong>Selesai:</strong> <?= date('d F Y', strtotime($kegiatan['tgl_selesai'])) ?><br>
                            <?php endif; ?>
                            <?php if ($kegiatan['dokumen_sumber']): ?>
                                <strong>Sumber:</strong> <?= htmlspecialchars($kegiatan['dokumen_sumber']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Video Modal -->
    <div id="videoModal" class="modal-overlay">
        <div class="modal-content" style="background: black; max-width: 900px; width: 90%; border-radius: 10px; position: relative; margin: 2rem auto;">
            <span class="close-modal" onclick="closeVideoModal()" style="position: absolute; top: 15px; right: 20px; font-size: 30px; cursor: pointer; color: white; z-index: 1001;">&times;</span>
            <div style="padding: 2rem;">
                <video id="modalVideo" controls style="width: 100%; height: auto; max-height: 70vh;">
                    <source src="" type="video/mp4">
                    Browser Anda tidak mendukung pemutaran video.
                </video>
            </div>
        </div>
    </div>

    <style>
        .gallery-card:hover .card-overlay {
            background: rgba(0,0,0,0.6) !important;
        }
        
        .gallery-card:hover .card-content {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            overflow-y: auto;
        }
        
        .modal-overlay.active {
            display: flex !important;
        }
        
        .modal-content {
            animation: modalSlideIn 0.3s ease;
            margin: auto;
            max-width: 90%;
            max-height: 90%;
            overflow-y: auto;
        }
        
        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        /* Ensure body doesn't scroll when modal is open */
        body.modal-open {
            overflow: hidden !important;
        }
        
        /* Hero Section Responsive Styles */
        .hero-title {
            font-size: 4rem;
            font-weight: bold;
            color: white;
            letter-spacing: 0.2em;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: white;
            max-width: 600px;
            margin: 0 auto 2rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
        }
        
        .hero-kegiatan-info {
            padding: 1.5rem;
            margin-top: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .kegiatan-title {
            color: var(--primary-orange, #ff6b35);
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }
        
        .kegiatan-date, .kegiatan-location, .kegiatan-coming-soon {
            color: rgba(255,255,255,0.95);
            font-size: 1rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .kegiatan-coming-soon {
            font-size: 1.1rem;
            color: var(--primary-orange, #ff6b35);
        }
        
        /* Carousel Controls Styling */
        .carousel-control-prev, .carousel-control-next {
            width: 5%;
            opacity: 0.8;
        }
        
        .carousel-control-prev:hover, .carousel-control-next:hover {
            opacity: 1;
        }
        
        .carousel-indicators {
            bottom: 20px;
        }
        
        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 5px;
            background-color: rgba(255,255,255,0.5);
            border: 2px solid white;
        }
        
        .carousel-indicators button.active {
            background-color: var(--primary-orange, #ff6b35);
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .modal-content {
                max-width: 95%;
                max-height: 95%;
                margin: 2.5%;
            }
            
            .hero-title {
                font-size: 2.5rem !important;
                letter-spacing: 0.1em;
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 1rem;
                padding: 0 1rem;
                line-height: 1.4;
            }
            
            .hero-kegiatan-info {
                padding: 1rem;
                margin: 1rem;
                max-width: calc(100% - 2rem);
            }
            
            .kegiatan-title {
                font-size: 1.2rem;
                line-height: 1.3;
            }
            
            .kegiatan-date, .kegiatan-location, .kegiatan-coming-soon {
                font-size: 0.9rem;
                flex-direction: column;
                gap: 0.2rem;
            }
            
            .col-md-4 {
                margin-bottom: 1rem;
            }
            
            .hero-slide {
                min-height: 70vh !important;
                padding: 1rem !important;
            }
            
            .carousel-control-prev, .carousel-control-next {
                width: 8%;
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem !important;
                margin-bottom: 0.5rem;
            }
            
            .hero-subtitle {
                font-size: 0.9rem;
                margin-bottom: 1rem;
            }
            
            .hero-kegiatan-info {
                padding: 0.8rem;
                margin: 0.5rem;
            }
            
            .kegiatan-title {
                font-size: 1.1rem;
            }
            
            .kegiatan-date, .kegiatan-location, .kegiatan-coming-soon {
                font-size: 0.8rem;
            }
            
            .hero-slide {
                min-height: 60vh !important;
            }
        }
        
        @media (max-width: 360px) {
            .hero-title {
                font-size: 1.8rem !important;
            }
            
            .hero-subtitle {
                font-size: 0.85rem;
            }
            
            .kegiatan-title {
                font-size: 1rem;
            }
        }
        
        /* Loading states */
        .loading-placeholder {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>

    <script>
        function openModal(modalId) {
            // Close any other open modals first
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                modal.classList.remove('active');
            });
            
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        function closeAllModals() {
            document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                modal.classList.remove('active');
            });
            document.body.style.overflow = 'auto';
        }
        
        function playVideo(videoSrc) {
            const videoModal = document.getElementById('videoModal');
            const modalVideo = document.getElementById('modalVideo');
            modalVideo.src = videoSrc;
            videoModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeVideoModal() {
            const videoModal = document.getElementById('videoModal');
            const modalVideo = document.getElementById('modalVideo');
            modalVideo.pause();
            modalVideo.src = '';
            videoModal.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeAllModals();
                closeVideoModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllModals();
                closeVideoModal();
            }
        });
        
        // Initialize carousel with touch support
        document.addEventListener('DOMContentLoaded', function() {
            closeAllModals();
            closeVideoModal();
            
            // Initialize Bootstrap carousel if available
            const carousel = document.querySelector('#heroCarousel');
            if (carousel && typeof bootstrap !== 'undefined') {
                new bootstrap.Carousel(carousel, {
                    interval: 6000,
                    ride: 'carousel',
                    pause: 'hover',
                    wrap: true
                });
            }
            
            // Add swipe support for mobile
            let touchStartX = 0;
            let touchEndX = 0;
            
            if (carousel) {
                carousel.addEventListener('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                });
                
                carousel.addEventListener('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                });
            }
            
            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        // Swipe left - next slide
                        const nextBtn = carousel.querySelector('.carousel-control-next');
                        if (nextBtn) nextBtn.click();
                    } else {
                        // Swipe right - previous slide  
                        const prevBtn = carousel.querySelector('.carousel-control-prev');
                        if (prevBtn) prevBtn.click();
                    }
                }
            }
        });
        
        // Add safety check to prevent stuck modals
        window.addEventListener('beforeunload', function() {
            document.body.style.overflow = 'auto';
        });
        
        // Lazy loading for images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img[data-src]');
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('loading-placeholder');
                        observer.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        });
        
        // Auto-refresh untuk konten dinamis (opsional)
        // setInterval(function() {
        //     // Bisa ditambahkan AJAX call untuk update konten tanpa refresh halaman
        // }, 300000); // refresh setiap 5 menit
    </script>
</section>