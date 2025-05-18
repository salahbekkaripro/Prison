<?php
session_start();
require_once '../includes/db.php';

$pageTitle = "Accueil - Forum des prisonniers";
?>

<!DOCTYPE html>
<html lang="fr">
<?php include '../includes/head.php'; ?>
<?php include('../includes/header.php'); ?>

<body>
<section class="slider-scene">
  <div class="parallax-layer layer-quote">
    <blockquote id="quote" class="animated-quote"></blockquote>
    <div class="quote-controls" style="text-align: center; margin-top: 20px;">
      <button id="prev-quote" class="quote-btn">&lt;</button>
      <span id="quote-count">1 / 8</span>
      <button id="next-quote" class="quote-btn">&gt;</button>
    </div>
  </div>
</section>

<div style="text-align:center; margin-top:30px;">
  <a href="login.php" class="sort-btn">Connexion</a>
  <a href="register.php" class="sort-btn">S'inscrire</a>
</div>


  <div class="about-box">
    <h2>à propos</h2>
    <p>Ce forum est un espace libre de discussions pour les personnes incarcérées ou isolées.
       Ici, la parole circule, les histoires se racontent, les soutiens se construisent.
       Anonymat garanti.</p>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>