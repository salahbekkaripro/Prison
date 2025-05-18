<?php
session_start();
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
require_once '../../includes/check_role.php';

// Vérifie si l'utilisateur est administrateur
checkRole('admin');
require_once '../../includes/head.php';
require_once '../../includes/header.php';
require_once '../../includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2c3e50, #34495e);
            color: #fff;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 40px 0;
        }

        h1 {
            font-size: 3rem;
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-out;
        }

        p {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 30px;
            animation: fadeIn 3s ease-out;
        }

        ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            animation: fadeIn 4s ease-out;
        }

        li {
            background-color: #3498db;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            width: 250px;
        }

        li a {
            display: block;
            padding: 20px;
            text-decoration: none;
            color: #fff;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        li a:hover {
            background-color: #2980b9;
            transform: translateY(-5px);
        }

        li:hover {
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            li {
                width: 100%;
                margin-bottom: 15px;
            }

            h1 {
                font-size: 2.2rem;
            }

            p {
                font-size: 1.2rem;
            }
        }

        /* Add some spacing and modernize the page */
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 40px;
            background-color: #34495e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue dans l'espace de travail de l'administrateur</h1>

        <p>Choisissez l'action que vous souhaitez effectuer :</p>

        <ul>
            <li><a href="/forum-prison/admin/manage_users.php">Gestion des utilisateurs</a></li>
            <li><a href="/forum-prison/admin/validate_post.php">Validation des posts</a></li>
            <li><a href="/forum-prison/admin/dashboard.php">Tableau de bord</a></li>
            <li><a href="/forum-prison/admin/manage_posts.php">Gérer les posts</a></li>
            <li><a href="/forum-prison/admin/manage_comments.php">Gérer les commentaires signalés</a></li>
            <li><a href="/forum-prison/views/admin/surveillance_cellule.php">Surveillance des cellules</a></li>
            <li><a href="/forum-prison/views/admin/infractions_admin.php">Gestion des infractions</a></li>
            <li><a href="/forum-prison/views/admin/planning_utilisateur.php">Gestion des plannings</a></li>
            <li><a href="/forum-prison/views/admin/ajout_planning.php">Ajouter un planning</a></li>
            <li><a href="/forum-prison/views/admin/logs.php">Logs d'activité</a></li>
            <li><a href="/forum-prison/views/admin/fouille_prisonnier.php">Fouille des prisonniers</a></li>
            <!-- Ajouter d'autres actions nécessaires -->
        </ul>
    </div>

    <div class="footer">
        <p>&copy; 2025 Prison Management System - Tous droits réservés</p>
    </div>
</body>
</html>
