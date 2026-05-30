<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>
<div class="container-lg my-5" style="min-height: 60vh;">
    <!-- Modern Page Header -->
    <div class="d-flex align-items-center mb-5">
        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
            <i class="fa-solid fa-clock-rotate-left fs-4"></i>
        </div>
        <div>
            <h2 class="mb-1 fw-bold text-dark">Lịch sử làm bài</h2>
            <p class="text-muted mb-0">Theo dõi và xem lại kết quả các bài thi của bạn</p>
        </div>
    </div>

    <!-- History Cards Grid -->
    <div class="row g-4" id="history-container">
        <!-- Dữ liệu được nạp bằng JS -->
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div id="pagination-container" class="d-flex justify-content-center mt-5 mb-4">
        <!-- Phân trang được nạp bằng JS -->
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const historyContainer = document.getElementById("history-container");
    const paginationContainer = document.getElementById("pagination-container");
    
    // Lấy trang hiện tại từ URL
    const urlParams = new URLSearchParams(window.location.search);
    let currentPage = parseInt(urlParams.get('p')) || 1;
    const limit = 10;

    const formatDate = (dateStr) => {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    };

    const loadHistory = async (page) => {
        const offset = (page - 1) * limit;
        historyContainer.innerHTML = `<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>`;
        
        try {
            const res = await fetch(apiUrl("history/list", { limit, offset }));
            const json = await res.json();

            if (json.success && json.data.length > 0) {
                historyContainer.innerHTML = json.data.map((row) => {
                    const isDone = row.trangthai === 'done';
                    const statusBadge = isDone
                        ? `<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">Đã nộp bài</span>`
                        : `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill">Đang làm dở</span>`;
                    
                    const scoreSection = isDone
                        ? `<div class="text-primary fw-bold fs-3 mb-0 lh-1">${row.diem ?? '0'} <span class="fs-6 text-muted fw-normal">/ 10</span></div>
                           <div class="text-muted small mt-2"><i class="fa-solid fa-check-circle text-success me-1"></i>${row.socaudung ?? '0'}/${row.tongcauhoi} câu đúng</div>`
                        : `<div class="text-muted fw-medium fs-5 mt-3 pt-2">Chưa có điểm</div>`;
                        
                    const actionBtn = isDone
                        ? `<a href="index.php?act=ketqua&id=${row.id_lanthi}" class="btn btn-outline-primary rounded-pill px-4" style="border-width: 2px;">Xem chi tiết</a>`
                        : `<a href="index.php?act=lambai&id=${row.id_baithi}" class="btn btn-primary rounded-pill px-4 shadow-sm">Tiếp tục thi</a>`;

                    return `
                    <div class="col-12 col-lg-6">
                        <div class="history-card bg-white p-4 rounded-4 shadow-sm position-relative h-100 d-flex flex-column transition-hover">
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-3">
                                <div>
                                    <h5 class="fw-bold text-dark mb-2 lh-base" style="font-size: 1.15rem;">${row.ten_baithi}</h5>
                                    <div class="text-muted small mb-1"><i class="fa-regular fa-clock me-1"></i> Bắt đầu: ${formatDate(row.thoigianbatdau)}</div>
                                    ${row.thoigiannop ? `<div class="text-muted small"><i class="fa-solid fa-check me-1"></i> Nộp bài: ${formatDate(row.thoigiannop)}</div>` : ''}
                                </div>
                                <div class="flex-shrink-0">
                                    ${statusBadge}
                                </div>
                            </div>
                            <hr class="text-muted opacity-25 my-auto pb-3">
                            <div class="d-flex justify-content-between align-items-end mt-2">
                                <div>
                                    ${scoreSection}
                                </div>
                                <div>
                                    ${actionBtn}
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                }).join('');
                
                renderPagination(json.total, page);
            } else {
                historyContainer.innerHTML = `<div class="col-12 text-center py-5">
                    <div class="mb-3"><i class="fa-solid fa-folder-open text-muted" style="font-size: 4rem; opacity: 0.5;"></i></div>
                    <h5 class="text-muted mb-3">Bạn chưa tham gia bài thi nào.</h5>
                    <a href="index.php?act=dethi" class="btn btn-primary rounded-pill px-4">Đến thư viện đề thi</a>
                </div>`;
                paginationContainer.innerHTML = '';
            }
        } catch (e) {
            console.error(e);
            historyContainer.innerHTML = `<div class="col-12 text-center py-4 text-danger">Lỗi tải dữ liệu.</div>`;
        }
    };

    const renderPagination = (total, page) => {
        const totalPages = Math.ceil(total / limit);
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination pagination-rounded mb-0">';
        html += `<li class="page-item ${page <= 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${page - 1}"><i class="fa-solid fa-chevron-left"></i></a>
                 </li>`;

        for (let p = 1; p <= totalPages; p++) {
            html += `<li class="page-item ${p === page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${p}">${p}</a>
                     </li>`;
        }

        html += `<li class="page-item ${page >= totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${page + 1}"><i class="fa-solid fa-chevron-right"></i></a>
                 </li>`;
        html += '</ul>';

        paginationContainer.innerHTML = html;

        paginationContainer.querySelectorAll(".page-link").forEach(link => {
            link.onclick = (e) => {
                e.preventDefault();
                const p = parseInt(link.dataset.page);
                if (p && p !== page) {
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('p', p);
                    window.history.pushState({}, '', newUrl);
                    loadHistory(p);
                }
            };
        });
    };

    loadHistory(currentPage);
});
</script>
