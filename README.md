# 🧱 Forum Prison – PHP/MySQL Forum immersif

Plateforme de discussion immersive avec rôles (membres/détenus/admin), effets animés, modération avancée, messagerie privée, votes, notifications et système de bannissement dynamique.

---

## 🚀 Fonctionnalités principales

- 👤 Comptes utilisateurs : inscription, connexion, avatar, bio, historique, rôles
- 💬 Forum dynamique : création de topics, commentaires imbriqués (type Reddit)
- 🗳️ Votes ▲▼ AJAX sur commentaires
- 🏷️ Système de tags + tri par popularité, date ou catégorie
- 🚩 Signalement + modération admin avec logs et validation manuelle
- 🔔 Notifications dynamiques (commentaires, messages, alertes)
- 📬 Messagerie privée avec effets visuels (Snap Thanos, autodestruction)
- 🛑 Bannissement immersif (temporaire ou permanent)
- 🎨 UI glassmorphism noir/orange, transitions parallax, effets immersifs

---

## 🗂️ Structure du projet

### 📁 **Racine**

| Fichier | Rôle |
|--------|------|

| `index.php`| Page d'accueil animée |
| `home.php` | Liste des discussions |
| `post.php` | Discussion + commentaires |
| `register.php` / `login.php` | Authentification avec captcha |
| `profil.php` | Affichage et édition du profil |
| `logout.php` | Déconnexion immersive |
| `ban_notice.php` | Animation de bannissement |
| `submit_comment.php`, `vote_comment.php` | Actions de base |
| `notifications.php` | Centre de notifications |
| `upload_avatar.php`, `update_bio.php` | Gestion utilisateur |
| `overlay_redirect.php` | Texte animé + redirection |
| `captcha_image.php`, `verify_captcha.php` | Captcha personnalisé |

---

### 📁 **admin/** *(accès restreint)*

| Fichier | Rôle |
|--------|------|
| `manage_users.php` | Bannir, promouvoir, rétrograder |
| `manage_comments.php` | Modérer les commentaires |
| `validate_post.php` | Valider les topics proposés |
| `logs.php` | Journal des actions admin |
| `edit_comment.php`, `edit_post.php` | Édition manuelle |
| `dashboard.php` | Vue admin globale |

---

### 📁 **ajax/** *(appelé par JS)*

| Fichier | Rôle |
|--------|------|
| `submit_comment.php` | Réponse AJAX |
| `vote_comment.php` | Vote AJAX |
| `report_comment.php` | Signaler un commentaire |
| `validate_post.php` | Validation admin dynamique |
| `delete_*.php`, `edit_*.php` | Modifications en direct |

---

### 📁 **messages/** *(messagerie privée)*

| Fichier | Rôle |
|--------|------|
| `inbox.php`, `sent.php` | Réception/envoi |
| `new_message.php`, `send.php` | Création d’un message |
| `view_*.php` | Affichage avec effet Thanos |
| `delete_*.php` | Suppression manuelle ou auto |

---

### 📁 **includes/**

| Fichier | Rôle |
|--------|------|
| `db.php` | Connexion MySQL |
| `functions.php` | Fonctions globales |
| `header.php`, `navbar.php` | Composants HTML |
| `head.php` / `footer.php` | Balises communes |

---

### 📁 **assets/**

- **`css/`** – Styles (thème, overlay, inscription, animation)
- **`js/`** – Transitions, AJAX, parallax, effets, gestion formulaire
- **`sounds/`** – Sons immersifs
- **`videos/`** – Vidéos de fond ou transitions
- **`fonts/`, `img/`** – Polices personnalisées et images

---

## 🧠 Technologies

- **PHP 8+** / **MySQL** (via XAMPP)
- JavaScript (Vanilla)
- CSS (glassmorphism + animations custom)
- AJAX pour toutes les interactions fluides

---

## 🔐 Sécurité

- Protection via rôles (`require_admin_login()`, `require_user_login()`)
- Vérification des IDs, des sessions, captcha anti-bot
- Prévention double vote, accès restreint, injection SQL protégée

---

## 📌 Auteur

Mohamed Boughmadi ([@Boughma](https://github.com/Boughma))  
Projet développé dans le cadre d’un forum immersif thématique "prison".

---

