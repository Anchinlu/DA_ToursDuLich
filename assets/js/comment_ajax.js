document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Xử lý nút Bật/Tắt khung bình luận
    const toggleButtons = document.querySelectorAll('.btn-toggle-comment');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const postCard = this.closest('.post-card');
            const commentSection = postCard.querySelector('.comment-section');
            
            // Toggle hiển thị
            if (commentSection.style.display === 'none' || commentSection.style.display === '') {
                commentSection.style.display = 'block';
                // Focus vào ô input ngay
                postCard.querySelector('.comment-input').focus();
            } else {
                commentSection.style.display = 'none';
            }
        });
    });

    // 2. Xử lý Gửi bình luận
    const sendButtons = document.querySelectorAll('.btn-send-comment');
    sendButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const postCard = this.closest('.post-card');
            const input = postCard.querySelector('.comment-input');
            const list = postCard.querySelector('.comment-list');
            const commentCountSpan = postCard.querySelector('.comment-count-display'); // Chỗ hiển thị số lượng ở trên
            
            const postId = this.dataset.id;
            const content = input.value.trim();

            if (!content) return; // Rỗng thì không gửi

            // Hiệu ứng đang gửi (có thể thêm spinner nếu thích)
            input.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('content', content);

            fetch('/DoAn_TourDuLich/process/comment_process.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                input.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane"></i>';
                input.focus();

                if (data.status === 'success') {
                    input.value = ''; // Xóa trắng ô nhập

                    // Tạo HTML bình luận mới
                    const newCommentHtml = `
                        <div class="comment-item" style="display:flex; gap:10px; margin-bottom:10px; animation: fadeIn 0.5s;">
                            <img src="${data.data.avatar}" class="comment-avatar" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                            <div class="comment-bubble" style="background:#f0f2f5; padding:8px 12px; border-radius:15px; width:fit-content;">
                                <div style="font-weight:bold; font-size:13px; color:#050505;">${data.data.name}</div>
                                <div style="font-size:14px; color:#050505;">${data.data.content}</div>
                                <div style="font-size:11px; color:#65676b; margin-top:2px;">${data.data.time}</div>
                            </div>
                        </div>
                    `;

                    // Chèn lên đầu danh sách (sau input box hoặc đầu list cũ)
                    // Ở đây mình chèn vào đầu danh sách comment cũ
                    list.insertAdjacentHTML('afterbegin', newCommentHtml);

                    // Cập nhật số lượng comment (nếu có thẻ hiển thị)
                    if(commentCountSpan) {
                         commentCountSpan.textContent = data.total_comments + ' bình luận';
                    }

                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                input.disabled = false;
                this.innerHTML = '<i class="fas fa-paper-plane"></i>';
            });
        });
    });

    // Cho phép ấn Enter để gửi luôn
    const inputs = document.querySelectorAll('.comment-input');
    inputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const btn = this.nextElementSibling; // Tìm nút gửi bên cạnh
                btn.click();
            }
        });
    });
});