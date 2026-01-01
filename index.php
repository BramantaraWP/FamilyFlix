<?php
// ============================================
// FAMILYFLIX - HOME PAGE (MOBILE RESPONSIVE)
// ============================================

session_start();
error_reporting(0);

// Include config
require_once 'config.php';

// Helper functions
function getFilms($limit = 100) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM films ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    $films = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $films[] = $row;
    }
    return $films;
}

function getFilm($id) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM films WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC);
}

$film_id = $_GET['film'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>FamilyFlix - Film Keluarga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ====== ORANGE THEME ====== */
        :root {
            --primary: #FF6B35;
            --primary-dark: #E55A2B;
            --primary-light: #FF8B5C;
            --accent: #FFD166;
            --dark: #0F1419;
            --darker: #0A0E13;
            --card-bg: #1A1F26;
            --gray: #8A8F98;
            --light: #FFFFFF;
            --success: #20C997;
            --warning: #FFC107;
        }
        
        /* ====== MOBILE FIRST BASE ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        html {
            font-size: 14px;
        }
        
        body {
            background: var(--darker);
            color: var(--light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            line-height: 1.5;
            overflow-x: hidden;
        }
        
        /* ====== UTILITY CLASSES ====== */
        .container {
            width: 100%;
            padding: 0 16px;
            margin: 0 auto;
        }
        
        .mobile-hidden {
            display: none;
        }
        
        .desktop-hidden {
            display: block;
        }
        
        /* ====== MOBILE NAVBAR ====== */
        .mobile-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(26, 31, 38, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 107, 53, 0.3);
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            z-index: 1000;
        }
        
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--gray);
            text-decoration: none;
            font-size: 10px;
            transition: all 0.3s;
        }
        
        .nav-item i {
            font-size: 20px;
            margin-bottom: 4px;
        }
        
        .nav-item.active {
            color: var(--primary);
        }
        
        /* ====== TOP BAR ====== */
        .top-bar {
            position: sticky;
            top: 0;
            background: rgba(15, 20, 25, 0.95);
            backdrop-filter: blur(20px);
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
            border-bottom: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            color: var(--primary);
            font-size: 24px;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .mobile-menu-btn {
            background: none;
            border: none;
            color: var(--light);
            font-size: 20px;
            padding: 8px;
        }
        
        /* ====== HERO SECTION ====== */
        .hero-section {
            padding: 20px 0 40px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(26, 31, 38, 0.8));
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            padding: 0 16px;
        }
        
        .hero-title {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            background: linear-gradient(to right, var(--light), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-subtitle {
            font-size: 16px;
            color: var(--gray);
            margin-bottom: 24px;
            max-width: 500px;
        }
        
        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        /* ====== BUTTONS ====== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            border: none;
            transition: all 0.3s;
            cursor: pointer;
            gap: 8px;
            min-height: 48px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            backdrop-filter: blur(10px);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .btn-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .btn-icon:hover {
            background: var(--primary);
            transform: scale(1.1);
        }
        
        /* ====== FILM GRID ====== */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 32px 0 20px;
            padding: 0 4px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
        }
        
        .see-all {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .films-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }
        
        .film-card {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }
        
        .film-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }
        
        .film-thumb {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }
        
        .film-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: flex-end;
            padding: 16px;
        }
        
        .film-card:hover .film-overlay {
            opacity: 1;
        }
        
        .film-info {
            padding: 16px;
        }
        
        .film-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .film-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--gray);
        }
        
        /* ====== CONTINUE WATCHING ====== */
        .continue-scroll {
            display: flex;
            overflow-x: auto;
            gap: 16px;
            padding: 8px 4px 24px;
            margin: 0 -16px;
            padding-left: 16px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .continue-scroll::-webkit-scrollbar {
            display: none;
        }
        
        .continue-card {
            flex: 0 0 280px;
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
        }
        
        .continue-thumb {
            width: 100px;
            height: 140px;
            object-fit: cover;
        }
        
        .continue-info {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .progress-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin: 12px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary);
            width: 65%;
            border-radius: 2px;
        }
        
        /* ====== PLAYER PAGE ====== */
        .player-page {
            padding-top: 80px;
        }
        
        .video-wrapper {
            position: relative;
            padding-top: 56.25%; /* 16:9 */
            background: #000;
        }
        
        .video-wrapper video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 0;
        }
        
        .player-controls {
            padding: 16px;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .control-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            padding: 8px;
            min-width: 44px;
            min-height: 44px;
        }
        
        .progress-container {
            flex: 1;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            margin: 0 12px;
            position: relative;
            cursor: pointer;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 2px;
            width: 0%;
        }
        
        .video-info {
            padding: 24px 16px;
        }
        
        .video-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .video-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 24px;
            color: var(--gray);
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .sidebar {
            padding: 16px;
            background: var(--card-bg);
            margin: 16px;
            border-radius: 12px;
        }
        
        .sidebar-list {
            display: flex;
            overflow-x: auto;
            gap: 12px;
            padding: 8px 0 16px;
        }
        
        .sidebar-item {
            flex: 0 0 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        
        .sidebar-item:hover {
            transform: scale(1.05);
        }
        
        /* ====== SEARCH OVERLAY ====== */
        .search-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--darker);
            z-index: 1001;
            display: none;
            padding: 80px 16px 16px;
        }
        
        .search-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .search-input {
            flex: 1;
            padding: 16px;
            background: var(--card-bg);
            border: 2px solid rgba(255, 107, 53, 0.3);
            border-radius: 12px;
            color: var(--light);
            font-size: 16px;
        }
        
        /* ====== FOOTER ====== */
        .footer {
            padding: 40px 16px;
            background: var(--dark);
            border-top: 1px solid rgba(255, 107, 53, 0.2);
        }
        
        .footer-links {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .footer-section h4 {
            font-size: 16px;
            margin-bottom: 12px;
            color: var(--light);
        }
        
        .footer-section a {
            display: block;
            color: var(--gray);
            text-decoration: none;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .copyright {
            text-align: center;
            color: var(--gray);
            font-size: 13px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* ====== TABLET (768px) ====== */
        @media (min-width: 768px) {
            html {
                font-size: 16px;
            }
            
            .container {
                max-width: 720px;
                padding: 0 24px;
            }
            
            .mobile-nav {
                display: none;
            }
            
            .desktop-hidden {
                display: none;
            }
            
            .mobile-hidden {
                display: flex;
            }
            
            .top-bar {
                padding: 20px 32px;
            }
            
            .nav-links {
                display: flex;
                gap: 32px;
                margin-left: 48px;
            }
            
            .nav-link {
                color: var(--gray);
                text-decoration: none;
                font-weight: 500;
                transition: color 0.3s;
            }
            
            .nav-link:hover,
            .nav-link.active {
                color: var(--primary);
            }
            
            .films-grid {
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 24px;
            }
            
            .film-thumb {
                height: 260px;
            }
            
            .hero-title {
                font-size: 48px;
            }
            
            .player-container {
                display: grid;
                grid-template-columns: 1fr 300px;
                gap: 32px;
                padding: 32px;
            }
            
            .video-wrapper {
                border-radius: 12px;
                overflow: hidden;
            }
            
            .sidebar {
                margin: 0;
                height: fit-content;
            }
            
            .sidebar-list {
                flex-direction: column;
                overflow-x: visible;
            }
            
            .sidebar-item {
                flex: 1;
            }
        }
        
        /* ====== DESKTOP (1024px) ====== */
        @media (min-width: 1024px) {
            .container {
                max-width: 1200px;
            }
            
            .hero-section {
                padding: 60px 0 80px;
            }
            
            .hero-title {
                font-size: 56px;
            }
            
            .films-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 32px;
            }
            
            .film-thumb {
                height: 300px;
            }
        }
        
        /* ====== LOADING ANIMATION ====== */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 107, 53, 0.3);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* ====== TOAST NOTIFICATION ====== */
        .toast {
            position: fixed;
            bottom: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 1002;
            display: none;
            box-shadow: 0 8px 24px rgba(255, 107, 53, 0.3);
        }
    </style>
</head>
<body>
    <?php if($film_id > 0): ?>
        <!-- PLAYER PAGE -->
        <?php $film = getFilm($film_id); ?>
        <?php if($film): ?>
            <!-- Top Bar for Player -->
            <div class="top-bar">
                <a href="?" class="logo" style="text-decoration: none;">
                    <i class="fas fa-film logo-icon"></i>
                    <span class="logo-text">FamilyFlix</span>
                </a>
                <button class="mobile-menu-btn" onclick="history.back()">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
            
            <div class="player-page">
                <div class="container">
                    <div class="video-wrapper">
                        <video id="mainVideo" controls crossorigin="anonymous" playsinline>
                            <source src="<?= htmlspecialchars($film['video_path']) ?>" type="video/mp4">
                            Browser tidak mendukung video.
                        </video>
                    </div>
                    
                    <div class="player-controls">
                        <button class="control-btn" onclick="togglePlay()">
                            <i class="fas fa-play" id="playBtn"></i>
                        </button>
                        <button class="control-btn" onclick="skip(-10)">
                            <i class="fas fa-backward"></i>
                        </button>
                        <button class="control-btn" onclick="skip(10)">
                            <i class="fas fa-forward"></i>
                        </button>
                        <div class="progress-container" onclick="seek(event)">
                            <div class="progress-fill" id="progressBar"></div>
                        </div>
                        <span id="timeDisplay" style="color: white; font-size: 14px; min-width: 100px;">00:00 / 00:00</span>
                        <button class="control-btn" onclick="toggleMute()">
                            <i class="fas fa-volume-up" id="volumeBtn"></i>
                        </button>
                        <button class="control-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i>
                        </button>
                        <button class="control-btn" onclick="downloadVideo()" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                    
                    <div class="video-info">
                        <h1 class="video-title"><?= htmlspecialchars($film['title']) ?></h1>
                        
                        <div class="video-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar" style="color: var(--primary);"></i>
                                <span><?= date('Y', strtotime($film['created_at'])) ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-clock" style="color: var(--primary);"></i>
                                <span>2h 15m</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-star" style="color: var(--accent);"></i>
                                <span>8.5/10</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-closed-captioning" style="color: var(--primary);"></i>
                                <span>Subtitle ID/EN</span>
                            </div>
                        </div>
                        
                        <div class="hero-actions">
                            <button class="btn btn-primary" onclick="playVideo()">
                                <i class="fas fa-play"></i> Putar Sekarang
                            </button>
                            <button class="btn btn-secondary" onclick="addToList()">
                                <i class="fas fa-plus"></i> Tambah ke Daftar
                            </button>
                        </div>
                        
                        <p style="color: var(--gray); line-height: 1.6; margin-top: 24px;">
                            <?= nl2br(htmlspecialchars($film['description'] ?? 'Tidak ada deskripsi')) ?>
                        </p>
                    </div>
                    
                    <div class="sidebar">
                        <h3 style="font-size: 20px; margin-bottom: 20px; color: var(--light);">
                            <i class="fas fa-fire" style="color: var(--primary); margin-right: 10px;"></i>
                            Film Lainnya
                        </h3>
                        
                        <div class="sidebar-list">
                            <?php $allFilms = getFilms(6); ?>
                            <?php foreach($allFilms as $f): ?>
                                <?php if($f['id'] != $film_id): ?>
                                    <a href="?film=<?= $f['id'] ?>" class="sidebar-item">
                                        <img src="<?= htmlspecialchars($f['thumbnail_path']) ?>" 
                                             alt="<?= htmlspecialchars($f['title']) ?>"
                                             style="width: 100%; height: 120px; object-fit: cover;">
                                        <div style="padding: 12px;">
                                            <div style="font-weight: 600; margin-bottom: 4px; font-size: 14px;">
                                                <?= htmlspecialchars($f['title']) ?>
                                            </div>
                                            <div style="font-size: 12px; color: var(--gray);">
                                                <?= date('Y', strtotime($f['created_at'])) ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Nav for Player -->
            <nav class="mobile-nav">
                <a href="?" class="nav-item">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="?page=films" class="nav-item">
                    <i class="fas fa-film"></i>
                    <span>Film</span>
                </a>
                <a href="?page=mylist" class="nav-item">
                    <i class="fas fa-bookmark"></i>
                    <span>Daftar</span>
                </a>
                <a href="admin.php" class="nav-item">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
            </nav>
            
        <?php else: ?>
            <!-- Film not found -->
            <div class="top-bar">
                <a href="?" class="logo" style="text-decoration: none;">
                    <i class="fas fa-film logo-icon"></i>
                    <span class="logo-text">FamilyFlix</span>
                </a>
            </div>
            
            <div class="container" style="padding-top: 100px; text-align: center;">
                <i class="fas fa-exclamation-triangle" style="font-size: 64px; color: var(--primary); margin-bottom: 24px;"></i>
                <h2 style="margin-bottom: 16px;">Film Tidak Ditemukan</h2>
                <p style="color: var(--gray); margin-bottom: 32px;">Film yang Anda cari tidak tersedia.</p>
                <a href="?" class="btn btn-primary">
                    <i class="fas fa-home"></i> Kembali ke Home
                </a>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- HOME PAGE -->
        <!-- Top Bar -->
        <div class="top-bar">
            <a href="?" class="logo" style="text-decoration: none;">
                <i class="fas fa-film logo-icon"></i>
                <span class="logo-text">FamilyFlix</span>
            </a>
            
            <div class="mobile-hidden" style="display: flex; align-items: center; gap: 20px;">
                <div class="nav-links">
                    <a href="?" class="nav-link active">Home</a>
                    <a href="?page=films" class="nav-link">Film</a>
                    <a href="?page=mylist" class="nav-link">Daftar Saya</a>
                    <a href="?page=trending" class="nav-link">Trending</a>
                </div>
                <button class="btn-icon" onclick="openSearch()" title="Search">
                    <i class="fas fa-search"></i>
                </button>
                <a href="admin.php" class="btn-secondary" style="padding: 10px 20px; text-decoration: none;">
                    <i class="fas fa-user-shield"></i> Admin
                </a>
            </div>
            
            <button class="mobile-menu-btn desktop-hidden" onclick="openSearch()">
                <i class="fas fa-search"></i>
            </button>
        </div>
        
        <!-- Hero Section -->
        <?php $featuredFilm = getFilms(1)[0] ?? null; ?>
        <div class="hero-section">
            <div class="hero-bg"></div>
            <div class="container">
                <div class="hero-content">
                    <?php if($featuredFilm): ?>
                        <h1 class="hero-title"><?= htmlspecialchars($featuredFilm['title']) ?></h1>
                        <p class="hero-subtitle">
                            <?= substr(htmlspecialchars($featuredFilm['description'] ?? 'Film keluarga terbaik'), 0, 120) ?>...
                        </p>
                        <div class="hero-actions">
                            <a href="?film=<?= $featuredFilm['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-play"></i> Putar Sekarang
                            </a>
                            <button class="btn btn-secondary" onclick="addToList(<?= $featuredFilm['id'] ?>)">
                                <i class="fas fa-plus"></i> Tambah ke Daftar
                            </button>
                        </div>
                    <?php else: ?>
                        <h1 class="hero-title">Selamat Datang di FamilyFlix</h1>
                        <p class="hero-subtitle">
                            Streaming film-film berkualitas untuk keluarga Anda. Aman, nyaman, dan menghibur.
                        </p>
                        <div class="hero-actions">
                            <a href="admin.php" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload Film Pertama
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="container">
            <!-- My List -->
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-bookmark" style="color: var(--primary); margin-right: 10px;"></i>
                    Daftar Saya
                </h2>
                <a href="?page=mylist" class="see-all">
                    Lihat Semua <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="films-grid">
                <?php $myListFilms = getFilms(6); ?>
                <?php if(empty($myListFilms)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--gray);">
                        <i class="fas fa-film" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>Belum ada film. Upload film pertama Anda!</p>
                        <a href="admin.php" class="btn btn-primary" style="margin-top: 16px;">
                            <i class="fas fa-upload"></i> Upload Film
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach($myListFilms as $film): ?>
                        <a href="?film=<?= $film['id'] ?>" class="film-card">
                            <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                                 alt="<?= htmlspecialchars($film['title']) ?>"
                                 class="film-thumb">
                            <div class="film-overlay">
                                <button class="btn-icon" style="margin-right: 8px;" onclick="event.preventDefault(); playFilm(<?= $film['id'] ?>)" title="Play">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button class="btn-icon" onclick="event.preventDefault(); addToList(<?= $film['id'] ?>)" title="Add to List">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="film-info">
                                <div class="film-title"><?= htmlspecialchars($film['title']) ?></div>
                                <div class="film-meta">
                                    <span><?= date('Y', strtotime($film['created_at'])) ?></span>
                                    <span>
                                        <i class="fas fa-star" style="color: var(--accent);"></i> 8.5
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Continue Watching -->
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-history" style="color: var(--primary); margin-right: 10px;"></i>
                    Lanjut Menonton
                </h2>
                <a href="?page=continue" class="see-all">
                    Lihat Semua <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="continue-scroll">
                <?php $continueFilms = getFilms(4); ?>
                <?php foreach($continueFilms as $film): ?>
                    <a href="?film=<?= $film['id'] ?>" class="continue-card">
                        <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                             alt="<?= htmlspecialchars($film['title']) ?>"
                             class="continue-thumb">
                        <div class="continue-info">
                            <div style="font-weight: 600; margin-bottom: 8px;"><?= htmlspecialchars($film['title']) ?></div>
                            <div style="font-size: 12px; color: var(--gray); margin-bottom: 12px;">
                                <?= date('Y', strtotime($film['created_at'])) ?> • 2h 15m
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <div style="font-size: 11px; color: var(--gray); margin-top: 8px;">
                                45:30 / 2:15:00
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Recommended -->
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-thumbs-up" style="color: var(--primary); margin-right: 10px;"></i>
                    Rekomendasi Untuk Anda
                </h2>
                <a href="?page=recommended" class="see-all">
                    Lihat Semua <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="films-grid">
                <?php $recommendedFilms = getFilms(8); ?>
                <?php foreach($recommendedFilms as $film): ?>
                    <a href="?film=<?= $film['id'] ?>" class="film-card">
                        <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                             alt="<?= htmlspecialchars($film['title']) ?>"
                             class="film-thumb">
                        <div class="film-overlay">
                            <button class="btn-icon" style="margin-right: 8px;" onclick="event.preventDefault(); playFilm(<?= $film['id'] ?>)" title="Play">
                                <i class="fas fa-play"></i>
                            </button>
                        </div>
                        <div class="film-info">
                            <div class="film-title"><?= htmlspecialchars($film['title']) ?></div>
                            <div class="film-meta">
                                <span><?= date('Y', strtotime($film['created_at'])) ?></span>
                                <span>Family</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="container">
                <div class="footer-links">
                    <div class="footer-section">
                        <h4>FamilyFlix</h4>
                        <a href="?">Home</a>
                        <a href="?page=films">Film</a>
                        <a href="?page=series">Series</a>
                        <a href="?page=kids">Kids</a>
                    </div>
                    <div class="footer-section">
                        <h4>Akun</h4>
                        <a href="?page=profile">Profil Saya</a>
                        <a href="?page=mylist">Daftar Saya</a>
                        <a href="?page=history">Riwayat</a>
                        <a href="admin.php">Admin Panel</a>
                    </div>
                    <div class="footer-section">
                        <h4>Bantuan</h4>
                        <a href="?page=help">Pusat Bantuan</a>
                        <a href="?page=contact">Kontak</a>
                        <a href="?page=privacy">Privasi</a>
                        <a href="?page=terms">Syarat</a>
                    </div>
                    <div class="footer-section">
                        <h4>Koneksi</h4>
                        <a href="#"><i class="fab fa-facebook" style="margin-right: 8px;"></i> Facebook</a>
                        <a href="#"><i class="fab fa-instagram" style="margin-right: 8px;"></i> Instagram</a>
                        <a href="#"><i class="fab fa-twitter" style="margin-right: 8px;"></i> Twitter</a>
                        <a href="#"><i class="fab fa-youtube" style="margin-right: 8px;"></i> YouTube</a>
                    </div>
                </div>
                
                <div class="copyright">
                    <p>© <?= date('Y') ?> FamilyFlix. All rights reserved.</p>
                    <p style="margin-top: 8px; font-size: 12px;">
                        Made with <i class="fas fa-heart" style="color: var(--primary);"></i> for Family Entertainment
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <nav class="mobile-nav desktop-hidden">
            <a href="?" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="?page=films" class="nav-item">
                <i class="fas fa-film"></i>
                <span>Film</span>
            </a>
            <a href="?page=mylist" class="nav-item">
                <i class="fas fa-bookmark"></i>
                <span>Daftar</span>
            </a>
            <a href="?page=search" class="nav-item" onclick="openSearch(); return false;">
                <i class="fas fa-search"></i>
                <span>Cari</span>
            </a>
            <a href="admin.php" class="nav-item">
                <i class="fas fa-user-shield"></i>
                <span>Admin</span>
            </a>
        </nav>
        
        <!-- Search Overlay -->
        <div class="search-overlay" id="searchOverlay">
            <div class="search-header">
                <button class="btn-icon" onclick="closeSearch()">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <input type="text" class="search-input" placeholder="Cari film..." id="searchInput">
                <button class="btn-icon" onclick="performSearch()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="searchResults"></div>
        </div>
        
        <!-- Toast Notification -->
        <div class="toast" id="toast"></div>
    <?php endif; ?>
    
    <script>
        // Video Player Functions
        let video = document.getElementById('mainVideo');
        
        if(video) {
            video.addEventListener('loadedmetadata', updateTimeDisplay);
            video.addEventListener('timeupdate', updateProgress);
            video.addEventListener('play', () => {
                document.getElementById('playBtn').className = 'fas fa-pause';
            });
            video.addEventListener('pause', () => {
                document.getElementById('playBtn').className = 'fas fa-play';
            });
        }
        
        function togglePlay() {
            if(video.paused) video.play();
            else video.pause();
        }
        
        function playVideo() {
            if(video) video.play();
        }
        
        function skip(seconds) {
            if(video) video.currentTime += seconds;
        }
        
        function toggleMute() {
            if(video) {
                video.muted = !video.muted;
                document.getElementById('volumeBtn').className = video.muted ? 'fas fa-volume-mute' : 'fas fa-volume-up';
            }
        }
        
        function toggleFullscreen() {
            const elem = video || document.documentElement;
            if (!document.fullscreenElement) {
                elem.requestFullscreen().catch(err => {
                    console.log(`Error: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        function seek(e) {
            if(video) {
                const rect = e.target.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                video.currentTime = pos * video.duration;
            }
        }
        
        function updateProgress() {
            if(video) {
                const progress = (video.currentTime / video.duration) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                updateTimeDisplay();
            }
        }
        
        function updateTimeDisplay() {
            if(video) {
                const current = formatTime(video.currentTime);
                const total = formatTime(video.duration);
                document.getElementById('timeDisplay').textContent = `${current} / ${total}`;
            }
        }
        
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
        
        function downloadVideo() {
            const filmTitle = "<?= isset($film) ? addslashes($film['title']) : 'video' ?>";
            const videoSrc = video ? video.querySelector('source')?.src : null;
            if(videoSrc) {
                const link = document.createElement('a');
                link.href = videoSrc;
                link.download = filmTitle.replace(/[^a-z0-9]/gi, '_') + '.mp4';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                showToast('Download dimulai...');
            }
        }
        
        // Search Functions
        function openSearch() {
            document.getElementById('searchOverlay').style.display = 'block';
            document.getElementById('searchInput').focus();
        }
        
        function closeSearch() {
            document.getElementById('searchOverlay').style.display = 'none';
        }
        
        function performSearch() {
            const query = document.getElementById('searchInput').value;
            if(query.trim()) {
                // Simulate search
                document.getElementById('searchResults').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: var(--gray);">
                        <i class="fas fa-search" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>Mencari: "${query}"</p>
                    </div>
                `;
            }
        }
        
        // Film Actions
        function playFilm(filmId) {
            window.location.href = `?film=${filmId}`;
        }
        
        function addToList(filmId) {
            showToast('Ditambahkan ke Daftar Saya');
            // Save to localStorage
            let myList = JSON.parse(localStorage.getItem('familyflix_mylist') || '[]');
            if(!myList.includes(filmId)) {
                myList.push(filmId);
                localStorage.setItem('familyflix_mylist', JSON.stringify(myList));
            }
        }
        
        // Toast Notification
        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.display = 'block';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }
        
        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            if(video) {
                switch(e.key.toLowerCase()) {
                    case ' ':
                    case 'k':
                        e.preventDefault();
                        togglePlay();
                        break;
                    case 'f':
                        toggleFullscreen();
                        break;
                    case 'm':
                        toggleMute();
                        break;
                    case 'arrowleft':
                        skip(-10);
                        break;
                    case 'arrowright':
                        skip(10);
                        break;
                }
            }
            
            // Search shortcut
            if(e.key === '/' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                openSearch();
            }
            
            // Escape to close search
            if(e.key === 'Escape') {
                closeSearch();
            }
        });
        
        // Close search when clicking outside
        document.getElementById('searchOverlay')?.addEventListener('click', function(e) {
            if(e.target === this) closeSearch();
        });
        
        // Mobile touch improvements
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', function(e) {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        document.addEventListener('touchend', function(e) {
            touchEndX = e.changedTouches[0].screenX;
            if(video && Math.abs(touchEndX - touchStartX) > 50) {
                if(touchEndX < touchStartX) skip(10); // Swipe left
                if(touchEndX > touchStartX) skip(-10); // Swipe right
            }
        });
        
        // Initialize
        console.log('🎬 FamilyFlix Mobile Ready!');
        console.log('📱 Mobile Optimized UI');
        console.log('🎨 Orange Theme Active');
    </script>
</body>
</html>
