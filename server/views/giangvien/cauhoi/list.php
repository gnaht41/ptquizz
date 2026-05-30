<?php $id_baithi = (int) ($_GET['id_baithi'] ?? 0); ?>

<div id="questionAlert"></div>

<div class="container" style="max-width: 1240px; margin: 0 auto; padding: 20px;">
    <div
        style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:25px; padding-bottom:15px; border-bottom:1px solid #eee;">
        <div>
            <h2 style="margin:0; font-size:24px; color:#1e293b;" id="questionExamTitle">Đang tải...</h2>
            <div id="questionMeta" style="margin-top:8px; color:#64748b; font-size:14px;"></div>
        </div>
        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:12px;">
            <div style="display:flex; gap:15px; align-items:center;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:13px; color:#475569;">Xem nhanh:</span>
                    <label class="premium-switch"><input type="checkbox" id="toggleAnswersUI" checked
                            onchange="renderQuestionTable()"><span class="premium-slider"></span></label>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:13px; color:#475569;">Hiện đáp án:</span>
                    <label class="premium-switch"><input type="checkbox" id="examShowAnswersToggle"
                            onchange="updateExamSetting({hien_dapan: this.checked})"><span
                            class="premium-slider"></span></label>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:13px; color:#475569;">Xáo trộn:</span>
                    <label class="premium-switch"><input type="checkbox" id="examShuffleToggle"
                            onchange="updateExamSetting({xao_tron: this.checked})"><span
                            class="premium-slider"></span></label>
                </div>
            </div>
            <div style="display:flex; gap:8px;">
                <a href="index.php?act=quanly-baithi"
                    style="background:#f1f5f9; color:#475569; padding:8px 16px; border-radius:6px; text-decoration:none; font-size:13px; font-weight:600; border:1px solid #e2e8f0;">Quay
                    lại</a>
                <button id="importWordBtn" onclick="openImportModal()"
                    style="background:#3b82f6; color:white; padding:8px 16px; border-radius:6px; border:none; cursor:pointer; font-size:13px; font-weight:600;">Import</button>
                <button id="importBankBtn" onclick="openBankModal()"
                    style="background:#8b5cf6; color:white; padding:8px 16px; border-radius:6px; border:none; cursor:pointer; font-size:13px; font-weight:600;">Ngân
                    hàng</button>
                <button id="addQuestionBtn" onclick="openAddModal()"
                    style="background:#10b981; color:white; padding:8px 16px; border-radius:6px; border:none; cursor:pointer; font-size:13px; font-weight:600;">Thêm
                    câu hỏi</button>
            </div>
        </div>
    </div>

    <style>
        .premium-switch {
            position: relative;
            display: inline-block;
            width: 38px;
            height: 20px;
        }

        .premium-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .premium-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .3s;
            border-radius: 20px;
        }

        .premium-slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        input:checked+.premium-slider {
            background-color: #3b82f6;
        }

        input:checked+.premium-slider:before {
            transform: translateX(18px);
        }

        input:disabled+.premium-slider {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <!-- Modal chọn từ Ngân hàng -->
    <div id="bankModal"
        style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
        <div style="background:white; padding:30px; border-radius:10px; width:500px;">
            <h3 style="margin-top:0;">Chọn Câu Hỏi Từ Ngân Hàng</h3>
            <form id="importBankForm">
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Chọn ngân hàng:</label>
                    <select id="selectBankId"
                        style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;" required></select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:20px;">
                    <div>
                        <label style="font-weight:bold; font-size:13px;">Số câu Dễ:</label>
                        <input type="number" id="countEasy" value="0" min="0"
                            style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                    <div>
                        <label style="font-weight:bold; font-size:13px;">Trung bình:</label>
                        <input type="number" id="countMedium" value="0" min="0"
                            style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                    <div>
                        <label style="font-weight:bold; font-size:13px;">Số câu Khó:</label>
                        <input type="number" id="countHard" value="0" min="0"
                            style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                </div>
                <div style="text-align:right;">
                    <button type="button" onclick="closeBankModal()"
                        style="padding:8px 20px; border:1px solid #ccc; border-radius:4px; cursor:pointer; background:white;">Hủy</button>
                    <button type="submit"
                        style="background:#8e44ad; color:white; border:none; padding:8px 25px; border-radius:4px; margin-left:10px; cursor:pointer;">Xác
                        nhận thêm</button>
                </div>
            </form>
        </div>
    </div>

    <div id="examInfoCard"
        style="background:white; border-radius:12px; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.08); margin-bottom:20px;">
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px;" id="examInfoGrid"></div>
        <div style="margin-top:16px;">
            <div style="font-weight:700; color:#333; margin-bottom:8px;">Miêu tả bài thi</div>
            <div id="examDescription"
                style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:10px; padding:14px; color:#495057; min-height:52px;">
            </div>
        </div>
        <div style="margin-top:16px; background:#f8f9fa; border:1px dashed #ced4da; border-radius:10px; padding:14px;">
            <div style="font-weight:700; color:#333; margin-bottom:8px;">Mẫu Word hỗ trợ import</div>
            <pre
                style="margin:0; white-space:pre-wrap; font-family:Consolas, monospace; font-size:13px; color:#495057;">Câu 1: PHP là viết tắt của cụm từ nào?
A. Personal Home Page
B. Private Home Page
C. Preprocessor Hypertext
D. Programming HTML Page
Đáp án: A
Độ khó: Dễ
Lời giải: (Nội dung giải thích chi tiết ở đây)</pre>
            <pre
                style="margin:0; white-space:pre-wrap; font-family:Consolas, monospace; font-size:13px; color:#495057;">Câu 2: Có công mài [...], có ngày nên [...] ?
Đáp án: sắt | kim
Độ khó: Dễ
Lời giải: (Nội dung giải thích chi tiết ở đây)</pre>
        </div>
    </div>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <table id="questionTable" style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 12px; text-align: center;">STT</th>
                    <th style="padding: 12px; text-align: left;">Nội dung câu hỏi</th>
                    <th style="padding: 12px; text-align: left;">Đáp án</th>
                    <th style="padding: 12px; text-align: center;">Độ khó</th>
                    <th style="padding: 12px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="questionTableBody">
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:#999;">Đang tải dữ liệu...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="questionModal"
    style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
    <div style="background:white; padding:30px; border-radius:10px; width:700px; max-height:90%; overflow-y:auto;">
        <h3 id="modalTitle" style="margin-top:0;">Thêm câu hỏi mới</h3>
        <form id="questionForm">
            <input type="hidden" name="id_baithi" value="<?= $id_baithi ?>">
            <input type="hidden" name="id_cauhoi" id="edit_id_cauhoi">

            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold;">Nội dung câu hỏi:</label>
                <textarea name="noidungcauhoi" id="noidungcauhoi" rows="3"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; gap:15px; margin-bottom:15px;">
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Loại câu hỏi:</label>
                    <select name="loai_cauhoi" id="loai_cauhoi" onchange="toggleAnswerType()"
                        style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                        <option value="1">Trắc nghiệm</option>
                        <option value="2">Điền từ</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Độ khó:</label>
                    <select name="dokho" id="dokho"
                        style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
                        <option value="Dễ">Dễ</option>
                        <option value="Trung bình">Trung bình</option>
                        <option value="Khó">Khó</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:15px;">
                <div id="answerHeader" style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="font-weight:bold;">Đáp án:</label>
                    <button type="button" id="btnAddOption" onclick="addOption()"
                        style="background:#3498db; color:white; border:none; padding:6px 12px; border-radius:4px; cursor:pointer; font-size:13px;">+
                        Thêm đáp án</button>
                </div>
                <p id="answerTypeDesc" style="display:none; color:#666; font-size:12px; margin:5px 0;">
                    Sử dụng <strong>[...]</strong> trong nội dung câu hỏi để tạo chỗ trống.<br>
                    Các đáp án dưới đây sẽ được khớp theo thứ tự các dấu [...] xuất hiện.
                </p>
                <div id="optionsContainer" style="margin-top:10px;"></div>
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px; font-weight:bold; color: #1e293b;">
                    Lời giải chi tiết:
                </label>
                <textarea name="loigiai_chitiet" id="loigiai_chitiet_input" rows="3"
                    placeholder="Bắt buộc: Giải thích tại sao đáp án này đúng..." required
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; box-sizing:border-box; font-size: 13.5px;"></textarea>
            </div>

            <div style="margin-top:20px; text-align:right; border-top:1px solid #eee; padding-top:15px;">
                <button type="button" onclick="closeModal()"
                    style="padding:8px 20px; border:1px solid #ccc; border-radius:4px; cursor:pointer; background:white;">Hủy</button>
                <button type="submit"
                    style="background:#27ae60; color:white; border:none; padding:8px 25px; border-radius:4px; margin-left:10px; cursor:pointer;">Lưu
                    câu hỏi</button>
            </div>
        </form>
    </div>
</div>

<div id="importModal"
    style="display:none; position:fixed; z-index:10000; inset:0; background:rgba(15,23,42,0.6); align-items:center; justify-content:center; padding:24px; backdrop-filter:blur(4px);">
    <div
        style="width:100%; max-width:520px; background:#fff; border-radius:20px; box-shadow:0 25px 60px rgba(0,0,0,0.2); padding:35px; position:relative;">
        <h4 style="margin:0 0 8px; color:#1e293b; font-weight:700;">Import Câu Hỏi Từ File Word / Excel / PDF</h4>
        <p style="color:#64748b; font-size:13.5px; margin-bottom:20px;">Hỗ trợ định dạng <strong>.docx, .xlsx</strong>
            hoặc <strong>.pdf</strong></p>

        <form id="importWordForm">
            <input type="hidden" name="id_baithi" value="<?= $id_baithi ?>">
            <div style="margin-bottom:20px;">
                <input type="file" name="word_file" id="wordFileInput" accept=".docx, .xlsx, .pdf" class="form-control"
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
                <button type="button" onclick="closeImportModal()" class="btn"
                    style="background:#f1f5f9; color:#64748b; font-weight:600; padding:10px 25px; border-radius:8px; border:none;">Hủy</button>
                <button type="submit" class="btn"
                    style="background:#3b82f6; color:white; font-weight:700; padding:10px 35px; border-radius:8px; border:none; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">
                    Bắt đầu Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function escapeHtml(text) {
        if (!text) return "";
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    const examId = <?= $id_baithi ?>;
    let questionItems = [];
    let examInfo = null;
    let maxQuestions = 0;
    let examLocked = false;
    let importInFlight = false;

    function escapeHtml(str) {
        return String(str ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
    }

    function showQuestionAlert(message, type = 'success') {
        const box = document.getElementById('questionAlert');
        const bg = type === 'success' ? '#d4edda' : '#f8d7da';
        const color = type === 'success' ? '#155724' : '#721c24';
        box.innerHTML = `<div style="background:${bg};color:${color};padding:15px;border-radius:8px;margin-bottom:20px;">${escapeHtml(message)}</div>`;
    }

    function renderExamInfoCard() {
        const items = [
            ['Số câu hỏi', `${examInfo.tongcauhoi || 0} câu`],
            ['Thời gian làm bài', `${examInfo.thoigianlam || 0} phút`],
            ['Trạng thái', `<span style="color:${examInfo.trangthai === 'Đang mở' ? '#27ae60' : '#e67e22'}; font-weight:700;">${examInfo.trangthai === 'Đang mở' ? 'Đang mở' : 'Đã đóng'}</span>`],
            ['Thời gian mở', examInfo.thoigianbatdau || '---'],
            ['Thời gian đóng', examInfo.thoigianketthuc || '---'],
            ['Xáo trộn', Number(examInfo.xao_tron) === 1 ? '<span class="badge bg-primary">Bật</span>' : '<span class="badge bg-secondary">Tắt</span>'],
            ['Ngày tạo', examInfo.ngaytao || '---'],
        ];

        document.getElementById('examInfoGrid').innerHTML = items.map(([label, value]) => `
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div style="color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; margin-bottom:8px;">${label}</div>
            <div style="font-size:15px; font-weight:700; color:#1e293b;">${value}</div>
        </div>
    `).join('');

        document.getElementById('examDescription').innerHTML = examInfo.mieuta ? escapeHtml(examInfo.mieuta) : '<span style="color:#94a3b8; font-style:italic;">Chưa có miêu tả cho bài thi này.</span>';
    }

    function addOption(content = '', isCorrect = false) {
        const type = parseInt(document.getElementById('loai_cauhoi').value || 1);
        const container = document.getElementById('optionsContainer');

        if (type === 2) {
            const div = document.createElement('div');
            div.className = 'option-item';
            div.style.cssText = 'margin-bottom:10px;display:flex;gap:10px;align-items:center;';
            div.innerHTML = `
            <input type="text" name="option[]" value="${escapeHtml(content)}" placeholder="Đáp án cho dấu [...] ..." required style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px;">
            <input type="hidden" name="is_correct_radio" value="on">
            <button type="button" onclick="this.parentElement.remove()" style="background:#e74c3c;color:white;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;">Xóa</button>
        `;
            container.appendChild(div);
            return;
        }

        const div = document.createElement('div');
        div.className = 'option-item';
        div.style.cssText = 'margin-bottom:10px;display:flex;gap:10px;align-items:center;';
        div.innerHTML = `
        <input type="text" name="option[]" value="${escapeHtml(content)}" placeholder="Nhập nội dung đáp án..." required style="flex:1;padding:8px;border:1px solid #ddd;border-radius:4px;">
        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;white-space:nowrap;">
            <input type="radio" name="is_correct_radio" ${isCorrect ? 'checked' : ''}> Đúng
        </label>
        <button type="button" onclick="this.parentElement.remove()" style="background:#e74c3c;color:white;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;">Xóa</button>
    `;
        container.appendChild(div);
    }

    function toggleAnswerType() {
        const type = parseInt(document.getElementById('loai_cauhoi').value || 1);
        const btnAdd = document.getElementById('btnAddOption');
        const desc = document.getElementById('answerTypeDesc');
        const container = document.getElementById('optionsContainer');

        // Get current answer values to preserve them
        const currentValues = Array.from(container.querySelectorAll('input[name="option[]"]')).map(inp => inp.value);

        // Clear and redraw
        container.innerHTML = '';
        if (currentValues.length > 0) {
            currentValues.forEach((val, i) => addOption(val, i === 0));
        } else {
            addOption();
            addOption();
        }

        if (type === 2) {
            btnAdd.style.display = 'inline-block';
            desc.style.display = 'block';
        } else {
            btnAdd.style.display = 'inline-block';
            desc.style.display = 'none';
        }
    }

    function renderQuestionActions(question) {
        if (examLocked) {
            return '<span style="color:#6c757d;font-size:13px;font-weight:600;">Chỉ xem</span>';
        }

        return `
        <button class="btn-edit-question" data-id="${Number(question.id_cauhoi)}" style="background:#f39c12;color:white;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;">Sửa</button>
        <button class="btn-delete-question" data-id="${Number(question.id_cauhoi)}" style="background:#e74c3c;color:white;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;margin-left:5px;">Xóa</button>
    `;
    }

    async function loadQuestions() {
        const tbody = document.getElementById('questionTableBody');
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:#999;">Đang tải dữ liệu...</td></tr>';

        try {
            const res = await fetch(serverApiUrl('cauhoi/list', { id_baithi: examId }));
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Không thể tải câu hỏi');

            examInfo = json.baithi;
            examLocked = !!json.is_locked;
            questionItems = json.questions || [];
            maxQuestions = Number(examInfo.tongcauhoi || 0);

            document.getElementById('questionExamTitle').innerText = (examInfo.ten_baithi ? 'Bài thi: ' + examInfo.ten_baithi : 'Quản lý câu hỏi');

            let metaHtml = `
            <div style="display:flex; align-items:center; gap:20px;">
                <span>Môn: <strong>${escapeHtml(examInfo.tenmonhoc || '---')}</strong></span>
                <span>Tiến độ: <strong>${questionItems.length}/${maxQuestions} câu</strong></span>
            </div>
        `;

            if (examLocked) {
                metaHtml += `
            <div style="margin-top:8px; color:#ef4444; font-size:13px; font-weight:600;">
                Thông báo: Đã có thí sinh làm bài, chỉ được phép thay đổi Tùy chọn hiển thị (không được Sửa/Xóa câu hỏi).
            </div>
            `;
            }

            document.getElementById('questionMeta').innerHTML = metaHtml;

            document.getElementById('addQuestionBtn').disabled = examLocked;
            document.getElementById('addQuestionBtn').style.opacity = examLocked ? '0.6' : '1';
            document.getElementById('importWordBtn').disabled = examLocked;
            document.getElementById('importWordBtn').style.opacity = examLocked ? '0.6' : '1';
            document.getElementById('importBankBtn').disabled = examLocked;
            document.getElementById('importBankBtn').style.opacity = examLocked ? '0.6' : '1';

            // Always enable these toggles
            document.getElementById('examShuffleToggle').disabled = false;
            document.getElementById('examShowAnswersToggle').disabled = false;
            document.getElementById('examShuffleToggle').checked = Number(examInfo.xao_tron) === 1;
            document.getElementById('examShowAnswersToggle').checked = Number(examInfo.hien_dapan) === 1;

            renderExamInfoCard();
            renderQuestionTable();
        } catch (error) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:40px;color:#999;">${escapeHtml(error.message)}</td></tr>`;
        }
    }

    async function updateExamSetting(settings) {
        try {
            const payload = {
                id_baithi: examId,
                only_toggle: true,
                ...settings
            };
            const res = await fetch(serverApiUrl('baithi/save'), {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Cập nhật thất bại');

            showQuestionAlert(json.message, 'success');

            // Update local examInfo
            Object.assign(examInfo, settings);
            renderExamInfoCard();
        } catch (error) {
            showQuestionAlert(error.message, 'error');
            // Revert toggle UI
            loadQuestions();
        }
    }

    function renderQuestionTable() {
        const tbody = document.getElementById('questionTableBody');
        const showAnswers = document.getElementById('toggleAnswersUI').checked;

        if (!questionItems.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:#999;">Chưa có câu hỏi nào.</td></tr>';
            return;
        }

        tbody.innerHTML = questionItems.map((ch, index) => {
            let answersHtml = '<span style="color:#94a3b8; font-style:italic; font-size:13px;">Đang ẩn đáp án</span>';
            if (showAnswers) {
                if (Number(ch.loai_cauhoi) === 2) {
                    const correct = (ch.dapan || [])[0]?.noidungdapan || '---';
                    answersHtml = `<div style="color:#27ae60; font-weight:bold; font-size:14px;">[Điền từ] ${escapeHtml(correct)}</div>`;
                } else {
                    answersHtml = (ch.dapan || []).map(d => `
                    <div style="margin:5px 0;display:flex;align-items:center;">
                        <input type="checkbox" ${Number(d.dapandung) === 1 ? 'checked' : ''} disabled style="margin-right:8px;">
                        <span style="${Number(d.dapandung) === 1 ? 'color:#27ae60;font-weight:bold;' : 'color:#666;'}">${escapeHtml(d.noidungdapan)}</span>
                    </div>
                `).join('');
                }
            }

            return `
            <tr style="border-bottom:1px solid #eee;" class="question-row">
                <td style="padding:12px;text-align:center;">${index + 1}</td>
                <td style="padding:12px;"><strong>${escapeHtml(ch.noidungcauhoi)}</strong></td>
                <td style="padding:12px;">${answersHtml}</td>
                <td style="padding:12px;text-align:center;">${escapeHtml(ch.dokho)}</td>
                <td style="padding:12px;text-align:center;">${renderQuestionActions(ch)}</td>
            </tr>
        `;
        }).join('');
    }

    function openAddModal() {
        if (examLocked) {
            showQuestionAlert('Bài thi đã có thí sinh làm, không được phép thêm câu hỏi.', 'error');
            return;
        }
        if (questionItems.length >= maxQuestions) {
            showQuestionAlert(`Bài thi đã đủ ${questionItems.length}/${maxQuestions} câu.`, 'error');
            return;
        }

        document.getElementById('modalTitle').innerText = 'Thêm câu hỏi mới';
        document.getElementById('questionForm').reset();
        document.getElementById('edit_id_cauhoi').value = '';
        document.getElementById('loai_cauhoi').value = '1';
        document.getElementById('optionsContainer').innerHTML = '';
        toggleAnswerType();
        addOption();
        addOption();
        document.getElementById('questionModal').style.display = 'flex';
    }

    function openEditModal(data) {
        if (examLocked) {
            showQuestionAlert('Bài thi đã có thí sinh làm, không được phép sửa câu hỏi.', 'error');
            return;
        }

        document.getElementById('modalTitle').innerText = 'Sửa câu hỏi';
        document.getElementById('noidungcauhoi').value = data.noidungcauhoi;
        document.getElementById('dokho').value = data.dokho;
        document.getElementById('edit_id_cauhoi').value = data.id_cauhoi;
        document.getElementById('loai_cauhoi').value = data.loai_cauhoi || 1;

        // Extract loigiai_chitiet from answers
        const loigiai = data.dapan ? (data.dapan.find(d => d.loigiai_chitiet)?.loigiai_chitiet || '') : '';
        document.getElementById('loigiai_chitiet_input').value = loigiai;

        toggleAnswerType();
        document.getElementById('optionsContainer').innerHTML = '';
        (data.dapan || []).forEach(d => addOption(d.noidungdapan, Number(d.dapandung) === 1));
        document.getElementById('questionModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('questionModal').style.display = 'none';
    }

    function openImportModal() {
        if (examLocked) {
            showQuestionAlert('Bài thi đã có thí sinh làm, không được phép import câu hỏi.', 'error');
            return;
        }
        document.getElementById('importWordForm').reset();
        document.getElementById('importModal').style.display = 'flex';
    }

    function closeImportModal() {
        document.getElementById('importModal').style.display = 'none';
    }

    async function openBankModal() {
        if (examLocked) {
            showQuestionAlert('Bài thi đã có thí sinh làm, không được phép import câu hỏi.', 'error');
            return;
        }
        const select = document.getElementById('selectBankId');
        select.innerHTML = '<option value="">Đang tải...</option>';
        document.getElementById('bankModal').style.display = 'flex';

        try {
            const res = await fetch(serverApiUrl('nganhang/list', { id_monhoc: examInfo.id_monhoc }));
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Không thể tải danh sách ngân hàng');

            const banks = json.banks || [];
            if (banks.length === 0) {
                select.innerHTML = '<option value="">Chưa có ngân hàng nào</option>';
            } else {
                select.innerHTML = banks.map(b => `<option value="${b.id_nganhang}">${escapeHtml(b.ten_nganhang)} (${b.tenmonhoc || 'Nhiều môn'})</option>`).join('');
            }
        } catch (error) {
            select.innerHTML = `<option value="">Lỗi: ${escapeHtml(error.message)}</option>`;
        }
    }

    function closeBankModal() {
        document.getElementById('bankModal').style.display = 'none';
    }

    document.getElementById('importBankForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id_nhch = document.getElementById('selectBankId').value;
        if (!id_nhch) return;

        const counts = {
            'Dễ': parseInt(document.getElementById('countEasy').value) || 0,
            'Trung bình': parseInt(document.getElementById('countMedium').value) || 0,
            'Khó': parseInt(document.getElementById('countHard').value) || 0
        };

        if (Object.values(counts).every(v => v === 0)) {
            showQuestionAlert('Vui lòng nhập ít nhất một loại độ khó', 'error');
            return;
        }

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

        try {
            const res = await fetch(serverApiUrl('cauhoi/import-bank'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_baithi: examId,
                    id_nhch: Number(id_nhch),
                    counts: counts
                })
            });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Import thất bại');

            closeBankModal();
            showQuestionAlert(json.message, 'success');
            loadQuestions();
        } catch (error) {
            showQuestionAlert(error.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    async function deleteQuestion(id) {
        if (examLocked) {
            showQuestionAlert('Bài thi đã có thí sinh làm, không được phép xóa câu hỏi.', 'error');
            return;
        }
        if (!confirm('Xóa câu hỏi này?')) return;

        try {
            const res = await fetch(serverApiUrl('cauhoi/delete'), {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_cauhoi: id })
            });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Xóa thất bại');
            showQuestionAlert(json.message, 'success');
            loadQuestions();
        } catch (error) {
            showQuestionAlert(error.message, 'error');
        }
    }

    document.getElementById('questionTableBody').addEventListener('click', function (event) {
        const editButton = event.target.closest('.btn-edit-question');
        if (editButton) {
            const question = questionItems.find(item => Number(item.id_cauhoi) === Number(editButton.dataset.id));
            if (question) {
                openEditModal(question);
            }
            return;
        }

        const deleteButton = event.target.closest('.btn-delete-question');
        if (deleteButton) {
            deleteQuestion(Number(deleteButton.dataset.id || 0));
        }
    });

    document.getElementById('questionForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const id_cauhoi = document.getElementById('edit_id_cauhoi').value;
        const noidung = document.getElementById('noidungcauhoi').value.trim();
        const optionsNodes = document.querySelectorAll('input[name="option[]"]');
        const optionsValues = Array.from(optionsNodes).map(input => input.value.trim());
        const radios = document.querySelectorAll('input[name="is_correct_radio"]');
        let selectedIndex = -1;
        radios.forEach((radio, index) => { if (radio.checked) selectedIndex = index; });
        const loai_cauhoi = parseInt(document.getElementById('loai_cauhoi').value || 1);

        if (!id_cauhoi && questionItems.length >= maxQuestions) {
            showQuestionAlert(`Bài thi đã đạt giới hạn tối đa ${maxQuestions} câu.`, 'error');
            return;
        }
        if (noidung === '') {
            showQuestionAlert('Nội dung câu hỏi không được để trống', 'error');
            return;
        }

        if (loai_cauhoi === 1) {
            if (optionsValues.length < 2 || optionsValues.some(opt => opt === '')) {
                showQuestionAlert('Trắc nghiệm cần ít nhất 2 đáp án hợp lệ', 'error');
                return;
            }
            if (new Set(optionsValues.map(v => v.toLowerCase())).size !== optionsValues.length) {
                showQuestionAlert('Các đáp án của một câu hỏi không được trùng nhau', 'error');
                return;
            }
            if (selectedIndex < 0) {
                showQuestionAlert('Vui lòng chọn một đáp án đúng', 'error');
                return;
            }
        } else {
            // Validation cho câu hỏi ĐIỀN TỪ
            const placeholderMatch = noidung.match(/\[\.\.\.\]/g);
            const placeholderCount = placeholderMatch ? placeholderMatch.length : 0;

            if (placeholderCount === 0) {
                showQuestionAlert('Câu hỏi điền từ phải có ít nhất một dấu [...] trong nội dung câu hỏi để tạo chỗ trống.', 'error');
                return;
            }

            if (optionsValues.length !== placeholderCount) {
                showQuestionAlert(`Số lượng đáp án (${optionsValues.length}) phải khớp với số lượng dấu [...] trong nội dung (${placeholderCount}).`, 'error');
                return;
            }

            selectedIndex = 0;
        }

        const loigiai = document.getElementById('loigiai_chitiet_input').value.trim();
        if (loigiai === '') {
            showQuestionAlert('Vui lòng nhập lời giải chi tiết cho câu hỏi này', 'error');
            return;
        }

        const payload = {
            id_baithi: examId,
            id_cauhoi: id_cauhoi || 0,
            noidungcauhoi: noidung,
            dokho: document.getElementById('dokho').value,
            loai_cauhoi: loai_cauhoi,
            options: optionsValues,
            correct_index: selectedIndex,
            loigiai_chitiet: loigiai
        };

        try {
            const res = await fetch(serverApiUrl('cauhoi/save'), {
                method: payload.id_cauhoi && Number(payload.id_cauhoi) > 0 ? 'PATCH' : 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Lưu thất bại');
            closeModal();
            showQuestionAlert(json.message, 'success');
            loadQuestions();
        } catch (error) {
            showQuestionAlert(error.message, 'error');
        }
    });

    document.getElementById('importWordForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        if (importInFlight) return;

        const formData = new FormData(this);
        const file = document.getElementById('wordFileInput').files[0];
        if (!file) {
            showQuestionAlert('Vui lòng chọn file Word .docx', 'error');
            return;
        }

        const submitButton = this.querySelector('button[type="submit"]');
        importInFlight = true;
        submitButton.disabled = true;
        submitButton.style.opacity = '0.7';
        submitButton.textContent = 'Đang import...';

        try {
            const res = await fetch(serverApiUrl('cauhoi/import-word'), {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.error || 'Import thất bại');
            closeImportModal();
            showQuestionAlert(json.message, 'success');
            loadQuestions();
        } catch (error) {
            showQuestionAlert(error.message, 'error');
        } finally {
            importInFlight = false;
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            submitButton.textContent = 'Import';
        }
    });

    window.addEventListener('click', (event) => {
        if (event.target === document.getElementById('questionModal')) {
            closeModal();
        }
        if (event.target === document.getElementById('importModal')) {
            closeImportModal();
        }
        if (event.target === document.getElementById('bankModal')) {
            closeBankModal();
        }
    });

    loadQuestions();
</script>