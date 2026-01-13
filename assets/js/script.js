function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const html = document.documentElement;
    const links = document.querySelectorAll('.transition-link');

    // 1. KHI VỪA VÀO TRANG (ENTER)
    // Thêm class để chạy hiệu ứng màn xanh bay đi
    body.classList.add('is-entering');
    
    // Khóa cuộn tuyệt đối
    body.classList.add('no-scroll');
    html.classList.add('no-scroll');

    // Mở khóa cuộn sau khi hiệu ứng kết thúc (0.9s)
    setTimeout(() => {
        body.classList.remove('no-scroll');
        html.classList.remove('no-scroll');
    }, 900); 


    // 2. KHI RỜI TRANG (EXIT)
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetUrl = this.href;

            // Khóa cuộn ngay lập tức
            body.classList.add('no-scroll');
            html.classList.add('no-scroll');
            
            body.classList.remove('is-entering');
            body.classList.add('is-exiting');

            // Đợi 500ms:
            // - Màn xanh đã che kín màn hình (mất khoảng 0.3s)
            // - Logo bắt đầu hiện ra (sau 0.3s delay)
            // => Chuyển trang lúc này là đẹp nhất
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 500); 
        });
    });
});