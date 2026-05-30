<div id="monhocAlert"></div>

<div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #333;">Môn học</h2>
        <button onclick="openFormModal()"
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 22px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); transition: transform 0.2s, box-shadow 0.2s;"
            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 15px rgba(16, 185, 129, 0.3)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.2)'">
            <i class="fas fa-plus"></i> Thêm môn học
        </button>
    </div>

    <div style="margin-bottom: 16px; color: #6c757d; font-size: 14px;">
        Bấm vào một dòng môn học để xem chi tiết số bài thi, số câu hỏi và số đáp án thuộc môn đó.
    </div>

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; width:60px;">STT</th>
                <th style="padding: 14px 20px; text-align: left; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em;">Môn học</th>
                <th style="padding: 14px 20px; text-align: center; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:150px;">Số Bài thi</th>
                <th style="padding: 14px 20px; text-align: right; color:#64748b; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:0.025em; width:180px;">Thao tác</th>
            </tr>
        </thead>
        <tbody id="monhocTableBody">
            <tr><td colspan="5" style="text-align:center;padding:20px;">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>
</div>

<div id="detailModal"
    style="display:none; position:fixed; z-index:10000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); align-items:center; justify-content:center;">
    <div style="background:white; width:680px; max-width:92vw; border-radius:12px; padding:28px; box-shadow:0 10px 30px rgba(0,0,0,0.25);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom:18px;">
            <div>
                <h3 id="detailTitle" style="margin:0 0 8px; color:#333;">Chi tiết môn học</h3>
                <div id="detailDate" style="color:#6c757d; font-size:14px;"></div>
            </div>
            <button type="button" onclick="closeDetailModal()" style="background:#f1f3f5; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">Đóng</button>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; margin-bottom:18px;">
            <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:10px; padding:16px;">
                <div style="color:#6c757d; font-size:13px; margin-bottom:6px;">Bài thi</div>
                <div id="detailExamCount" style="font-size:24px; font-weight:700; color:#333;">0</div>
            </div>
            <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:10px; padding:16px;">
                <div style="color:#6c757d; font-size:13px; margin-bottom:6px;">Câu hỏi</div>
                <div id="detailQuestionCount" style="font-size:24px; font-weight:700; color:#333;">0</div>
            </div>
            <div style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:10px; padding:16px;">
                <div style="color:#6c757d; font-size:13px; margin-bottom:6px;">Đáp án</div>
                <div id="detailAnswerCount" style="font-size:24px; font-weight:700; color:#333;">0</div>
            </div>
        </div>

        <div style="margin-bottom:18px;">
            <div style="font-weight:700; color:#333; margin-bottom:8px;">Miêu tả</div>
            <div id="detailDescription" style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:10px; padding:14px; color:#495057; min-height:52px;"></div>
        </div>

        <div>
            <div style="font-weight:700; color:#333; margin-bottom:8px;">Danh sách bài thi thuộc môn</div>
            <div id="detailExamList" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
        </div>
    </div>
</div>

<div id="formModal"
    style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center;">
    <div style="background: white; padding: 35px; border-radius: 16px; width: 520px; max-width:92vw; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
        <h3 id="formTitle" style="margin-top: 0; color: #1e293b; font-size: 20px; font-weight: 700;">Thêm môn học</h3>

        <form id="monhocForm">
            <input type="hidden" name="id_monhoc" id="form_id_monhoc">

            <div style="margin: 18px 0;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Tên môn học</label>
                <input type="text" name="tenmonhoc" id="form_tenmonhoc" required
                    placeholder="VD: Lập trình Web, Cơ sở dữ liệu..."
                    style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 15px; box-sizing: border-box; outline: none; transition: border-color 0.2s; background: #f8fafc;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.background='#fff'"
                    onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'">
            </div>
            <div style="margin: 18px 0;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">Miêu tả môn học</label>
                <textarea name="mieuta" id="form_mieuta" placeholder="Nhập ghi chú hoặc miêu tả môn học..."
                    style="width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 15px; box-sizing: border-box; min-height: 120px; font-family: inherit; outline: none; transition: border-color 0.2s; background: #f8fafc;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.background='#fff'"
                    onblur="this.style.borderColor='#e2e8f0'; this.style.background='#f8fafc'"></textarea>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 30px;">
                <button type="button" onclick="closeFormModal()"
                    style="background: #f1f5f9; color: #475569; padding: 10px 22px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: background 0.2s;">Hủy</button>
                <button type="submit" id="formSubmitBtn"
                    style="background: #3b82f6; color: white; padding: 10px 28px; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; transition: transform 0.2s; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
let monhocItems = [];

function formatDate(dateStr) {
    if (!dateStr) return '---';
    const d = new Date(dateStr);
    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
}

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
}

function showAlert(message, type = 'success') {
    const box = document.getElementById('monhocAlert');
    const bg = type === 'success' ? '#d4edda' : '#f8d7da';
    const color = type === 'success' ? '#155724' : '#721c24';
    const border = type === 'success' ? '#c3e6cb' : '#f5c6cb';
    box.innerHTML = `<div style="background:${bg};color:${color};padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid ${border};">${escapeHtml(message)}</div>`;
}

function getSubjectById(id) {
    return monhocItems.find(item => Number(item.id_monhoc) === Number(id)) || null;
}

function renderExamList(raw) {
    if (!raw) {
        return '<span style="color:#6c757d;">Chưa có bài thi nào.</span>';
    }

    return raw.split('||')
        .filter(Boolean)
        .map(name => `<span style="background:#eef2ff; color:#334155; padding:6px 10px; border-radius:999px; font-size:13px; border:1px solid #dbe4ff;">${escapeHtml(name)}</span>`)
        .join('');
}

function openDetailModal(subject) {
    document.getElementById('detailTitle').innerText = subject.tenmonhoc || 'Chi tiết môn học';
    document.getElementById('detailDate').innerText = `Ngày tạo: ${formatDate(subject.ngaythem)}`;
    document.getElementById('detailExamCount').innerText = Number(subject.so_bai_thi || 0);
    document.getElementById('detailQuestionCount').innerText = Number(subject.so_cau_hoi || 0);
    document.getElementById('detailAnswerCount').innerText = Number(subject.so_dap_an || 0);
    document.getElementById('detailDescription').innerHTML = subject.mieuta ? escapeHtml(subject.mieuta) : '<span style="color:#6c757d;">Chưa có miêu tả.</span>';
    document.getElementById('detailExamList').innerHTML = renderExamList(subject.ds_baithi || '');
    document.getElementById('detailModal').style.display = 'flex';
}

function closeDetailModal() {
    document.getElementById('detailModal').style.display = 'none';
}

async function loadMonHoc() {
    const tbody = document.getElementById('monhocTableBody');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;">Đang tải dữ liệu...</td></tr>';

    try {
        const res = await fetch(serverApiUrl('monhoc/list'));
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Không thể tải môn học');

        monhocItems = json.data || [];
        if (!monhocItems.length) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:#64748b;">Chưa có môn học nào.</td></tr>';
            return;
        }

        tbody.innerHTML = monhocItems.map((mon, index) => `
            <tr class="subject-row" data-id="${Number(mon.id_monhoc)}" style="border-bottom:1px solid #f1f5f9; cursor:pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                <td style="padding:16px 20px; text-align:center; color:#94a3b8; font-size:14px; font-weight:600;">${index + 1}</td>
                <td style="padding:16px 20px;">
                    <div style="font-weight:700; color:#1e293b; font-size:15px;">${escapeHtml(mon.tenmonhoc)}</div>
                    <div style="font-size:12px; color:#64748b; margin-top:4px; max-width:400px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <span style="color:#94a3b8; font-weight:500;">Mô tả:</span> ${mon.mieuta ? escapeHtml(mon.mieuta) : '...'}
                    </div>
                </td>
                <td style="padding:16px 20px; text-align:center;">
                    <span style="background:#eff6ff; color:#1d4ed8; padding:4px 14px; border-radius:12px; font-size:12px; font-weight:700; border:1px solid #dbeafe;">
                        ${Number(mon.so_bai_thi || 0)} Bài thi
                    </span>
                </td>
                <td style="padding:16px 20px; text-align:right;">
                    <div style="display:flex; gap:8px; justify-content:flex-end;">
                        <button class="btn-edit-monhoc" data-id="${Number(mon.id_monhoc)}" style="background:#fff; color:#f59e0b; border:1px solid #fef3c7; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:600;">Sửa</button>
                        <button class="btn-delete-monhoc" data-id="${Number(mon.id_monhoc)}" data-name="${escapeHtml(mon.tenmonhoc)}" data-count="${Number(mon.so_bai_thi || 0)}" style="background:#fff; color:#ef4444; border:1px solid #fee2e2; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:13px; font-weight:600;">Xóa</button>
                    </div>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:40px;color:#ef4444;">${escapeHtml(error.message)}</td></tr>`;
    }
}

function openFormModal(id = null, name = '', description = '') {
    const modal = document.getElementById('formModal');
    const title = document.getElementById('formTitle');
    const inputId = document.getElementById('form_id_monhoc');
    const inputName = document.getElementById('form_tenmonhoc');
    const inputDesc = document.getElementById('form_mieuta');
    const submitBtn = document.getElementById('formSubmitBtn');

    if (id) {
        title.innerText = 'Cập nhật môn học';
        inputId.value = id;
        inputName.value = name;
        inputDesc.value = description;
        submitBtn.innerText = 'Cập nhật';
        submitBtn.style.background = '#f59e0b';
        submitBtn.style.boxShadow = '0 4px 12px rgba(245, 158, 11, 0.25)';
    } else {
        title.innerText = 'Thêm môn học';
        inputId.value = '';
        inputName.value = '';
        inputDesc.value = '';
        submitBtn.innerText = 'Lưu môn học';
        submitBtn.style.background = '#3b82f6';
        submitBtn.style.boxShadow = '0 4px 12px rgba(59, 130, 246, 0.25)';
    }

    modal.style.display = 'flex';
    inputName.focus();
}

function closeFormModal() {
    document.getElementById('formModal').style.display = 'none';
}

async function deleteMonHoc(id, name, count) {
    if (count > 0) {
        showAlert(`Môn ${name} đang có ${count} bài thi. Hãy xóa bài thi trước.`, 'error');
        return;
    }

    if (!confirm(`Xóa môn ${name}? Thao tác này không thể hoàn tác.`)) return;

    try {
        const res = await fetch(serverApiUrl('monhoc/delete'), {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_monhoc: id })
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Xóa thất bại');
        showAlert(json.message, 'success');
        loadMonHoc();
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

document.getElementById('monhocForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const payload = {
        id_monhoc: document.getElementById('form_id_monhoc').value || 0,
        tenmonhoc: document.getElementById('form_tenmonhoc').value,
        mieuta: document.getElementById('form_mieuta').value
    };

    try {
        const res = await fetch(serverApiUrl('monhoc/save'), {
            method: payload.id_monhoc && Number(payload.id_monhoc) > 0 ? 'PATCH' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.error || 'Lưu thất bại');
        closeFormModal();
        showAlert(json.message, 'success');
        loadMonHoc();
    } catch (error) {
        showAlert(error.message, 'error');
    }
});

document.getElementById('monhocTableBody').addEventListener('click', function(event) {
    const actionButton = event.target.closest('button');
    if (!actionButton) {
        const row = event.target.closest('.subject-row');
        if (row) {
            const subject = getSubjectById(row.dataset.id);
            if (subject) openDetailModal(subject);
        }
        return;
    }

    const editButton = event.target.closest('.btn-edit-monhoc');
    if (editButton) {
        const subject = getSubjectById(editButton.dataset.id);
        if (subject) {
            openFormModal(Number(subject.id_monhoc), subject.tenmonhoc || '', subject.mieuta || '');
        }
        return;
    }

    const deleteButton = event.target.closest('.btn-delete-monhoc');
    if (deleteButton) {
        deleteMonHoc(
            Number(deleteButton.dataset.id || 0),
            deleteButton.dataset.name || '',
            Number(deleteButton.dataset.count || 0)
        );
    }
});

window.addEventListener('click', (event) => {
    if (event.target === document.getElementById('formModal')) {
        closeFormModal();
    }
    if (event.target === document.getElementById('detailModal')) {
        closeDetailModal();
    }
});

loadMonHoc();
</script>
