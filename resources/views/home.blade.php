<div class="content container-fluid" style="padding: 0; margin: 0;">
<x-guest-layout>

<style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url('{{ asset('assets/img/background-kict.jpg') }}') no-repeat center center / cover;
        background-attachment: fixed;
        overflow-x: hidden;
    }

    .hero {
        text-align: center;
        padding: 70px 20px 40px;
        color: #fff;
        background: rgba(0, 0, 0, 0.45);
    }

    .hero img {
        width: 80px;
        margin-bottom: 20px;
    }

    .hero h1 {
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .hero p {
        font-size: 1.2rem;
        max-width: 640px;
        margin: 0 auto 25px auto;
        color: #e6e6e6;
    }

    .login-btn {
        margin-top: 20px;
        padding: 14px 40px;
        border-radius: 25px;
        background: #345bb2;
        color: #fff;
        font-size: 1.15rem;
        font-weight: 600;
        border: none;
        box-shadow: 0 2px 8px #bbc3e8;
        transition: background 0.2s;
        cursor: pointer;
        text-decoration: none;
    }

    .login-btn:hover {
        background: #25407c;
    }

    .center-portal {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 40px 20px;
    }

    .steps, .features {
        display: flex;
        gap: 32px;
        justify-content: center;
        flex-wrap: wrap;
        max-width: 1100px;
        margin-bottom: 40px;
    }

    .step-card, .feature-card {
        background: #ffffffd9;
        border-radius: 18px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 28px 20px;
        text-align: center;
        transition: transform 0.2s ease;
    }

    .step-card:hover, .feature-card:hover {
        transform: translateY(-4px);
    }

    .step-card { width: 220px; }
    .feature-card { width: 280px; }

    .step-icon, .feature-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .step-icon { color: #3677dd; }
    .feature-icon { color: #5ca769; }

    .section-title {
        font-size: 1.75rem;
        font-weight: bold;
        color: #fff;
        margin-bottom: 20px;
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
    }

    .system-images {
    display: flex;
    flex-wrap: wrap;
    gap: 32px;
    justify-content: center;
    margin-top: 20px;
    }

    .image-container {
    position: relative;
    width: 100%;
    max-width: 520px;
    overflow: hidden;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
    }

    .image-container img {
    width: 100%;
    display: block;
    border-radius: 14px;
    transition: transform 0.3s ease;
    }

    .image-container:hover img {
        transform: scale(1.02);
    }

    .hover-text {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 16px;
        background: rgba(0, 0, 0, 0.65);
        color: #fff;
        font-size: 1rem;
        text-align: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .image-container:hover .hover-text {
        opacity: 1;
    }

    .image-card {
        position: relative;
        max-width: 500px;
        width: 100%;
    }

    .image-card img {
        width: 100%;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.2);
        transition: transform 0.2s ease;
    }

    .image-card:hover img {
        transform: scale(1.02);
    }

    .image-caption {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.55);
        color: #fff;
        padding: 12px;
        font-weight: 500;
        font-size: 0.95rem;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
        text-align: center;
    }

     :root {
        --content-max-width: 1200px;
    }

    /* Update your video wrapper styles */
    .videos-wrapper {
        display: flex;
        justify-content: center;
        gap: 30px;
        width: 100%;
        max-width: var(--content-max-width);
        margin: 40px auto;
        padding: 0 20px;
    }

    .video-section {
        flex: 1;
        min-width: 0;
        /* background: rgba(255, 255, 255, 0.9); */
        border-radius: 12px;
        padding: 20px;
        /* box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); */
    }

    .video-section .section-title {
        color: white;
        font-size: 1.5rem;
        margin-bottom: 20px;
        text-shadow: none;
    }

    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        border-radius: 8px;
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    @media (max-width: 768px) {
        .videos-wrapper {
            flex-direction: column;
            gap: 40px;
        }

        .video-section {
            width: 100%;
        }
    }

    .portal-footer {
        text-align: center;
        margin-top: 50px;
        font-size: 0.95em;
        color: #f1f1f1;
        padding: 30px 20px;
        background: rgba(0, 0, 0, 0.5);
    }
</style>

<div class="hero">
    {{-- <img src="{{ asset('assets/img/logokict.png') }}" alt="MYKICT Logo" /> --}}
    <div style="display:flex; justify-content:center; align-items:center; margin-bottom:25px;">
        <img src="{{ asset('assets/img/logokict2.png') }}" alt="MYKICT" width="80" />
    </div>
    <h1>Plan Your Study, Shape Your Future</h1>
    <p>MYKICT: Smart Study Planner helps IIUM students select courses, track their academic path, and optimize graduation timelines. Less confusion, more confidence—your study journey made simple.</p>
    <a href="{{ route('login') }}" class="login-btn">Login to MYKICT</a>
</div>

<div class="center-portal">

    <h2 class="section-title">How It Works</h2>
    <div class="steps">
        <div class="step-card">
            <div class="step-icon"><i class="fas fa-lightbulb"></i></div>
            <h5>Plan</h5>
            <small>Auto-suggests the best courses based on your program & CGPA</small>
        </div>
        <div class="step-card">
            <div class="step-icon"><i class="fas fa-user-cog"></i></div>
            <h5>Personalize</h5>
            <small>Customize your study plan to fit your pace and specialization</small>
        </div>
        <div class="step-card">
            <div class="step-icon"><i class="fas fa-save"></i></div>
            <h5>Save</h5>
            <small>One-click to save your selected plan for the semester</small>
        </div>
        <div class="step-card">
            <div class="step-icon"><i class="fas fa-chart-line"></i></div>
            <h5>Track</h5>
            <small>Visualize your academic progress & stay on target for graduation</small>
        </div>
    </div>

    <h2 class="section-title">Key Features</h2>
    <div class="features">
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
            <strong>Automated Course Suggestions</strong>
            <p style="margin-top: 10px;">Intelligent planner suggests courses that fit your year, specialization, and CGPA status.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-users-cog"></i></div>
            <strong>Personalized Study Experience</strong>
            <p style="margin-top: 10px;">Manage elective preferences, visualize study paths, and avoid timetable clashes.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
            <strong>Progress Monitoring & Prediction</strong>
            <p style="margin-top: 10px;">Track credit hours, see your progress, and predict your graduation date.</p>
        </div>
    </div>

    <h2 class="section-title">System Interface Previews</h2>
    <div class="system-images">
    <div class="image-container">
        <img src="{{ asset('assets/img/student-dashboard-up.png') }}" alt="System Screenshot 1">
        <div class="hover-text">Student Dashboard: View your semester plan</div>
    </div>
    <div class="image-container">
        <img src="{{ asset('assets/img/student-dashboard-bottom.png') }}" alt="System Screenshot 2">
        <div class="hover-text">Course Selection: Choose based on CGPA and progress</div>
    </div>
    <div class="image-container">
        <img src="{{ asset('assets/img/admin-dashboard.jpg') }}" alt="System Screenshot 3">
        <div class="hover-text">Admin Dashboard: Monitor student academic status</div>
    </div>
    </div>

    <div class="videos-wrapper">
    <div class="video-section">
        <h2 class="section-title">Demo Student View</h2>
        <div class="video-container">
            <iframe src="https://www.youtube.com/embed/dsr-OpyxxOI" allowfullscreen></iframe>
        </div>
    </div>
    <div class="video-section">
        <h2 class="section-title">Demo Admin View</h2>
        <div class="video-container">
            <iframe src="https://www.youtube.com/embed/sY6yN9aGF0U" allowfullscreen></iframe>
        </div>
    </div>
</div>

</div>

<div class="portal-footer">
    <span>
        <strong>Project by:</strong> Nur Fatihah Adawiyah binti Rusdi | Nur Ain binti Lizam &bull; FYP 2 BIT &bull; KICT IIUM<br>
        <strong>Powered by:</strong> VSCode | XAMPP | phpMyAdmin | Laravel | PHP | MySQL | Bootstrap<br>
        <strong>Supervised by:</strong> Asst. Prof. Dr. Mohd Khairul Azmi bin Hassan
    </span>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</x-guest-layout>
</div>
