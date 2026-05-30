</main>
</div>
<footer style="text-align: center; padding: 15px; color: #888; font-size: 0.8rem; background: #fff; border-top: 1px solid #ddd;">
    &copy; 2026 PT QUIZ - Hệ thống trắc nghiệm an toàn API
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Dọn sạch Bootstrap modal backdrop bị kẹt khi điều hướng trang
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
});
</script>

</body>
</html>
