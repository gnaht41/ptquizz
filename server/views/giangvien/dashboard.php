<?php 
$stats = $data['stats']; 
$chartData = $data['chartData'];
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="card border-0 shadow-sm" style="border-radius:20px;overflow:hidden;">
    <div style="padding:40px 30px;background:linear-gradient(135deg,#eff6ff 0%,#f8fafc 100%);border-bottom:1px solid #e2e8f0;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
            <div>
                <h2 style="margin:0;color:#0f172a;font-size:28px;font-weight:800;">Chào mừng, <?= $_SESSION['user']['ten'] ?>!</h2>
                <p style="margin:8px 0 0;color:#64748b;font-size:16px;">Theo dõi hiệu suất giảng dạy và kết quả thi của sinh viên.</p>
            </div>
            <div style="width:64px;height:64px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:flex;align-items:center;justify-content:center;font-size:28px;box-shadow: 0 4px 12px rgba(29, 78, 216, 0.15);">
                <i class="fas fa-chalkboard-user"></i>
            </div>
        </div>
    </div>

    <div class="card-body" style="padding:30px;">
        <!-- STATS CARDS -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div style="background:#fff; padding:24px; border-radius:18px; border:1px solid #f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.03); display:flex; align-items:center; gap:16px;">
                    <div style="width:52px; height:52px; border-radius:14px; background:#eff6ff; color:#3b82f6; display:flex; align-items:center; justify-content:center; font-size:22px;">
                        <i class="fas fa-book"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; color:#64748b; font-weight:600;">Môn học</div>
                        <div style="font-size:24px; font-weight:800; color:#1e293b;"><?= $stats['subjects'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background:#fff; padding:24px; border-radius:18px; border:1px solid #f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.03); display:flex; align-items:center; gap:16px;">
                    <div style="width:52px; height:52px; border-radius:14px; background:#ecfeff; color:#0891b2; display:flex; align-items:center; justify-content:center; font-size:22px;">
                        <i class="fas fa-copy"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; color:#64748b; font-weight:600;">Bài thi</div>
                        <div style="font-size:24px; font-weight:800; color:#1e293b;"><?= $stats['exams'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background:#fff; padding:24px; border-radius:18px; border:1px solid #f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.03); display:flex; align-items:center; gap:16px;">
                    <div style="width:52px; height:52px; border-radius:14px; background:#fff7ed; color:#f59e0b; display:flex; align-items:center; justify-content:center; font-size:22px;">
                        <i class="fas fa-circle-question"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; color:#64748b; font-weight:600;">Câu hỏi</div>
                        <div style="font-size:24px; font-weight:800; color:#1e293b;"><?= $stats['questions'] ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div style="background:#fff; padding:24px; border-radius:18px; border:1px solid #f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.03); display:flex; align-items:center; gap:16px;">
                    <div style="width:52px; height:52px; border-radius:14px; background:#f0fdf4; color:#10b981; display:flex; align-items:center; justify-content:center; font-size:22px;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <div style="font-size:14px; color:#64748b; font-weight:600;">Lượt thi</div>
                        <div style="font-size:24px; font-weight:800; color:#1e293b;"><?= $stats['attempts'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <!-- CHART -->
            <div class="col-md-8">
                <div style="background:#fff; padding:25px; border-radius:20px; border:1px solid #f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.03); height:100%;">
                    <h4 style="margin:0 0 20px; color:#1e293b; font-weight:700; font-size:18px;">Lượt thi theo môn học</h4>
                    <div style="height:300px; position:relative;">
                        <canvas id="submissionsChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- QUICK LINKS -->
            <div class="col-md-4">
                <div style="background:#fff; padding:25px; border-radius:20px; border:1px solid #f1f5f9; box-shadow:0 4px 12px rgba(0,0,0,0.03); height:100%;">
                    <h4 style="margin:0 0 20px; color:#1e293b; font-weight:700; font-size:18px;">Truy cập nhanh</h4>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <a href="index.php?act=quanly-monhoc" style="display:flex; align-items:center; gap:12px; padding:15px; border-radius:14px; background:#f8fafc; text-decoration:none; transition: all 0.2s;" onmouseover="this.style.background='#eff6ff'; this.style.transform='translateX(5px)'" onmouseout="this.style.background='#f8fafc'; this.style.transform='translateX(0)'">
                            <div style="width:36px; height:36px; border-radius:10px; background:#3b82f6; color:white; display:flex; align-items:center; justify-content:center;"><i class="fas fa-book"></i></div>
                            <span style="color:#1e293b; font-weight:600; font-size:14px;">Môn học</span>
                            <i class="fas fa-chevron-right" style="margin-left:auto; color:#94a3b8; font-size:12px;"></i>
                        </a>
                        <a href="index.php?act=quanly-dethi" style="display:flex; align-items:center; gap:12px; padding:15px; border-radius:14px; background:#f8fafc; text-decoration:none; transition: all 0.2s;" onmouseover="this.style.background='#ecfeff'; this.style.transform='translateX(5px)'" onmouseout="this.style.background='#f8fafc'; this.style.transform='translateX(0)'">
                            <div style="width:36px; height:36px; border-radius:10px; background:#0891b2; color:white; display:flex; align-items:center; justify-content:center;"><i class="fas fa-copy"></i></div>
                            <span style="color:#1e293b; font-weight:600; font-size:14px;">Bài thi & Đề thi</span>
                            <i class="fas fa-chevron-right" style="margin-left:auto; color:#94a3b8; font-size:12px;"></i>
                        </a>
                        <a href="index.php?act=ketqua-thi" style="display:flex; align-items:center; gap:12px; padding:15px; border-radius:14px; background:#f8fafc; text-decoration:none; transition: all 0.2s;" onmouseover="this.style.background='#f0fdf4'; this.style.transform='translateX(5px)'" onmouseout="this.style.background='#f8fafc'; this.style.transform='translateX(0)'">
                            <div style="width:36px; height:36px; border-radius:10px; background:#10b981; color:white; display:flex; align-items:center; justify-content:center;"><i class="fas fa-chart-line"></i></div>
                            <span style="color:#1e293b; font-weight:600; font-size:14px;">Kết quả thi</span>
                            <i class="fas fa-chevron-right" style="margin-left:auto; color:#94a3b8; font-size:12px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('submissionsChart').getContext('2d');
    const chartData = <?= json_encode($chartData) ?>;
    
    const labels = chartData.map(item => item.tenmonhoc);
    const scores = chartData.map(item => item.so_luot_lam);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sô lượt làm bài',
                data: scores,
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: 'rgba(59, 130, 246, 0.4)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, stepSize: 1 },
                    grid: { borderDash: [5, 5], color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
