<!-- page/visi-misi.php -->
<style>
    :root {
        --card-w: 250px;
        --card-h: 320px;
    }

    .wrap {
        /* min-height: 100vh; */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        gap: 50px;
    }

    /* Title Section */
    .title-section {
        text-align: center;
        max-width: 600px;
    }

    .main-title {
        font-size: 3.5rem;
        font-weight: 800;
        margin: 0 0 12px 0;
        background: linear-gradient(135deg, #fff 0%, #a8a8a8 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -0.02em;
        text-shadow: none;
    }

    .subtitle {
        font-size: 1.25rem;
        color: #888;
        margin: 0;
        font-weight: 400;
        line-height: 1.5;
    }

    /* Carousel stage */
    .carousel-stage {
        width: 100%;
        max-width: 1100px;
        height: var(--card-h);
        position: relative;
        perspective: 1400px;
        perspective-origin: 50% 40%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
    }

    .cards {
        position: relative;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 30px;
        transform-style: preserve-3d;
    }

    .card-item {
        width: var(--card-w);
        height: var(--card-h);
        border-radius: 18px;
        overflow: hidden;
        position: absolute;
        left: 50%;
        top: 50%;
        transform-style: preserve-3d;
        transform-origin: center center;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), 0 8px 20px rgba(0, 0, 0, 0.5);
        transition: transform .6s cubic-bezier(.2, .9, .3, 1), opacity .5s ease, filter .5s ease;
        background: #111;
        cursor: pointer;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
    }

    .card-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .card-caption {
        position: absolute;
        left: 16px;
        right: 16px;
        bottom: 14px;
        color: white;
        font-weight: 600;
        font-size: 15px;
        text-shadow: 0 6px 18px rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(6px);
        padding: 8px 12px;
        border-radius: 10px;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.16), rgba(0, 0, 0, 0.38));
    }

    /* Card overlays */
    .card-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        align-items: flex-start;
        text-align: left;
        padding: 32px 24px;
        padding-left: 24px;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .card-number {
        font-size: 5rem;
        font-weight: 900;
        color: white;
        margin: 0;
        text-shadow: 0 8px 24px rgba(0, 0, 0, 0.8);
        line-height: 0.9;
        opacity: 0.95;
    }

    .card-period {
        font-size: 1rem;
        font-weight: 700;
        color: white;
        margin: 8px 0 4px 0;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
        letter-spacing: 0.5px;
    }

    .card-subtitle {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        font-weight: 500;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
        line-height: 1.3;
    }

    /* Only show overlay on center card */
    .card-item.center .card-overlay {
        opacity: 1;
        visibility: visible;
    }

    /* Center card gets bigger text */
    .card-item.center .card-number {
        font-size: 6.5rem;
    }

    .card-item.center .card-period {
        font-size: 1.2rem;
    }

    .card-item.center .card-subtitle {
        font-size: 1rem;
    }

    /* Card states */
    .card-item.center {
        transform: translate(-50%, -50%) translateZ(120px) scale(1) rotateY(0deg);
        z-index: 30;
        opacity: 1;
    }

    .card-item.left {
        transform: translate(-50%, -50%) translateX(calc(-1 * (var(--card-w) + 28px))) translateZ(20px) scale(.92) rotateY(30deg);
        z-index: 20;
        opacity: 1;
        filter: brightness(.95);
    }

    .card-item.right {
        transform: translate(-50%, -50%) translateX(calc(var(--card-w) + 28px)) translateZ(20px) scale(.92) rotateY(-30deg);
        z-index: 20;
        opacity: 1;
        filter: brightness(.95);
    }

    .card-item.left2 {
        transform: translate(-50%, -50%) translateX(calc(-2 * (var(--card-w) + 28px))) translateZ(-40px) scale(.78) rotateY(40deg);
        z-index: 10;
        opacity: .6;
        filter: blur(.4px) brightness(.85);
    }

    .card-item.right2 {
        transform: translate(-50%, -50%) translateX(calc(2 * (var(--card-w) + 28px))) translateZ(-40px) scale(.78) rotateY(-40deg);
        z-index: 10;
        opacity: .6;
        filter: blur(.4px) brightness(.85);
    }

    .card-item.hide {
        transform: translate(-50%, -50%) translateZ(-220px) scale(.6);
        z-index: 1;
        opacity: 0;
        pointer-events: none;
    }

    /* Arrow controls - positioned outside the carousel */
    .controls {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        pointer-events: none;
        z-index: 9999;
    }

    .btn-arrow {
        pointer-events: auto;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(10px);
    }

    .btn-arrow:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.6);
    }

    .btn-arrow:active {
        transform: scale(0.95);
    }

    /* Position arrows on far left and right */
    .btn-arrow.prev {
        margin-left: -30px;
    }

    .btn-arrow.next {
        margin-right: -30px;
    }

    /* Responsive adjustments */
    @media (max-width:860px) {
        :root {
            --card-w: 160px;
            --card-h: 240px;
        }

        .btn-arrow {
            width: 50px;
            height: 50px;
            font-size: 18px;
        }

        .btn-arrow.prev {
            margin-left: -25px;
        }

        .btn-arrow.next {
            margin-right: -25px;
        }

        .main-title {
            font-size: 2.8rem;
        }

        .subtitle {
            font-size: 1.1rem;
        }

        .wrap {
            gap: 40px;
        }
    }

    @media (max-width:520px) {
        .btn-arrow {
            width: 44px;
            height: 44px;
            font-size: 16px;
        }

        .btn-arrow.prev {
            margin-left: -22px;
        }

        .btn-arrow.next {
            margin-right: -22px;
        }

        .main-title {
            font-size: 2.2rem;
        }

        .subtitle {
            font-size: 1rem;
        }

        .wrap {
            gap: 30px;
            padding: 40px 20px;
        }

        .card-number {
            font-size: 3.5rem;
        }

        .card-period {
            font-size: 0.85rem;
        }

        .card-subtitle {
            font-size: 0.8rem;
        }

        .card-overlay {
            padding: 20px 16px;
            padding-left: 16px;
        }

        .card-item.center .card-number {
            font-size: 4.5rem;
        }

        .card-item.center .card-period {
            font-size: 1rem;
        }

        .card-item.center .card-subtitle {
            font-size: 0.9rem;
        }
    }
</style>
<section class="content-section bg-secondary" style="min-height: 100vh;">
    <div class="wrap">
        <!-- Title Section -->
        <div class="title-section">
            <h1 class="main-title">JEJAK KETUA UMUM</h1>
            <p class="subtitle">Discover our latest creative works and explorations</p>
        </div>

        <div class="carousel-stage">
            <div class="cards" id="cards">
                <div class="card-item" data-index="0">
                    <img src="assets/img/firdausputraginting.jpg" alt="1">
                    <div class="card-overlay">
                        <div class="card-number">01</div>
                        <div class="card-period">Ketua 2024-2025</div>
                        <div class="card-subtitle">Angkatan Selected projects from the last month</div>
                    </div>
                </div>
                <div class="card-item" data-index="1">
                    <img src="https://placehold.co/600x900/7b2f00/ffffff?text=" alt="2">
                    <div class="card-overlay">
                        <div class="card-number">02</div>
                        <div class="card-period">Explore 2024</div>
                        <div class="card-subtitle">Otherworldly places located on Earth</div>
                    </div>
                </div>
                <div class="card-item" data-index="2">
                    <img src="https://placehold.co/600x900/034f84/ffffff?text=" alt="3">
                    <div class="card-overlay">
                        <div class="card-number">03</div>
                        <div class="card-period">Audio Visual</div>
                        <div class="card-subtitle">Visualize distorted sound waves</div>
                    </div>
                </div>
                <div class="card-item" data-index="3">
                    <img src="https://placehold.co/600x900/5a3b7a/ffffff?text=" alt="4">
                    <div class="card-overlay">
                        <div class="card-number">04</div>
                        <div class="card-period">Research 2024</div>
                        <div class="card-subtitle">Explorations & field notes</div>
                    </div>
                </div>
                <div class="card-item" data-index="4">
                    <img src="https://placehold.co/600x900/ff6b6b/ffffff?text=" alt="5">
                    <div class="card-overlay">
                        <div class="card-number">05</div>
                        <div class="card-period">Portfolio</div>
                        <div class="card-subtitle">A quick look at recent work</div>
                    </div>
                </div>
            </div>

            <!-- Improved arrow controls -->
            <div class="controls">
                <button class="btn-arrow prev" id="prev" aria-label="Previous slide">‹</button>
                <button class="btn-arrow next" id="next" aria-label="Next slide">›</button>
            </div>
        </div>
    </div>
</section>

<script>
    const cardsEl = document.getElementById('cards');
    const items = Array.from(cardsEl.querySelectorAll('.card-item'));
    let index = 2; // center
    const total = items.length;

    function render() {
        items.forEach((el, i) => {
            el.className = 'card-item';
            const diff = ((i - index) + total) % total;
            if (diff === 0) el.classList.add('center');
            else if (diff === 1) el.classList.add('right');
            else if (diff === 2) el.classList.add('right2');
            else if (diff === total - 1) el.classList.add('left');
            else if (diff === total - 2) el.classList.add('left2');
            else el.classList.add('hide');
        });
    }

    function next() { index = (index + 1) % total; render(); }
    function prev() { index = (index - 1 + total) % total; render(); }
    function goTo(i) { index = i; render(); }

    // Event listeners
    document.getElementById('next').addEventListener('click', () => { next(); resetTimer(); });
    document.getElementById('prev').addEventListener('click', () => { prev(); resetTimer(); });
    items.forEach((el, i) => el.addEventListener('click', () => { goTo(i); resetTimer(); }));

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { prev(); resetTimer(); }
        else if (e.key === 'ArrowRight') { next(); resetTimer(); }
    });

    // Auto-play functionality
    let timer = setInterval(next, 3500);
    function resetTimer() { clearInterval(timer); timer = setInterval(next, 3500); }

    // Pause on hover
    const stage = document.querySelector('.carousel-stage');
    stage.addEventListener('mouseenter', () => clearInterval(timer));
    stage.addEventListener('mouseleave', () => timer = setInterval(next, 3500));

    // Initialize
    render();
</script>