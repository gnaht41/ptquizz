<?php
// views/giangvien/cauhoi/edit.php
$title = "Sửa câu hỏi";
?>

<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h2>Sửa Câu Hỏi</h2>

        <form method="POST" action="index.php?act=cauhoi-edit">
            <input type="hidden" name="id_cauhoi" value="<?= $cauhoi['id_cauhoi'] ?>">
            <input type="hidden" name="id_baithi" value="<?= $cauhoi['id_baithi'] ?>">

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 600;">Nội dung câu hỏi:</label>
                <textarea name="noidungcauhoi" rows="3" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;"><?= htmlspecialchars($cauhoi['noidungcauhoi']) ?></textarea>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 600;">Độ khó:</label>
                <select name="dokho" style="width: 200px; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="Dễ" <?= $cauhoi['dokho'] == 'Dễ' ? 'selected' : '' ?>>Dễ</option>
                    <option value="Trung bình" <?= $cauhoi['dokho'] == 'Trung bình' ? 'selected' : '' ?>>Trung bình
                    </option>
                    <option value="Khó" <?= $cauhoi['dokho'] == 'Khó' ? 'selected' : '' ?>>Khó</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label style="font-weight: 600;">Đáp án:</label>
                <div id="optionsContainer">
                    <?php foreach ($cauhoi['dapan'] as $index => $dap): ?>
                    <div style="margin-bottom: 10px; display: flex; gap: 10px;">
                        <input type="text" name="option[]" value="<?= htmlspecialchars($dap['noidungdapan']) ?>"
                            style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" name="is_correct[]" value="1"
                                <?= $dap['dapandung'] == 1 ? 'checked' : '' ?>> Đáp án đúng
                        </label>
                        <button type="button" onclick="this.parentElement.remove()"
                            style="background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Xóa</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="addOption()"
                    style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; margin-top: 10px;">
                    + Thêm đáp án
                </button>
            </div>

            <div style="margin-top: 20px;">
                <a href="index.php?act=cauhoi-list&id_baithi=<?= $cauhoi['id_baithi'] ?>"
                    style="padding: 8px 20px; border: 1px solid #ccc; border-radius: 4px; text-decoration: none;">Hủy</a>
                <button type="submit"
                    style="background: #27ae60; color: white; border: none; padding: 8px 25px; border-radius: 4px; margin-left: 10px;">Cập
                    nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
function addOption() {
    const container = document.getElementById('optionsContainer');
    const newDiv = document.createElement('div');
    newDiv.style.marginBottom = '10px';
    newDiv.style.display = 'flex';
    newDiv.style.gap = '10px';
    newDiv.innerHTML = `
            <input type="text" name="option[]" placeholder="Nội dung đáp án" 
                   style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <label style="display: flex; align-items: center; gap: 5px;">
                <input type="checkbox" name="is_correct[]" value="1"> Đáp án đúng
            </label>
            <button type="button" onclick="this.parentElement.remove()" 
                    style="background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Xóa</button>
        `;
    container.appendChild(newDiv);
}
</script>
