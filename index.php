<?php
session_start();

// --- KONFIGURACJA ---
$admin_password = "admin"; 
$log_file = "wizyty.txt";

// Pobieranie i zapisywanie IP
$user_ip = $_SERVER['REMOTE_ADDR'];
$date = date('d.m.Y H:i');
if (!isset($_SESSION['admin'])) {
    $entry = "$date | $user_ip" . PHP_EOL;
    file_put_contents($log_file, $entry, FILE_APPEND);
}

// Logowanie
if (isset($_POST['pass'])) {
    if ($_POST['pass'] === $admin_password) {
        $_SESSION['admin'] = true;
    } else {
        $error = "Nieprawidłowe hasło!";
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
    <title>My Universe | Personal Space</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        :root {
            --primary: #00f2ff;
            --secondary: #bc13fe;
            --bg: #050505;
            --glass: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background: var(--bg); color: white; line-height: 1.6; overflow-x: hidden; }

        /* TŁO */
        .bg-glow {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 20% 30%, #1a0b2e 0%, transparent 40%),
                        radial-gradient(circle at 80% 70%, #071a2e 0%, transparent 40%);
            z-index: -1;
        }

        /* HERO SECTION */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            animation: fadeIn 1.5s ease;
        }

        h1 { font-size: clamp(3.5rem, 12vw, 6rem); font-weight: 700; letter-spacing: -2px; line-height: 1; margin-bottom: 20px; background: linear-gradient(to right, #fff, var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero p { font-size: 1.2rem; color: rgba(255,255,255,0.6); max-width: 600px; }

        /* SOCIAL LINKS */
        .social-grid {
            display: flex; gap: 20px; margin-top: 40px; flex-wrap: wrap; justify-content: center;
        }

        .social-card {
            background: var(--glass);
            border: 1px solid var(--border);
            padding: 15px 30px;
            border-radius: 15px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            transition: 0.4s;
            backdrop-filter: blur(10px);
        }

        .social-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 242, 255, 0.2);
        }

        /* ADMIN SECTION */
        .admin-wrapper {
            display: none; /* Ukryte dopóki nie ma #admin */
            width: 100%;
            max-width: 900px;
            margin: 0 auto 100px;
            padding: 20px;
        }

        #admin:target { display: block; }

        .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(25px);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 50px;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
        }

        /* TABELA IP */
        .ip-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .ip-table th { text-align: left; padding: 15px; border-bottom: 1px solid var(--border); color: var(--primary); }
        .ip-table td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); font-family: monospace; font-size: 0.9rem; }

        /* INPUTY I PRZYCISKI */
        input[type="password"] {
            width: 100%;
            padding: 18px;
            background: rgba(0,0,0,0.3);
            border: 1px solid var(--border);
            border-radius: 15px;
            color: white;
            margin: 20px 0;
            outline: none;
        }
        input[type="password"]:focus { border-color: var(--secondary); }

        .btn {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }
        .btn:hover { opacity: 0.9; transform: scale(1.02); }

        /* STOPKA Z UKRYTYM WEJŚCIEM */
        footer { padding: 50px; text-align: center; opacity: 0.4; font-size: 0.8rem; }
        .secret-gate { color: transparent; text-decoration: none; cursor: default; }
        .secret-gate:hover { color: rgba(255,255,255,0.1); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="bg-glow"></div>

    <section class="hero">
        <h1>TWÓJ NICK</h1>
        <p>Pasjonat technologii, designu i kreatywnego kodu. Znajdziesz mnie tutaj:</p>

        <div class="social-grid">
            <a href="https://discord.com" class="social-card">Discord</a>
            <a href="https://instagram.com" class="social-card">Instagram</a>
            <a href="https://tiktok.com" class="social-card">TikTok</a>
        </div>
    </section>

    <div id="admin" class="admin-wrapper">
        <div class="glass-panel">
            <h2 style="font-size: 2rem; margin-bottom: 10px;">🛡️ Dashboard</h2>
            
            <?php if (!isset($_SESSION['admin'])): ?>
                <p style="opacity: 0.6;">Wymagana autoryzacja administratora.</p>
                <form method="POST">
                    <input type="password" name="pass" placeholder="Podaj klucz dostępu..." required>
                    <button type="submit" class="btn">AUTORYZUJ</button>
                    <?php if (isset($error)) echo "<p style='color: #ff4757; margin-top: 15px;'>$error</p>"; ?>
                </form>
            <?php else: ?>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <p style="color: var(--primary);">System aktywny. Śledzenie IP włączone.</p>
                    <a href="?logout=1" style="color: #ff4757; text-decoration: none; font-weight: bold;">WYLOGUJ</a>
                </div>
                
                <table class="ip-table">
                    <thead>
                        <tr>
                            <th>Data i godzina</th>
                            <th>Adres IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (file_exists($log_file)) {
                            $lines = array_reverse(file($log_file));
                            foreach ($lines as $line) {
                                $data = explode('|', $line);
                                if(count($data) == 2) {
                                    echo "<tr><td>$data[0]</td><td><strong>$data[1]</strong></td></tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Twoja Przestrzeń. Wszystkie prawa zastrzeżone.</p>
        <a href="#admin" class="secret-gate">.</a>
    </footer>

</body>
</html>
