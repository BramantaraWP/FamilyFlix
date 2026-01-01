<?php
// ============================================
// FAMILYFLIX - HOME PAGE & PLAYER
// ============================================

session_start();
error_reporting(0);

// Include config
require_once 'config.php';

// ============= HELPER FUNCTIONS ============
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

// ============= HANDLE ACTIONS ==============
$action = $_GET['action'] ?? '';
$film_id = $_GET['film'] ?? 0;

// ============= HTML START ==================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamilyFlix - Streaming Film Keluarga</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* ====== VARIABLES ====== */
        :root {
            --primary: #0D6EFD;
            --secondary: #6C757D;
            --dark: #0F1419;
            --darker: #0A0E13;
            --light: #FFFFFF;
            --gray: #8A8F98;
            --success: #20C997;
            --card-bg: #1A1F26;
        }
        
        /* ====== BASE STYLES ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--darker);
            color: var(--light);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* ====== NAVBAR ====== */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(180deg, rgba(15,20,25,0.95) 0%, rgba(15,20,25,0.8) 100%);
            backdrop-filter: blur(10px);
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            color: var(--primary);
            font-size: 28px;
        }
        
        .logo-text {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        
        .nav-links {
            display: flex;
            gap: 40px;
            margin-left: 60px;
        }
        
        .nav-link {
            color: var(--light);
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-link:hover {
            color: var(--primary);
        }
        
        .nav-link.active {
            color: var(--primary);
            font-weight: 600;
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        
        .search-box {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 24px;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .search-input {
            background: transparent;
            border: none;
            color: var(--light);
            font-size: 14px;
            width: 200px;
        }
        
        .search-input:focus {
            outline: none;
        }
        
        .profile-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #0dcaf0);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        /* ====== HERO SECTION ====== */
        .hero-section {
            margin-top: 100px;
            padding: 60px 0;
            position: relative;
        }
        
        .hero-title {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(to right, #fff 30%, var(--primary) 70%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-subtitle {
            font-size: 18px;
            color: var(--gray);
            max-width: 600px;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        
        .hero-actions {
            display: flex;
            gap: 20px;
        }
        
        .btn {
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            backdrop-filter: blur(10px);
        }
        
        .btn-secondary:hover {
            background: rgba(255,255,255,0.2);
        }
        
        /* ====== SECTION HEADER ====== */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 60px 0 30px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 700;
        }
        
        .see-all {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* ====== FILM GRID ====== */
        .films-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .film-card {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }
        
        .film-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }
        
        .film-thumb {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .film-info {
            padding: 20px;
        }
        
        .film-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .film-meta {
            display: flex;
            gap: 15px;
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .film-rating {
            color: #FFD700;
        }
        
        .film-desc {
            color: var(--gray);
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .film-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-icon:hover {
            background: var(--primary);
        }
        
        /* ====== CONTINUE WATCHING ====== */
        .continue-watching {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .continue-card {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
        }
        
        .continue-thumb {
            width: 120px;
            height: 160px;
            object-fit: cover;
        }
        
        .continue-info {
            padding: 20px;
            flex: 1;
        }
        
        .progress-bar {
            height: 4px;
            background: rgba(255,255,255,0.1);
            border-radius: 2px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary);
            width: 65%;
        }
        
        /* ====== PLAYER PAGE ====== */
        .player-page {
            margin-top: 100px;
        }
        
        .player-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
        }
        
        .video-wrapper {
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        
        #mainVideo {
            width: 100%;
            border-radius: 12px;
        }
        
        .video-controls {
            padding: 20px;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .controls-left, .controls-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .video-info {
            margin-top: 30px;
        }
        
        .video-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .video-meta {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
        }
        
        .video-desc {
            color: var(--gray);
            line-height: 1.6;
            max-width: 800px;
        }
        
        /* ====== SIDEBAR ====== */
        .sidebar {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            height: fit-content;
        }
        
        .sidebar-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sidebar-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .sidebar-item {
            display: flex;
            gap: 15px;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            transition: background 0.3s;
        }
        
        .sidebar-item:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-thumb {
            width: 80px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
        }
        
        .sidebar-item-info {
            flex: 1;
        }
        
        .sidebar-item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .sidebar-item-meta {
            font-size: 12px;
            color: var(--gray);
        }
        
        /* ====== MY LIST SECTION ====== */
        .my-list-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 40px 0 30px;
        }
        
        .my-list-title {
            font-size: 28px;
            font-weight: 700;
        }
        
        .add-to-list {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* ====== RESPONSIVE ====== */
        @media (max-width: 1200px) {
            .player-container {
                grid-template-columns: 1fr;
            }
            
            .films-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }
            
            .nav-links {
                display: none;
            }
            
            .hero-title {
                font-size: 36px;
            }
            
            .films-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-film logo-icon"></i>
            <span class="logo-text">FamilyFlix</span>
        </div>
        
        <div class="nav-links">
            <a href="?" class="nav-link active">Home</a>
            <a href="?page=films" class="nav-link">Movies</a>
            <a href="?page=series" class="nav-link">Series</a>
            <a href="?page=mylist" class="nav-link">My List</a>
        </div>
        
        <div class="nav-actions">
            <div class="search-box">
                <i class="fas fa-search" style="color: var(--gray);"></i>
                <input type="text" class="search-input" placeholder="Search movies...">
            </div>
            <a href="admin.php" class="btn btn-secondary" style="padding: 10px 20px;">
                <i class="fas fa-user-shield"></i> Admin
            </a>
            <div class="profile-btn">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </nav>
    
    <!-- MAIN CONTENT -->
    <div class="container">
        <?php if($film_id > 0): ?>
            <!-- PLAYER PAGE -->
            <?php $film = getFilm($film_id); ?>
            <?php if($film): ?>
                <div class="player-page">
                    <div class="player-container">
                        <!-- Main Video -->
                        <div>
                            <div class="video-wrapper">
                                <video id="mainVideo" controls crossorigin="anonymous">
                                    <source src="<?= htmlspecialchars($film['video_path']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            
                            <div class="video-info">
                                <h1 class="video-title"><?= htmlspecialchars($film['title']) ?></h1>
                                
                                <div class="video-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?= date('Y', strtotime($film['created_at'])) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>2h 15m</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-star" style="color: #FFD700;"></i>
                                        <span>8.5/10</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-closed-captioning"></i>
                                        <span>Subtitle ID/EN</span>
                                    </div>
                                </div>
                                
                                <div class="hero-actions">
                                    <button class="btn btn-primary" onclick="playVideo()">
                                        <i class="fas fa-play"></i> Play Now
                                    </button>
                                    <button class="btn btn-secondary" onclick="addToList()">
                                        <i class="fas fa-plus"></i> Add to List
                                    </button>
                                    <button class="btn btn-secondary" onclick="downloadVideo()">
                                        <i class="fas fa-download"></i> Download
                                    </button>
                                </div>
                                
                                <p class="video-desc">
                                    <?= nl2br(htmlspecialchars($film['description'] ?? 'No description available')) ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="sidebar">
                            <h3 class="sidebar-title">
                                <i class="fas fa-fire"></i> More Like This
                            </h3>
                            
                            <div class="sidebar-list">
                                <?php $allFilms = getFilms(6); ?>
                                <?php foreach($allFilms as $f): ?>
                                    <?php if($f['id'] != $film_id): ?>
                                        <a href="?film=<?= $f['id'] ?>" class="sidebar-item-link">
                                            <div class="sidebar-item">
                                                <img src="<?= htmlspecialchars($f['thumbnail_path']) ?>" 
                                                     alt="<?= htmlspecialchars($f['title']) ?>"
                                                     class="sidebar-thumb">
                                                <div class="sidebar-item-info">
                                                    <div class="sidebar-item-title"><?= htmlspecialchars($f['title']) ?></div>
                                                    <div class="sidebar-item-meta">
                                                        <span><?= date('Y', strtotime($f['created_at'])) ?></span>
                                                        • <span>Action</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Shortcuts Hint -->
                            <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 8px;">
                                <h4 style="font-size: 14px; margin-bottom: 10px; color: var(--gray);">
                                    <i class="fas fa-keyboard"></i> Keyboard Shortcuts
                                </h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px;">
                                    <div><kbd>Space</kbd> Play/Pause</div>
                                    <div><kbd>F</kbd> Fullscreen</div>
                                    <div><kbd>M</kbd> Mute</div>
                                    <div><kbd>← →</kbd> Seek 10s</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 100px 0;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 20px;"></i>
                    <h2 style="margin-bottom: 20px;">Film Not Found</h2>
                    <a href="?" class="btn btn-primary">Back to Home</a>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- HOME PAGE -->
            <div class="hero-section">
                <?php $featuredFilm = getFilms(1)[0] ?? null; ?>
                <?php if($featuredFilm): ?>
                    <div style="position: relative;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
                                  background: linear-gradient(90deg, var(--darker) 0%, transparent 50%, transparent 100%); 
                                  z-index: 1;"></div>
                        <img src="<?= htmlspecialchars($featuredFilm['thumbnail_path']) ?>" 
                             alt="Featured Film"
                             style="width: 100%; height: 600px; object-fit: cover; border-radius: 16px;">
                        <div style="position: absolute; bottom: 60px; left: 60px; z-index: 2; max-width: 600px;">
                            <h1 class="hero-title"><?= htmlspecialchars($featuredFilm['title']) ?></h1>
                            <p class="hero-subtitle">
                                <?= substr(htmlspecialchars($featuredFilm['description'] ?? ''), 0, 150) ?>...
                            </p>
                            <div class="hero-actions">
                                <a href="?film=<?= $featuredFilm['id'] ?>" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Play Now
                                </a>
                                <button class="btn btn-secondary">
                                    <i class="fas fa-info-circle"></i> More Info
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- MY LIST SECTION -->
            <div class="my-list-header">
                <h2 class="my-list-title">
                    <i class="fas fa-bookmark" style="color: var(--primary); margin-right: 10px;"></i>
                    My List
                </h2>
                <a href="#" class="add-to-list">
                    <i class="fas fa-plus"></i> Add More
                </a>
            </div>
            
            <div class="films-grid">
                <?php $myListFilms = getFilms(6); ?>
                <?php foreach($myListFilms as $film): ?>
                    <div class="film-card">
                        <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                             alt="<?= htmlspecialchars($film['title']) ?>"
                             class="film-thumb">
                        <div class="film-info">
                            <h3 class="film-title"><?= htmlspecialchars($film['title']) ?></h3>
                            <div class="film-meta">
                                <span class="film-rating">
                                    <i class="fas fa-star"></i> 8.5
                                </span>
                                <span><?= date('Y', strtotime($film['created_at'])) ?></span>
                                <span>2h 15m</span>
                            </div>
                            <p class="film-desc">
                                <?= substr(htmlspecialchars($film['description'] ?? 'No description'), 0, 100) ?>...
                            </p>
                            <div class="film-actions">
                                <a href="?film=<?= $film['id'] ?>" class="btn-icon" title="Play">
                                    <i class="fas fa-play"></i>
                                </a>
                                <button class="btn-icon" title="Add to List">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button class="btn-icon" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- CONTINUE WATCHING -->
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-history" style="margin-right: 10px;"></i>
                    Continue Watching
                </h2>
                <a href="#" class="see-all">
                    See All <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="continue-watching">
                <?php $continueFilms = getFilms(3); ?>
                <?php foreach($continueFilms as $film): ?>
                    <div class="continue-card">
                        <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                             alt="<?= htmlspecialchars($film['title']) ?>"
                             class="continue-thumb">
                        <div class="continue-info">
                            <h4 style="font-weight: 600; margin-bottom: 10px;"><?= htmlspecialchars($film['title']) ?></h4>
                            <p style="color: var(--gray); font-size: 14px; margin-bottom: 15px;">
                                <?= substr(htmlspecialchars($film['description'] ?? ''), 0, 60) ?>...
                            </p>
                            <div class="progress-bar">
                                <div class="progress-fill"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--gray);">
                                <span>45:30 / 2:15:00</span>
                                <a href="?film=<?= $film['id'] ?>" style="color: var(--primary); text-decoration: none;">
                                    Continue <i class="fas fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- MORE LIKE THIS -->
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-thumbs-up" style="margin-right: 10px;"></i>
                    Recommended For You
                </h2>
                <a href="#" class="see-all">
                    See All <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="films-grid">
                <?php $recommendedFilms = getFilms(6); ?>
                <?php foreach($recommendedFilms as $film): ?>
                    <div class="film-card">
                        <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                             alt="<?= htmlspecialchars($film['title']) ?>"
                             class="film-thumb">
                        <div class="film-info">
                            <h3 class="film-title"><?= htmlspecialchars($film['title']) ?></h3>
                            <div class="film-meta">
                                <span><?= date('Y', strtotime($film['created_at'])) ?></span>
                                <span>Action • Drama</span>
                            </div>
                            <div class="film-actions">
                                <a href="?film=<?= $film['id'] ?>" class="btn-icon" title="Play">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- FOOTER -->
    <footer style="background: var(--dark); padding: 60px 0; margin-top: 100px;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
                <div>
                    <div class="logo" style="margin-bottom: 20px;">
                        <i class="fas fa-film logo-icon"></i>
                        <span class="logo-text">FamilyFlix</span>
                    </div>
                    <p style="color: var(--gray); line-height: 1.6;">
                        Streaming platform khusus keluarga. Konten aman, berkualitas, dan menghibur untuk semua usia.
                    </p>
                </div>
                
                <div>
                    <h4 style="font-size: 18px; margin-bottom: 20px;">Quick Links</h4>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="?" style="color: var(--gray); text-decoration: none;">Home</a>
                        <a href="?page=films" style="color: var(--gray); text-decoration: none;">Movies</a>
                        <a href="?page=series" style="color: var(--gray); text-decoration: none;">Series</a>
                        <a href="admin.php" style="color: var(--primary); text-decoration: none;">Admin Panel</a>
                    </div>
                </div>
                
                <div>
                    <h4 style="font-size: 18px; margin-bottom: 20px;">Contact</h4>
                    <div style="color: var(--gray);">
                        <p style="margin-bottom: 10px;">
                            <i class="fas fa-envelope" style="margin-right: 10px;"></i>
                            support@familyflix.com
                        </p>
                        <p>
                            <i class="fas fa-shield-alt" style="margin-right: 10px;"></i>
                            100% Family Safe Content
                        </p>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 40px; padding-top: 30px; 
                      text-align: center; color: var(--gray);">
                <p>© <?= date('Y') ?> FamilyFlix. All rights reserved.</p>
                <p style="margin-top: 10px; font-size: 14px;">
                    Made with <i class="fas fa-heart" style="color: #dc3545;"></i> for Family Entertainment
                </p>
            </div>
        </div>
    </footer>
    
    <script>
        // Video Player Functions
        function playVideo() {
            const video = document.getElementById('mainVideo');
            if(video) {
                video.play();
            }
        }
        
        function addToList() {
            alert('Added to My List!');
        }
        
        function downloadVideo() {
            const filmTitle = "<?= isset($film) ? addslashes($film['title']) : 'video' ?>";
            const videoSrc = document.querySelector('#mainVideo source')?.src;
            if(videoSrc) {
                const link = document.createElement('a');
                link.href = videoSrc;
                link.download = filmTitle + '.mp4';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
        
        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            const video = document.getElementById('mainVideo');
            if(!video) return;
            
            switch(e.key.toLowerCase()) {
                case ' ':
                case 'k':
                    e.preventDefault();
                    if(video.paused) video.play();
                    else video.pause();
                    break;
                case 'f':
                    if(!document.fullscreenElement) {
                        video.requestFullscreen();
                    } else {
                        document.exitFullscreen();
                    }
                    break;
                case 'm':
                    video.muted = !video.muted;
                    break;
                case 'arrowleft':
                    video.currentTime -= 10;
                    break;
                case 'arrowright':
                    video.currentTime += 10;
                    break;
            }
        });
        
        // Auto-play featured video on hover
        const filmCards = document.querySelectorAll('.film-card');
        filmCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
