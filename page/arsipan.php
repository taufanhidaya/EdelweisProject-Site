<!-- page/visi-misi.php -->
<section class="content-section bg-secondary" style="min-height: 100vh;">
    <div class="container-fluid">
        <div class="main-container">
            <h1 class="title">Arsipan Data UKM-PA Edelweis PNL</h1>
            
            <!-- Period Selection -->
            <div class="row">
                <div class="col-md-6">
                    <div class="text-center">
                        <h3 class="period-title">Periode Lama</h3>
                        <div class="dropdown">
                            <button class="btn period-btn dropdown-toggle" type="button" id="periodDropdown1" data-bs-toggle="dropdown">
                                Select periode
                            </button>
                            <ul class="dropdown-menu" id="periodList1">
                                <!-- 15 periode pertama akan diload dari database -->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-center">
                        <h3 class="period-title">Periode Baru</h3>
                        <div class="dropdown">
                            <button class="btn period-btn dropdown-toggle" type="button" id="periodDropdown2" data-bs-toggle="dropdown">
                                Select periode
                            </button>
                            <ul class="dropdown-menu" id="periodList2">
                                <!-- 15 periode kedua akan diload dari database -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3" id="selectedPeriodInfo" style="display: none;">
                <div class="col-12">
                    <div class="text-center">
                        <div class="alert alert-info">
                            <strong>Periode Terpilih:</strong> <span id="selectedPeriodText"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section-divider"></div>
            
            <!-- Member Cards -->
            <div class="row" id="memberCards">
                <div class="col-lg-4 col-md-6">
                    <div class="member-card">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <span class="badge bg-secondary mb-2">Periode 1998-1999</span>
                            </div>
                            <img src="https://via.placeholder.com/300x250/4a90e2/ffffff?text=FOTO" 
                                 alt="Khairul Ausa" class="member-photo">
                            <div class="member-name-overlay">KHAIRUL AUSA</div>
                            <div class="member-batch">ANGKATAN I</div>
                            <div class="badge-container">
                                <button class="btn member-badge">G</button>
                                <button class="btn member-badge">A</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="member-card">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <span class="badge bg-secondary mb-2">Periode 1998-1999</span>
                            </div>
                            <img src="https://via.placeholder.com/300x250/e74c3c/ffffff?text=FOTO" 
                                 alt="Member 2" class="member-photo">
                            <div class="member-name-overlay">NAMA ANGGOTA 2</div>
                            <div class="member-batch">ANGKATAN I</div>
                            <div class="badge-container">
                                <button class="btn member-badge">G</button>
                                <button class="btn member-badge">A</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="member-card">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <span class="badge bg-secondary mb-2">Periode 1998-1999</span>
                            </div>
                            <img src="https://via.placeholder.com/300x250/f39c12/ffffff?text=FOTO" 
                                 alt="Member 3" class="member-photo">
                            <div class="member-name-overlay">NAMA ANGGOTA 3</div>
                            <div class="member-batch">ANGKATAN I</div>
                            <div class="badge-container">
                                <button class="btn member-badge">G</button>
                                <button class="btn member-badge">A</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Add Reset/Show All Button -->
            <div class="text-center mt-4">
                <button class="btn btn-lg me-3" 
                        style="background: linear-gradient(135deg, #6c757d, #495057); color: white; border-radius: 50px; padding: 12px 40px; font-weight: 600;" 
                        onclick="showAllMembers()">
                    Tampilkan Semua
                </button>
                <button class="btn btn-lg" 
                        style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 50px; padding: 12px 40px; font-weight: 600;" 
                        onclick="addMoreMembers()">
                    Tambah Anggota
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    // Sample database data - dalam implementasi nyata akan diambil dari API/Database
    const periodsData = [
        { id_periode: 1, periode: '1998-1999' },
        { id_periode: 2, periode: '1999-2000' },
        { id_periode: 3, periode: '2000-2001' },
        { id_periode: 4, periode: '2001-2002' },
        { id_periode: 5, periode: '2002-2003' },
        { id_periode: 6, periode: '2003-2004' },
        { id_periode: 7, periode: '2004-2005' },
        { id_periode: 8, periode: '2005-2006' },
        { id_periode: 9, periode: '2006-2007' },
        { id_periode: 10, periode: '2007-2008' },
        { id_periode: 11, periode: '2008-2009' },
        { id_periode: 12, periode: '2009-2010' },
        { id_periode: 13, periode: '2010-2011' },
        { id_periode: 14, periode: '2011-2012' },
        { id_periode: 15, periode: '2012-2013' },
        { id_periode: 16, periode: '2013-2014' },
        { id_periode: 17, periode: '2014-2015' },
        { id_periode: 18, periode: '2015-2016' },
        { id_periode: 19, periode: '2016-2017' },
        { id_periode: 20, periode: '2017-2018' },
        { id_periode: 21, periode: '2018-2019' },
        { id_periode: 22, periode: '2019-2020' },
        { id_periode: 23, periode: '2020-2021' },
        { id_periode: 24, periode: '2021-2022' },
        { id_periode: 25, periode: '2022-2023' },
        { id_periode: 26, periode: '2023-2024' },
        { id_periode: 27, periode: '2024-2025' },
        { id_periode: 28, periode: '2025-2026' },
        { id_periode: 29, periode: '2026-2027' },
        { id_periode: 30, periode: '2027-2028' }
    ];

    // Function to load periods into two dropdowns (15 each)
    function loadPeriods() {
        const periodList1 = document.getElementById('periodList1');
        const periodList2 = document.getElementById('periodList2');

        periodList1.innerHTML = '';
        periodList2.innerHTML = '';

        // Split periods into two groups of 15
        const firstHalf = periodsData.slice(0, 15);
        const secondHalf = periodsData.slice(15, 30);

        // Load first 15 periods
        firstHalf.forEach(period => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `
                    <a class="dropdown-item" 
                       href="#" 
                       onclick="selectPeriod(${period.id_periode}, '${period.periode}', 'periodDropdown1')">
                        ${period.periode}
                    </a>
                `;
            periodList1.appendChild(listItem);
        });

        // Load next 15 periods
        secondHalf.forEach(period => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `
                    <a class="dropdown-item" 
                       href="#" 
                       onclick="selectPeriod(${period.id_periode}, '${period.periode}', 'periodDropdown2')">
                        ${period.periode}
                    </a>
                `;
            periodList2.appendChild(listItem);
        });
    }

    // Function to select period
    function selectPeriod(id, periode, dropdownId) {
        // Update the specific dropdown button text
        document.getElementById(dropdownId).textContent = periode;

        // Show selected period info
        document.getElementById('selectedPeriodText').textContent = periode;
        document.getElementById('selectedPeriodInfo').style.display = 'block';

        // Reset other dropdown to default
        const otherDropdownId = dropdownId === 'periodDropdown1' ? 'periodDropdown2' : 'periodDropdown1';
        document.getElementById(otherDropdownId).textContent = 'Select periode';

        // Filter member cards based on selected period
        filterMembersByPeriod(periode);

        // Scroll to the filtered members section
        setTimeout(() => {
            scrollToFilteredMembers();
        }, 300);

        console.log(`Selected Period: ID=${id}, Periode=${periode}`);
    }

    // Function to filter members by period and arrange in rows of 4
    function filterMembersByPeriod(selectedPeriode) {
        const memberCards = document.querySelectorAll('.member-card');
        const memberCardsContainer = document.getElementById('memberCards');
        const filteredCards = [];

        // Hide all cards first
        memberCards.forEach(card => {
            const cardElement = card.parentElement;
            const periodBadge = card.querySelector('.badge');
            const cardPeriod = periodBadge.textContent.replace('Periode ', '');

            if (cardPeriod === selectedPeriode) {
                filteredCards.push(cardElement);
                cardElement.style.display = 'block';
            } else {
                cardElement.style.display = 'none';
            }
        });

        // Rearrange filtered cards into rows of 4
        arrangeCardsInRows(filteredCards, memberCardsContainer);
    }

    // Function to arrange cards in rows of 4
    function arrangeCardsInRows(cards, container) {
        // Create a temporary container for filtered cards
        const tempContainer = document.createElement('div');
        tempContainer.className = 'row';
        tempContainer.id = 'filteredMemberCards';

        // Remove existing filtered container if it exists
        const existingFiltered = document.getElementById('filteredMemberCards');
        if (existingFiltered) {
            existingFiltered.remove();
        }

        // Clone and add filtered cards to temp container
        cards.forEach((card, index) => {
            const clonedCard = card.cloneNode(true);
            clonedCard.className = 'col-lg-3 col-md-6 col-sm-12'; // 4 cards per row on large screens
            clonedCard.style.display = 'block';
            tempContainer.appendChild(clonedCard);
        });

        // Hide original container and show filtered container
        container.style.display = 'none';
        container.parentNode.insertBefore(tempContainer, container.nextSibling);

        // Add animation to filtered cards
        const filteredCards = tempContainer.querySelectorAll('.member-card');
        filteredCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    // Function to scroll to filtered members
    function scrollToFilteredMembers() {
        const filteredSection = document.getElementById('filteredMemberCards') || document.getElementById('memberCards');
        if (filteredSection) {
            const offsetTop = filteredSection.offsetTop - 100; // 100px offset from top
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    }

    // Function to show all members
    function showAllMembers() {
        // Remove filtered container if exists
        const filteredContainer = document.getElementById('filteredMemberCards');
        if (filteredContainer) {
            filteredContainer.remove();
        }

        // Show original container
        const originalContainer = document.getElementById('memberCards');
        originalContainer.style.display = 'flex';

        // Show all original cards
        const memberCards = originalContainer.querySelectorAll('.member-card');
        memberCards.forEach(card => {
            card.style.display = 'block';
            card.parentElement.style.display = 'block';
        });

        // Reset both dropdowns
        document.getElementById('periodDropdown1').textContent = 'Select periode';
        document.getElementById('periodDropdown2').textContent = 'Select periode';
        document.getElementById('selectedPeriodInfo').style.display = 'none';

        // Scroll back to member section
        setTimeout(() => {
            scrollToFilteredMembers();
        }, 300);
    }

    // Function to add more members (updated for database integration)
    function addMoreMembers() {
        const memberCards = document.getElementById('memberCards');
        const randomPeriod = periodsData[Math.floor(Math.random() * periodsData.length)];

        const newMemberHTML = `
                <div class="col-lg-4 col-md-6">
                    <div class="member-card">
                        <div class="card-body p-4">
                            <div class="text-center mb-3">
                                <span class="badge bg-secondary mb-2">Periode ${randomPeriod.periode}</span>
                            </div>
                            <div style="position: relative;">
                                <img src="https://via.placeholder.com/300x250/9b59b6/ffffff?text=FOTO" 
                                     alt="New Member" class="member-photo">
                                <div class="member-name-overlay">ANGGOTA BARU</div>
                            </div>
                            <div class="member-batch">ANGKATAN BARU</div>
                            <div class="badge-container">
                                <button class="btn member-badge">G</button>
                                <button class="btn member-badge">A</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        memberCards.insertAdjacentHTML('beforeend', newMemberHTML);
    }

    // Function to fetch periods from actual database (template)
    async function fetchPeriodsFromDatabase() {
        try {
            // Template untuk implementasi dengan API
            // const response = await fetch('/api/periods');
            // const periods = await response.json();
            // return periods;

            // Untuk demo, return sample data
            return periodsData;
        } catch (error) {
            console.error('Error fetching periods:', error);
            return [];
        }
    }

    // Initialize periods when page loads
    document.addEventListener('DOMContentLoaded', function () {
        loadPeriods();

        // Add smooth scroll animations
        const cards = document.querySelectorAll('.member-card');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });
</script>