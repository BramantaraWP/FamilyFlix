<?php
// ============================================
// FAMILYFLIX - ADMIN PANEL DENGAN PASTE LINK
// ============================================

session_start();
error_reporting(0);
require_once 'config.php';

// Auto-create directories
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
    mkdir('uploads/videos', 0777, true);
    mkdir('uploads/thumbnails', 0777, true);
}

// Login check
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
            $_SESSION['admin'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FamilyFlix - Admin Login</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary: #FF6B35;
                --primary-dark: #E55A2B;
                --dark: #0F1419;
                --light: #FFFFFF;
            }
            
            * { margin: 0; padding: 0; box-sizing: border-box; }
            
            body {
                background: var(--dark);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .login-box {
                background: rgba(255,255,255,0.05);
                backdrop-filter: blur(20px);
                border-radius: 20px;
                padding: 40px;
                width: 100%;
                max-width: 400px;
                border: 1px solid rgba(255,107,53,0.3);
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            }
            
            .logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                margin-bottom: 30px;
            }
            
            .logo i { font-size: 40px; color: var(--primary); }
            .logo-text {
                font-size: 32px;
                font-weight: 800;
                background: linear-gradient(135deg, var(--primary), #FFD166);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            .form-group { margin-bottom: 25px; }
            label { display: block; color: var(--light); margin-bottom: 8px; font-weight: 500; }
            
            .form-input {
                width: 100%;
                padding: 15px;
                background: rgba(255,255,255,0.1);
                border: 2px solid rgba(255,255,255,0.2);
                border-radius: 12px;
                color: var(--light);
                font-size: 16px;
                transition: border-color 0.3s;
            }
            
            .form-input:focus {
                outline: none;
                border-color: var(--primary);
            }
            
            .btn {
                width: 100%;
                padding: 16px;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            }
            
            .btn:hover { background: var(--primary-dark); transform: translateY(-2px); }
            
            .error {
                background: rgba(220,53,69,0.2);
                border: 1px solid #dc3545;
                color: #dc3545;
                padding: 15px;
                border-radius: 12px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .info-box {
                background: rgba(255,107,53,0.1);
                border: 1px solid rgba(255,107,53,0.3);
                border-radius: 12px;
                padding: 15px;
                margin-top: 25px;
                font-size: 14px;
                color: var(--light);
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <?php if(isset($error)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <div class="logo">
                <i class="fas fa-film"></i>
                <span class="logo-text">FamilyFlix</span>
            </div>
            
            <h2 style="text-align: center; color: var(--light); margin-bottom: 30px;">
                <i class="fas fa-lock"></i> Admin Panel
            </h2>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                
                <input type="hidden" name="login" value="1">
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="info-box">
                <i class="fas fa-info-circle"></i> 
                Default: admin / admin123
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle actions
$message = '';
$action = $_GET['action'] ?? '';

// Upload film (file OR link)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if ($title) {
        $videoPath = '';
        $thumbPath = '';
        
        // OPTION 1: Upload files
        if (!empty($_FILES['video_file']['name']) && !empty($_FILES['thumbnail_file']['name'])) {
            $videoFile = $_FILES['video_file'];
            $thumbFile = $_FILES['thumbnail_file'];
            
            $videoExt = strtolower(pathinfo($videoFile['name'], PATHINFO_EXTENSION));
            $thumbExt = strtolower(pathinfo($thumbFile['name'], PATHINFO_EXTENSION));
            
            if (in_array($videoExt, ['mp4', 'webm', 'mkv']) && 
                in_array($thumbExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                
                $videoName = uniqid() . '.' . $videoExt;
                $thumbName = uniqid() . '.' . $thumbExt;
                
                $videoPath = 'uploads/videos/' . $videoName;
                $thumbPath = 'uploads/thumbnails/' . $thumbName;
                
                move_uploaded_file($videoFile['tmp_name'], $videoPath);
                move_uploaded_file($thumbFile['tmp_name'], $thumbPath);
            }
        }
        // OPTION 2: Use links
        elseif (!empty($_POST['video_link']) && !empty($_POST['thumbnail_link'])) {
            $videoPath = trim($_POST['video_link']);
            $thumbPath = trim($_POST['thumbnail_link']);
        }
        
        if ($videoPath && $thumbPath) {
            $stmt = $db->prepare("INSERT INTO films (title, description, video_path, thumbnail_path) 
                                  VALUES (:title, :desc, :video, :thumb)");
            $stmt->bindValue(':title', $title, SQLITE3_TEXT);
            $stmt->bindValue(':desc', $description, SQLITE3_TEXT);
            $stmt->bindValue(':video', $videoPath, SQLITE3_TEXT);
            $stmt->bindValue(':thumb', $thumbPath, SQLITE3_TEXT);
            
            if ($stmt->execute()) {
                $message = '<div class="alert success"><i class="fas fa-check"></i> Film berhasil diupload!</div>';
            } else {
                $message = '<div class="alert error"><i class="fas fa-times"></i> Database error!</div>';
            }
        } else {
            $message = '<div class="alert error"><i class="fas fa-times"></i> Harap upload file atau masukkan link!</div>';
        }
    } else {
        $message = '<div class="alert error"><i class="fas fa-times"></i> Judul film wajib diisi!</div>';
    }
}

// Delete film
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $db->prepare("SELECT video_path, thumbnail_path FROM films WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $film = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($film) {
        // Only delete physical files if they're in uploads folder
        if (strpos($film['video_path'], 'uploads/') === 0 && file_exists($film['video_path'])) {
            unlink($film['video_path']);
        }
        if (strpos($film['thumbnail_path'], 'uploads/') === 0 && file_exists($film['thumbnail_path'])) {
            unlink($film['thumbnail_path']);
        }
        
        $stmt = $db->prepare("DELETE FROM films WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        $message = '<div class="alert success"><i class="fas fa-check"></i> Film berhasil dihapus!</div>';
    }
}

// Get all films
$films = [];
$stmt = $db->prepare("SELECT * FROM films ORDER BY created_at DESC");
$result = $stmt->execute();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $films[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamilyFlix - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--darker);
            color: var(--light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }
        
        .admin-header {
            background: rgba(15, 20, 25, 0.95);
            backdrop-filter: blur(20px);
            padding: 20px 32px;
            border-bottom: 1px solid rgba(255, 107, 53, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-icon {
            color: var(--primary);
            font-size: 24px;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .admin-nav {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .admin-nav a {
            color: var(--light);
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .admin-nav a:hover {
            background: rgba(255, 107, 53, 0.2);
            color: var(--primary);
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px;
        }
        
        .alert {
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }
        
        .alert.success {
            background: rgba(25,135,84,0.2);
            border: 1px solid #198754;
            color: #20C997;
        }
        
        .alert.error {
            background: rgba(220,53,69,0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        
        .upload-section {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            border: 2px solid rgba(255, 107, 53, 0.2);
        }
        
        .section-title {
            font-size: 24px;
            margin-bottom: 24px;
            color: var(--light);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title i {
            color: var(--primary);
        }
        
        .upload-tabs {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
            padding-bottom: 16px;
        }
        
        .tab-btn {
            padding: 12px 24px;
            background: none;
            border: none;
            color: var(--gray);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s;
            position: relative;
        }
        
        .tab-btn.active {
            color: var(--primary);
            background: rgba(255, 107, 53, 0.1);
        }
        
        .tab-btn:after {
            content: '';
            position: absolute;
            bottom: -18px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .tab-btn.active:after {
            opacity: 1;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--light);
            font-weight: 500;
        }
        
        .form-input {
            width: 100%;
            padding: 16px;
            background: rgba(255,255,255,0.05);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: var(--light);
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        textarea.form-input {
            min-height: 120px;
            resize: vertical;
        }
        
        .file-upload {
            border: 2px dashed rgba(255, 107, 53, 0.4);
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 107, 53, 0.05);
        }
        
        .file-upload:hover {
            background: rgba(255, 107, 53, 0.1);
            border-color: var(--primary);
        }
        
        .file-upload i {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 16px;
        }
        
        .upload-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 18px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            justify-content: center;
        }
        
        .upload-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 107, 53, 0.3);
        }
        
        .films-section {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
        }
        
        .films-header {
            padding: 24px 32px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .films-title {
            font-size: 24px;
            font-weight: 700;
        }
        
        .films-count {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .films-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .films-table th {
            padding: 20px;
            text-align: left;
            color: var(--gray);
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .films-table td {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .film-thumb {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid rgba(255, 107, 53, 0.3);
        }
        
        .film-actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 10px;
            border-radius: 8px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
        }
        
        .action-btn.view {
            background: rgba(13,110,253,0.2);
            border: 1px solid rgba(13,110,253,0.3);
        }
        
        .action-btn.view:hover {
            background: rgba(13,110,253,0.3);
        }
        
        .action-btn.delete {
            background: rgba(220,53,69,0.2);
            border: 1px solid rgba(220,53,69,0.3);
        }
        
        .action-btn.delete:hover {
            background: rgba(220,53,69,0.3);
        }
        
        .empty-state {
            padding: 80px 32px;
            text-align: center;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 24px;
            color: var(--primary);
            opacity: 0.5;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .admin-header {
                padding: 16px;
                flex-direction: column;
                gap: 16px;
            }
            
            .admin-nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .admin-nav a {
                padding: 8px 16px;
                font-size: 14px;
            }
            
            .admin-container {
                padding: 16px;
            }
            
            .upload-section {
                padding: 20px;
            }
            
            .upload-tabs {
                flex-direction: column;
            }
            
            .films-table {
                display: block;
                overflow-x: auto;
            }
            
            .film-thumb {
                width: 60px;
                height: 45px;
            }
        }
        
        .link-help {
            background: rgba(255, 107, 53, 0.1);
            border: 1px solid rgba(255, 107, 53, 0.3);
            border-radius: 8px;
            padding: 12px;
            margin-top: 8px;
            font-size: 13px;
            color: var(--primary-light);
        }
        
        .link-help i {
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="admin-header">
        <div class="logo">
            <i class="fas fa-film logo-icon"></i>
            <span class="logo-text">FamilyFlix</span>
            <span style="color: var(--gray); margin-left: 10px;">| Admin Panel</span>
        </div>
        
        <div class="admin-nav">
            <a href="index.php">
                <i class="fas fa-eye"></i> View Site
            </a>
            <a href="admin.php" style="background: rgba(255, 107, 53, 0.2); color: var(--primary);">
                <i class="fas fa-upload"></i> Upload Film
            </a>
            <a href="?action=logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="admin-container">
        <?= $message ?>
        
        <!-- UPLOAD SECTION -->
        <div class="upload-section">
            <h2 class="section-title">
                <i class="fas fa-cloud-upload-alt"></i> Upload Film Baru
            </h2>
            
            <!-- Tabs -->
            <div class="upload-tabs">
                <button class="tab-btn active" onclick="switchTab('file')">
                    <i class="fas fa-file-upload"></i> Upload File
                </button>
                <button class="tab-btn" onclick="switchTab('link')">
                    <i class="fas fa-link"></i> Paste Link
                </button>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Basic Info -->
                <div class="form-group">
                    <label><i class="fas fa-film"></i> Judul Film *</label>
                    <input type="text" name="title" class="form-input" required placeholder="Masukkan judul film">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Deskripsi</label>
                    <textarea name="description" class="form-input" placeholder="Masukkan deskripsi film..."></textarea>
                </div>
                
                <!-- File Upload Tab -->
                <div class="tab-content active" id="fileTab">
                    <div class="form-group">
                        <label><i class="fas fa-video"></i> Video File *</label>
                        <div class="file-upload" onclick="document.getElementById('videoFile').click()">
                            <i class="fas fa-video"></i>
                            <div>Klik untuk upload video (MP4, WebM, MKV)</div>
                            <input type="file" id="videoFile" name="video_file" accept="video/*" style="display: none;" onchange="showFileName(this, 'videoFileName')">
                            <div id="videoFileName" style="margin-top: 10px; font-size: 14px; color: var(--primary-light);"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Thumbnail *</label>
                        <div class="file-upload" onclick="document.getElementById('thumbnailFile').click()">
                            <i class="fas fa-image"></i>
                            <div>Klik untuk upload thumbnail (JPG, PNG, GIF)</div>
                            <input type="file" id="thumbnailFile" name="thumbnail_file" accept="image/*" style="display: none;" onchange="showFileName(this, 'thumbnailFileName')">
                            <div id="thumbnailFileName" style="margin-top: 10px; font-size: 14px; color: var(--primary-light);"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Link Paste Tab -->
                <div class="tab-content" id="linkTab">
                    <div class="form-group">
                        <label><i class="fas fa-link"></i> Video Link *</label>
                        <input type="url" name="video_link" class="form-input" placeholder="https://example.com/video.mp4">
                        <div class="link-help">
                            <i class="fas fa-info-circle"></i>
                            Masukkan link langsung ke file video (MP4, WebM, atau MKV)
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-image"></i> Thumbnail Link *</label>
                        <input type="url" name="thumbnail_link" class="form-input" placeholder="https://example.com/thumbnail.jpg">
                        <div class="link-help">
                            <i class="fas fa-info-circle"></i>
                            Masukkan link langsung ke gambar thumbnail (JPG, PNG, atau GIF)
                        </div>
                    </div>
                    
                    <div style="background: rgba(255,215,102,0.1); border: 1px solid var(--accent); border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                        <h4 style="color: var(--accent); margin-bottom: 8px;">
                            <i class="fas fa-lightbulb"></i> Tips Upload Link:
                        </h4>
                        <ul style="padding-left: 20px; color: var(--gray); font-size: 14px;">
                            <li>Gunakan Google Drive/Dropbox untuk video besar</li>
                            <li>Upload thumbnail ke Imgur/Postimages untuk link</li>
                            <li>Pastikan link bisa diakses publik</li>
                            <li>Format video: MP4 direkomendasikan</li>
                        </ul>
                    </div>
                </div>
                
                <input type="hidden" name="upload" value="1">
                <button type="submit" class="upload-btn">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Film
                </button>
            </form>
        </div>
        
        <!-- FILMS LIST -->
        <div class="films-section">
            <div class="films-header">
                <h2 class="films-title">Daftar Film</h2>
                <span class="films-count"><?= count($films) ?> Film</span>
            </div>
            
            <?php if(empty($films)): ?>
                <div class="empty-state">
                    <i class="fas fa-film"></i>
                    <h3>Belum ada film</h3>
                    <p>Upload film pertama Anda menggunakan form di atas</p>
                </div>
            <?php else: ?>
                <table class="films-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Judul</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($films as $film): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                                         alt="Thumbnail" 
                                         class="film-thumb"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA4MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjMkEzMDM5Ii8+CjxwYXRoIGQ9Ik0yNSA0MEw0MCAyOUw1NSA0MEgyNVoiIGZpbGw9IiNGRjZDQjUiLz4KPHBhdGggZD0iTTM1IDI4QzM1IDI5LjY1NjkgMzMuNjU2OSAzMSAzMiAzMUMzMC4zNDMxIDMxIDI5IDI5LjY1NjkgMjkgMjhDMjkgMjYuMzQzMSAzMC4zNDMxIDI1IDMyIDI1QzMzLjY1NjkgMjUgMzUgMjYuMzQzMSAzNSAyOFoiIGZpbGw9IiNGRjZDQjUiLz4KPC9zdmc+Cg=='">
                                </td>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 4px;">
                                        <?= htmlspecialchars($film['title']) ?>
                                    </div>
                                    <div style="font-size: 13px; color: var(--gray);">
                                        <?= substr(htmlspecialchars($film['description'] ?? ''), 0, 60) ?>...
                                    </div>
                                </td>
                                <td>
                                    <div style="color: var(--gray); font-size: 14px;">
                                        <?= date('d/m/Y', strtotime($film['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="film-actions">
                                        <a href="index.php?film=<?= $film['id'] ?>" 
                                           class="action-btn view" 
                                           title="View" 
                                           target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="admin.php?action=delete&id=<?= $film['id'] ?>" 
                                           class="action-btn delete" 
                                           title="Delete"
                                           onclick="return confirm('Hapus film ini? Tindakan ini tidak dapat dibatalkan.')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Tab Switching
        function switchTab(tab) {
            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.tab-btn[onclick="switchTab('${tab}')"]`).classList.add('active');
            
            // Update content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tab + 'Tab').classList.add('active');
        }
        
        // Show file name when selected
        function showFileName(input, targetId) {
            const fileName = input.files[0]?.name || 'Tidak ada file dipilih';
            document.getElementById(targetId).textContent = `File: ${fileName}`;
        }
        
        // Auto-switch to link tab if files not selected
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileTab = document.getElementById('fileTab').classList.contains('active');
            const videoFile = document.getElementById('videoFile').files.length;
            const thumbFile = document.getElementById('thumbnailFile').files.length;
            
            if(fileTab && !videoFile && !thumbFile) {
                e.preventDefault();
                alert('Silakan pilih file atau gunakan tab Paste Link!');
                switchTab('link');
            }
        });
        
        // Sample link generator
        function pasteSampleLink(type) {
            const sampleLinks = {
                video: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                thumbnail: 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Big_buck_bunny_poster_big.jpg/640px-Big_buck_bunny_poster_big.jpg'
            };
            
            if(type === 'video') {
                document.querySelector('input[name="video_link"]').value = sampleLinks.video;
            } else {
                document.querySelector('input[name="thumbnail_link"]').value = sampleLinks.thumbnail;
            }
            
            showToast('Link contoh ditempel!');
        }
        
        // Toast notification
        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: var(--primary);
                color: white;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: 500;
                z-index: 1000;
                animation: slideIn 0.3s;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Add paste buttons to link tab
        document.addEventListener('DOMContentLoaded', function() {
            const videoInput = document.querySelector('input[name="video_link"]');
            const thumbInput = document.querySelector('input[name="thumbnail_link"]');
            
            if(videoInput && thumbInput) {
                // Add paste buttons
                const videoGroup = videoInput.parentElement;
                const thumbGroup = thumbInput.parentElement;
                
                videoGroup.innerHTML += `
                    <button type="button" onclick="pasteSampleLink('video')" style="
                        background: rgba(255, 107, 53, 0.2);
                        border: 1px solid var(--primary);
                        color: var(--primary);
                        padding: 8px 16px;
                        border-radius: 8px;
                        margin-top: 8px;
                        cursor: pointer;
                        font-size: 14px;
                    ">
                        <i class="fas fa-paste"></i> Tempel Contoh Link Video
                    </button>
                `;
                
                thumbGroup.innerHTML += `
                    <button type="button" onclick="pasteSampleLink('thumbnail')" style="
                        background: rgba(255, 107, 53, 0.2);
                        border: 1px solid var(--primary);
                        color: var(--primary);
                        padding: 8px 16px;
                        border-radius: 8px;
                        margin-top: 8px;
                        cursor: pointer;
                        font-size: 14px;
                    ">
                        <i class="fas fa-paste"></i> Tempel Contoh Thumbnail
                    </button>
                `;
            }
        });
        
        console.log('🎬 FamilyFlix Admin Ready!');
        console.log('📱 Mobile Friendly Admin Panel');
        console.log('🔗 Paste Link Feature Enabled');
    </script>
</body>
</html>
