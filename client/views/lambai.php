<?php
if (!isset($_SESSION['user'])) {
    header("Location:index.php?act=dangnhap");
    exit;
}
$id_baithi = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];
?>
<style>
    body { background: #f8fafc; }
    
    /* Sidebar */
    .exam-sidebar {
        position: sticky;
        top: 20px;
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
    }

    /* Timer */
    .timer-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 14px;
        text-align: center;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
    }
    .timer-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
    .countdown-timer {
        font-size: 28px;
        font-weight: 800;
        color: #1e293b;
        font-variant-numeric: tabular-nums;
        letter-spacing: 2px;
    }

    /* Question Nav */
    .question-nav { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 16px; }
    .qnav-wrapper { position: relative; }
    .qnav {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        transition: 0.15s;
    }
    .qnav:hover { border-color: #3b5bdb; color: #3b5bdb; }
    .qnav.answered { background: #3b5bdb; border-color: #3b5bdb; color: #fff; }
    .flag-icon {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 14px;
        height: 14px;
        background: #ef4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flag-icon i { font-size: 7px; color: #fff; }

    /* Question Cards */
    .question-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .q-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .q-num {
        font-size: 13px;
        font-weight: 700;
        color: #3b5bdb;
        background: #eef2ff;
        padding: 3px 10px;
        border-radius: 20px;
    }
    .flag-btn {
        background: none;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px 8px;
        color: #94a3b8;
        cursor: pointer;
        transition: 0.15s;
    }
    .flag-btn:hover, .flag-btn.active { border-color: #ef4444; color: #ef4444; background: #fef2f2; }
    .q-content { font-size: 15px; color: #1e293b; line-height: 1.7; margin-bottom: 16px; }

    /* Answer Options */
    .answer-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: 0.15s;
        font-size: 14px;
        color: #334155;
    }
    .answer-option:hover { border-color: #3b5bdb; background: #f5f8ff; }
    .answer-option input[type=radio] { accent-color: #3b5bdb; width: 16px; height: 16px; flex-shrink: 0; }
    .answer-option:has(input:checked) { border-color: #3b5bdb; background: #eef2ff; color: #1e40af; font-weight: 500; }

    /* Anti-Copy */
    body {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .q-content { font-style: normal; }
</style>

<div class="container-fluid px-3 px-lg-4 my-4" id="exam-app">
    <!-- Loading -->
    <div class="text-center py-5" id="loading-screen">
        <div class="spinner-grow text-primary" role="status"></div>
        <h5 class="mt-3 text-muted">Đang chuẩn bị đề thi...</h5>
    </div>

    <div id="exam-content" style="display:none;">
        <h4 class="text-center fw-bold mb-4" id="exam-title"></h4>

        <div class="row g-4">
            <!-- Questions -->
            <div class="col-lg-8" id="questions-container"></div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="exam-sidebar">
                    <!-- Timer -->
                    <div class="timer-box">
                        <div class="timer-label mb-1">Thời gian còn lại</div>
                        <div id="countdown" class="countdown-timer">00:00</div>
                    </div>

                    <!-- Nav Grid -->
                    <div class="fw-semibold small text-muted mb-2">Câu hỏi</div>
                    <div class="question-nav" id="question-nav"></div>
                    
                    <div class="d-flex gap-2 small text-muted mb-3">
                        <span><span class="badge bg-primary me-1"> </span>Đã trả lời</span>
                        <span><span class="badge text-bg-light border me-1"> </span>Chưa làm</span>
                    </div>

                    <hr class="my-2">
                    <button id="submitBtn" class="btn btn-success w-100 fw-bold mb-2">
                        <i class="fas fa-paper-plane me-2"></i>Nộp bài
                    </button>
                    <button id="exitBtn" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-door-open me-2"></i>Thoát & Lưu
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function escapeHtml(text) {
    if (!text) return "";
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
}

document.addEventListener("DOMContentLoaded", async () => {
    const id_baithi = <?= $id_baithi ?>;
    const userId = <?= $user_id ?>;
    
    const examApp = document.getElementById("exam-app");
    const loadingScreen = document.getElementById("loading-screen");
    const examContent = document.getElementById("exam-content");
    const examTitle = document.getElementById("exam-title");
    const questionsContainer = document.getElementById("questions-container");
    const questionNav = document.getElementById("question-nav");
    const countdownEl = document.getElementById("countdown");

    let id_lanthi = null;
    let answers = {};
    let flags = JSON.parse(localStorage.getItem(`flags_${userId}_${id_baithi}`)) || {};
    let remainingSeconds = 0;
    let timerInterval = null;

    try {
        const res = await fetch(apiUrl("exam/questions", { 
            id: id_baithi, 
            token: "WEB_<?= session_id() ?>" 
        }));
        const data = await res.json();

        if (!data.success) {
            const errorMsg = data.message || data.error || 'Lỗi tải đề thi';
            examApp.innerHTML = `<div class="alert alert-danger shadow-sm border-start border-danger border-4">
                <i class="fas fa-exclamation-triangle me-2"></i> ${errorMsg}
            </div>`;
            return;
        }

        // Initialize Exam Data
        id_lanthi = data.id_lanthi;
        examTitle.textContent = data.ten_baithi;
        
        // Handle answers (Priority: Server > LocalStorage)
        if (data.cautraloi_tam) {
            data.cautraloi_tam.split("|").forEach(p => {
                let parts = p.split(":");
                if(parts.length === 2) answers[parts[0]] = parts[1];
            });
        } else {
            const saved = localStorage.getItem(`answers_${userId}_${id_baithi}`);
            if (saved) answers = JSON.parse(saved);
        }

        // Handle flags (Priority: Server > LocalStorage)
        if (data.flagged_questions) {
            flags = {};
            data.flagged_questions.split(",").forEach(qId => {
                if (qId.trim()) flags[qId.trim()] = true;
            });
            localStorage.setItem(`flags_${userId}_${id_baithi}`, JSON.stringify(flags));
        } else {
            const savedFlags = localStorage.getItem(`flags_${userId}_${id_baithi}`);
            if (savedFlags) flags = JSON.parse(savedFlags);
        }

        // Handle Time
        const savedTime = localStorage.getItem(`exam_time_${userId}_${id_baithi}`);
        remainingSeconds = data.thoigianconlai !== null ? data.thoigianconlai : (savedTime ? parseInt(savedTime) : data.thoigianlam * 60);

        // Render UI
        renderQuestions(data.cauhoi);
        renderNav(data.cauhoi);
        startCountdown();

        loadingScreen.style.display = "none";
        examContent.style.display = "block";

    } catch (e) {
        console.error(e);
        examApp.innerHTML = `<div class="alert alert-danger">Lỗi kết nối máy chủ</div>`;
    }

    function renderQuestions(questions) {
        questionsContainer.innerHTML = questions.map((q, i) => {
            let finalContent = q.noidung;
            let inlineInputs = '';
            
            if (q.loai_cauhoi === 2) {
                let count = 0;
                const hasCloze = finalContent.includes('[...]');
                
                if (hasCloze) {
                    finalContent = finalContent.replace(/\[\.\.\.\]/g, () => {
                        const currentIdx = count++;
                        const currentVal = (Array.isArray(answers[q.id_cauhoi]) ? answers[q.id_cauhoi][currentIdx] : (currentIdx === 0 ? answers[q.id_cauhoi] : '')) || '';
                        return `<input type="text" class="cloze-input" data-id="${q.id_cauhoi}" data-index="${currentIdx}" 
                                 value="${escapeHtml(currentVal)}" 
                                 style="border:none; border-bottom:2px solid #3b5bdb; width:120px; text-align:center; outline:none; font-weight:600; color:#1e40af; background:transparent;">`;
                    });
                } else {
                    const currentVal = (Array.isArray(answers[q.id_cauhoi]) ? answers[q.id_cauhoi][0] : answers[q.id_cauhoi]) || '';
                    inlineInputs = `<input type="text" class="form-control fill-in-input" data-id="${q.id_cauhoi}" 
                                     placeholder="Nhập câu trả lời của bạn..." 
                                     value="${escapeHtml(currentVal)}" 
                                     style="border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 12px 16px; width: 100%;">`;
                }
            }

            return `
                <div class="question-card" id="q${q.id_cauhoi}">
                    <div class="q-header">
                        <span class="q-num">Câu ${i + 1}</span>
                        <button type="button" class="flag-btn ${flags[q.id_cauhoi] ? 'active' : ''}" data-id="${q.id_cauhoi}" title="Đánh dấu">
                            <i class="fa-solid fa-flag"></i>
                        </button>
                    </div>
                    <div class="q-content">${finalContent}</div>
                    <div class="answers">
                        ${q.loai_cauhoi === 2 
                            ? inlineInputs
                            : q.dapan.map(ans => `
                                <label class="answer-option">
                                    <input type="radio" name="q${q.id_cauhoi}" value="${ans.id_dapan}"
                                        ${answers[q.id_cauhoi] == ans.id_dapan ? 'checked' : ''}>
                                    <span>${ans.noidungdapan}</span>
                                </label>
                            `).join('')}
                    </div>
                </div>
            `;
        }).join('');

        // Listen for changes
        questionsContainer.querySelectorAll("input[type=radio]").forEach(radio => {
            radio.onchange = () => {
                const qid = radio.name.replace("q", "");
                answers[qid] = radio.value;
                onAnswerChanged(qid);
            };
        });

        questionsContainer.querySelectorAll(".fill-in-input, .cloze-input").forEach(input => {
            input.oninput = (e) => {
                const qid = input.dataset.id;
                const idx = input.dataset.index;
                
                if (idx !== undefined) {
                    // Cloze multi-input
                    if (!Array.isArray(answers[qid])) {
                        // Initialize array if it was a string or null
                        const oldVal = answers[qid];
                        answers[qid] = [];
                        if (oldVal) answers[qid][0] = oldVal;
                    }
                    answers[qid][parseInt(idx)] = e.target.value;
                } else {
                    // Single input
                    answers[qid] = e.target.value;
                }
                onAnswerChanged(qid);
            };
        });

        // Flag logic
        questionsContainer.querySelectorAll(".flag-btn").forEach(btn => {
            btn.onclick = () => {
                const id = btn.dataset.id;
                if (flags[id]) {
                    delete flags[id];
                    btn.classList.remove("active");
                    const f = document.getElementById(`flag-${id}`);
                    if (f) f.style.display = "none";
                } else {
                    flags[id] = true;
                    btn.classList.add("active");
                    const f = document.getElementById(`flag-${id}`);
                    if (f) f.style.display = "flex";
                }
                localStorage.setItem(`flags_${userId}_${id_baithi}`, JSON.stringify(flags));
            };
        });
    }

    function onAnswerChanged(qid) {
        localStorage.setItem(`answers_${userId}_${id_baithi}`, JSON.stringify(answers));
        const navBtn = document.querySelector(`.qnav[data-id='${qid}']`);
        if (answers[qid] && answers[qid].toString().trim() !== "") {
            navBtn.classList.add("answered");
        } else {
            navBtn.classList.remove("answered");
        }
        syncDraft();
    }

    function renderNav(questions) {
        questionNav.innerHTML = questions.map((q, i) => `
            <div class="qnav-wrapper">
                <button type="button" class="qnav ${answers[q.id_cauhoi] ? 'answered' : ''}" data-id="${q.id_cauhoi}">
                    ${i + 1}
                </button>
                <span class="flag-icon" id="flag-${q.id_cauhoi}" style="display: ${flags[q.id_cauhoi] ? 'flex' : 'none'}">
                    <i class="fa-solid fa-flag"></i>
                </span>
            </div>
        `).join('');

        questionNav.querySelectorAll(".qnav").forEach(btn => {
            btn.onclick = () => {
                document.getElementById("q" + btn.dataset.id).scrollIntoView({ behavior: "smooth", block: "center" });
            };
        });
    }

    function startCountdown() {
        // Calculate absolute end time
        const endTime = Date.now() + (remainingSeconds * 1000);
        
        const tick = () => {
            const now = Date.now();
            remainingSeconds = Math.max(0, Math.floor((endTime - now) / 1000));
            
            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                localStorage.removeItem(`exam_time_${userId}_${id_baithi}`);
                alert("Hết thời gian làm bài!");
                sendSubmit();
                return;
            }
            
            localStorage.setItem(`exam_time_${userId}_${id_baithi}`, remainingSeconds);
            
            const mins = Math.floor(remainingSeconds / 60);
            const secs = remainingSeconds % 60;
            countdownEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            
            if (remainingSeconds <= 60) countdownEl.className = "countdown-timer text-danger fw-bold";
            else if (remainingSeconds <= 300) countdownEl.className = "countdown-timer text-warning";
        };
        tick();
        timerInterval = setInterval(tick, 1000);
    }

    // Disable Right Click, Copy, and DevTools Shortcuts
    document.addEventListener("contextmenu", e => e.preventDefault());
    document.addEventListener("copy", e => e.preventDefault());
    document.addEventListener("keydown", e => {
        if (e.ctrlKey && (e.key === "c" || e.key === "x" || e.key === "u" || e.key === "s" || e.key === "a")) {
            e.preventDefault();
            alert("Hành động này bị chặn để đảm bảo tính công bằng!");
        }
        if (e.key === "F12" || (e.ctrlKey && e.shiftKey && e.key === "I")) {
            e.preventDefault();
        }
    });

    async function syncDraft() {
        try {
            await fetch(apiUrl("exam/sync-draft"), {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ 
                    id_lanthi: id_lanthi, 
                    thoigianconlai: remainingSeconds, 
                    answers, 
                    flags: Object.keys(flags), 
                    token: "WEB_<?= session_id() ?>" 
                }),
                keepalive: true
            });
        } catch (e) {}
    }
    setInterval(syncDraft, 15000);

    async function sendSubmit() {
        if (timerInterval) clearInterval(timerInterval);
        try {
            const res = await fetch(apiUrl("exam/submit"), {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id_lanthi, id_baithi, answers })
            });
            const data = await res.json();
            
            localStorage.removeItem(`answers_${userId}_${id_baithi}`);
            localStorage.removeItem(`flags_${userId}_${id_baithi}`);
            localStorage.removeItem(`exam_time_${userId}_${id_baithi}`);
            
            window.location.href = `index.php?act=ketqua&id=${data.id_lanthi}`;
        } catch (e) {
            alert("Lỗi khi nộp bài!");
        }
    }

    document.getElementById("submitBtn").onclick = () => {
        if (confirm("Bạn có chắc chắn muốn nộp bài?")) {
            window.onbeforeunload = null;
            sendSubmit();
        }
    };

    document.getElementById("exitBtn").onclick = async () => {
        if (confirm("Thoát và lưu lại tiến trình?")) {
            window.onbeforeunload = null; // Disable the prompt
            try {
                await fetch(apiUrl("exam/sync-draft"), {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ 
                        id_lanthi: id_lanthi, 
                        thoigianconlai: remainingSeconds, 
                        answers, 
                        flags: Object.keys(flags), 
                        token: "WEB_<?= session_id() ?>",
                        release: true 
                    })
                });
            } catch (e) {}
            window.location.href = "index.php?act=dethi";
        }
    };

    window.onbeforeunload = (e) => {
        syncDraft();
        e.preventDefault();
        e.returnValue = '';
    };
});
</script>




