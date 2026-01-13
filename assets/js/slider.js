document.addEventListener("DOMContentLoaded", function() {
    const slides = document.querySelectorAll(".slide");
    let currentSlide = 0;
    const slideInterval = 5000; // Thời gian đổi ảnh: 5 giây

    function nextSlide() {
        // Tắt slide hiện tại
        slides[currentSlide].classList.remove("active");
        
        // Tính chỉ số slide tiếp theo
        currentSlide = (currentSlide + 1) % slides.length;
        
        // Bật slide tiếp theo
        slides[currentSlide].classList.add("active");
    }

    // Bắt đầu vòng lặp tự động
    setInterval(nextSlide, slideInterval);
});