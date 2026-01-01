<?php
// ============================================
// FAMILYFLIX - CONFIGURATION & AUTO-SETUP
// ============================================

// Admin credentials (CHANGE THESE!)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// Database file
define('DB_FILE', 'familyflix.db');

// Auto-setup function
function autoSetup() {
    // Create database if not exists
    if (!file_exists(DB_FILE)) {
        $db = new SQLite3(DB_FILE);
        
        // Create films table
        $db->exec("CREATE TABLE IF NOT EXISTS films (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            video_path TEXT NOT NULL,
            thumbnail_path TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create sample films (only if table is empty)
        $result = $db->query("SELECT COUNT(*) as count FROM films");
        $row = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($row['count'] == 0) {
            $sampleFilms = [
                [
                    'title' => 'Big Buck Bunny',
                    'description' => 'Animasi keluarga tentang kelinci besar yang ingin hidup damai.',
                    'video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                    'thumb' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Big_buck_bunny_poster_big.jpg/640px-Big_buck_bunny_poster_big.jpg'
                ],
                [
                    'title' => 'Elephant Dream',
                    'description' => 'Film pendek tentang mimpi gajah, penuh makna dan inspirasi.',
                    'video' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                    'thumb' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Elephants_Dream_s5_1024.png/640px-Elephants_Dream_s5_1024.png'
                ]
            ];
            
            foreach ($sampleFilms as $film) {
                $stmt = $db->prepare("INSERT INTO films (title, description, video_path, thumbnail_path) 
                                      VALUES (:title, :desc, :video, :thumb)");
                $stmt->bindValue(':title', $film['title'], SQLITE3_TEXT);
                $stmt->bindValue(':desc', $film['description'], SQLITE3_TEXT);
                $stmt->bindValue(':video', $film['video'], SQLITE3_TEXT);
                $stmt->bindValue(':thumb', $film['thumb'], SQLITE3_TEXT);
                $stmt->execute();
            }
        }
        
        $db->close();
    }
    
    // Create upload directories
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
        mkdir('uploads/videos', 0777, true);
        mkdir('uploads/thumbnails', 0777, true);
        
        // Security files
        file_put_contents('uploads/.htaccess', "Deny from all\n");
        file_put_contents('uploads/index.html', '<html><body><h1>403 Forbidden</h1></body></html>');
        file_put_contents('uploads/videos/index.html', '<html><body><h1>403 Forbidden</h1></body></html>');
        file_put_contents('uploads/thumbnails/index.html', '<html><body><h1>403 Forbidden</h1></body></html>');
    }
}

// Initialize database connection
autoSetup();
$db = new SQLite3(DB_FILE);
?>
