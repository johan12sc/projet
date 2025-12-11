<?php
session_start();

if (!isset($_SESSION['fiche_id'])) {
    header('Location: fiche.html');
    exit;
}

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

// Récupérer les données de la fiche
$sql = "SELECT * FROM fiches_sante WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['fiche_id']]);
$fiche = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fiche) {
    die("Fiche introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Fiche de Santé - <?php echo htmlspecialchars($fiche['prenoms'] . ' ' . $fiche['nom']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card { animation: slideIn 0.6s ease-out; }
        
        .health-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
        }
        
        .info-box {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .profile-photo {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-50 min-h-screen py-12 px-4">

    <div class="container mx-auto max-w-5xl">
        
        <!-- HEADER BUTTONS -->
        <div class="flex justify-between mb-8 no-print">
            <a href="fiche.html" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
            <div class="flex gap-4">
                <button onclick="window.print()" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-print mr-2"></i> Imprimer
                </button>
                <a href="logout.php" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                    <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- CARTE DE SANTÉ -->
        <div class="health-card mb-8 card">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="flex-shrink-0">
                    <?php if (!empty($fiche['photo'])): ?>
                        <img src="<?php echo htmlspecialchars($fiche['photo']); ?>" alt="Photo" class="profile-photo">
                    <?php else: ?>
                        <div class="profile-photo bg-white flex items-center justify-center">
                            <i class="fas fa-user text-6xl text-gray-400"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-bold mb-2">
                        <?php echo htmlspecialchars($fiche['prenoms'] . ' ' . $fiche['nom']); ?>
                    </h1>
                    <div class="flex flex-wrap gap-4 justify-center md:justify-start text-lg">
                        <span><i class="fas fa-birthday-cake mr-2"></i><?php echo $fiche['age']; ?> ans</span>
                        <span><i class="fas fa-venus-mars mr-2"></i><?php echo htmlspecialchars($fiche['sexe']); ?></span>
                        <?php if ($fiche['groupe_sanguin']): ?>
                            <span><i class="fas fa-tint mr-2"></i><?php echo htmlspecialchars($fiche['groupe_sanguin']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4 inline-block bg-white bg-opacity-20 px-4 py-2 rounded-lg">
                        <i class="fas fa-heartbeat mr-2"></i>
                        <strong>État:</strong> <?php echo htmlspecialchars($fiche['etat']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- INFORMATIONS MÉDICALES -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            
            <!-- MALADIE -->
            <?php if ($fiche['maladie']): ?>
            <div class="info-box card">
                <h3 class="text-xl font-bold text-red-600 mb-3">
                    <i class="fas fa-virus mr-2"></i> Maladie Principale
                </h3>
                <p class="text-gray-700"><?php echo htmlspecialchars($fiche['maladie']); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- TRAITEMENT -->
            <?php if ($fiche['traitement']): ?>
            <div class="info-box card">
                <h3 class="text-xl font-bold text-blue-600 mb-3">
                    <i class="fas fa-pills mr-2"></i> Traitement
                </h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($fiche['traitement'])); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- ALLERGIES -->
            <?php if ($fiche['allergies']): ?>
            <div class="info-box card">
                <h3 class="text-xl font-bold text-yellow-600 mb-3">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Allergies
                </h3>
                <p class="text-gray-700"><?php echo htmlspecialchars($fiche['allergies']); ?></p>
            </div>
            <?php endif; ?>
            
            <!-- ANTÉCÉDENTS -->
            <?php if ($fiche['antecedents']): ?>
            <div class="info-box card">
                <h3 class="text-xl font-bold text-purple-600 mb-3">
                    <i class="fas fa-history mr-2"></i> Antécédents
                </h3>
                <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($fiche['antecedents'])); ?></p>
            </div>
            <?php endif; ?>
            
        </div>

        <!-- MESURES & CONTACTS -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            
            <?php if ($fiche['poids']): ?>
            <div class="info-box card text-center">
                <i class="fas fa-weight text-4xl text-green-600 mb-3"></i>
                <p class="text-gray-600 text-sm">Poids</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $fiche['poids']; ?> kg</p>
            </div>
            <?php endif; ?>
            
            <?php if ($fiche['taille']): ?>
            <div class="info-box card text-center">
                <i class="fas fa-ruler-vertical text-4xl text-blue-600 mb-3"></i>
                <p class="text-gray-600 text-sm">Taille</p>
                <p class="text-2xl font-bold text-gray-800"><?php echo $fiche['taille']; ?> cm</p>
            </div>
            <?php endif; ?>
            
            <div class="info-box card text-center">
                <i class="fas fa-phone-alt text-4xl text-red-600 mb-3"></i>
                <p class="text-gray-600 text-sm">Contact d'Urgence</p>
                <p class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($fiche['contact_urgence']); ?></p>
            </div>
            
        </div>

        <!-- MÉDECIN TRAITANT -->
        <?php if ($fiche['medecin_traitant']): ?>
        <div class="info-box card">
            <h3 class="text-xl font-bold text-teal-600 mb-3">
                <i class="fas fa-user-md mr-2"></i> Médecin Traitant
            </h3>
            <p class="text-gray-700"><?php echo htmlspecialchars($fiche['medecin_traitant']); ?></p>
        </div>
        <?php endif; ?>

        <!-- FOOTER -->
        <div class="text-center mt-12 text-gray-600">
            <p class="text-sm">
                <i class="fas fa-calendar-alt mr-2"></i>
                Fiche créée le <?php echo date('d/m/Y', strtotime($fiche['date_creation'])); ?>
            </p>
            <p class="text-xs mt-2">InfoSanté CI - Votre Santé, Notre Mission</p>
        </div>

    </div>

</body>
</html>