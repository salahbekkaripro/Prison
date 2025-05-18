<?php
session_start();

$user = $_SESSION['user'] ?? null;
$username = $user['username'] ?? 'utilisateur';
$role = $user['role'] ?? 'user';

$redirect = $_SESSION['overlay_redirect'] ?? 'home.php';
unset($_SESSION['overlay_redirect']);

if (isset($_SESSION['overlay_force_message'])) {
    $message = $_SESSION['overlay_force_message'];
    unset($_SESSION['overlay_force_message']);
} else {
    if ($role === 'admin') {
        $message = "🛡️ Faites régner l'ordre... $username";
    } else {
        $message = "🔐 Ne troublez pas l'ordre... $username";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Redirection...</title>
  <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background: linear-gradient(145deg, #2a2a2a, #1c1c1c, #2f2f2f, #1a1a1a);
      color: #f8f8f8;
      font-family: 'Rajdhani', sans-serif;
      font-size: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      overflow: hidden;
    }
    #overlay-text {
      animation: overlayZoomGlow 3s ease-in-out infinite;
      text-align: center;
    }
    @keyframes overlayZoomGlow {
      0% { transform: scale(1); text-shadow: 0 0 4px #ccc, 0 0 10px #eee; }
      50% { transform: scale(1.03); text-shadow: 0 0 12px #fff, 0 0 24px #ddd; }
      100% { transform: scale(1); text-shadow: 0 0 4px #ccc, 0 0 10px #eee; }
    }
    #flash {
      position: fixed;
      inset: 0;
      background: white;
      opacity: 0;
      z-index: 10000;
      pointer-events: none;
    }
    #flash.show {
      animation: flashFadeInOut 1.5s ease forwards;
    }
    @keyframes flashFadeInOut {
      0% { opacity: 0; }
      30% { opacity: 1; }
      70% { opacity: 1; }
      100% { opacity: 0; }
    }
  </style>
</head>
<body>
<div id="overlay-text"></div>
<div id="flash"></div>
<?php
$sound = $_SESSION['overlay_sound'] ?? 'login.mp3';
unset($_SESSION['overlay_sound']);
?>
<audio id="sound" src="assets/sounds/<?= htmlspecialchars($sound) ?>" preload="auto"></audio>

<script>
const text = <?= json_encode($message) ?>;
const redirectUrl = <?= json_encode($redirect) ?>;
const textDiv = document.getElementById('overlay-text');
const flash = document.getElementById('flash');
const sound = document.getElementById('sound');

let i = 0;
sound.play();
const typewriter = setInterval(() => {
  if (i < text.length) {
    textDiv.textContent += text[i];
    i++;
  } else {
    clearInterval(typewriter);
    setTimeout(() => {
      flash.classList.add('show');
      setTimeout(() => {
        window.location.href = redirectUrl;
      }, 700);
    }, 1000);
  }
}, 100);
</script>
</body>
</html>
