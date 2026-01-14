<style>
    .main-footer {
        background-color: #1a1a1a;
        color: #b0b0b0; 
        padding-top: 50px;
        margin-top: auto;
        font-family: system-ui, -apple-system, sans-serif;
    }

    .footer-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        justify-content: space-between;
    }

    .footer-col {
        flex: 1;
        min-width: 250px;
    }

    .footer-col h3 {
        color: #fff;
        font-size: 18px;
        margin-bottom: 20px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .footer-desc {
        line-height: 1.6;
        font-size: 14px;
        margin-bottom: 20px;
    }

    /* Social Icons */
    .social-links {
        display: flex;
        gap: 10px;
    }
    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: #333;
        color: #fff;
        border-radius: 50%;
        text-decoration: none;
        transition: 0.3s;
    }
    .social-links a:hover {
        background: #1877f2; 
        transform: translateY(-3px);
    }

    /* Links List */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-links li {
        margin-bottom: 12px;
    }
    .footer-links a {
        color: #b0b0b0;
        text-decoration: none;
        transition: 0.2s;
        font-size: 14px;
    }
    .footer-links a:hover {
        color: #fff;
        padding-left: 5px; 
    }

    /* Contact Info */
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 15px;
        font-size: 14px;
    }
    .contact-item i {
        color: #1877f2; 
        margin-top: 4px;
    }

    /* Footer Bottom */
    .footer-bottom {
        background-color: #111;
        padding: 20px 0;
        margin-top: 50px;
        text-align: center;
        font-size: 13px;
        border-top: 1px solid #333;
    }
</style>

<footer class="main-footer">
    <div class="footer-container">
        
        <div class="footer-col">
            <h3>Về ChinLiu Tour</h3>
            <p class="footer-desc">
                Chúng tôi cam kết mang đến những trải nghiệm du lịch tuyệt vời nhất. 
                Khám phá vẻ đẹp Việt Nam và thế giới cùng đội ngũ chuyên nghiệp, tận tâm.
            </p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

        <div class="footer-col">
            <h3>Liên kết nhanh</h3>
            <ul class="footer-links">
                <li><a href="/DoAn_TourDuLich/index.php">Trang chủ</a></li>
                <li><a href="/DoAn_TourDuLich/pages/about.php">Giới thiệu</a></li>
                <li><a href="/DoAn_TourDuLich/pages/tours.php">Danh sách Tour</a></li>
                <li><a href="/DoAn_TourDuLich/pages/community.php">Cộng đồng vi vu</a></li>
                <li><a href="/DoAn_TourDuLich/pages/contact.php">Liên hệ</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Liên hệ</h3>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>123 Đường Nguyễn Thiện Thành , Trà Vinh</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone-alt"></i>
                <span>Hotline: 1900 1234</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>contact@chinliu.com</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-clock"></i>
                <span>Giờ làm việc: 8:00 - 17:30 (T2 - T7)</span>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        &copy; <?php echo date("Y"); ?> <strong>ChinLiu Tour</strong>. All Rights Reserved. Designed by nhom da22tta.
    </div>
</footer>