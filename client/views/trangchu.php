<!-- Carousel  -->
<div class="home-section pt-0 mt-0">
    <div class="swiper homeSwiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <a href="#">
                    <img src="public/img/luyen_de.png" class="banner-img">
                </a>
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide">
                <a href="#">
                    <img src="public/img/luyen_de_1.png" class="banner-img">
                </a>
            </div>
        </div>
        <!-- Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>
</div>
<!-- End Carousel -->

<!-- Đề thi mới nhất -->
<div class="home-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="home-title mb-0">Đề thi mới nhất</h2>
            <a href="javascript:void(0)" onclick="requireLogin('index.php?act=dethi')" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3" id="latest-exams-container">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const container = document.getElementById("latest-exams-container");
    try {
        const res = await fetch(apiUrl("exam/list", { limit: 8 }));
        const json = await res.json();
        
        if (json.success && json.data && json.data.length > 0) {
            container.innerHTML = json.data.map(row => {
                const isOngoing = parseInt(row.is_ongoing) === 1;
                
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
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">Chưa có đề thi</div>
                </div>
            `;
        }
    } catch (e) {
        console.error("Lỗi khi tải đề thi:", e);
        container.innerHTML = `<div class="col-12 alert alert-danger text-center">Không thể tải dữ liệu</div>`;
    }
});
</script>
<!--  End Đề thi -->


<!-- Banner-->
<div class="container-fluid" style="margin-top: 20px;">
    <section class="testonline-banner">
        <img src="public/img/testonline_banner.png" alt="Test Online Banner">
    </section>
</div>
<!-- End Banner  -->

<!-- Giới thiệu + Form -->
<div class="home-section" style="background: #f8fafc; margin-top: 0; padding: 60px 0;">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- LEFT: Info -->
            <div class="col-12 col-lg-7">
                <h2 class="fw-bold mb-4" style="font-size: 24px;">
                    Phần mềm luyện thi online — <span style="color:#3b5bdb;">PT QUIZ</span>
                </h2>
                <div class="quiz-content">
                    <p>PT QUIZ là nền tảng luyện thi trắc nghiệm online giúp người học ôn tập hiệu quả với nhiều chủ đề như TOEIC, IELTS, Lập trình, Toán học và nhiều lĩnh vực khác.</p>
                    <p>Hệ thống mô phỏng đề thi thật, cung cấp ngân hàng câu hỏi đa dạng, luyện tập theo chủ đề hoặc làm đề full test.</p>
                    <p>Theo dõi tiến độ học tập, thống kê kết quả chi tiết và gợi ý lộ trình phù hợp.</p>
                    <p class="highlight-text">Luyện tập miễn phí ngay hôm nay cùng PT QUIZ!</p>
                </div>
                <div class="mt-4 d-flex gap-3 flex-wrap">
                    <a href="javascript:void(0)" onclick="requireLogin('index.php?act=dethi')" class="btn btn-primary px-4 rounded-pill">
                        <i class="fas fa-book-open me-2"></i>Vào thư viện đề thi
                    </a>
                    <a href="index.php?act=gioithieu" class="btn btn-outline-secondary px-4 rounded-pill">
                        Tìm hiểu thêm
                    </a>
                </div>
            </div>

            <!-- RIGHT: Form -->
            <div class="col-12 col-lg-5">
                <div class="card premium-card border-0 p-4 p-md-5">
                    <h5 class="fw-bold mb-4 text-dark">
                        <i class="fas fa-graduation-cap me-2 text-primary fs-4 align-middle"></i>Tư vấn lộ trình học
                    </h5>
                    <form>
                        <div class="mb-3">
                            <input class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Họ tên *" required>
                        </div>
                        <div class="mb-3">
                            <input class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Số điện thoại *" type="tel" required>
                        </div>
                        <div class="mb-3">
                            <input class="form-control bg-light border-0 py-3 px-4 rounded-3" placeholder="Khu vực học *" required>
                        </div>
                        <div class="mb-4 pb-2">
                            <select class="form-select bg-light border-0 py-3 px-4 rounded-3 text-muted shadow-none">
                                <option>Môn học bạn quan tâm</option>
                                <option>TOEIC</option>
                                <option>IELTS</option>
                                <option>Lập trình</option>
                                <option>Toán học</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm custom-btn text-white mt-1">
                            Đăng ký tư vấn miễn phí
                        </button>
                    </form>
                </div>
            </div>

</div>


        </div>

    </div>

</div>

<!--End Tư vấn -->





<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<!-- SCRIPT Carousel -->
<script>
    const swiper = new Swiper(".homeSwiper", {
        loop: true,

        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },

        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },

        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>

<script>
    const courseSwiper = new Swiper(".courseSwiper", {

        spaceBetween: 20,

        pagination: {
            el: ".courseSwiper .swiper-pagination",
            clickable: true,
        },

        breakpoints: {

            0: {
                slidesPerView: 1.2,
            },

            576: {
                slidesPerView: 2,
            },

            992: {
                slidesPerView: 3,
            },

            1200: {
                slidesPerView: 3,
            }

        }

    });
</script>
