<div id="bankAlert"></div>

<div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:24px;">
    <div>
        <h2 style="margin:0;color:#0f172a;">Ngân hàng câu hỏi</h2>
        <p style="margin:6px 0 0;color:#64748b;">Tạo ngân hàng, gắn môn học và quản lý câu hỏi theo độ khó bằng API nội
            bộ.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <button type="button" onclick="openBankModal()"
            style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 10px 22px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); transition: all 0.2s;">
            <i class="fas fa-folder-plus"></i> Thêm ngân hàng
        </button>
        <button type="button" id="openQuestionBtn" onclick="openQuestionModal()" disabled
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 22px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: all 0.2s; opacity: 0.5;">
            <i class="fas fa-plus"></i> Thêm câu hỏi
        </button>
        <button type="button" id="openImportWordBtn" onclick="openImportWordModal()" disabled
            style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 10px 22px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2); transition: all 0.2s; opacity: 0.5;">
            <i class="fas fa-file-word"></i> Import File
        </button>
    </div>
</div>

<!-- Modal Import Word -->
<div id="importWordModalBank"
    style="display:none;position:fixed;z-index:10000;inset:0;background:rgba(15,23,42,0.6);align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px);">
    <div
        style="width:100%; max-width:520px; background:#fff; border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.2); padding:35px; position:relative;">
        <h4 style="margin:0 0 8px; color:#1e293b; font-weight:700;">Import Câu Hỏi Từ Word / Excel / PDF</h4>
        <p style="color:#64748b; font-size:13.5px; margin-bottom:20px;">Hỗ trợ định dạng <strong>.docx, .xlsx</strong>
            hoặc <strong>.pdf</strong></p>
        <form id="bankImportWordForm">
            <div style="margin-bottom:20px;">
                <input type="file" class="form-control" id="bankWordFile" accept=".docx, .xlsx, .pdf" required
                    style="padding:10px; border-radius:8px;">
            </div>

            <div
                style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px; margin-bottom:25px; font-size:13px; color:#475569; line-height:1.6;">
                <div style="font-weight:600; color:#1e293b; margin-bottom:6px;">Quy tắc nội dung (Word, Excel, PDF):
                </div>
                <div style="margin-bottom:8px; color:#6366f1; font-style:italic;">* File PDF & Word: Soạn nội dung theo
                    từng dòng.<br>* File Excel: Soạn nội dung lần lượt vào từng ô của một cột.</div>
                <div>`Câu 1: ...`</div>
                <div>`A. ...`</div>
                <div>`B. ...`</div>
                <div>`C. ...`</div>
                <div>`D. ...`</div>
                <div>`Đáp án: A`</div>
                <div>`Độ khó: Dễ`</div>
                <div>`Lời giải: (Nội dung giải thích chi tiết ở đây)`</div>
                <div>`Câu 2: [...] ....., [...], ......`</div>
                <div>`Đáp án: đáp án 1 | đáp án 2`</div>
                <div>`Độ khó: Dễ`</div>
                <div>`Lời giải: (Nội dung giải thích chi tiết ở đây)`</div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn" onclick="closeImportWordModal()"
                    style="background:#f1f5f9; color:#64748b; font-weight:600; padding:10px 25px; border-radius:8px; border:none;">Hủy</button>
                <button type="submit" class="btn"
                    style="background:#3b82f6; color:white; font-weight:700; padding:10px 35px; border-radius:8px; border:none; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
                    Bắt đầu Import
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:18px;">
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px;">
                    <div>
                        <h5 style="margin:0;">Danh sách ngân hàng</h5>
                        <small class="text-muted" id="bankCountLabel">Đang tải...</small>
                    </div>
                </div>
                <div id="bankList" style="display:flex;flex-direction:column;gap:12px;">
                    <div class="text-muted">Đang tải ngân hàng câu hỏi...</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:18px;">
            <div class="card-body">
                <div
                    style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
                    <div>
                        <h4 style="margin:0;color:#0f172a;" id="questionPanelTitle">Chọn một ngân hàng câu hỏi</h4>
                        <p style="margin:6px 0 0;color:#64748b;" id="questionPanelMeta">Bạn có thể quản lý câu hỏi theo
                            từng môn học trong ngân hàng.</p>
                    </div>
                    <div style="min-width:220px;">
                        <select id="subjectFilter" class="form-select" disabled></select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" style="margin-bottom:0;">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Nội dung câu hỏi</th>
                                <th>Đáp án</th>
                                <th>Độ khó</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="bankQuestionTableBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu để hiển thị.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="bankModal"
    style="display:none;position:fixed;z-index:10000;inset:0;background:rgba(15,23,42,0.6);align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px);">
    <div
        style="width:100%;max-width:720px;background:#fff;border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;">
        <form id="bankForm">
            <div
                style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:35px 35px 0;">
                <div>
                    <h4 id="bankModalTitle" style="margin:0; color:#1e293b; font-weight:700; font-size:22px;">Thêm ngân
                        hàng câu hỏi</h4>
                    <p style="margin:8px 0 0; color:#64748b; font-size:14px;">Mỗi ngân hàng câu hỏi chỉ thuộc về một môn
                        học duy nhất.</p>
                </div>
                <button type="button" class="btn-close" onclick="closeBankModal()" aria-label="Close"></button>
            </div>
            <div style="padding:18px 24px 8px;">
                <input type="hidden" id="bankId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên ngân hàng</label>
                        <input type="text" class="form-control" id="bankName" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select class="form-select" id="bankStatus">
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Đã khóa</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea class="form-control" id="bankDescription" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Môn học</label>
                        <div id="bankSubjectList"
                            style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;">
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:12px;padding:8px 24px 24px;">
                <button type="button" class="btn btn-light" onclick="closeBankModal()">Đóng</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Lưu ngân hàng
                </button>
            </div>
        </form>
    </div>
</div>

<div id="questionModalBank"
    style="display:none;position:fixed;z-index:10000;inset:0;background:rgba(15,23,42,0.6);align-items:center;justify-content:center;padding:24px;backdrop-filter:blur(4px);">
    <div
        style="width:100%;max-width:860px;background:#fff;border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,0.2);overflow:hidden;max-height:90vh;display:flex;flex-direction:column;">
        <form id="bankQuestionForm" style="display:contents;">
            <div
                style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:35px 35px 0;">
                <div>
                    <h4 id="bankQuestionModalTitle" style="margin:0; color:#1e293b; font-weight:700; font-size:22px;">
                        Thêm câu hỏi ngân hàng</h4>
                    <p style="margin:8px 0 0; color:#64748b; font-size:14px;">Câu hỏi sẽ được gắn vào môn học đang chọn
                        trong ngân hàng.</p>
                </div>
                <button type="button" class="btn-close" onclick="closeQuestionModal()" aria-label="Close"></button>
            </div>
            <div style="padding:18px 24px 8px;">
                <input type="hidden" id="bankQuestionId">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Môn học</label>
                        <select class="form-select" id="bankQuestionSubject" required></select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Loại câu hỏi</label>
                        <select class="form-select" id="bankQuestionType" onchange="toggleBankAnswerType()">
                            <option value="1">Trắc nghiệm</option>
                            <option value="2">Điền từ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Độ khó</label>
                        <select class="form-select" id="bankQuestionDifficulty">
                            <option value="de">Dễ</option>
                            <option value="trungbinh">Trung bình</option>
                            <option value="kho">Khó</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select class="form-select" id="bankQuestionStatus">
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Đã khóa</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nội dung câu hỏi</label>
                        <textarea class="form-control" id="bankQuestionContent" rows="4" required></textarea>
                    </div>
                    <div class="col-12">
                        <div id="bankAnswerHeader"
                            style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                            <label class="form-label fw-semibold mb-0">Đáp án</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddNewAnswer"
                                onclick="addBankAnswerOption()">
                                <i class="fas fa-plus me-1"></i>Thêm đáp án
                            </button>
                        </div>
                        <p id="bankAnswerDesc" class="text-muted small mt-1 mb-0" style="display:none;">
                            Sử dụng ký hiệu <strong>[...]</strong> trong nội dung câu hỏi để tạo chỗ trống.<br>
                            Ví dụ: "Học đi đôi với [...]" -> thêm 1 đáp án là "hành".
                        </p>
                        <div id="bankAnswerOptions"
                            style="display:flex;flex-direction:column;gap:10px;margin-top:12px;"></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Lời giải chi tiết</label>
                        <textarea class="form-control" id="bankQuestionLoigiai" rows="3"
                            placeholder="Bắt buộc: Giải thích tại sao đáp án này đúng..." required></textarea>
                    </div>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:12px;padding:20px 35px 35px;background:#fff;">
                <button type="button" class="btn"
                    style="background:#f1f5f9; color:#64748b; font-weight:600; padding:10px 25px; border-radius:8px; border:none;"
                    onclick="closeQuestionModal()">Đóng</button>
                <button type="submit" class="btn"
                    style="background:#3b82f6; color:#white; font-weight:700; padding:10px 35px; border-radius:8px; border:none; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
                    <i class="fas fa-save me-2"></i>Lưu câu hỏi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let bankItems = [];
    let subjectItems = [];

    function escapeHtml(text) {
        if (!text) return "";
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    let selectedBank = null;
    let bankQuestions = [];

    function showBankAlert(message, type = 'success') {
        document.getElementById('bankAlert').innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius:14px;">
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    }

    function difficultyLabel(value) {
        if (!value) return 'Dễ';
        let v = String(value).toLowerCase();
        if (v === 'kho' || v === '3' || v === 'khó') return 'Khó';
        if (v === 'trungbinh' || v === '2' || v === 'trung bình') return 'Trung bình';
        return 'Dễ';
    }

    function statusLabel(value) {
        return value === 'inactive' ? 'Đã khóa' : 'Đang hoạt động';
    }

    function renderSubjectCheckboxes(selectedIds = []) {
        const box = document.getElementById('bankSubjectList');
        if (!subjectItems.length) {
            box.innerHTML = '<div class="text-muted">Chưa có môn học khả dụng.</div>';
            return;
        }

        box.innerHTML = subjectItems.map((subject) => `
        <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border:1px solid #e2e8f0;border-radius:12px;background:#f8fafc;cursor:pointer;">
            <input type="radio" name="bank_subject_radio" class="bank-subject-radio" value="${Number(subject.id_monhoc)}" ${selectedIds.includes(Number(subject.id_monhoc)) ? 'checked' : ''} required>
            <span>
                <strong style="display:block;color:#0f172a;">${escapeHtml(subject.tenmonhoc)}</strong>
                <small class="text-muted">${escapeHtml(subject.ten || '')}</small>
            </span>
        </label>
    `).join('');
    }

    function renderBankList() {
        const listEl = document.getElementById('bankList');
        document.getElementById('bankCountLabel').textContent = `${bankItems.length} ngân hàng`;

        if (!bankItems.length) {
            listEl.innerHTML = '<div class="text-muted">Chưa có ngân hàng câu hỏi nào.</div>';
            return;
        }

        listEl.innerHTML = bankItems.map((bank) => {
            const isActive = Number(selectedBank?.id_nganhang || 0) === Number(bank.id_nganhang);
            return `
            <div class="bank-item" data-id="${Number(bank.id_nganhang)}" style="padding:16px;border:1px solid ${isActive ? '#bfdbfe' : '#e2e8f0'};border-radius:14px;background:${isActive ? '#eff6ff' : '#fff'};cursor:pointer;">
                <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">
                    <div>
                        <h6 style="margin:0 0 6px;color:#0f172a;">${escapeHtml(bank.ten_nganhang)}</h6>
                        <div class="text-muted" style="font-size:13px;">${escapeHtml(bank.ds_monhoc || 'Chưa gắn môn học')}</div>
                    </div>
                    <span class="badge ${bank.trangthai === 'inactive' ? 'text-bg-secondary' : 'text-bg-success'}">${escapeHtml(statusLabel(bank.trangthai))}</span>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:12px;font-size:13px;color:#475569;">
                    <span><i class="fas fa-book me-1"></i>${Number(bank.so_monhoc || 0)} môn</span>
                    <span><i class="fas fa-circle-question me-1"></i>${Number(bank.so_cauhoi || 0)} câu hỏi</span>
                </div>
                <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit-bank" data-id="${Number(bank.id_nganhang)}">Sửa</button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-bank" data-id="${Number(bank.id_nganhang)}">Xóa</button>
                </div>
            </div>
        `;
        }).join('');
    }

    function renderSubjectFilter() {
        const select = document.getElementById('subjectFilter');
        if (!selectedBank || !Array.isArray(selectedBank.subjects) || !selectedBank.subjects.length) {
            select.disabled = true;
            select.innerHTML = '<option value="">Chưa có môn học</option>';
            return;
        }

        select.disabled = false;
        select.innerHTML = selectedBank.subjects.map((subject) => `
        <option value="${Number(subject.id_monhoc)}">${escapeHtml(subject.tenmonhoc)}</option>
    `).join('');
    }

    function renderQuestionPanelMeta() {
        const title = document.getElementById('questionPanelTitle');
        const meta = document.getElementById('questionPanelMeta');
        const openBtn = document.getElementById('openQuestionBtn');

        if (!selectedBank) {
            title.textContent = 'Chọn một ngân hàng câu hỏi';
            meta.textContent = 'Bạn có thể quản lý câu hỏi theo từng môn học trong ngân hàng.';
            openBtn.disabled = true;
            return;
        }

        title.textContent = selectedBank.ten_nganhang || 'Ngân hàng câu hỏi';
        const qCount = bankQuestions.length;
        meta.textContent = `${selectedBank.subjects?.length || 0} môn học | Tổng số ${qCount} câu hỏi đang hiển thị`;

        const hasSubjects = !!(selectedBank.subjects && selectedBank.subjects.length);
        openBtn.disabled = !hasSubjects;
        document.getElementById('openImportWordBtn').disabled = !hasSubjects;
    }

    function openImportWordModal() {
        if (!selectedBank) return;
        document.getElementById('bankImportWordForm').reset();
        document.getElementById('importWordModalBank').style.display = 'flex';
    }

    function closeImportWordModal() {
        document.getElementById('importWordModalBank').style.display = 'none';
    }

    document.getElementById('bankImportWordForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const fileInput = document.getElementById('bankWordFile');
        if (!fileInput.files.length) return;

        const formData = new FormData();
        formData.append('id_nhch', selectedBank.id_nganhang);
        formData.append('word_file', fileInput.files[0]);

        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

        try {
            const res = await fetch(serverApiUrl('nganhang/import-word'), {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Import thất bại');

            closeImportWordModal();
            showBankAlert(json.message);
            await loadBankQuestions(Number(selectedBank.id_nganhang), document.getElementById('subjectFilter').value || '');
            await loadBanks();
        } catch (error) {
            showBankAlert(error.message, 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    function renderQuestionTable() {
        const tbody = document.getElementById('bankQuestionTableBody');

        if (!selectedBank) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Chọn một ngân hàng câu hỏi để xem dữ liệu.</td></tr>';
            return;
        }

        if (!bankQuestions.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Chưa có câu hỏi nào cho môn học đang chọn.</td></tr>';
            return;
        }

        tbody.innerHTML = bankQuestions.map((question, index) => `
        <tr>
            <td>${index + 1}</td>
            <td style="max-width:260px;">
                <div style="font-weight:600;color:#0f172a;">${escapeHtml(question.noidungcauhoi)}</div>
            </td>
            <td>
                ${(question.dapan || []).map((answer) => `
                    <div style="margin-bottom:4px;color:${Number(answer.dapandung) === 1 ? '#15803d' : '#475569'};">
                        ${Number(answer.dapandung) === 1 ? '<strong>[Đúng]</strong> ' : ''}${escapeHtml(answer.noidungdapan)}
                    </div>
                `).join('')}
            </td>
            <td>${Number(question.loai_cauhoi) === 2 ? 'Điền từ' : 'Trắc nghiệm'}</td>
            <td>${escapeHtml(difficultyLabel(question.dokho))}</td>
            <td><span class="badge ${question.trangthai === 'inactive' ? 'text-bg-secondary' : 'text-bg-success'}">${escapeHtml(statusLabel(question.trangthai))}</span></td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-primary btn-edit-bank-question" data-id="${Number(question.id_cauhoi_nganhang)}">Sửa</button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-bank-question" data-id="${Number(question.id_cauhoi_nganhang)}">Xóa</button>
            </td>
        </tr>
    `).join('');
    }

    async function loadBanks() {
        const res = await fetch(serverApiUrl('nganhang/list'));
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể tải ngân hàng câu hỏi');
        }

        bankItems = Array.isArray(json.banks) ? json.banks : [];
        subjectItems = Array.isArray(json.subjects) ? json.subjects : [];

        if (selectedBank) {
            const freshBank = bankItems.find((item) => Number(item.id_nganhang) === Number(selectedBank.id_nganhang));
            if (!freshBank) {
                selectedBank = null;
                bankQuestions = [];
            }
        }

        renderBankList();
        renderQuestionPanelMeta();
        renderQuestionTable();
    }

    async function loadBankQuestions(bankId, subjectId = '') {
        const res = await fetch(serverApiUrl('nganhang/cauhoi/list', {
            id_nganhang: bankId,
            id_monhoc: subjectId
        }));
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể tải câu hỏi ngân hàng');
        }

        selectedBank = json.bank || null;
        bankQuestions = Array.isArray(json.questions) ? json.questions : [];
        renderBankList();
        renderQuestionPanelMeta();
        renderSubjectFilter();
        if (json.selected_subject_id) {
            document.getElementById('subjectFilter').value = String(json.selected_subject_id);
        }
        renderQuestionTable();
    }

    function openBankModal(id = 0) {
        document.getElementById('bankForm').reset();
        document.getElementById('bankId').value = '';
        document.getElementById('bankModalTitle').textContent = 'Thêm ngân hàng câu hỏi';
        renderSubjectCheckboxes([]);

        if (id) {
            const bank = selectedBank && Number(selectedBank.id_nganhang) === Number(id)
                ? selectedBank
                : null;
            const baseBank = bank || bankItems.find((item) => Number(item.id_nganhang) === Number(id));
            if (!baseBank) return;

            const selectedIds = Array.isArray(baseBank.subjects)
                ? baseBank.subjects.map((item) => Number(item.id_monhoc))
                : [];

            document.getElementById('bankModalTitle').textContent = 'Cập nhật ngân hàng câu hỏi';
            document.getElementById('bankId').value = baseBank.id_nganhang;
            document.getElementById('bankName').value = baseBank.ten_nganhang || '';
            document.getElementById('bankDescription').value = baseBank.mieuta || '';
            document.getElementById('bankStatus').value = baseBank.trangthai || 'active';
            renderSubjectCheckboxes(selectedIds);
        }

        document.getElementById('bankModal').style.display = 'flex';
    }

    function closeBankModal() {
        document.getElementById('bankModal').style.display = 'none';
    }

    function getSelectedBankSubjectIds() {
        const radio = document.querySelector('.bank-subject-radio:checked');
        return radio ? [Number(radio.value)] : [];
    }

    async function saveBank(payload) {
        const method = payload.id_nganhang ? 'PATCH' : 'POST';
        const res = await fetch(serverApiUrl('nganhang/save'), {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể lưu ngân hàng câu hỏi');
        }
        return json;
    }

    async function deleteBank(id) {
        const res = await fetch(serverApiUrl('nganhang/delete'), {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_nganhang: id })
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể khóa ngân hàng câu hỏi');
        }
        return json;
    }

    function fillQuestionSubjectOptions(selectedId = '') {
        const select = document.getElementById('bankQuestionSubject');
        if (!selectedBank || !selectedBank.subjects?.length) {
            select.innerHTML = '<option value="">Chưa có môn học</option>';
            return;
        }
        select.innerHTML = selectedBank.subjects.map((subject) => `
        <option value="${Number(subject.id_monhoc)}" ${String(subject.id_monhoc) === String(selectedId) ? 'selected' : ''}>${escapeHtml(subject.tenmonhoc)}</option>
    `).join('');
    }

    function addBankAnswerOption(content = '', checked = false) {
        const type = parseInt(document.getElementById('bankQuestionType').value || 1);
        const container = document.getElementById('bankAnswerOptions');

        if (type === 2) {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:10px;';
            row.innerHTML = `
            <input type="text" class="form-control bank-answer-input" value="${escapeHtml(content)}" placeholder="Nhập từ cần điền (theo thứ tự các dấu [...])">
            <input type="hidden" name="bank_correct_answer" value="on">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">Xóa</button>
        `;
            container.appendChild(row);
            return;
        }

        const index = container.children.length;
        const row = document.createElement('div');
        row.style.cssText = 'display:flex;align-items:center;gap:10px;';
        row.innerHTML = `
        <input type="text" class="form-control bank-answer-input" value="${escapeHtml(content)}" placeholder="Nhập nội dung đáp án">
        <label style="display:flex;align-items:center;gap:6px;white-space:nowrap;">
            <input type="radio" name="bank_correct_answer" ${checked ? 'checked' : ''}> Đúng
        </label>
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove()">Xóa</button>
    `;
        container.appendChild(row);
    }

    function toggleBankAnswerType() {
        const type = parseInt(document.getElementById('bankQuestionType').value || 1);
        const btnAdd = document.getElementById('btnAddNewAnswer');
        const desc = document.getElementById('bankAnswerDesc');
        const container = document.getElementById('bankAnswerOptions');

        // Get current values to preserve
        const currentValues = Array.from(container.querySelectorAll('.bank-answer-input')).map(inp => inp.value);

        // Refresh container
        container.innerHTML = '';
        if (currentValues.length > 0) {
            currentValues.forEach((val, i) => addBankAnswerOption(val, i === 0));
        } else {
            addBankAnswerOption();
            addBankAnswerOption();
        }

        if (type === 2) {
            btnAdd.style.display = 'inline-block'; // Allow adding multiple blanks
            desc.style.display = 'block';
        } else {
            btnAdd.style.display = 'inline-block';
            desc.style.display = 'none';
        }
    }

    function openQuestionModal(id = 0) {
        if (!selectedBank) {
            showBankAlert('Vui lòng chọn ngân hàng câu hỏi trước.', 'danger');
            return;
        }

        document.getElementById('bankQuestionForm').reset();
        document.getElementById('bankQuestionId').value = '';
        document.getElementById('bankQuestionLoigiai').value = '';
        document.getElementById('bankQuestionModalTitle').textContent = 'Thêm câu hỏi ngân hàng';
        document.getElementById('bankAnswerOptions').innerHTML = '';
        fillQuestionSubjectOptions(document.getElementById('subjectFilter').value || '');
        addBankAnswerOption('', true);
        addBankAnswerOption('', false);

        if (id) {
            const question = bankQuestions.find((item) => Number(item.id_cauhoi_nganhang) === Number(id));
            if (!question) return;

            document.getElementById('bankQuestionModalTitle').textContent = 'Cập nhật câu hỏi ngân hàng';
            document.getElementById('bankQuestionId').value = question.id_cauhoi_nganhang;
            document.getElementById('bankQuestionContent').value = question.noidungcauhoi || '';
            document.getElementById('bankQuestionLoigiai').value = (question.dapan && question.dapan.find(d => d.loigiai_chitiet)?.loigiai_chitiet) || '';
            let diff = question.dokho || 'de';
            if (diff == '1' || diff.toLowerCase() === 'dễ') diff = 'de';
            if (diff == '2' || diff.toLowerCase() === 'trung bình') diff = 'trungbinh';
            if (diff == '3' || diff.toLowerCase() === 'khó') diff = 'kho';
            document.getElementById('bankQuestionDifficulty').value = diff;
            document.getElementById('bankQuestionStatus').value = question.trangthai || 'active';
            document.getElementById('bankQuestionType').value = question.loai_cauhoi || 1;
            toggleBankAnswerType();
            fillQuestionSubjectOptions(question.id_monhoc || '');
            document.getElementById('bankAnswerOptions').innerHTML = '';
            (question.dapan || []).forEach((answer) => {
                addBankAnswerOption(answer.noidungdapan || '', Number(answer.dapandung) === 1);
            });
        }

        document.getElementById('questionModalBank').style.display = 'flex';
    }

    function closeQuestionModal() {
        document.getElementById('questionModalBank').style.display = 'none';
    }

    async function saveBankQuestion(payload) {
        const method = payload.id_cauhoi_nganhang ? 'PATCH' : 'POST';
        const res = await fetch(serverApiUrl('nganhang/cauhoi/save'), {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể lưu câu hỏi ngân hàng');
        }
        return json;
    }

    async function deleteBankQuestion(id) {
        const res = await fetch(serverApiUrl('nganhang/cauhoi/delete'), {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_cauhoi_nganhang: id })
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.error || 'Không thể khóa câu hỏi ngân hàng');
        }
        return json;
    }

    document.getElementById('bankForm').addEventListener('submit', async function (event) {
        event.preventDefault();

        const payload = {
            id_nganhang: Number(document.getElementById('bankId').value || 0),
            ten_nganhang: document.getElementById('bankName').value.trim(),
            mieuta: document.getElementById('bankDescription').value.trim(),
            trangthai: document.getElementById('bankStatus').value,
            subject_ids: getSelectedBankSubjectIds()
        };

        try {
            const json = await saveBank(payload);
            closeBankModal();
            showBankAlert(json.message || 'Lưu ngân hàng câu hỏi thành công');
            await loadBanks();
            const savedBankId = json.data?.id_nganhang || payload.id_nganhang;
            if (savedBankId) {
                await loadBankQuestions(savedBankId);
            }
        } catch (error) {
            showBankAlert(error.message, 'danger');
        }
    });

    document.getElementById('bankList').addEventListener('click', async function (event) {
        const editBtn = event.target.closest('.btn-edit-bank');
        if (editBtn) {
            event.stopPropagation();
            if (!selectedBank || Number(selectedBank.id_nganhang) !== Number(editBtn.dataset.id)) {
                await loadBankQuestions(Number(editBtn.dataset.id));
            }
            openBankModal(Number(editBtn.dataset.id));
            return;
        }

        const deleteBtn = event.target.closest('.btn-delete-bank');
        if (deleteBtn) {
            event.stopPropagation();
            if (!confirm('Bạn có chắc muốn XÓA VĨNH VIỄN ngân hàng câu hỏi này và toàn bộ câu hỏi bên trong không?')) {
                return;
            }
            try {
                const json = await deleteBank(Number(deleteBtn.dataset.id));
                showBankAlert(json.message || 'Xóa ngân hàng thành công');
                if (selectedBank && Number(selectedBank.id_nganhang) === Number(deleteBtn.dataset.id)) {
                    selectedBank = null;
                    bankQuestions = [];
                }
                await loadBanks();
            } catch (error) {
                showBankAlert(error.message, 'danger');
            }
            return;
        }

        const item = event.target.closest('.bank-item');
        if (item) {
            try {
                await loadBankQuestions(Number(item.dataset.id));
            } catch (error) {
                showBankAlert(error.message, 'danger');
            }
        }
    });

    document.getElementById('subjectFilter').addEventListener('change', async function () {
        if (!selectedBank) return;
        try {
            await loadBankQuestions(Number(selectedBank.id_nganhang), this.value);
        } catch (error) {
            showBankAlert(error.message, 'danger');
        }
    });

    document.getElementById('bankQuestionForm').addEventListener('submit', async function (event) {
        event.preventDefault();

        const optionInputs = Array.from(document.querySelectorAll('.bank-answer-input'));
        const radioInputs = Array.from(document.querySelectorAll('input[name="bank_correct_answer"]'));
        const options = optionInputs.map((input) => input.value.trim());
        const correctIndex = radioInputs.findIndex((input) => input.checked);

        const loai_cauhoi = parseInt(document.getElementById('bankQuestionType').value || 1);
        const noidung = document.getElementById('bankQuestionContent').value.trim();

        if (loai_cauhoi === 1) {
            if (options.length < 2 || options.some(opt => opt === '')) {
                showBankAlert('Trắc nghiệm cần ít nhất 2 đáp án hợp lệ', 'danger');
                return;
            }
            if (new Set(options.map(v => v.toLowerCase())).size !== options.length) {
                showBankAlert('Các đáp án không được trùng nhau', 'danger');
                return;
            }
            if (correctIndex < 0) {
                showBankAlert('Vui lòng chọn một đáp án đúng', 'danger');
                return;
            }
        } else {
            // Validation cho câu hỏi ĐIỀN TỪ
            const placeholderMatch = noidung.match(/\[\.\.\.\]/g);
            const placeholderCount = placeholderMatch ? placeholderMatch.length : 0;

            if (placeholderCount === 0) {
                showBankAlert('Câu hỏi điền từ phải có ít nhất một dấu [...] trong nội dung câu hỏi để tạo chỗ trống.', 'danger');
                return;
            }

            if (options.length !== placeholderCount) {
                showBankAlert(`Số lượng đáp án (${options.length}) phải khớp với số lượng dấu [...] trong nội dung (${placeholderCount}).`, 'danger');
                return;
            }

            if (options.some(opt => opt === '')) {
                showBankAlert('Vui lòng nhập đầy đủ nội dung cho các đáp án điền từ.', 'danger');
                return;
            }
        }

        const loigiai = document.getElementById('bankQuestionLoigiai').value.trim();
        if (loigiai === '') {
            showBankAlert('Vui lòng nhập lời giải chi tiết cho câu hỏi', 'danger');
            return;
        }

        const payload = {
            id_cauhoi_nganhang: Number(document.getElementById('bankQuestionId').value || 0),
            id_nganhang: Number(selectedBank?.id_nganhang || 0),
            id_monhoc: Number(document.getElementById('bankQuestionSubject').value || 0),
            noidungcauhoi: noidung,
            dokho: document.getElementById('bankQuestionDifficulty').value,
            loai_cauhoi: loai_cauhoi,
            trangthai: document.getElementById('bankQuestionStatus').value,
            options,
            correct_index: loai_cauhoi === 2 ? 0 : correctIndex,
            loigiai_chitiet: loigiai
        };

        try {
            const json = await saveBankQuestion(payload);
            closeQuestionModal();
            showBankAlert(json.message || 'Lưu câu hỏi ngân hàng thành công');
            await loadBankQuestions(Number(selectedBank.id_nganhang), document.getElementById('subjectFilter').value || '');
            await loadBanks();
        } catch (error) {
            showBankAlert(error.message, 'danger');
        }
    });

    document.getElementById('bankQuestionTableBody').addEventListener('click', async function (event) {
        const editBtn = event.target.closest('.btn-edit-bank-question');
        if (editBtn) {
            openQuestionModal(Number(editBtn.dataset.id));
            return;
        }

        const deleteBtn = event.target.closest('.btn-delete-bank-question');
        if (deleteBtn) {
            if (!confirm('Bạn có chắc muốn XÓA VĨNH VIỄN câu hỏi ngân hàng này không?')) {
                return;
            }
            try {
                const json = await deleteBankQuestion(Number(deleteBtn.dataset.id));
                showBankAlert(json.message || 'Xóa câu hỏi thành công');
                await loadBankQuestions(Number(selectedBank.id_nganhang), document.getElementById('subjectFilter').value || '');
                await loadBanks();
            } catch (error) {
                showBankAlert(error.message, 'danger');
            }
        }
    });

    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('bankModal')) {
            closeBankModal();
        }
        if (event.target === document.getElementById('questionModalBank')) {
            closeQuestionModal();
        }
    });

    (async function initQuestionBankPage() {
        try {
            await loadBanks();
            if (bankItems.length) {
                await loadBankQuestions(Number(bankItems[0].id_nganhang));
            }
        } catch (error) {
            showBankAlert(error.message, 'danger');
        }
    })();
</script>