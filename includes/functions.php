<?php
function envoyer_annonce_generale(PDO $pdo, string $message) {
    // ⚠️ Remplace ici par l'ID du user 'system'
    $system_id = 1;

    $stmt = $pdo->query("SELECT id FROM users WHERE is_banned = 0");
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $now = date('Y-m-d H:i:s');

    $insert = $pdo->prepare("
        INSERT INTO notifications (recipient_id, sender_id, type, message, is_read, created_at)
        VALUES (?, ?, 'annonce_generale', ?, 0, ?)
    ");

    foreach ($utilisateurs as $user_id) {
        $insert->execute([$user_id, $system_id, $message, $now]);
    }
}

// 🔒 Bloque le prisonnier s'il a une sanction active de type "mise_au_trou"
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'prisonnier') {
    $userId = $_SESSION['user']['id'];

    $stmt = $pdo->prepare("
        SELECT s.id, s.fin_sanction
        FROM sanction s
        JOIN prisonnier p ON s.prisonnier_id = p.id
        WHERE p.utilisateur_id = ?
          AND s.type_sanction = 'mise_au_trou'
        ORDER BY s.date_sanction DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    if ($row) {
        $fin = $row['fin_sanction'];

        if (!$fin || strtotime($fin) > time()) {
            // Sanction toujours active
            header("Location: views/cachot.php");
            exit;
        } else {
            // Libération automatique : suppression ou mise à jour optionnelle
            $stmt = $pdo->prepare("DELETE FROM sanction WHERE id = ?");
            $stmt->execute([$row['id']]);
        }
    }
}


function is_admin_logged_in(): bool {
    return isset($_SESSION['admin']);
}

function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: ../admin/login.php');
        exit;
    }
}

function is_user_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_user_login() {
    if (!is_user_logged_in()) {
        header('Location: /forum-prison/login.php');
        exit;
    }
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function showOverlayRedirect($message, $redirectUrl, $forceMessage = null, $sound = 'login.mp3') {
    if ($forceMessage !== null) {
        $_SESSION['overlay_force_message'] = $forceMessage;
    }
    $_SESSION['overlay_redirect'] = $redirectUrl;
    $_SESSION['overlay_sound'] = $sound;
    header("Location: overlay_redirect.php");
    exit;
}


function verifier_degradation_sante(PDO $pdo, int $prisonnier_id): ?string {
    $stmt = $pdo->prepare("SELECT etat, derniere_maj_etat FROM prisonnier WHERE id = ?");
    $stmt->execute([$prisonnier_id]);
    $data = $stmt->fetch();

    if (!$data) return null;

    $etatActuel = $data['etat'];
    $dateMaj = $data['derniere_maj_etat'];

    if (!$dateMaj || $etatActuel === 'décédé') return null;

    $jours = (new DateTime())->diff(new DateTime($dateMaj))->days;
    if ($jours < 7) return null;

    $nextEtat = match ($etatActuel) {
        'sain' => 'malade',
        'malade' => 'blessé',
        'blessé' => 'décédé',
        default => null
    };

    if ($nextEtat) {
        $pdo->prepare("UPDATE prisonnier SET etat = ?, derniere_maj_etat = CURDATE() WHERE id = ?")
            ->execute([$nextEtat, $prisonnier_id]);
        return $nextEtat;
    }

    return null;
}
