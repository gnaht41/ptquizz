<?php
// Khách có thể xem danh sách đề thi.
// Việc chặn truy cập được xử lý ở phía JS (confirmLambai).
?>
<style>
    /* Tag-style for subjects */
    #subjects-nav .nav-link {
        border-radius: 30px;
        padding: 5px 15px;
        margin-right: 8px;
        margin-bottom: 8px;
        background: #f0f2f5;
        color: #4b5563;
        font-size: 14px;
        border: 1px solid transparent;
        transition: 0.2s;
    }
    #subjects-nav .nav-link:hover {
        background: #e5e7eb;
    }
    #subjects-nav .nav-link.active {
        background: #3b5bdb !important;
        color: #fff !important;
    }
    .testitem-wrapper:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06) !important;
    }
    .testitem-title {
        color: #333;
        text-decoration: none;
    }
    #subjects-nav .nav-link.active {
        background: #3b5bdb !important;
        color: #fff !important;
    }
    .testitem-wrapper:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06) !important;
    }
    .testitem-title {
        color: #333;
        text-decoration: none;
    }
</style>

<div class="home-section">
    <div class="container">
        <!-- Tiêu đề & Lọc -->
        <div class="row align-items-center mb-4">
            <div class="col-md-7">
                <h1 class="home-title text-start mb-3">Thư viện đề thi</h1>
                <ul class="nav nav-pills" id="subjects-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-mon="">Tất cả</a>
                    </li>
                    <!-- Loaded via JS -->
                </ul>
            </div>
            <div class="col-md-5">
                <form id="search-form" class="mt-3 mt-md-0">
                    <div class="input-group">
                        <input type="text" class="form-control shadow-none" id="search-input" placeholder="Tìm tên đề thi..." 
                               value="<?= htmlspecialchars($_GET['term'] ?? '') ?>">
                        <button class="btn btn-primary btn-submit mt-0 px-4" type="submit" style="width: auto;">Tìm kiếm</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danh sách đề thi -->
        <div class="row g-3" id="exams-container">
            <!-- Loaded via JS -->
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const examsContainer = document.getElementById("exams-container");
    const subjectsNav = document.getElementById("subjects-nav");
    const searchForm = document.getElementById("search-form");
    const searchInput = document.getElementById("search-input");
    
    let currentMon = '';
    let currentTerm = searchInput.value;

    const loadExams = async () => {
        examsContainer.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';
        try {
            const res = await fetch(apiUrl("exam/list", {
                mon: currentMon,
                term: currentTerm
            }));
            const json = await res.json();

            if (json.success && json.data && json.data.length > 0) {
                examsContainer.innerHTML = json.data.map(row => {
                    const isOngoing = parseInt(row.is_ongoing) === 1;
                    const btnClass = isOngoing ? 'btn-lam-tiep' : 'btn-lam-bai';
                    const btnText = isOngoing ? 'Làm tiếp' : 'Làm bài';
                    
                    return `
                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                            <div class="card premium-card h-100 border-0 overflow-hidden d-flex flex-column transition-hover">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Badge subject -->
                                    <div class="mb-3 d-flex justify-content-between align-items-start">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-medium">${row.tenmonhoc}</span>
                                        ${isOngoing ? '<span class="badge bg-warning-subtle text-warning-emphasis rounded-circle p-2" title="Đang làm dở" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-clock-rotate-left"></i></span>' : ''}
                                    </div>
                                    
                                    <!-- Title -->
                                    <h5 class="card-title fw-bold text-dark lh-base mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; min-height: 48px; font-size: 1.1rem;">
                                        ${row.ten_baithi}
                                    </h5>
                                    
                                    <!-- Meta Info -->
                                    <div class="d-flex text-muted small mt-auto mb-4 bg-light rounded-3 p-2">
                                        <div class="flex-fill text-center border-end border-2 border-white d-flex flex-column justify-content-center">
                                            <i class="fa-regular fa-clock text-primary mb-1 fs-6"></i>
                                            <span class="fw-medium">${row.thoigianlam} phút</span>
                                        </div>
                                        <div class="flex-fill text-center d-flex flex-column justify-content-center">
                                            <i class="fa-regular fa-circle-question text-primary mb-1 fs-6"></i>
                                            <span class="fw-medium">${row.tongcauhoi} câu</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <button onclick="confirmLambai(${row.id_baithi}, ${isOngoing})" class="btn ${isOngoing ? 'btn-warning text-dark' : 'btn-primary'} w-100 rounded-pill py-2 shadow-sm fw-bold custom-btn mt-auto">
                                        ${isOngoing ? '<i class="fa-solid fa-play me-2"></i>Làm tiếp bài' : '<i class="fa-solid fa-pen me-2"></i>Bắt đầu thi'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                examsContainer.innerHTML = '<div class="col-12 text-center py-5"><div class="alert alert-light border shadow-sm">Không tìm thấy đề thi.</div></div>';
            }
        } catch (e) {
            examsContainer.innerHTML = '<div class="col-12 text-center py-5 text-danger">Lỗi tải dữ liệu.</div>';
        }
    };

    const loadSubjects = async () => {
        try {
            const res = await fetch(apiUrl("exam/subjects"));
            const json = await res.json();
            if (json.success) {
                const subjectsHtml = json.data.map(m => `
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-mon="${m.id_monhoc}">${m.tenmonhoc}</a>
                    </li>
                `).join('');
                subjectsNav.innerHTML = `<li class="nav-item"><a class="nav-link active" href="#" data-mon="">Tất cả</a></li>` + subjectsHtml;

                subjectsNav.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        subjectsNav.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                        link.classList.add('active');
                        currentMon = link.getAttribute('data-mon');
                        loadExams();
                    });
                });
            }
        } catch (e) {}
    };

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        currentTerm = searchInput.value;
        loadExams();
    });

    loadSubjects();
    loadExams();
});
</script>
