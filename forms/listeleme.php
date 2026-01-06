<?php
session_start();

// Güvenlik kontrolü - Giriş yapmamış kullanıcıları login'e yönlendir
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Cache kontrolleri
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "34273427Aa";
$dbname = "mydatabase";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM iletisim ORDER BY created_at DESC";
    $stmt = $conn->query($sql);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = "Bağlantı hatası: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İletişim Mesajları - Yönetim Paneli</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="1" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            z-index: 2;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 30px;
            background: #f8fafc;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: #4f46e5;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 2rem;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #64748b;
            font-weight: 500;
        }

        .messages-section {
            padding: 30px;
        }

        .section-title {
            font-size: 1.8rem;
            color: #1e293b;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            border-left: 5px solid #4f46e5;
        }

        .message-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .message-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .message-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item i {
            color: #4f46e5;
            font-size: 1.1rem;
            width: 20px;
        }

        .info-item span {
            font-weight: 600;
            color: #1e293b;
        }

        .message-subject {
            background: #4f46e5;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
        }

        .message-body {
            padding: 25px;
        }

        .message-text {
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #4f46e5;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #374151;
        }

        .message-date {
            text-align: right;
            margin-top: 15px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .no-messages {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .no-messages i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 20px;
            border-radius: 10px;
            margin: 20px;
            border-left: 4px solid #dc2626;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }

            .logout-btn {
                position: static;
                margin-top: 20px;
                display: inline-block;
            }

            .message-info {
                grid-template-columns: 1fr;
            }

            .container {
                margin: 10px;
                border-radius: 15px;
            }
        }

        /* Animasyon efektleri */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .message-card:nth-child(1) { animation-delay: 0.1s; }
        .message-card:nth-child(2) { animation-delay: 0.2s; }
        .message-card:nth-child(3) { animation-delay: 0.3s; }
        .message-card:nth-child(4) { animation-delay: 0.4s; }
        .message-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
            </a>
            <h1><i class="fas fa-envelope"></i> İletişim Yönetimi</h1>
            <p>Gelen mesajları görüntüleyin ve yönetin</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <div class="stats">
                <div class="stat-card">
                    <i class="fas fa-envelope"></i>
                    <h3><?php echo count($messages); ?></h3>
                    <p>Toplam Mesaj</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo count(array_filter($messages, function($msg) { 
                        return strtotime($msg['created_at']) > strtotime('-7 days'); 
                    })); ?></h3>
                    <p>Son 7 Gün</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-day"></i>
                    <h3><?php echo count(array_filter($messages, function($msg) { 
                        return date('Y-m-d', strtotime($msg['created_at'])) == date('Y-m-d'); 
                    })); ?></h3>
                    <p>Bugün</p>
                </div>
            </div>

            <div class="messages-section">
                <h2 class="section-title">
                    <i class="fas fa-comments"></i>
                    Gelen Mesajlar
                </h2>

                <?php if (empty($messages)): ?>
                    <div class="no-messages">
                        <i class="fas fa-inbox"></i>
                        <h3>Henüz mesaj bulunmuyor</h3>
                        <p>Yeni mesajlar buraya görünecektir.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="message-card">
                            <div class="message-header">
                                <div class="message-info">
                                    <div class="info-item">
                                        <i class="fas fa-user"></i>
                                        <span><?php echo htmlspecialchars($message['isim']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($message['mail']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?php echo htmlspecialchars($message['telefon']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('d.m.Y H:i', strtotime($message['created_at'])); ?></span>
                                    </div>
                                </div>
                                <div class="message-subject">
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($message['konu']); ?>
                                </div>
                            </div>
                            <div class="message-body">
                                <div class="message-text">
                                    <?php echo nl2br(htmlspecialchars($message['mesaj'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Smooth scroll animasyonu
        document.querySelectorAll('.message-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Çıkış onaylama
        document.querySelector('.logout-btn').addEventListener('click', function(e) {
            if (!confirm('Çıkış yapmak istediğinizden emin misiniz?')) {
                e.preventDefault();
            }
        });

        // Otomatik yenileme (isteğe bağlı)
        // setInterval(() => {
        //     location.reload();
        // }, 30000); // 30 saniyede bir yenile
    </script>
</body>
</html>