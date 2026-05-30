<?php
$info = $data['info'];
$questions = $data['questions'];
?>
<div class="card" style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #dee2e6; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
        <div>
            <h2 style="margin: 0; color: #333; font-size: 22px;">Chi tiết bài làm</h2>
            <div style="margin-top: 8px; color: #475569; font-size: 15px;">
                Thí sinh: <strong style="color: #1e293b;"><?= htmlspecialchars($info['ten']) ?></strong>
            </div>
            <div style="margin-top: 4px; color: #64748b; font-size: 14px;">
                Bài thi: <?= htmlspecialchars($info['ten_baithi']) ?>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 28px; font-weight: 800; color: #1d4ed8; line-height: 1;"><?= $info['diem'] ?>/10</div>
            <div style="font-size: 13px; color: #64748b; margin-top: 5px;">Đúng <?= $info['socaudung'] ?>/<?= $info['tongcauhoi'] ?> câu</div>
            <a href="index.php?act=ketqua-thi&id_baithi=<?= $info['id_baithi'] ?>" 
               style="display: inline-block; margin-top: 15px; background:#f1f5f9; color:#475569; padding:6px 12px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:600; border:1px solid #e2e8f0;">
               <i class="fas fa-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php foreach ($questions as $index => $q): ?>
            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; background: #fff;">
                <div style="display: flex; gap: 12px; margin-bottom: 15px;">
                    <div style="background: #f1f5f9; color: #475569; width: 30px; height: 30px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0;">
                        <?= $index + 1 ?>
                    </div>
                    <div style="font-weight: 600; color: #1e293b; line-height: 1.5; font-size: 15px;">
                        <?= htmlspecialchars($q['noidungcauhoi']) ?>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px; padding-left: 42px;">
                    <?php if ((int)$q['loai_cauhoi'] == 2): // Điền từ ?>
                        <div style="font-size: 14px; margin-bottom: 5px;">
                            <span style="color:#64748b;">Sinh viên điền:</span> 
                            <strong style="color: #1e293b;"><?= htmlspecialchars($q['noidung_thisinh'] ?: '(Bỏ trống)') ?></strong>
                        </div>
                        <div style="font-size: 14px; background: #f0fdf4; border: 1px solid #dcfce7; color: #15803d; padding: 10px; border-radius: 8px;">
                            <span style="font-weight: 600;">Đáp án đúng:</span> <?= htmlspecialchars($q['options'][0]['noidungdapan']) ?>
                        </div>
                    <?php else: // Trắc nghiệm ?>
                        <?php foreach ($q['options'] as $opt): 
                            $isChosen = ($q['cautraloichon'] == $opt['id_dapan']);
                            $isCorrect = ($opt['dapandung'] == 1);
                            
                            $bg = 'transparent';
                            $border = '#f1f5f9';
                            $icon = null;
                            
                            if ($isChosen && $isCorrect) {
                                $bg = '#f0fdf4'; $border = '#dcfce7'; $icon = '<i class="fas fa-check-circle" style="color: #15803d;"></i>';
                            } elseif ($isChosen && !$isCorrect) {
                                $bg = '#fef2f2'; $border = '#fee2e2'; $icon = '<i class="fas fa-times-circle" style="color: #b91c1c;"></i>';
                            } elseif ($isCorrect) {
                                $bg = '#f0fdf4'; $border = '#dcfce7'; $icon = '<i class="fas fa-check" style="color: #15803d;"></i>';
                            }
                        ?>
                            <div style="display: flex; align-items: center; gap: 10px; padding: 10px 15px; border-radius: 8px; border: 1px solid <?= $border ?>; background: <?= $bg ?>; position: relative;">
                                <div style="flex-shrink: 0; width: 16px;"><?= $icon ?></div>
                                <div style="font-size: 14px; color: <?= $isChosen ? '#1e293b' : '#64748b' ?>; font-weight: <?= $isChosen ? '600' : '400' ?>;">
                                    <?= htmlspecialchars($opt['noidungdapan']) ?>
                                </div>
                                <?php if ($isChosen): ?>
                                    <span style="margin-left: auto; font-size: 11px; text-transform: uppercase; font-weight: 700; color: #94a3b8;">Lựa chọn của SV</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
