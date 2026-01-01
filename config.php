<?php
// ============================================
// FAMILYFLIX - CONFIGURATION
// ============================================

// Database configuration
define('DB_FILE', 'familyflix.db');

// Admin credentials
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123'); // Change this!

// Auto-setup function
function autoSetup() {
    // Create database if not exists
    if (!file_exists(DB_FILE)) {
        $db = new SQLite3(DB_FILE);
        
        // Create films table
        $db->exec("CREATE TABLE films (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            video_path TEXT NOT NULL,
            thumbnail_path TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create sample data
        $db->exec("INSERT INTO films (title, description, video_path, thumbnail_path) 
                   VALUES ('Demo Film', 'Sample film for demonstration', 'demo.mp4', 'demo.jpg')");
        
        $db->close();
    }
    
    // Create upload directories
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
        mkdir('uploads/videos', 0777, true);
        mkdir('uploads/thumbnails', 0777, true);
        
        // Add .htaccess protection
        file_put_contents('uploads/.htaccess', "Deny from all\n");
    }
}

// Initialize
autoSetup();

// Database connection
$db = new SQLite3(DB_FILE);
?>
