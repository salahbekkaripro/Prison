<?php
if (!isset($pdo)) {
    require_once '../includes/db.php';
}

$notifCount = 0;
$pendingPostsCount = 0;
$unreadMessages = 0;
$replyNotifCount = 0;
$pendingSanctionsCount = 0;

$isAdmin = false;
$isGestionnaire = false;
$isPrisonnier = false;

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $role = $_SESSION['user']['role'] ?? '';

    $isAdmin = ($role === 'admin' || $role === 'gardien'); // Gardien = admin
    $isGestionnaire = $role === 'gestionnaire';
    $isPrisonnier = $role === 'prisonnier';

    // 🔔 Notifications
    $stmtReplyNotif = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE recipient_id = ? AND is_read = 0");
    $stmtReplyNotif->execute([$userId]);
    $replyNotifCount = (int) $stmtReplyNotif->fetchColumn();

    // 📬 Messages
    $stmtUnread = $pdo->prepare("SELECT COUNT(*) FROM private_messages WHERE receiver_id = ? AND is_read = 0");
    $stmtUnread->execute([$userId]);
    $unreadMessages = (int) $stmtUnread->fetchColumn();

    // 🔎 Sanctions en attente (gestionnaire)
    if ($isGestionnaire) {
        $stmtSanctions = $pdo->query("
            SELECT COUNT(*) 
            FROM infraction i 
            LEFT JOIN sanction s ON s.infraction_id = i.id 
            WHERE s.id IS NULL
        ");
        $pendingSanctionsCount = (int) $stmtSanctions->fetchColumn();
    }

    // Admin: commentaires signalés et posts à valider
    if ($isAdmin) {
        $stmtNotif = $pdo->query("SELECT COUNT(*) FROM comments WHERE reported = 1 AND validated_by_admin = 0");
        $notifCount = (int) $stmtNotif->fetchColumn();

        $stmtPosts = $pdo->query("SELECT COUNT(*) FROM posts WHERE is_approved = 0");
        $pendingPostsCount = (int) $stmtPosts->fetchColumn();
    }
}
?>

<nav class="navbar-box">
    <div class="nav-top">
        <a href="/forum-prison/views/home.php">🏠 Forum</a>
        <a href="/forum-prison/views/mon_planning.php">📅 Mon planning</a>



        <?php if ($isGestionnaire): ?>
            | <a href="/forum-prison/views/gestionnaire/gestion_stock.php">📦 Gestion stock</a>
            | <a href="/forum-prison/views/prisonnier/rapport_journalier.php">📋 Rapports</a>
            | <a href="/forum-prison/views/gestionnaire/gestion_sanctions.php">⚖️ Sanctions
                
                <?php if ($pendingSanctionsCount > 0): ?>
                    <span class="notif-red"><?= $pendingSanctionsCount ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>

        <?php if ($isPrisonnier): ?>
            | <a href="/forum-prison/views/prisonnier/prisonnier_dashboard.php">🏠 Tableau de bord</a>
            | <a href="/forum-prison/views/prisonnier/acheter_objet.php">🛒 Boutique</a>
            | <a href="/forum-prison/views/prisonnier/infractions_prisonnier.php">📄 Mes infractions</a>
            | <a href="/forum-prison/views/prisonnier/travail_prisonnier.php">🧱 Travail</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
            | <a href="/forum-prison/views/inbox.php">
                📬 Messages
                <span id="msg-count" class="notif-red" style="<?= $unreadMessages > 0 ? '' : 'display:none;' ?>">
                    <?= $unreadMessages ?>
                </span>
            </a>
            | <a href="/forum-prison/views/notifications.php">
                🔔 Réponses
                <span id="notif-count" class="notif-red" style="<?= $replyNotifCount > 0 ? '' : 'display:none;' ?>">
                    <?= $replyNotifCount ?>
                </span>
            </a>
            | <a href="/forum-prison/views/profil.php">👤 Profil</a>
            | <a href="/forum-prison/ajax/logout_process.php">🚪 Déconnexion</a>
        <?php else: ?>
            | <a href="/forum-prison/views/login.php">Connexion</a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="nav-bottom">
            <?php if ($isAdmin): ?>
            | <a href="/forum-prison/views/admin/.php">🏠 Tableau de bord</a>

                <!-- On redirige vers l'espace de travail -->
                <a href="/forum-prison/views/admin/work_page.php">📊 Espace de travail</a>
                <a href="/forum-prison/admin/new_post.php">🆕 Proposer un sujet</a>
            <?php endif; ?>

            <span class="username-tag">
                👤 <?= htmlspecialchars($_SESSION['user']['username']) ?>
                <?php if ($isAdmin): ?>
                    <span class="admin-badge">ADMIN</span>
                <?php elseif ($isGestionnaire): ?>
                    <span class="gestionnaire-badge">GESTION</span>
                <?php elseif ($isPrisonnier): ?>
                    <span class="prisonnier-badge">PRISONNIER</span>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>
</nav>
