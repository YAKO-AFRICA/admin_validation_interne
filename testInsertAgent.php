<?php
// ======= CONFIGURATION BASE DE DONNÉES =======
$host = 'localhost';
$dbname = 'laloyale_bdrh';
$user = 'root';
$pass = '';

// ======= CONNEXION PDO =======
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "[✔] Connexion à la base réussie.\n";
} catch (PDOException $e) {
    die("[✘] Erreur de connexion : " . $e->getMessage());
}

// ======= FICHIER CSV À IMPORTER =======
$csvFile = 'agent-yako.csv';
if (!file_exists($csvFile)) {
    die("[✘] Fichier CSV introuvable : $csvFile");
}

// ======= TRAITEMENT =======
try {
    $pdo->beginTransaction();

    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ";"); // sauter l'en-tête

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            if (count($data) < 9) continue;

            $matricule = trim($data[0]);
            $nomComplet = trim(strtoupper($data[1]));
            $dateEmbauche = !empty($data[2]) ? date('Y-m-d', strtotime($data[2])) : null;
            $poste = ucfirst(trim($data[3]));
            $direction = trim($data[4]);
            $typeContrat = trim($data[5]);
            $supHierarchique = strtoupper(trim($data[6]));
            $email = trim($data[7]) ?: "agent-" . strtolower($matricule) . "@yakoafricassur.com";
            $telephone = trim(str_replace([" ", "-", "/", "."], "", $data[8]));

            if (!$matricule) continue;

            $nom = $prenom = '';
            if ($nomComplet) {
                $nomPrenom = extraireNomPrenom($nomComplet);
                $nom = $nomPrenom['nom'];
                $prenom = $nomPrenom['prenom'];
            }

            $codeSup = null;

            // ==== Vérifier si l’agent existe déjà ====
            $existingAgent = checkMatricule($matricule);
            if ($existingAgent) {
                // === MISE À JOUR ===
                $sql = "
                    UPDATE agents 
                    SET nom = ?, prenom = ?, date_embauche = ?, poste = ?, telephone = ?, email = ?, superieur_hierarchique = ?, type_contrat = ?, code_superieur_hierarchique = ?, identifiant_agent = ?, uuid = id
                    WHERE matricule = ?
                ";

                $params = [
                    $nom, $prenom, $dateEmbauche, $poste, $telephone, $email,
                    $supHierarchique, $typeContrat, $codeSup,
                    "yaav-" . strtolower($matricule), $matricule
                ];

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                echo "[↺] Mise à jour : $matricule\n";

                // Optionnel : retournerDirection()
                // $result = retourneDirection($existingAgent[0]->direction, $poste);
                // print_r($result);
            } else {
                // === INSERTION ===
                $uuid = json_decode(insertAgent($email, $nomComplet))->user->id ?? null;

                $stmt = $pdo->prepare("
                    INSERT INTO agents (matricule, nom, prenom, date_embauche, poste, direction, telephone, email, superieur_hierarchique, type_contrat, identifiant_agent, uuid)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $matricule, $nom, $prenom, $dateEmbauche, $poste, $direction,
                    $telephone, $email, $supHierarchique, $typeContrat,
                    "yaav-" . strtolower($matricule), $uuid
                ]);

                echo "[+] Insertion : $matricule\n";
            }
        }

        fclose($handle);
        $pdo->commit();
        echo "\n[✔] Import terminé avec succès.\n";
    } else {
        throw new Exception("Impossible d'ouvrir le fichier CSV.");
    }
} catch (Exception $e) {
    $pdo->rollBack();
    die("[✘] Erreur pendant l'import : " . $e->getMessage());
}


// ======= FONCTIONS UTILITAIRES =======

function checkMatricule($matricule, $champ = "matricule") {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM agents WHERE $champ = ?");
    $stmt->execute([$matricule]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function insertAgent($email, $nom) {
    $url = "http://gestionagentyako.test/api/register";

    $data = json_encode([
        "name" => $nom,
        "type" => "agent",
        "password" => "12345678",
        "email" => $email
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

function extraireNomPrenom($chaine) {
    $chaine = mb_convert_encoding(trim(preg_replace('/\s+/', ' ', $chaine)), 'UTF-8', 'auto');
    $parts = explode(' ', $chaine);

    if (count($parts) < 2) {
        return ['nom' => mb_strtoupper($chaine), 'prenom' => ''];
    }

    $nom = mb_strtoupper(array_shift($parts));
    $prenom = ucwords(mb_strtolower(implode(' ', $parts)));

    return ['nom' => $nom, 'prenom' => $prenom];
}
