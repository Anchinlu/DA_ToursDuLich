document.addEventListener('DOMContentLoaded', function() {
    // Lấy tất cả các nút Like
    const likeButtons = document.querySelectorAll('.btn-like-action');

    likeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Chặn load lại trang

            const postId = this.dataset.id;
            const icon = this.querySelector('i'); // Icon trái tim trong nút bấm
            
            // Tìm thẻ cha lớn nhất (Card) để từ đó tìm ra dòng thống kê (Stats)
            const postCard = this.closest('.post-card');
            const likeInfoSpan = postCard.querySelector('.like-info');

            // Gửi dữ liệu sang PHP xử lý
            const formData = new FormData();
            formData.append('post_id', postId);

            fetch('/DoAn_TourDuLich/process/like_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // 1. CẬP NHẬT TRẠNG THÁI NÚT BẤM (Đỏ/Trắng)
                    if (data.action === 'liked') {
                        // Chuyển thành tim đỏ
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.classList.add('active');
                    } else {
                        // Chuyển thành tim rỗng
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.classList.remove('active');
                    }

                    // 2. CẬP NHẬT CON SỐ LƯỢT THÍCH (Ở dòng thống kê bên trên)
                    // Nếu số like > 0 thì hiện tim đỏ nhỏ + số lượng
                    if (data.likes > 0) {
                        likeInfoSpan.innerHTML = `<i class="fas fa-heart" style="color: #e41e3f; font-size: 12px;"></i> ${data.likes}`;
                    } else {
                        // Nếu 0 like thì ẩn luôn cho gọn
                        likeInfoSpan.innerHTML = '';
                    }

                } else if (data.status === 'error') {
                    alert(data.message); // Báo lỗi (ví dụ chưa đăng nhập)
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});