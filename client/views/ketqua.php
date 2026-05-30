<?php
if (!isset($_SESSION['user'])) {
    header("Location:index.php?act=dangnhap");
    exit;
}
$id_lanthi = (int)($_GET['id'] ?? 0);
?>
<style>
    .result-header {
        background: #fff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }
    .score-value {
        font-size: 48px;
        font-weight: 800;
        color: #3b5bdb;
        line-height: 1;
    }
    .stat-card {
        padding: 15px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        text-align: center;
    }
    .stat-num {
        font-size: 20px;
        font-weight: 700;
        display: block;
    }
    .stat-txt {
        font-size: 13px;
        color: #64748b;
    }
    .question-item {
        border-left: 4px solid #e5e7eb;
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .question-item.correct { border-left-color: #22c55e; }
    .question-item.wrong { border-left-color: #ef4444; }
    .question-item.empty { border-left-color: #cbd5e1; }
    
    .ans-opt {
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 8px;
        border: 1px solid #f1f5f9;
    }
    .ans-opt.correct { background: #f0fdf4; border-color: #bcf0da; color: #166534; }
    .ans-opt.selected-wrong { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
</style>

<div class="container my-5" id="result-container">
    <div class="text-center py-5">
        <div class="spinner-border text-primary"></div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", async () => {
    const container = document.getElementById("result-container");
    const id_lanthi = <?= $id_lanthi ?>;

    function escapeHtml(text) {
        if (!text) return "";
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    function json_decode_safe(str) {
        try { return JSON.parse(str); } catch(e) { return null; }
    }

    try {
        const res = await fetch(apiUrl("result/detail", { id: id_lanthi }));
        const json = await res.json();

        if (json.success) {
            const { lanthi, questions, stats } = json;
            const canSeeAnswers = parseInt(lanthi.hien_dapan) === 1;
            
            const { correct, wrong, empty } = stats;

            let html = `
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- Simplified Summary -->
                        <div class="result-header text-center">
                            <h4 class="fw-bold mb-4">${lanthi.ten_baithi}</h4>
                            <div class="mb-4">
                                <span class="text-muted d-block small mb-1">Điểm số</span>
                                <span class="score-value">${lanthi.diem}</span>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="stat-card">
                                        <span class="stat-num text-success">${correct}</span>
                                        <span class="stat-txt">Đúng</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-card">
                                        <span class="stat-num text-danger">${wrong}</span>
                                        <span class="stat-txt">Sai</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-card">
                                        <span class="stat-num text-warning">${empty}</span>
                                        <span class="stat-txt">Bỏ trống</span>
                                    </div>
                                </div>
                            </div>
                        <!-- Mini Review -->
                        <div class="mt-5">
                            ${canSeeAnswers ? `
                                <h5 class="fw-bold mb-4">Xem lại bài làm</h5>
                                ${questions.map((q, i) => {
                                     let typeCls = q.status; 
                                     let qContent = q.noidungcauhoi;
                                     let answersHtml = '';

                                     if (q.loai_cauhoi === 2) {
                                         const decoded = json_decode_safe(q.user_text_ans);
                                         const userAns = Array.isArray(decoded) ? decoded : [q.user_text_ans || ''];
                                         const correctAnswers = q.answers; 
                                         let count = 0;

                                         qContent = qContent.replace(/\[\.\.\.\]/g, () => {
                                             const idx = count++;
                                             const sub = userAns[idx] || '';
                                             const cor = correctAnswers[idx]?.noidungdapan || '';
                                             let isMatch = sub.trim().toLowerCase() === cor.trim().toLowerCase();

                                             let style = 'border-bottom: 2px solid #cbd5e1; padding: 0 5px; font-weight: 600;';
                                             if (sub) {
                                                 style += isMatch ? 'color: #166534; border-color: #22c55e;' : 'color: #991b1b; border-color: #ef4444;';
                                             }
                                             return `<span style="${style}">${escapeHtml(sub || '...')}</span> ${((!isMatch && sub)) ? `<small class="text-success">[${escapeHtml(cor)}]</small>` : ''}`;
                                         });

                                         if (typeCls === 'wrong' || typeCls === 'empty') {
                                             answersHtml = `<div class="mt-2 text-muted small">Đáp án đúng là: ${correctAnswers.map(a => `<b class='text-success'>${a.noidungdapan}</b>`).join(', ')}</div>`;
                                         }
                                     } else {
                                         answersHtml = `
                                             <div class="options text-start">
                                                 ${q.answers.map((ans, aIdx) => {
                                                     const prefix = String.fromCharCode(65 + aIdx);
                                                     let cls = "ans-opt d-flex align-items-start";
                                                     if (ans.dapandung) cls += " correct";
                                                     if (ans.selected && !ans.dapandung) cls += " selected-wrong";
                                                     return `<div class="${cls}">
                                                                 <strong class="me-2">${prefix}.</strong> 
                                                                 <span>${ans.noidungdapan}</span> 
                                                                 ${ans.selected ? '<strong class="ms-2">(Bạn chọn)</strong>' : ''}
                                                             </div>`;
                                                 }).join('')}
                                             </div>
                                         `;
                                     }

                                     return `
                                         <div class="question-item text-start ${typeCls}">
                                             <p class="fw-bold mb-3">Câu ${i+1}: ${qContent}</p>
                                             ${answersHtml}
                                             ${q.loigiai_chitiet ? (
                                                <?php echo (isset($_SESSION['user']['premium_status']) && $_SESSION['user']['premium_status'] == 1) ? 'true' : 'false'; ?> 
                                                ? `
                                                 <details class="mt-3 border rounded shadow-sm" style="border-color: #e2e8f0; border-left: 4px solid #94a3b8 !important; background-color: #f8fafc;">
                                                     <summary class="p-3 small fw-bold text-secondary" style="cursor: pointer; outline: none; list-style: none;">
                                                         <i class="fas fa-book-open me-2 text-secondary"></i>Xem lời giải chi tiết
                                                     </summary>
                                                     <div class="p-3 pt-0 small text-dark border-top" style="border-color: #e2e8f0 !important; color: #334155 !important;">
                                                        ${q.loigiai_chitiet}
                                                     </div>
                                                 </details>
                                                ` : `
                                                 <div class="mt-3 p-3 bg-light border-left-warning rounded" style="border-left: 4px solid #f59e0b;">
                                                     <div class="small fw-bold text-warning mb-1"><i class="fas fa-lock me-1"></i>Tính năng cao cấp</div>
                                                     <div class="small text-muted">Bạn cần <a href="index.php?act=premium" class="fw-bold text-warning text-decoration-none">Nâng cấp Premium</a> để có thể xem lời giải chi tiết cho câu hỏi này.</div>
                                                 </div>
                                                `
                                             ) : ''}
                                         </div>
                                     `;
                                 }).join('')}
                            ` : `
                                <div class="alert alert-light text-center p-5 border shadow-sm" style="border-radius: 15px; background: #fff;">
                                    <div class="mb-3">
                                        <i class="fas fa-lock text-muted opacity-50" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Tính năng xem lại đã bị khóa</h5>
                                    <p class="text-muted small mx-auto" style="max-width: 300px;">Giảng viên không cho phép xem lại nội dung câu hỏi và đáp án cho đề thi này.</p>
                                </div>
                            `}
                        </div>
                    </div>
                </div>
            `;
            
            html += `
                <div class="d-flex gap-3 justify-content-center mt-5 mb-5">
                    <a href="index.php?act=dethi" class="btn btn-primary px-4 rounded-pill">
                        <i class="fas fa-list me-2"></i>Trang đề thi
                    </a>
                    <a href="index.php?act=lichsu" class="btn btn-outline-secondary px-4 rounded-pill">
                        <i class="fas fa-history me-2"></i>Lịch sử làm bài
                    </a>
                </div>
            `;
            container.innerHTML = html;
        } else {
            container.innerHTML = `<div class='alert alert-danger'>${json.error || 'Lỗi không xác định'}</div>`;
        }
    } catch (e) {
        console.error(e);
        container.innerHTML = "<div class='alert alert-danger'>Lỗi kết nối máy chủ</div>";
    }
});
</script>
