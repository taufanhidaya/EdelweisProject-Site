<!-- page/visi-misi.php -->
<section class="content-section bg-secondary" style="min-height: 100vh;">
    <div class="container bg-secondary mt-5">
        <h2 class="section-title text-center mb-5">Proker UKM-PA Edelweis</h2>
        
        <div class="timeline">
            <!-- Timeline Item 1 -->
            <div class="timeline-container left">
                <div class="timeline-content">
                    <h3>Pendaftaran Anggota Baru</h3>
                    <p class="timeline-date">September 2023</p>
                    <p>Penerimaan anggota baru melalui proses seleksi administrasi dan wawancara.</p>
                </div>
            </div>
            
            <!-- Timeline Item 2 -->
            <div class="timeline-container right">
                <div class="timeline-content">
                    <h3>Masa Pengenalan</h3>
                    <p class="timeline-date">Oktober 2023</p>
                    <p>Pengenalan organisasi, struktur, dan program kerja kepada anggota baru.</p>
                </div>
            </div>
            
            <!-- Timeline Item 3 -->
            <div class="timeline-container left">
                <div class="timeline-content">
                    <h3>Pelatihan Dasar</h3>
                    <p class="timeline-date">November 2023</p>
                    <p>Pelatihan dasar kepencintaalaman dan survival di alam bebas.</p>
                </div>
            </div>
            
            <!-- Timeline Item 4 -->
            <div class="timeline-container right">
                <div class="timeline-content">
                    <h3>Ekspedisi Tahunan</h3>
                    <p class="timeline-date">Desember 2023</p>
                    <p>Pendakian gunung sebagai puncak kegiatan tahunan UKM-PA Edelweis.</p>
                </div>
            </div>
            
            <!-- Timeline Item 5 -->
            <div class="timeline-container left">
                <div class="timeline-content">
                    <h3>Kegiatan Sosial</h3>
                    <p class="timeline-date">Januari 2024</p>
                    <p>Aksi bersih gunung dan penyulaman tanaman di area pendakian.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Timeline Style */
    .timeline {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px 0;
    }
    
    .timeline::after {
        content: '';
        position: absolute;
        width: 6px;
        background-color: #28a745;
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -3px;
        border-radius: 10px;
    }
    
    .timeline-container {
        padding: 10px 40px;
        position: relative;
        background-color: inherit;
        width: 50%;
    }
    
    .timeline-container::after {
        content: '';
        position: absolute;
        width: 25px;
        height: 25px;
        right: -12px;
        background-color: white;
        border: 4px solid #28a745;
        top: 15px;
        border-radius: 50%;
        z-index: 1;
    }
    
    .left {
        left: 0;
    }
    
    .right {
        left: 50%;
    }
    
    .left::before {
        content: " ";
        height: 0;
        position: absolute;
        top: 22px;
        width: 0;
        z-index: 1;
        right: 30px;
        border: medium solid white;
        border-width: 10px 0 10px 10px;
        border-color: transparent transparent transparent white;
    }
    
    .right::before {
        content: " ";
        height: 0;
        position: absolute;
        top: 22px;
        width: 0;
        z-index: 1;
        left: 30px;
        border: medium solid white;
        border-width: 10px 10px 10px 0;
        border-color: transparent white transparent transparent;
    }
    
    .right::after {
        left: -12px;
    }
    
    .timeline-content {
        padding: 20px 30px;
        background-color: white;
        position: relative;
        border-radius: 6px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .timeline-date {
        color: #28a745;
        font-weight: bold;
    }
    
    @media screen and (max-width: 600px) {
        .timeline::after {
            left: 31px;
        }
        
        .timeline-container {
            width: 100%;
            padding-left: 70px;
            padding-right: 25px;
        }
        
        .timeline-container::before {
            left: 60px;
            border: medium solid white;
            border-width: 10px 10px 10px 0;
            border-color: transparent white transparent transparent;
        }
        
        .left::after, .right::after {
            left: 18px;
        }
        
        .right {
            left: 0%;
        }
    }
</style>