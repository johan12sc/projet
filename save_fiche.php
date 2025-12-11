<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'infosante_ci';
$username = 'root';  // Par défaut avec WAMP
$password = '';      // Par défaut avec WAMP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Récupération des données
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenoms = htmlspecialchars(trim($_POST['prenoms']));
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $age = intval($_POST['age']);
    $sexe = htmlspecialchars($_POST['sexe']);
    $maladie = htmlspecialchars($_POST['maladie']);
    $etat = htmlspecialchars($_POST['etat']);
    $traitement = htmlspecialchars($_POST['traitement']);
    $allergies = htmlspecialchars($_POST['allergies']);
    $groupe_sanguin = htmlspecialchars($_POST['groupe_sanguin']);
    $antecedents = htmlspecialchars($_POST['antecedents']);
    $poids = floatval($_POST['poids']);
    $taille = floatval($_POST['taille']);
    $contact_urgence = htmlspecialchars($_POST['contact_urgence']);
    $medecin_traitant = htmlspecialchars($_POST['medecin_traitant']);
    
    // Gestion de la photo
    $photo_name = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'webp'];
        $file_info = pathinfo($_FILES['photo']['name']);
        $file_extension = strtolower($file_info['extension']);
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Créer un nom unique
            $photo_name = uniqid('fiche_', true) . '.' . $file_extension;
            $upload_dir = 'uploads/photos/';
            
            // Créer le dossier si nécessaire
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Déplacer le fichier
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name)) {
                $photo_name = $upload_dir . $photo_name;
            } else {
                die("Erreur lors de l'upload de la photo.");
            }
        } else {
            die("Extension de fichier non autorisée.");
        }
    }
    
    // Vérifier si l'utilisateur existe déjà
    $check_sql = "SELECT id FROM fiches_sante WHERE nom = ? AND prenoms = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$nom, $prenoms]);
    
    if ($check_stmt->rowCount() > 0) {
        echo "<script>alert('Une fiche existe déjà pour " . $nom . " " . $prenoms . "'); window.location.href='fiche.html';</script>";
        exit;
    }
    
    // Insertion dans la base de données
    $sql = "INSERT INTO fiches_sante (nom, prenoms, mot_de_passe, age, sexe, maladie, etat, traitement, allergies, groupe_sanguin, antecedents, poids, taille, contact_urgence, medecin_traitant, photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([
            $nom, $prenoms, $mot_de_passe, $age, $sexe, $maladie, $etat, $traitement, 
            $allergies, $groupe_sanguin, $antecedents, $poids, $taille, 
            $contact_urgence, $medecin_traitant, $photo_name
        ]);
        
        echo "<script>alert('Fiche de santé créée avec succès !'); window.location.href='fiche.html';</script>";
        
    } catch(PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>