<?php include '../includes/header.php'; ?>

<style>
    /* 1. HERO SECTION */
    .about-hero {
        background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1528127269322-539801943592?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
        margin-top: -20px; 
    }
    .about-hero h1 { font-size: 3.5rem; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; }
    .about-hero p { font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6; }

    /* 2. STORY SECTION */
    .story-section { padding: 80px 0; }
    .story-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; }
    .story-content h2 { color: #166534; font-size: 2rem; margin-bottom: 20px; font-weight: 700; }
    .story-content p { color: #555; line-height: 1.8; margin-bottom: 20px; font-size: 1.05rem; text-align: justify; }
    .story-img-group { position: relative; height: 500px; }
    .story-img { position: absolute; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); object-fit: cover; }
    .img-1 { width: 70%; height: 60%; top: 0; left: 0; z-index: 1; }
    .img-2 { width: 65%; height: 55%; bottom: 0; right: 0; z-index: 2; border: 5px solid white; }

    /* 3. VALUES SECTION */
    .values-section { background: #f0fdf4; padding: 80px 0; }
    .section-header { text-align: center; margin-bottom: 50px; }
    .section-header h2 { color: #166534; font-size: 2.2rem; font-weight: 700; }
    .section-header p { color: #666; max-width: 600px; margin: 10px auto; }
    
    .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
    .value-card { background: white; padding: 40px 30px; border-radius: 15px; text-align: center; transition: 0.3s; border: 1px solid #dcfce7; }
    .value-card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(22, 101, 52, 0.1); }
    .value-icon { font-size: 40px; color: #166534; margin-bottom: 20px; background: #dcfce7; width: 80px; height: 80px; line-height: 80px; border-radius: 50%; margin: 0 auto 20px; }
    .value-card h3 { font-size: 1.25rem; font-weight: 700; margin-bottom: 15px; color: #333; }
    .value-card p { color: #666; line-height: 1.6; font-size: 0.95rem; }

    /* 4. STATISTICS */
    .stats-section { background: #166534; color: white; padding: 60px 0; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); text-align: center; gap: 30px; }
    .stat-number { font-size: 3rem; font-weight: 800; margin-bottom: 10px; }
    .stat-label { font-size: 1.1rem; opacity: 0.9; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .about-hero h1 { font-size: 2rem; }
        .story-grid { grid-template-columns: 1fr; }
        .story-img-group { height: 400px; margin-top: 30px; }
        .values-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: 1fr 1fr; gap: 40px; }
    }
</style>

<section class="about-hero">
    <div class="container">
        <h1>Hồn Việt - Tour Xanh</h1>
        <p>Kết nối con người với thiên nhiên hùng vĩ và văn hóa ngàn đời của dải đất hình chữ S.</p>
    </div>
</section>

<div class="container story-section">
    <div class="story-grid">
        <div class="story-content">
            <span style="color: #15803d; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">Về chúng tôi</span>
            <h2>Mang Thiên Nhiên & Văn Hóa Việt <br>Đến Gần Bạn Hơn</h2>
            <p>
                Việt Nam không chỉ có những thành phố sầm uất, mà còn ẩn chứa những viên ngọc quý giữa đại ngàn và biển cả. Tại <strong>CHINLIU TRAVEL</strong>, chúng tôi tin rằng du lịch không chỉ là đi để thấy, mà là đi để <strong>cảm nhận và trân trọng</strong>.
            </p>
            <p>
                Chúng tôi chuyên tổ chức các chuyến "Tour Xanh" - nơi bạn được hòa mình vào những cánh rừng nguyên sinh, những thửa ruộng bậc thang vàng óng, hay những làng chài yên bình ven biển. Mỗi hành trình là một cam kết: <strong>Không rác thải nhựa, Tôn trọng văn hóa bản địa, và Hỗ trợ cộng đồng địa phương.</strong>
            </p>
            <a href="index.php" class="btn btn-primary" style="background:#166534; border:none; padding: 12px 30px; border-radius: 30px;">Khám phá ngay</a>
        </div>
        
        <div class="story-img-group">
            <img src="https://images.pexels.com/photos/35067087/pexels-photo-35067087.jpeg?_gl=1*12h9jw*_ga*NDA4NTk5NzU4LjE3NjUzMjg3MTg.*_ga_8JE65Q40S6*czE3NjUzMjg3MTgkbzEkZzEkdDE3NjUzMjg3MjYkajUyJGwwJGgw" class="story-img img-1" alt="Nhà gỗ trên đồi xanh">
            <img src="https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?q=80&w=2000&auto=format&fit=crop" class="story-img img-2" alt="Thiên nhiên Việt Nam">
        </div>
    </div>
</div>

<div class="values-section">
    <div class="container">
        <div class="section-header">
            <h2>Giá Trị Của Những Chuyến Đi</h2>
            <p>Chúng tôi không bán vé du lịch, chúng tôi trao tặng những trải nghiệm sống.</p>
        </div>

        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon"><i class="fas fa-leaf"></i></div>
                <h3>Du Lịch Xanh</h3>
                <p>Ưu tiên các điểm đến sinh thái, giảm thiểu tác động đến môi trường và bảo vệ cảnh quan thiên nhiên nguyên bản.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="fas fa-praying-hands"></i></div>
                <h3>Đậm Đà Bản Sắc</h3>
                <p>Trải nghiệm sâu sắc văn hóa địa phương, từ ẩm thực dân dã đến các lễ hội truyền thống lâu đời.</p>
            </div>
            <div class="value-card">
                <div class="value-icon"><i class="fas fa-heart"></i></div>
                <h3>Cộng Đồng Bền Vững</h3>
                <p>Một phần lợi nhuận từ mỗi chuyến đi được trích lại để hỗ trợ sinh kế cho người dân bản địa tại điểm đến.</p>
            </div>
        </div>
    </div>
</div>

<div class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div>
                <div class="stat-number">50+</div>
                <div class="stat-label">Điểm đến Xanh</div>
            </div>
            <div>
                <div class="stat-number">10k+</div>
                <div class="stat-label">Khách hàng Hạnh phúc</div>
            </div>
            <div>
                <div class="stat-number">100%</div>
                <div class="stat-label">Hướng dẫn viên Bản địa</div>
            </div>
            <div>
                <div class="stat-number">5+</div>
                <div class="stat-label">Năm Kinh nghiệm</div>
            </div>
        </div>
    </div>
</div>

<div style="text-align: center; padding: 80px 0; background: url('https://images.unsplash.com/photo-1464817739973-0128fe77aaa1?q=80&w=2070&auto=format&fit=crop'); background-attachment: fixed; background-size: cover; position: relative;">
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6);"></div>
    <div class="container" style="position: relative; z-index: 2; color: white;">
        <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 20px;">Sẵn sàng cho hành trình trở về Thiên nhiên?</h2>
        <p style="font-size: 1.1rem; margin-bottom: 30px; opacity: 0.9;">Hãy để thiên nhiên chữa lành tâm hồn bạn. Đặt tour ngay hôm nay để nhận ưu đãi đặc biệt.</p>
        <a href="index.php" class="btn btn-primary" style="background: white; color: #166534; border: none; padding: 15px 40px; font-weight: 700; border-radius: 30px;">Xem Các Tour</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>