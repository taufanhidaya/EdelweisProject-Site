<?php
// Konfigurasi Database
include 'config/connect.php';

// Cek koneksi - pastikan $conn tersedia dari connect.php
if (!isset($conn) || !$conn) {
    die("Koneksi database gagal!");
}

// Ambil parameter judul dari URL atau set default
$judul_berita = isset($_GET['judul']) && !empty($_GET['judul']) ? $_GET['judul'] : 'Ekspedisi Gunung Khung-Pase Memperingati 17 Agustus';

// Escape string untuk keamanan
$judul_berita_escaped = mysqli_real_escape_string($conn, $judul_berita);

// Query untuk mengambil data artikel berdasarkan judul
$query = "SELECT * FROM berita WHERE judul_berita = '$judul_berita_escaped'";
$result = mysqli_query($conn, $query);

// Cek apakah query berhasil
if (!$result) {
    die("Error query: " . mysqli_error($conn));
}

// Cek apakah ada data
if (mysqli_num_rows($result) == 0) {
    die("Artikel tidak ditemukan! Judul yang dicari: " . htmlspecialchars($judul_berita));
}

// Ambil semua data artikel
$articles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $articles[] = $row;
}

// Ambil data artikel pertama untuk info umum
$main_article = $articles[0];

// Kumpulkan semua media foto
$media_photos = [];
foreach ($articles as $article) {
    if (!empty($article['media_foto'])) {
        $media_photos[] = $article['media_foto'];
    }
}

// Foto utama untuk background hero
$hero_background = !empty($media_photos) ? $media_photos[0] : 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80';

// Format tanggal
$upload_date = !empty($main_article['tgl_upload']) ? date('d M', strtotime($main_article['tgl_upload'])) : '08 SEP';
$upload_date_full = !empty($main_article['tgl_upload']) ? date('d F Y', strtotime($main_article['tgl_upload'])) : '08 September 2023';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($main_article['judul_berita']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('<?php echo htmlspecialchars($hero_background); ?>');
            background-size: cover;
            background-position: center;
            min-height: 500px;
            position: relative;
            color: white;
            display: flex;
            align-items: center;
        }
        
        .hero-content {
            z-index: 2;
        }
        
        .date-badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: inline-block;
        }
        
        .hero-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .author-info {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .breadcrumb-custom {
            background: #f8f9fa;
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .article-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        
        .media-gallery img {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        
        .media-gallery img:hover {
            transform: scale(1.05);
        }
        
        .content-section {
            margin-bottom: 40px;
        }
        
        .photo-counter {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-section {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="date-badge">
                    <?php echo strtoupper($upload_date); ?>
                </div>
                <h1 class="hero-title"><?php echo htmlspecialchars($main_article['judul_berita']); ?></h1>
                <div class="author-info">
                    By <strong><?php echo htmlspecialchars($main_article['org_input']); ?></strong> / 
                    <span><?php echo htmlspecialchars($main_article['sumber_berita']); ?></span>
                </div>
            </div>
            <?php if (count($media_photos) > 1): ?>
            <div class="photo-counter">
                <i class="bi bi-camera"></i> <?php echo count($media_photos); ?> Foto
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Breadcrumb -->
    <section class="breadcrumb-custom">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">BERANDA</a></li>
                    <li class="breadcrumb-item active" aria-current="page">ARTIKEL</li>
                </ol>
            </nav>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Article Content -->
                <article>
                    <div class="content-section">
                        <p class="article-content">
                            <?php echo !empty($main_article['deskripsi']) ? nl2br(htmlspecialchars($main_article['deskripsi'])) : 'Lorem ipsum dolor sit amet consectetur adipiscing elit. Quis in pariatur totam, laboriosam sint exercitationem nam sequi, voluptatem perspiciatis libero quos. Laborum, neque illo dolor maxime incidunt possimus laudantium tempore.'; ?>
                        </p>
                    </div>

                    <?php if (count($media_photos) > 1): ?>
                    <!-- Media Gallery - Multiple Photos -->
                    <div class="media-gallery">
                        <h5 class="mb-3">Galeri Foto</h5>
                        <div class="row g-3 mb-4">
                            <?php 
                            $photo_count = 0;
                            foreach ($media_photos as $photo): 
                                $photo_count++;
                                if ($photo_count == 1) continue; // Skip foto pertama karena sudah jadi background
                                
                                $col_class = 'col-md-6';
                                if ($photo_count > 3) {
                                    $col_class = 'col-md-4';
                                }
                            ?>
                            <div class="<?php echo $col_class; ?>">
                                <img src="<?php echo htmlspecialchars($photo); ?>" 
                                     class="img-fluid rounded shadow" 
                                     alt="<?php echo htmlspecialchars($main_article['judul_berita']); ?> - Foto <?php echo $photo_count - 1; ?>"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#photoModal" 
                                     data-photo="<?php echo htmlspecialchars($photo); ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="content-section">
                        <p class="article-content">
                            Kegiatan ekspedisi ini merupakan bagian dari peringatan kemerdekaan Indonesia yang ke-78. 
                            Para peserta ekspedisi melakukan pendakian dengan membawa bendera merah putih untuk 
                            ditancapkan di puncak gunung sebagai simbol cinta tanah air.
                        </p>
                    </div>
                </article>

                <!-- Article Meta Information -->
                <div class="row mt-5 pt-4 border-top">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informasi Artikel</h6>
                        <div class="small">
                            <div><strong>ID Berita:</strong> <?php echo $main_article['id_berita']; ?></div>
                            <div><strong>Judul:</strong> <?php echo htmlspecialchars($main_article['judul_berita']); ?></div>
                            <div><strong>Total Media:</strong> <?php echo count($media_photos); ?> Foto</div>
                            <div><strong>Sumber:</strong> <?php echo htmlspecialchars($main_article['sumber_berita']); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Detail Publikasi</h6>
                        <div class="small">
                            <div><strong>Upload:</strong> <?php echo $upload_date_full; ?></div>
                            <div><strong>Penulis:</strong> <?php echo htmlspecialchars($main_article['org_input']); ?></div>
                            <div><strong>Status:</strong> <span class="badge bg-success">Published</span></div>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <?php
                    // Query untuk artikel sebelumnya dan selanjutnya dengan MySQLi
                    $prev_article = null;
                    $next_article = null;
                    
                    // Cari artikel sebelumnya
                    $prev_query = "SELECT judul_berita FROM berita WHERE id_berita < (SELECT MIN(id_berita) FROM berita WHERE judul_berita = '$judul_berita_escaped') ORDER BY id_berita DESC LIMIT 1";
                    $prev_result = mysqli_query($conn, $prev_query);
                    if ($prev_result && mysqli_num_rows($prev_result) > 0) {
                        $prev_article = mysqli_fetch_assoc($prev_result);
                    }
                    
                    // Cari artikel selanjutnya
                    $next_query = "SELECT judul_berita FROM berita WHERE id_berita > (SELECT MAX(id_berita) FROM berita WHERE judul_berita = '$judul_berita_escaped') ORDER BY id_berita ASC LIMIT 1";
                    $next_result = mysqli_query($conn, $next_query);
                    if ($next_result && mysqli_num_rows($next_result) > 0) {
                        $next_article = mysqli_fetch_assoc($next_result);
                    }
                    ?>
                    
                    <?php if ($prev_article): ?>
                    <a href="?judul=<?php echo urlencode($prev_article['judul_berita']); ?>" class="btn btn-outline-primary">
                        ← Artikel Sebelumnya
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>
                    
                    <?php if ($next_article): ?>
                    <a href="?judul=<?php echo urlencode($next_article['judul_berita']); ?>" class="btn btn-outline-primary">
                        Artikel Selanjutnya →
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Articles Section -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4 text-center">Artikel Lainnya</h3>
                </div>
            </div>
            <div class="row">
                <?php
                // Query untuk artikel terkait (3 artikel terbaru selain artikel saat ini)
                $related_query = "
                    SELECT judul_berita, media_foto, org_input, sumber_berita, tgl_upload 
                    FROM berita 
                    WHERE judul_berita != '$judul_berita_escaped' 
                    GROUP BY judul_berita 
                    ORDER BY tgl_upload DESC 
                    LIMIT 3
                ";
                $related_result = mysqli_query($conn, $related_query);
                
                if ($related_result && mysqli_num_rows($related_result) > 0) {
                    while ($related = mysqli_fetch_assoc($related_result)) {
                        $related_image = !empty($related['media_foto']) ? $related['media_foto'] : 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($related_image); ?>" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover;" 
                             alt="<?php echo htmlspecialchars($related['judul_berita']); ?>">
                        <div class="card-body">
                            <h6 class="card-title"><?php echo htmlspecialchars(substr($related['judul_berita'], 0, 50)) . (strlen($related['judul_berita']) > 50 ? '...' : ''); ?></h6>
                            <p class="card-text small text-muted">
                                By <?php echo htmlspecialchars($related['org_input']); ?> / 
                                <?php echo htmlspecialchars($related['sumber_berita']); ?>
                            </p>
                            <a href="?judul=<?php echo urlencode($related['judul_berita']); ?>" class="btn btn-sm btn-primary">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                    echo "<p class='text-center'>Tidak ada artikel terkait.</p>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Artikel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" class="img-fluid rounded" alt="Foto Artikel">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Photo modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            var photoModal = document.getElementById('photoModal');
            if (photoModal) {
                photoModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var photoSrc = button.getAttribute('data-photo');
                    var modalPhoto = photoModal.querySelector('#modalPhoto');
                    modalPhoto.src = photoSrc;
                });
            }
        });
    </script>
</body>
</html>