<?php

/************************************************
 * CONFIGURATION
 ************************************************/
$host = "51.255.64.8";
$dbname = "laloyale_nsilweb";
$user = "laloyale_masterdev";
$pass = "1Mot2Passe.DSI";


/************************************************
 * CONNEXION PDO
 ************************************************/
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

/************************************************
 * PARAMÈTRES D’ENTRÉE
 ************************************************/
$pidcontrat = 2862238;
$pdateevaluation = '16/12/2025'; // FORMAT DD/MM/AAAA

$pidcontrat = !empty($_REQUEST['pidcontrat']) ? intval($_REQUEST['pidcontrat']) : null;
$pdateevaluation = !empty($_REQUEST['pdateevaluation']) ? date('d/m/Y', strtotime($_REQUEST['pdateevaluation'])) : null;

if (empty($pidcontrat) || empty($pdateevaluation)) {
    die("Paramètres d'entrée manquants");
} else {
    print "Paramètres d'entrée OK : pidcontrat=$pidcontrat, pdateevaluation=$pdateevaluation";

    $tabloCritere = [
        'idContrat' => $pidcontrat
    ];

    $result_encaissementBis = getAPI($tabloCritere, "https://api.laloyalevie.com/oldweb/encaissement-bis");

    print_r($result_encaissementBis);
    if ($result_encaissementBis) {
        if (isset($result_encaissementBis->error) && $result_encaissementBis->error == true) {

            print $result_encaissementBis->message;
        } else {

            $encaissementDetails = $result_encaissementBis->details[0];
            print_r($encaissementDetails);
            if (!empty($result_encaissementBis->details) && $encaissementDetails->OnStdbyOff == 1) {
            } else {
                print "contrat arrete / en veille";
            }
            //     $resultat = array("result" => "SUCCES", "type" => "EncaissementBis", "total" => count($result->details), "data" => $result->details, "form" => $parm_form);
            // } else {
            //     $resultat = array("result" => "NULL", "total" => 0, "type" => "EncaissementBis", "data" => $result_encaissementBis->details, "form" => $parm_form);
            // }
        }
    } else {
        print "-1";
    }
}


/************************************************
 * APPEL DE LA PROCÉDURE STOCKÉE
 ************************************************/
try {

    $sql = "
		CALL calcpm(
			:pidcontrat,
			:pdateevaluation,
			@datefinadhesion,
			@vaenc,
			@vaprest,
			@vpenalite,
			@vrachat,
			@pm
		)
	";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':pidcontrat', $pidcontrat, PDO::PARAM_INT);
    $stmt->bindValue(':pdateevaluation', $pdateevaluation, PDO::PARAM_STR);
    $stmt->execute();

    // IMPORTANT : libérer les jeux de résultats internes
    $stmt->closeCursor();
} catch (PDOException $e) {
    die("Erreur appel procédure : " . $e->getMessage());
}

/************************************************
 * RÉCUPÉRATION DES PARAMÈTRES OUT
 ************************************************/
$result = $pdo->query("
	SELECT
		@datefinadhesion AS datefinadhesion,
		@vaenc AS vaenc,
		@vaprest AS vaprest,
		@vpenalite AS vpenalite,
		@vrachat AS vrachat,
		@pm AS pm
")->fetch();

/************************************************
 * AFFICHAGE DU RÉSULTAT
 ************************************************/
echo "<pre>";
print_r([
    "ID Contrat"         => $pidcontrat,
    "Date évaluation"    => $pdateevaluation,
    "Date fin adhésion"  => $result['datefinadhesion'],
    "Valeur encaissement" => $result['vaenc'],
    "Valeur prestation" => $result['vaprest'],
    "Pénalité"           => $result['vpenalite'],
    "Valeur rachat"      => $result['vrachat'],
    "Provision math."   => $result['pm']
]);
echo "</pre>";


function getAPI($tabloCritere, $url_api)
{
    try {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 2);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($tabloCritere));
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 60000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("cache-control: no-cache", "content-type: application/json",));
        $data = curl_exec($ch);
        $data = json_decode($data);
        return $data;
    } catch (Exception $e) {
        echo 'Exception reçue : ', $e->getMessage(), "\n";
        return 0;
    }
}
