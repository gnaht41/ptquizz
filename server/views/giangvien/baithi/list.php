<div id="baithiAlert"></div>

<div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #333;">Bài thi</h2>
        <button onclick="openExamModal()"
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 22px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: transform 0.2s, box-shadow 0.2s;"
            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 15px rgba(16, 185, 129, 0.3)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.2)'">
            <i class="fas fa-plus"></i> Thêm bài thi
        </button>
    </div>

    <div style="margin-bottom: 16px; color: #6c757d; font-size: 14px;">
        Bấm vào một dòng bài thi để xem chi tiết câu hỏi, đáp án và đáp án đúng.
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; width:60px;">STT</th>
                <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em;">Bài thi</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:150px;">Cấu hình</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:180px;">Trạng thái</th>
                <th style="padding: 14px 20px; text-align: right; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="baithiTableBody">
            <tr><td colspan="7" style="text-align:center;padding:20px;">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>
</div>

<div id="examModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center;">
    <div style="background: white; padding: 35px; border-radius: 16px; width: 680px; max-width: 95vw; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
        <h3 id="modalTitle" style="margin-top: 0; color: #1e293b; font-size: 20px; font-weight: 700;">Thêm Bài Thi</h3>
        <form id="examForm">
            <input type="hidden" name="id_baithi" id="m_id">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                <div style="grid-column: span 2;">
                    <label style="font-weight: 600;">Tên bài thi</label>
                    <input type="text" name="ten_baithi" id="m_ten" required placeholder="Nhập tên bài thi..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div>
                    <label style="font-weight: 600;">Môn học</label>
                    <select name="id_monhoc" id="m_mon" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"></select>
                </div>
                <div>
                    <label style="font-weight: 600;">Trạng thái</label>
                    <select name="trangthai" id="m_status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                        <option value="Đang mở">Đang mở</option>
                        <option value="Đóng">Đóng</option>
                    </select>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" name="xao_tron" id="m_shuffle" value="1">
                    <label for="m_shuffle" style="font-weight:600; margin:0;">Xáo trộn câu hỏi và đáp án</label>
                </div>
                <div>
                    <label style="font-weight: 600;">Số câu (≥ 5)</label>
                    <input type="number" name="tongcauhoi" id="m_cau" required min="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div>
                    <label style="font-weight: 600;">Thời gian (phút)</label>
                    <input type="number" name="thoigianlam" id="m_time" required min="2" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div>
                    <label style="font-weight: 600;">Bắt đầu</label>
                    <input type="datetime-local" name="thoigianbatdau" id="m_start" required onchange="updateEndMin()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div>
                    <label style="font-weight: 600;">Kết thúc</label>
                    <input type="datetime-local" name="thoigianketthuc" id="m_end" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                </div>
                <div style="grid-column: span 2;">
                    <label style="font-weight: 600;">Miêu tả</label>
                    <textarea name="mieuta" id="m_mieuta" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
                </div>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 35px;">
                <button type="button" onclick="closeExamModal()"
                    style="background: #f1f5f9; color: #64748b; padding: 10px 25px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;">Hủy</button>
                <button type="submit" id="examSubmitBtn"
                    style="background: #3b82f6; color: white; border: none; padding: 10px 35px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">Lưu bài thi</button>
            </div>
        </form>
    </div>
</div>

<div id="shuffleModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center;">
    <div style="background: white; padding: 35px; border-radius: 16px; width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
        <h3 style="margin-top: 0; color: #1e293b; font-size: 18px; font-weight: 700;">Chỉnh Xáo Trộn Câu Hỏi</h3>
        <form id="shuffleForm">
            <input type="hidden" id="s_id" value="">
            <div style="margin: 20px 0;">
                <label style="font-weight: 600; display:flex; align-items:center; gap:10px; cursor:pointer;">
                    <input type="checkbox" id="s_shuffle" style="width:20px;height:20px;cursor:pointer;">
                    <span>Xáo trộn câu hỏi và đáp án</span>
                </label>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" onclick="closeShuffleModal()"
                    style="background: #f1f5f9; color: #64748b; padding: 10px 22px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer;">Hủy</button>
                <button type="submit"
                    style="background: #3b82f6; color: white; border: none; padding: 10px 28px; border-radius: 8px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">Lưu cấu hình</button>
            </div>
        </form>
    </div>
</div>

<script>
let examItems = [];
let subjectItems = [];

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
}

function showExamAlert(message, type = 'success') {
    const box = document.getElementById('baithiAlert');
    const bg = type === 'success' ? '#d4edda' : '#f8d7da';
    const color = type === 'success' ? '#155724' : '#721c24';
    const border = type === 'success' ? '#c3e6cb' : '#f5c6cb';
    box.innerHTML = `<div style="background:${bg};color:${color};padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid ${border};">${escapeHtml(message)}</div>`;
}

function getLocalDateTimeString(date) {
    if (!date || isNaN(date.getTime())) return '';
    const tzOffset = date.getTimezoneOffset() * 60000;
    return (new Date(date - tzOffset)).toISOString().slice(0, 16);
}

function updateEndMin() {
    const startVal = document.getElementById('m_start').value;
    if (startVal) document.getElementById('m_end').min = startVal;
}

function renderSubjectOptions(selectedId = '') {
    const select = document.getElementById('m_mon');
    select.innerHTML = subjectItems.map(mh => `<option value="${mh.id_monhoc}" ${String(mh.id_monhoc) === String(selectedId) ? 'selected' : ''}>${escapeHtml(mh.tenmonhoc)}</option>`).join('');
}

function getExamById(id) {
    return examItems.find(item => Number(item.id_baithi) === Number(id)) || null;
}

async function loadExamData() {
    const tbody = document.getElementById('baithiTableBody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;">Đang tải dữ liệu...</td></tr>';

    try {
        const res = await fetch(serverApiUrl('baithi/list'));
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Không thể tải bài thi');

        examItems = json.data || [];
        subjectItems = json.subjects || [];
        renderSubjectOptions();

        if (!examItems.length) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:40px;color:#64748b;">Chưa có bài thi nào được tạo.</td></tr>';
            return;
        }

        tbody.innerHTML = examItems.map((bt, index) => {
            const isLocked = !!bt.is_locked;
            const statusLabel = isLocked ? 'Đã có người làm' : 'Chưa có người làm';
            const statusColor = isLocked ? '#b91c1c' : '#15803d';
            const statusBg = isLocked ? '#fef2f2' : '#f0fdf4';
            const statusBorder = isLocked ? '#fee2e2' : '#dcfce7';

            return `
            <tr class="exam-row" data-id="${Number(bt.id_baithi)}" style="border-bottom:1px solid #f1f5f9; cursor:pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <td style="padding:16px 20px; text-align:center; color:#94a3b8; font-size:14px; font-weight:600;">${index + 1}</td>
                <td style="padding:16px 20px;">
                    <div style="font-weight:700; color:#1e293b; font-size:15px;">${escapeHtml(bt.ten_baithi)}</div>
                    <div style="font-size:12px; color:#64748b; margin-top:4px;">
                        Môn: <span style="color:#3b82f6; font-weight:600;">${escapeHtml(bt.tenmonhoc)}</span>
                    </div>
                </td>
                <td style="padding:16px 20px; text-align:center;">
                    <div style="font-size:13px; color:#1e293b; font-weight:600;">${bt.tongcauhoi} câu</div>
                    <div style="font-size:11px; color:#94a3b8; margin-top:2px;">${bt.thoigianlam} phút</div>
                </td>
                <td style="padding:16px 20px; text-align:center;">
                    <span style="display:inline-block; background:${statusBg}; color:${statusColor}; border:1px solid ${statusBorder}; padding:4px 12px; border-radius:12px; font-size:11px; font-weight:700; white-space:nowrap;">
                        ${statusLabel}
                    </span>
                </td>
                <td style="padding:16px 20px; text-align:right;">
                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                        <button class="btn-edit-exam" data-id="${Number(bt.id_baithi)}" style="background:#fff; color:#f59e0b; border:1px solid #fef3c7; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:600;">Sửa</button>
                        <button class="btn-delete-exam" data-id="${Number(bt.id_baithi)}" style="background:#fff; color:#ef4444; border:1px solid #fee2e2; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:600;">Xóa</button>
                    </div>
                </td>
            </tr>
        `}).join('');
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:40px;color:#ef4444;">${escapeHtml(error.message)}</td></tr>`;
    }
}

function validateExamForm() {
    const tenVal = document.getElementById('m_ten').value.trim();
    if (tenVal.length < 5) {
        showExamAlert('Tên bài thi phải ít nhất 5 ký tự', 'error');
        return false;
    }

    const startStr = document.getElementById('m_start').value;
    const endStr = document.getElementById('m_end').value;
    if (endStr && new Date(endStr) <= new Date(startStr)) {
        showExamAlert('Ngày kết thúc phải sau ngày bắt đầu', 'error');
        return false;
    }

    return true;
}

function openExamModal(data = null) {
    const modal = document.getElementById('examModal');
    const submitBtn = document.getElementById('examSubmitBtn'); 
    const startInput = document.getElementById('m_start');
    const endInput = document.getElementById('m_end');
    const now = new Date();
    const currentStr = getLocalDateTimeString(now);

    if (data) {
        document.getElementById('modalTitle').innerText = 'Cập nhật bài thi';
        document.getElementById('m_id').value = data.id_baithi;
        document.getElementById('m_ten').value = data.ten_baithi;
        renderSubjectOptions(data.id_monhoc);
        document.getElementById('m_status').value = data.trangthai;
        document.getElementById('m_shuffle').checked = !!Number(data.xao_tron);
        document.getElementById('m_cau').value = data.tongcauhoi;
        document.getElementById('m_time').value = data.thoigianlam;
        
        if (data.thoigianbatdau && data.thoigianbatdau !== '0000-00-00 00:00:00') {
            const startD = new Date(data.thoigianbatdau.replace(' ', 'T'));
            startInput.value = getLocalDateTimeString(startD);
            startInput.min = ''; 
        } else {
            startInput.value = '';
        }
        
        if (data.thoigianketthuc && data.thoigianketthuc !== '0000-00-00 00:00:00') {
            const endD = new Date(data.thoigianketthuc.replace(' ', 'T'));
            endInput.value = getLocalDateTimeString(endD);
        } else {
            endInput.value = '';
        }
        
        document.getElementById('m_mieuta').value = data.mieuta || '';

        // Khóa các trường nhạy cảm nếu đã có người làm
        const isLocked = !!data.is_locked;
        document.getElementById('m_mon').disabled = isLocked;
        document.getElementById('m_cau').readOnly = isLocked;
        document.getElementById('m_cau').style.background = isLocked ? '#f8fafc' : 'white';
        
        if (isLocked) {
            showExamAlert('Bài thi này đã có người làm. Bạn chỉ có thể sửa thông tin cơ bản và thời gian.', 'info');
        }

        submitBtn.innerText = 'Cập nhật';
        submitBtn.style.background = '#f59e0b';
        submitBtn.style.boxShadow = '0 4px 12px rgba(245, 158, 11, 0.25)';
    } else {
        document.getElementById('modalTitle').innerText = 'Thêm Bài Thi Mới';
        document.getElementById('examForm').reset();
        document.getElementById('m_id').value = '';
        document.getElementById('m_cau').value = '10';
        document.getElementById('m_time').value = '15';
        document.getElementById('m_status').value = 'Đang mở';
        document.getElementById('m_shuffle').checked = false;
        renderSubjectOptions();
        document.getElementById('m_mieuta').value = '';

        // Mở khóa tất cả cho bài mới
        document.getElementById('m_mon').disabled = false;
        document.getElementById('m_cau').readOnly = false;
        document.getElementById('m_cau').style.background = 'white';
        
        submitBtn.innerText = 'Lưu bài thi';
        submitBtn.style.background = '#3b82f6';
        submitBtn.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.25)';
        startInput.value = currentStr;
        startInput.min = currentStr;
        endInput.value = '';
        endInput.min = currentStr;
    }

    modal.style.display = 'flex';
}

function closeExamModal() {
    document.getElementById('examModal').style.display = 'none';
}

function openShuffleModal(data) {
    const modal = document.getElementById('shuffleModal');
    document.getElementById('s_id').value = data.id_baithi;
    document.getElementById('s_shuffle').checked = !!data.xao_tron;
    modal.style.display = 'flex';
}

document.getElementById('baithiTableBody').addEventListener('click', function(event) {
    const actionButton = event.target.closest('button');
    if (!actionButton) {
        const row = event.target.closest('.exam-row');
        if (row) {
            window.location.href = `index.php?act=cauhoi-list&id_baithi=${Number(row.dataset.id || 0)}`;
        }
        return;
    }

    const editButton = event.target.closest('.btn-edit-exam');
    if (editButton) {
        const exam = getExamById(editButton.dataset.id);
        if (exam) {
            openExamModal(exam);
        }
        return;
    }

    const shuffleButton = event.target.closest('.btn-shuffle-exam');
    if (shuffleButton) {
        event.stopPropagation();
        const exam = getExamById(shuffleButton.dataset.id);
        if (exam) {
            openShuffleModal(exam);
        }
        return;
    }

    const deleteButton = event.target.closest('.btn-delete-exam');
    if (deleteButton) {
        event.stopPropagation();
        deleteExam(Number(deleteButton.dataset.id || 0));
    }
});

function closeShuffleModal() {
    document.getElementById('shuffleModal').style.display = 'none';
}

async function deleteExam(id) {
    if (!confirm('Xóa bài thi này?')) return;

    try {
        const res = await fetch(serverApiUrl('baithi/delete'), {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_baithi: id })
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Xóa thất bại');
        showExamAlert(json.message, 'success');
        loadExamData();
    } catch (error) {
        showExamAlert(error.message, 'error');
    }
}

document.getElementById('examForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validateExamForm()) return;

    const form = document.getElementById('examForm');
    const disabledFields = form.querySelectorAll(':disabled');
    disabledFields.forEach(f => f.disabled = false);

    const payload = Object.fromEntries(new FormData(form).entries());

    // Khôi phục trạng thái disabled sau khi lấy dữ liệu
    disabledFields.forEach(f => f.disabled = true);

    try {
        const res = await fetch(serverApiUrl('baithi/save'), {
            method: payload.id_baithi && Number(payload.id_baithi) > 0 ? 'PATCH' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Lưu thất bại');
        closeExamModal();
        showExamAlert(json.message, 'success');
        loadExamData();
    } catch (error) {
        showExamAlert(error.message, 'error');
    }
});

window.addEventListener('click', (event) => {
    if (event.target === document.getElementById('examModal')) {
        closeExamModal();
    }
    if (event.target === document.getElementById('shuffleModal')) {
        closeShuffleModal();
    }
});

document.getElementById('shuffleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const id = document.getElementById('s_id').value;
    const xao_tron = document.getElementById('s_shuffle').checked ? 1 : 0;

    try {
        const res = await fetch(serverApiUrl('baithi/save'), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                id_baithi: id, 
                xao_tron: xao_tron,
                only_xao_tron: true
            })
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Lưu thất bại');
        closeShuffleModal();
        showExamAlert(json.message, 'success');
        loadExamData();
    } catch (error) {
        showExamAlert(error.message, 'error');
    }
});

loadExamData();
</script>
