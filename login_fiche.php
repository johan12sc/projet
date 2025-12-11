<?php
session_start();

// Connexion à la base de données
$host = 'localhost';
$dbname = 'infosante_ci';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenoms = htmlspecialchars(trim($_POST['prenoms']));
    $mot_de_passe = $_POST['mot_de_passe'];
    
    // Rechercher l'utilisateur
    $sql = "SELECT * FROM fiches_sante WHERE nom = ? AND prenoms = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $prenoms]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
        // Authentification réussie
        $_SESSION['fiche_id'] = $user['id'];
        header('Location: voir_fiche.php');
        exit;
    } else {
        echo "<script>alert('Nom, prénoms ou mot de passe incorrect.'); window.location.href='fiche.html';</script>";
    }
}
?>