<?php
session_start();

// --- KONFIGURACJA ---
$admin_password = "admin"; // Twoje nowe hasło
$log_file = "wizyty.txt";

// Pobieranie IP i zapisywanie wizyty
$user_ip = $_SERVER['REMOTE_ADDR'];
$date = date('d.m.Y H:i:s');
// Nie zapisujemy IP admina, żeby nie śmiecić w logach
if (!isset($_SESSION['admin'])) {
    $entry = "📅 $date | 🌐 IP: $user_ip" . PHP_EOL;
    file_put_contents($log_file, $entry, FILE_APPEND);
}

// Logowanie
if (isset($_POST['pass'])) {
    if ($_POST['pass'] === $admin_password) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Błędne hasło!";
    }
}

// Wylogowanie
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona z Panelem</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #00f2ff;
            --bg: #0a0a0c;
            --card: rgba(255, 255, 255, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        
        body { 
            background: var(--bg); 
            color: white; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Animowane tło */
        .gradient-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, #1a0b2e 0%, #0a0a0c 100%);
            z-index: -1;
        }

        /* Sekcja główna */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h1 { font-size: clamp(3rem, 10vw, 5rem); color: var(--accent); margin-bottom: 10px; }
        p { opacity: 0.6; font-size: 1.2rem; }

        /* Panel Admina */
        .admin-section {
            width: 100%;
            max-width: 800px;
            padding: 40px 20px;
            display: none; /* Ukryte domyślnie */
        }

        /* Magia: pokazywanie panelu przez #admin w URL */
        #admin:target { display: block; }

        .glass-card {
            background: var(--card);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .ip-box {
            background: rgba(0,0,0,0.5);
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
            height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            border: 1px solid var(--accent);
            color: var(--accent);
        }

        /* Formularz */
        input[type="password"] {
            width: 100%;
            padding: 15px;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .btn {
            background: var(--accent);
            color: black;
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover { transform: scale(1.05); box-shadow: 0 0 20px var(--accent); }
        .btn-logout { background: #ff4757; color: white; margin-top: 20px; }

        .error { color: #ff4757; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="gradient-bg"></div>

    <section class="hero">
        <h1>Witaj na Stronie</h1>
        <p>Przewiń w dół, aby zobaczyć więcej.</p>
    </section>

    <div id="admin" class="admin-section">
        <div class="glass-card">
            <h2 style="margin-bottom: 20px;">🛡️ Panel Zarządzania</h2>

            <?php if (!isset($_SESSION['admin'])): ?>
                <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                <form method="POST">
                    <input type="password" name="pass" placeholder="Wpisz hasło 'admin'..." required>
                    <button type="submit" class="btn">ZALOGUJ</button>
                </form>
            <?php else: ?>
                <p>Ostatnie wejścia na stronę (Adresy IP):</p>
                <div class="ip-box">
                    <?php
                    if (file_exists($log_file)) {
                        $lines = array_reverse(file($log_file));
                        foreach ($lines as $line) {
                            echo htmlspecialchars($line) . "<br>";
                        }
                    } else {
                        echo "Brak logów.";
                    }
                    ?>
                </div>
                <a href="?logout=1" class="btn btn-logout">WYLOGUJ</a>
            <?php endif; ?>
        </div>
    </div>

    <footer style="padding: 50px; opacity: 0.3;">
        &copy; 2026 Twoja Nazwa
    </footer>

</body>
</html>
