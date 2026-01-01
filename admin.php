<?php
// ============================================
// FAMILYFLIX - ADMIN PANEL (TERPISAH)
// ============================================

session_start();
error_reporting(0);

// Include config
require_once 'config.php';

// Auto-setup jika belum ada
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
    mkdir('uploads/videos', 0777, true);
    mkdir('uploads/thumbnails', 0777, true);
}

// Login check
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Handle login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($username === ADMIN_USER && $password === ADMIN_PASS) {
            $_SESSION['admin'] = true;
            header('Location: admin.php');
            exit;
        } else {
            $error = "Invalid credentials!";
        }
    }
    
    // Show login form
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
                --primary: #0D6EFD;
                --dark: #0F1419;
                --light: #FFFFFF;
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                background: var(--dark);
                font-family: 'Segoe UI', system-ui, sans-serif;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .login-container {
                background: rgba(255,255,255,0.05);
                backdrop-filter: blur(10px);
                border-radius: 16px;
                padding: 50px;
                width: 100%;
                max-width: 450px;
                border: 1px solid rgba(255,255,255,0.1);
            }
            
            .logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                margin-bottom: 40px;
            }
            
            .logo i {
                font-size: 36px;
                color: var(--primary);
            }
            
            .logo-text {
                font-size: 32px;
                font-weight: 800;
                background: linear-gradient(135deg, var(--primary), #0dcaf0);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            .form-group {
                margin-bottom: 25px;
            }
            
            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: var(--light);
                font-weight: 500;
            }
            
            .form-control {
                width: 100%;
                padding: 14px 18px;
                background: rgba(255,255,255,0.1);
                border: 1px solid rgba(255,255,255,0.2);
                border-radius: 8px;
                color: var(--light);
                font-size: 16px;
            }
            
            .form-control:focus {
                outline: none;
                border-color: var(--primary);
            }
            
            .btn {
                width: 100%;
                padding: 16px;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.3s;
            }
            
            .btn:hover {
                background: #0b5ed7;
            }
            
            .error {
                background: rgba(220,53,69,0.2);
                border: 1px solid #dc3545;
                color: #dc3545;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .login-info {
                margin-top: 30px;
                padding: 15px;
                background: rgba(13,110,253,0.1);
                border-radius: 8px;
                border: 1px solid rgba(13,110,253,0.3);
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <?php if(isset($error)): ?>
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <div class="logo">
                <i class="fas fa-film"></i>
                <span class="logo-text">FamilyFlix</span>
            </div>
            
            <h2 style="text-align: center; color: var(--light); margin-bottom: 30px; font-size: 24px;">
                Admin Panel Login
            </h2>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <input type="hidden" name="login" value="1">
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="login-info">
                <i class="fas fa-info-circle"></i> 
                Default credentials: admin / admin123
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ============= ADMIN DASHBOARD =============
// Handle actions
$action = $_GET['action'] ?? '';
$message = '';

// Upload film
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    
    // Handle file uploads
    $videoFile = $_FILES['video'] ?? null;
    $thumbnailFile = $_FILES['thumbnail'] ?? null;
    
    if ($videoFile && $thumbnailFile && $title) {
        // Generate unique filenames
        $videoExt = strtolower(pathinfo($videoFile['name'], PATHINFO_EXTENSION));
        $thumbExt = strtolower(pathinfo($thumbnailFile['name'], PATHINFO_EXTENSION));
        
        if (in_array($videoExt, ['mp4', 'webm', 'mkv']) && 
            in_array($thumbExt, ['jpg', 'jpeg', 'png', 'gif'])) {
            
            $videoName = uniqid() . '.' . $videoExt;
            $thumbName = uniqid() . '.' . $thumbExt;
            
            $videoPath = 'uploads/videos/' . $videoName;
            $thumbPath = 'uploads/thumbnails/' . $thumbName;
            
            if (move_uploaded_file($videoFile['tmp_name'], $videoPath) &&
                move_uploaded_file($thumbnailFile['tmp_name'], $thumbPath)) {
                
                // Insert into database
                $stmt = $db->prepare("INSERT INTO films (title, description, video_path, thumbnail_path) 
                                      VALUES (:title, :desc, :video, :thumb)");
                $stmt->bindValue(':title', $title, SQLITE3_TEXT);
                $stmt->bindValue(':desc', $description, SQLITE3_TEXT);
                $stmt->bindValue(':video', $videoPath, SQLITE3_TEXT);
                $stmt->bindValue(':thumb', $thumbPath, SQLITE3_TEXT);
                
                if ($stmt->execute()) {
                    $message = '<div class="alert success">Film uploaded successfully!</div>';
                } else {
                    $message = '<div class="alert error">Database error!</div>';
                }
            } else {
                $message = '<div class="alert error">File upload failed!</div>';
            }
        } else {
            $message = '<div class="alert error">Invalid file types!</div>';
        }
    } else {
        $message = '<div class="alert error">Please fill all fields!</div>';
    }
}

// Delete film
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get film info
    $stmt = $db->prepare("SELECT video_path, thumbnail_path FROM films WHERE id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $film = $result->fetchArray(SQLITE3_ASSOC);
    
    if ($film) {
        // Delete files
        if (file_exists($film['video_path'])) unlink($film['video_path']);
        if (file_exists($film['thumbnail_path'])) unlink($film['thumbnail_path']);
        
        // Delete from database
        $stmt = $db->prepare("DELETE FROM films WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->execute();
        
        $message = '<div class="alert success">Film deleted successfully!</div>';
    }
}

// Logout
if ($action === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
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
            --primary: #0D6EFD;
            --secondary: #6C757D;
            --dark: #0F1419;
            --darker: #0A0E13;
            --light: #FFFFFF;
            --gray: #8A8F98;
            --card-bg: #1A1F26;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--darker);
            color: var(--light);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        
        .admin-header {
            background: var(--dark);
            padding: 20px 40px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            background: linear-gradient(135deg, var(--primary), #0dcaf0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .admin-nav a {
            color: var(--light);
            text-decoration: none;
            margin-left: 25px;
            font-weight: 500;
        }
        
        .admin-nav a:hover {
            color: var(--primary);
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .admin-title {
            font-size: 32px;
            margin-bottom: 30px;
            color: var(--light);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert.success {
            background: rgba(25,135,84,0.2);
            border: 1px solid #198754;
            color: #198754;
        }
        
        .alert.error {
            background: rgba(220,53,69,0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        
        .upload-form {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .form-title {
            font-size: 20px;
            margin-bottom: 25px;
            color: var(--light);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group.full {
            grid-column: span 2;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--gray);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: var(--light);
            font-size: 16px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .file-upload {
            border: 2px dashed rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .file-upload:hover {
            border-color: var(--primary);
        }
        
        .file-upload i {
            font-size: 36px;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .btn {
            padding: 14px 28px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            background: #0b5ed7;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #bb2d3b;
        }
        
        .films-list {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .films-header {
            padding: 25px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .films-title {
            font-size: 20px;
            font-weight: 600;
        }
        
        .films-count {
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .films-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .films-table th {
            padding: 20px;
            text-align: left;
            color: var(--gray);
            font-weight: 500;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .films-table td {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .film-thumb-admin {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .film-actions {
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }
        
        .action-btn:hover {
            background: var(--primary);
        }
        
        .action-btn.delete:hover {
            background: #dc3545;
        }
        
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--gray);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: var(--primary);
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
            <a href="index.php"><i class="fas fa-home"></i> View Site</a>
            <a href="admin.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <!-- MAIN CONTENT -->
    <div class="admin-container">
        <h1 class="admin-title">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </h1>
        
        <?= $message ?>
        
        <!-- UPLOAD FORM -->
        <div class="upload-form">
            <h2 class="form-title">
                <i class="fas fa-cloud-upload-alt"></i> Upload New Film
            </h2>
            
            <form method="POST" enctype="multipart/form-data" class="form-grid">
                <div class="form-group">
                    <label>Film Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Release Year</label>
                    <input type="number" name="year" class="form-control" value="<?= date('Y') ?>">
                </div>
                
                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter film description..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Thumbnail Image</label>
                    <div class="file-upload">
                        <i class="fas fa-image"></i>
                        <div>Click to upload thumbnail</div>
                        <input type="file" name="thumbnail" accept="image/*" required 
                               style="display: none;" id="thumbnailInput" onchange="previewThumbnail(event)">
                    </div>
                    <div id="thumbnailPreview" style="margin-top: 10px;"></div>
                </div>
                
                <div class="form-group">
                    <label>Video File</label>
                    <div class="file-upload">
                        <i class="fas fa-video"></i>
                        <div>Click to upload video</div>
                        <input type="file" name="video" accept="video/*" required 
                               style="display: none;" id="videoInput">
                    </div>
                    <div id="videoInfo" style="margin-top: 10px; font-size: 14px; color: var(--gray);"></div>
                </div>
                
                <div class="form-group full" style="text-align: right;">
                    <input type="hidden" name="upload" value="1">
                    <button type="submit" class="btn">
                        <i class="fas fa-upload"></i> Upload Film
                    </button>
                </div>
            </form>
        </div>
        
        <!-- FILMS LIST -->
        <div class="films-list">
            <div class="films-header">
                <h2 class="films-title">All Films</h2>
                <span class="films-count"><?= count($films) ?> Films</span>
            </div>
            
            <?php if(empty($films)): ?>
                <div class="empty-state">
                    <i class="fas fa-film"></i>
                    <h3>No films uploaded yet</h3>
                    <p>Upload your first film using the form above</p>
                </div>
            <?php else: ?>
                <table class="films-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Title</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($films as $film): ?>
                            <tr>
                                <td>
                                    <img src="<?= htmlspecialchars($film['thumbnail_path']) ?>" 
                                         alt="Thumbnail"
                                         class="film-thumb-admin">
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($film['title']) ?></div>
                                    <div style="font-size: 14px; color: var(--gray); margin-top: 5px;">
                                        <?= substr(htmlspecialchars($film['description'] ?? ''), 0, 60) ?>...
                                    </div>
                                </td>
                                <td>
                                    <div style="color: var(--gray); font-size: 14px;">
                                        <?= date('M d, Y', strtotime($film['created_at'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="film-actions">
                                        <a href="index.php?film=<?= $film['id'] ?>" 
                                           class="action-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="#" class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="admin.php?action=delete&id=<?= $film['id'] ?>" 
                                           class="action-btn delete" 
                                           title="Delete"
                                           onclick="return confirm('Delete this film?')">
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
        // File upload preview
        function previewThumbnail(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('thumbnailPreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" 
                             style="max-width: 150px; border-radius: 8px; border: 2px solid var(--primary);">
                    `;
                };
                reader.readAsDataURL(file);
                
                // Update label
                event.target.parentElement.querySelector('div:nth-child(2)').textContent = file.name;
            }
        }
        
        // Video file info
        document.getElementById('videoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const info = document.getElementById('videoInfo');
            
            if (file) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
                info.innerHTML = `
                    <div><strong>File:</strong> ${file.name}</div>
                    <div><strong>Size:</strong> ${sizeMB} MB</div>
                    <div><strong>Type:</strong> ${file.type}</div>
                `;
                
                // Update label
                e.target.parentElement.querySelector('div:nth-child(2)').textContent = file.name;
            }
        });
        
        // Make file upload areas clickable
        document.querySelectorAll('.file-upload').forEach(area => {
            const input = area.querySelector('input[type="file"]');
            area.addEventListener('click', () => input.click());
        });
        
        // System info
        console.log('FamilyFlix Admin Panel Loaded');
        console.log('Total Films: <?= count($films) ?>');
    </script>
</body>
</html>
