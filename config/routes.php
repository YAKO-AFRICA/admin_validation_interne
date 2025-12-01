<?php

session_start();

include("../autoload.php");
/*$fonction = new  fonction();
$dbAcces = new dbAcess();
*/

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'];

$lienEnvoiMail = "$url/notification-mail.php?";
$maintenant =  @date('Y-m-d H:i:s');

if ($request->action != null) {
    switch ($request->action) {
        case "connexion":


            $passW = GetParameter::FromArray($_REQUEST, 'passW');
            $login = GetParameter::FromArray($_REQUEST, 'login');

            if ($passW != null && $login != null) {
                $plus = " AND login = '$login' AND password='$passW'  ";
                $retourUsers = $fonction->_GetUsers($plus);
                if ($retourUsers != NULL) {

                    $_SESSION["id"] = $retourUsers->id;
                    $_SESSION["typeCompte"] = $retourUsers->typeCompte;
                    $_SESSION["utilisateur"] = $retourUsers->userConnect;
                    $_SESSION["profil"] = $retourUsers->profil;
                    $_SESSION["cible"] = $retourUsers->cible;
                    $_SESSION["codeagent"] = $retourUsers->codeagent;
                    $_SESSION["infos"] = $retourUsers->infos;

                    echo json_encode($retourUsers->infos);
                } else echo json_encode("-1");
            } else echo json_encode("-1");

            break;

        case 'motdepasseOublie':

            $loginPO = GetParameter::FromArray($_REQUEST, 'login');
            $email = GetParameter::FromArray($_REQUEST, 'email');

            //print_r($_REQUEST);

            if (isset($loginPO) && $loginPO != null) {
                $plus = " AND login = '$loginPO' AND etat = '1' ";

                $sqlSelect = "SELECT * FROM users  WHERE login = '$loginPO'  ";
                $retourUsers = $fonction->_getSelectDatabases($sqlSelect);
                //$retourUsers = $fonction->_GetUsers($plus);
                if ($retourUsers != null) {

                    $users = new users($retourUsers[0]);
                    if ($users->etat != "1") {
                        $result = array("result" => "ERROR", "code" => '100', "data" =>  "Desolé ce compte est desactivé !!");
                    } else {
                        if (isset($users->email) && $users->email != null) {
                            $url_notification = $url . "/recuperation-mail?i=" . trim($users->id) . "&p=rp-" . date('YmdHis');
                            // file_get_contents($url_notification);
                            $result = array("result" => "SUCCESS", "code" => '0', "data" =>  "Merci de continuer le traitement en suivant le lien envoyé par mail a l'adresse " . $users->email . " !!");
                        } else {
                            $result = array("result" => "ERROR", "code" => '101', "data" =>  "Merci de contacter l'administrateur !!");
                        }
                    }
                    echo json_encode($result);
                } else {
                    echo json_encode("-1");
                }
            } else {
                echo json_encode("-1");
            }

            break;

        case "intro":

            $type = GetParameter::FromArray($_REQUEST, 'type');

            if ($type == Config::TYPE_SERVICE_PRESTATION) {
                $retourStatut = $fonction->_recapGlobalePrestations();
                $global = $fonction->pourcentageAllTypePrestation();

                $result = array(
                    "retourStatut" => $retourStatut,
                    "global" => $global
                );
                echo json_encode($result);
            } elseif ($type == Config::TYPE_SERVICE_RDV) {

                $retourStatut = $fonction->pourcentageRDVBy("statut");
                $retourStatutVille = $fonction->pourcentageRDVBy("ville");
                $retourStatutuser = $fonction->pourcentageRDVBy("user");
                $retourStatutType = $fonction->pourcentageRDVBy("type");
                $result = array(
                    "retourStatut" => $retourStatut,
                    "retourStatVille" => $retourStatutVille,
                    "retourStatuser" => $retourStatutuser,
                    "retourStatutType" => $retourStatutType
                );
                echo json_encode($result);
            } else {
                echo json_encode("-1");
            }
            break;

        case "modifierPasse":

            $id = GetParameter::FromArray($_REQUEST, 'idusers');
            $pass2 = GetParameter::FromArray($_REQUEST, 'pass2');
            $pass1 = GetParameter::FromArray($_REQUEST, 'pass1');

            $plus = " AND id = '$id'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {

                $result = $fonction->_UpdateMotDePasse($retourUsers, $pass1);
                echo json_encode($result);
            } else {
                echo json_encode("-1");
            }

            break;


        case "passeOublie":

            $emailPro = GetParameter::FromArray($_REQUEST, 'emailPro');

            $plus = " AND `login` = '$emailPro'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {

                if (strlen($retourUsers->password) > 10) {

                    if (isset($retourUsers->telephone) && $retourUsers->telephone != "") $newpasse =  substr($retourUsers->telephone, -8);
                    else $newpasse = "1234567";

                    $result = $fonction->_UpdateMotDePasse($retourUsers, $newpasse);
                } else {
                    $newpasse = $retourUsers->password;
                }

                echo json_encode($retourUsers->userConnect);
                //$url_notification = "http://localhost/mes-projets/yako-africa/admin-prestation/notification-mail.php?action=passeOublie&id=" . trim($retourUsers->id);
                //file_get_contents($url_notification);
            } else {
                echo json_encode("-1");
            }
            break;


        case "checkUsers":
            $idusers = GetParameter::FromArray($_REQUEST, 'idusers');
            $plus = " AND `id` = '$idusers'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {
                echo json_encode($retourUsers);
            } else {
                echo json_encode("-1");
            }
            break;

        case "ModifierMesInfos":

            $idusers = GetParameter::FromArray($_REQUEST, 'idusers');
            $nom = GetParameter::FromArray($_REQUEST, 'nom');
            $prenoms = GetParameter::FromArray($_REQUEST, 'prenoms');
            $telephone = GetParameter::FromArray($_REQUEST, 'telephone');
            $email = GetParameter::FromArray($_REQUEST, 'email');
            $mobile2 = GetParameter::FromArray($_REQUEST, 'mobile2');

            $plus = " AND `id` = '$idusers'  ";
            $retourUsers = $fonction->_GetUsers($plus);
            if ($retourUsers != NULL) {

                $result = $fonction->_UpdateInformationUsers($retourUsers, $nom, $prenoms, $telephone, $email, $mobile2);
                echo json_encode($result);
            } else {
                echo json_encode("-1");
            }
            break;

        case "confirmerRejetRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $motif = GetParameter::FromArray($_REQUEST, 'motif');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');

            // list($idmotif, $motif) = explode('|', $motif, 2);
            //$retour = $fonction->_getRetournePrestation(" WHERE id='" . trim($idrdv) . "'");
            $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $rdv = $retour[0];
                $idmotif = "";
                $etat = "2";
                $retour = traitementApresReceptionRDV($rdv, "1", "", $observation);
                echo json_encode($retour);
            } else echo json_encode("-1");

            break;

        case "operationRDVReception":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'gestionnaire');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $objetRDV = GetParameter::FromArray($_REQUEST, 'objetRDV');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $resultatOpe = GetParameter::FromArray($_REQUEST, 'resultatOpe');
            $observation = GetParameter::FromArray($_REQUEST, 'obervation');


            //$retour = $fonction->_getRetournePrestation(" WHERE id='" . trim($idrdv) . "'");
            $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $rdv = $retour[0];
                //print_r($rdv);

                $tablo = array(
                    "partielle" => array("etat" => "2", "libelle" => "Le client a demandé un rachat partiel", "operation" => "Rachat partiel"),
                    "avance" => array("etat" => "2", "libelle" => "Le client a demandé une avance / pret", "operation" => "Avance ou prêt"),
                    "renonce" => array("etat" => "3", "libelle" => "Le client décide de conserver son contrat", "operation" => "Renonce"),
                    "absent" => array("etat" => "3", "libelle" => "Le client ne c'est pas presenté", "operation" => "Absent"),
                    "transformation" => array("etat" => "4", "libelle" => "Le client a demandé une transformation", "operation" => "transformation"),
                    "autres" => array("etat" => "3", "libelle" => "Autre observation", "operation" => "Autres")
                );


                $rrr = $tablo[strtolower($resultatOpe)];
                $etat = $rrr["etat"];
                $libelleTraitement = $rrr["libelle"];
                $operation = $rrr["operation"];

                switch (strtolower($resultatOpe)) {

                    case "transformation":
                        $retour = traitementApresReceptionRDVAutres($rdv, $etat, $libelleTraitement, $observation, $resultatOpe);
                        echo json_encode($resultatOpe);
                        break;
                    case "partielle" || "avance":
                        $retour = traitementApresReceptionRDV($rdv, $etat, $libelleTraitement, $observation, $operation);
                        echo json_encode($retour);
                        break;

                    default:
                        $retour = traitementApresReceptionRDVAutres($rdv, $etat, $libelleTraitement, $observation, $resultatOpe);
                        echo json_encode($retour);
                        break;
                }
            }
            break;

        case "listeMotifRejet":


            $retourListeMotifRejet = $fonction->_GetListeMotifRejet();

            if (!empty($retourListeMotifRejet)) {
                // Réindexation du tableau si nécessaire
                $retourListeMotifRejet = array_values($retourListeMotifRejet);
                echo json_encode($retourListeMotifRejet);
            } else {
                // En cas de données vides, renvoyer un tableau vide
                echo json_encode([]);
            }
            break;

        case "confirmerRejet":

            $idprestation = GetParameter::FromArray($_REQUEST, 'idprestation');
            $motif = GetParameter::FromArray($_REQUEST, 'motif');
            $traiterpar = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');
            // list($idmotif, $motif) = explode('|', $motif, 2);

            $retour = $fonction->_getRetournePrestation(" WHERE id='" . trim($idprestation) . "'");
            if ($retour != null) {
                $prestation = new tbl_prestations($retour[0]);
                $idmotif = "";

                if ($motif != null) {
                    $tablos = explode(',', $motif);

                    foreach ($tablos as $tablo) {
                        // Exemple de décomposition "id|libelle"
                        list($codemotif, $libelle) = explode('|', $tablo);
                        //echo "ID: $codemotif, Libellé: $libelle<br>";
                        $idmotif = $codemotif;

                        //SELECT * FROM `tbl_motifrejetbyprestats`
                        $sqlQuery = "SELECT * FROM tbl_motifrejetbyprestats WHERE codemotif='" . $codemotif . "' and codeprestation='" . $prestation->code . "' LIMIT 1 ";
                        $resultatMotif = $fonction->_getSelectDatabases($sqlQuery);

                        if ($resultatMotif != NULL) {
                            $sqlUpdatePrestation = "UPDATE tbl_motifrejetbyprestats SET updated_at= ? WHERE id = ?";
                            $queryOptions = array(
                                $maintenant,
                                $resultatMotif[0]->id
                            );

                            $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
                        } else {
                            $fonction->_InsertMotifRejetPrestation($prestation->code, $codemotif, $maintenant);
                        }
                    }

                    $result = $fonction->_UpdatePrestationRejet($prestation, $traiterpar, $idmotif, $observation);
                    if ($result != null) {
                        $retour = $prestation->code;

                        $numero = "225" . substr($prestation->cel, -10);
                        $sms_envoi = new SMSService();
                        $ref_sms = "YAAV-SMS-" . $prestation->id;
                        $message = "Cher client(e), votre prestation n° " . $prestation->code . " a été rejetée." . PHP_EOL . " Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";
                        if (strlen($message) > 160) $message = substr($message, 0, 160);

                        $sms_envoi->sendOtpInfobip($numero, $message,  "YAKO AFRICA");

                        $url_notification = $lienEnvoiMail . "action=confirmerRejet&data=[idprestation:" . trim($idprestation) . "]";
                        file_get_contents($url_notification);
                    } else $retour = 0;
                }

                echo json_encode($retour);
            } else echo json_encode("-1");

            break;

        case "validerprestation":

            $idprestation = GetParameter::FromArray($_REQUEST, 'idprestation');
            $code = GetParameter::FromArray($_REQUEST, 'code');
            $traiterpar = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $typeOpe = GetParameter::FromArray($_REQUEST, 'typeOpe');
            $ListeOpe = GetParameter::FromArray($_REQUEST, 'ListeOpe');
            $delaiTrait = GetParameter::FromArray($_REQUEST, 'delaiTrait');
            $provenance = GetParameter::FromArray($_REQUEST, 'provenance');
            $ListePartenaire = GetParameter::FromArray($_REQUEST, 'ListePartenaire');

            if ($ListePartenaire != null) {
                list($idpartenaire, $codePartenaire, $nomPartenaire) = explode('-', $ListePartenaire, 3);
            } else {
                $idpartenaire = null;
                $codePartenaire = null;
                $nomPartenaire = null;
            }




            $retour = $fonction->_getRetournePrestation(" WHERE id='" . trim($idprestation) . "'");
            if ($retour != null) {
                $prestation = new tbl_prestations($retour[0]);

                list($keyType, $typeOperation) = explode("-", $typeOpe);
                $tablo = explode("-", $ListeOpe);
                $count = count($tablo);
                if ($count > 1) {
                    $CodeTypeAvenant = $tablo[0];
                    $DelaiTraitement = $tablo[1];
                    $operation = $tablo[2];
                }

                $retourDetail = $fonction->_GetDetailsTraitementPrestation($prestation->id);
                if ($retourDetail != null) {
                    $result = $retourDetail;
                } else {
                    $result = $fonction->_InsertDetailPrestation($CodeTypeAvenant, $operation, $DelaiTraitement, $idcontrat, $prestation->id, $maintenant);
                }

                if ($result != null) {

                    $numero = "225" . substr($prestation->cel, -10);
                    $fonction->_UpdatePrestationValiderNSIL($prestation, $traiterpar, $codePartenaire);
                    $resultat = array("result" => "SUCCESS", "total" => '0', "data" =>  "validation de la prestation");

                    $sms_envoi = new SMSService();
                    $ref_sms = "YAAV-SMS-" . $prestation->id;
                    $message = "Cher client(e), votre prestation n° " . $prestation->code . " a été acceptée." . PHP_EOL . " Consultez son état sur votre espace personnel : urlr.me/9ZXGSr";
                    if (strlen($message) > 160) $message = substr($message, 0, 160);

                    $sms_envoi->sendOtpInfobip($numero, $message,  "YAKO AFRICA");

                    //$url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=validerprestation&idprestation=" . trim($idprestation);
                    $url_notification = $lienEnvoiMail . "action=validerprestation&data=[idprestation:" . trim($idprestation) . "]";
                    file_get_contents($url_notification);
                } else {
                    $resultat = array("result" => "ERROR", "total" => '0', "data" =>  "erreur lors de l'enregistrement du détail de la prestation");
                }
            } else {
                $resultat = array("result" => "ERROR", "total" => '0', "data" =>  "erreur lors de la recherche de la prestation");
            }

            echo json_encode($resultat);

            break;

        case "exporterExcel":
            $search = GetParameter::FromArray($_REQUEST, 'search');
            $resultat = $fonction->_getRetournePrestation($search);
            if ($resultat != null) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;


        case "afficherGestionnaire":

            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $sqlQuery = "SELECT users.id , users.email , users.codeagent ,  TRIM(CONCAT(users.nom ,' ', users.prenom)) as gestionnairenom ,tblvillebureau.libelleVilleBureau as villeEffect FROM users INNER JOIN tblvillebureau ON tblvillebureau.idVilleBureau = users.ville WHERE  users.etat='1' AND tblvillebureau.idVilleBureau='$idVilleEff'  ORDER BY users.id ASC";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;

        case "ListCompteurGestionnaire":

            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $idgestionnaire = GetParameter::FromArray($_REQUEST, 'idusers');

            //$sqlQuery = "SELECT users.id , users.email , users.codeagent ,  TRIM(CONCAT(users.nom ,' ', users.prenom)) as gestionnairenom ,tblvillebureau.libelleVilleBureau as villeEffect FROM users INNER JOIN tblvillebureau ON tblvillebureau.idVilleBureau = users.ville WHERE  users.etat='1' AND tblvillebureau.idVilleBureau='$idVilleEff'  ORDER BY users.id ASC";
            $params = "";
            if ($idgestionnaire != null) {
                $params = " AND tblrdv.gestionnaire='$idgestionnaire' ";
            }

            $sqlQuery = "SELECT  u.id,    u.email,     u.codeagent,  TRIM(CONCAT(u.nom, ' ', u.prenom)) AS gestionnairenom,  v.libelleVilleBureau AS villeEffect,  COUNT(r.idrdv) AS totalrdv
                FROM     users u  INNER JOIN      tblvillebureau v ON v.idVilleBureau = u.ville 
                INNER JOIN      tblrdv r ON r.gestionnaire = u.id   AND DATE(r.daterdveff) = '$daterdveff'  AND r.villeEffective = '$idVilleEff'  $params
                WHERE     u.etat = '1'     AND v.idVilleBureau = '$idVilleEff' 
                GROUP BY     u.id, u.email, u.codeagent, u.nom, u.prenom, v.libelleVilleBureau ORDER BY   totalrdv DESC ";

            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;

        case "ListCompteurGestionnaireByNISSA":

            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $idgestionnaire = GetParameter::FromArray($_REQUEST, 'idusers');

            $totalrdv = 0;
            $sqlQuery = "SELECT 
                            u.id,  u.email,  u.codeagent,  TRIM(CONCAT(u.nom, ' ', u.prenom)) AS gestionnairenom,
                            v.libelleVilleBureau AS villeEffect,    COUNT(r.idrdv) AS totalrdv FROM users u
                        INNER JOIN tblvillebureau v ON v.idVilleBureau = u.ville LEFT JOIN tblrdv r 
                            ON r.gestionnaire = u.id     AND DATE(r.daterdveff) = '$daterdveff'      AND r.villeEffective = '$idVilleEff'
                        WHERE     u.etat = '1'   AND v.idVilleBureau = '$idVilleEff' GROUP BY u.id, u.email, u.codeagent, u.nom, u.prenom, v.libelleVilleBureau ORDER BY u.id ASC ";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;

        case "getListeVillesTransformations":

            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');

            $params = "";
            // if ($idVilleEff != null) {
            //     $params = " AND idVilleBureau != '$idVilleEff' ";
            // }
            $sqlQuery = "SELECT * FROM laloyale_bdweb.tblvillebureau WHERE idVilleBureau NOT IN ('6','7')  $params ORDER BY idVilleBureau ";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;

        case "compteurGestionnaire":

            $idrdv = GetParameter::FromArray($_REQUEST, 'id');
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $idgestionnaire = GetParameter::FromArray($_REQUEST, 'idusers');
            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $daterdv = GetParameter::FromArray($_REQUEST, 'daterdv');
            $params = "";

            if ($idgestionnaire != null) {
                $params = " AND tblrdv.gestionnaire='$idgestionnaire' ";
            }

            $sqlQuery = "SELECT count(*) as totalrdv , tblrdv.gestionnaire , tblrdv.villeEffective , TRIM(CONCAT(users.nom ,' ', users.prenom)) as utilisateur FROM tblrdv INNER JOIN users ON tblrdv.gestionnaire = users.id WHERE date(tblrdv.daterdveff)='" . $daterdveff . "'  AND tblrdv.villeEffective='$idVilleEff' AND users.etat='1' $params GROUP BY gestionnaire,villeEffective ORDER BY totalrdv DESC";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                echo json_encode($resultat);
            } else {
                echo json_encode("-1");
            }
            break;

        case "receptionJourRdv":
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $retourJourReception = $fonction->getRetourJourReception($idVilleEff);
            $tablo = [];
            if ($retourJourReception != null) {
                foreach ($retourJourReception as $key => $value) {
                    $tablo[$key] = (int)$value->codejour;
                }
            }
            echo json_encode($tablo);
            break;

        case "compteurRdv":

            $daterdveff = GetParameter::FromArray($_REQUEST, 'daterdveff');
            $idVilleEff = GetParameter::FromArray($_REQUEST, 'idVilleEff');
            $daterdv = GetParameter::FromArray($_REQUEST, 'daterdv');
            $parms = GetParameter::FromArray($_REQUEST, 'parms');
            $retourJourReception = $fonction->getRetourJourReception($idVilleEff);


            // if ($parms == '1') {
            //     $daterdv = $daterdveff;
            //     list($annee, $mois, $jour) = explode('-', $daterdveff, 3);
            //     $daterdv = trim($jour . '/' . $mois . '/' . $annee);/**/
            // } else {
            //     /**/
            //     list($jour, $mois, $annee) = explode('/', $daterdv, 3);
            //     $daterdv = date_create($annee . '-' . $mois . '-' . $jour);
            //     $daterdv_affiche = date_format($daterdv, "d/m/Y");
            //     $daterdv = date_format($daterdv, "Y-m-d");
            // }
            // list($jour, $mois, $annee) = explode('-', $daterdveff, 3);
            // $daterdv = date_create($annee . '-' . $mois . '-' . $jour);
            // $daterdv_affiche = date_format($daterdv, "d/m/Y");
            // $daterdv = date_format($daterdv, "Y-m-d");
            $daterdv = $daterdveff;

            // // ✅ Récupération du jour de la semaine en français
            // setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
            // $timestamp = strtotime($daterdv);


            // // Affichage (pour test)
            // echo "Date : $daterdv<br>";

            $dateAfficher = date('d/m/Y', strtotime($daterdv));
            $sqlQuery = "SELECT  COUNT(*) AS totalrdv, tblrdv.villeEffective,  tblrdv.daterdveff,  MIN(tblrdv.daterdv) AS daterdv_min FROM tblrdv WHERE tblrdv.villeEffective = '$idVilleEff'   AND DATE(tblrdv.daterdveff) = '" . $daterdv . "' GROUP BY tblrdv.villeEffective, tblrdv.daterdveff ORDER BY totalrdv DESC";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {
                $retour = array("daterdv" => $dateAfficher, "idVilleEff" => $idVilleEff, "total" => count($resultat), "data" => $resultat[0], "retourJourReception" => $retourJourReception);
            } else {
                $retour = array("daterdv" => $dateAfficher, "idVilleEff" => $idVilleEff, "total" => "0", "data" => null, "retourJourReception" => $retourJourReception);
            }
            echo json_encode($retour);

            break;

        case "transmettreRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'gestionnaire');
            $objetRDV = GetParameter::FromArray($_REQUEST, 'objetRDV');
            $dateRDVEff = GetParameter::FromArray($_REQUEST, 'daterdveff');


            if ($idrdv != null  && $gestionnaire != null && $objetRDV != null && $dateRDVEff != null) {

                $datetraitement = date('d/m/Y à H:i:s');
                $etat = "2";
                $traiterpar = $_SESSION["id"];
                $reponse = "";

                list($idVilleEff, $VilleEff) = explode(';', $objetRDV, 2);
                list($idgestionnaire, $nomgestionnaire, $idvilleGestionnaire, $villesGestionnaire) = explode('|', $gestionnaire, 4);

                $sqlSelect = "SELECT *  FROM tblrdv WHERE idrdv = '" . $idrdv . "' ";
                $retour = $fonction->_getSelectDatabases($sqlSelect);
                if ($retour != null) {
                    $rdv = $retour[0];

                    //$fonction->_InsertHistorisqueRdv($idrdv);
                    $result = $fonction->_TransmettreRDVGestionnaire($etat, $reponse, $dateRDVEff, $datetraitement, $idgestionnaire, $idrdv, $idVilleEff, $traiterpar);
                    $dateeffective = date('d/m/Y', strtotime($dateRDVEff));

                    $sqlQuery2 = "SELECT id , email , codeagent , telephone,  TRIM(CONCAT(nom ,' ', prenom)) as gestionnairenom FROM users WHERE  id='" . $idgestionnaire . "' ";
                    $result2 = $fonction->_getSelectDatabases($sqlQuery2);
                    if ($result2 != NULL) {
                        $retourGestionnaire = $result2[0];
                        $telGestionnaire = $retourGestionnaire->telephone;
                        $emailGestionnaire = $retourGestionnaire->email;
                        $nomGestionnaire = $retourGestionnaire->gestionnairenom;

                        $message = "Votre RDV est prévu le $dateeffective à $VilleEff. Un conseiller client vous recevra. Pour plus d'informations, Consultez votre espace client: urlr.me/9ZXGSr . ";
                    } else {
                        $message = "Suite à votre demande de rendez-vous, un conseiller client vous recevra le " . $dateeffective . " afin d'apporter une solution à votre préoccupation.";
                        //$message = "Cher(e) client(e), suite à votre demande de rendez-vous, un conseiller vous recevra le " . $dateeffective . "." . PHP_EOL ."Pour toute information complémentaire, veuillez contacter le " . $telGestionnaire . ".";
                    }

                    $numero = "225" . substr($rdv->tel, -10);
                    $ref_sms = "RDV-" . $idrdv;

                    $sms_envoi = new SMSService();
                    if (strlen($message) > 160) $message = substr($message, 0, 160);
                    $retour = $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

                    $sqlUpdatePrestation = "UPDATE tblrdv SET etatSms =? WHERE idrdv = ?";
                    $queryOptions = array(
                        '1',
                        intval($idrdv)
                    );
                    $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
                    if ($result != null) {
                        //$url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=transmettreRDV&idrdv=" . trim($idrdv);
                        $url_notification = $lienEnvoiMail . "action=transmettreRDV&data=[idrdv:" . trim($idrdv) . "]";
                        $retour = file_get_contents($url_notification);
                    }
                    echo json_encode($idrdv);
                } else {
                    echo json_encode("-1");
                }
            } else {
                echo json_encode("-1");
            }
            break;
        case "rejeterRDV":

            $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            $motif = GetParameter::FromArray($_REQUEST, 'motif');
            $gestionnaire = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');

            $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $rdv = $retour[0];
                $idmotif = "";
                $etat = "0";

                $sqlUpdatePrestation = "UPDATE tblrdv SET etat= ?, reponse=?, datetraitement=?, gestionnaire=?, updatedAt =? , etatSms =? WHERE idrdv = ?";
                $queryOptions = array(
                    $etat,
                    addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
                    $maintenant,
                    $gestionnaire,
                    $maintenant,
                    '1',
                    intval($idrdv)
                );


                $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
                if ($result != null) {
                    $retour = $idrdv;

                    $numero = "225" . substr($rdv->tel, -10);
                    $ref_sms = "RDV-" . $idrdv;

                    //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));
                    $message = "Cher client(e), votre demande de rdv n° " . $rdv->codedmd . "  du " . $rdv->daterdv . " a été rejetée." . PHP_EOL . "Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";
                    $sms_envoi = new SMSService();
                    if (strlen($message) > 160) $message = substr($message, 0, 160);
                    $retour = $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

                    //$url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejetRDV&idrdv=" . trim($idrdv);
                    $url_notification = $lienEnvoiMail . "action=confirmerRejetRDV&data=[idrdv:" . trim($idrdv) . "]";

                    file_get_contents($url_notification);
                } else $retour = 0;

                echo json_encode($retour);
            } else echo json_encode("-1");

            break;

        case "getTraitementAjoutUtilisateur":

            $typeaction = GetParameter::FromArray($_REQUEST, 'typeaction');
            $agent_id = GetParameter::FromArray($_REQUEST, 'agent_id');
            $nom = GetParameter::FromArray($_REQUEST, 'nom');
            $prenom = GetParameter::FromArray($_REQUEST, 'prenom');
            $email = GetParameter::FromArray($_REQUEST, 'email');
            $telephone = GetParameter::FromArray($_REQUEST, 'telephone');
            $typeCompte = GetParameter::FromArray($_REQUEST, 'typeCompte');
            $profil = GetParameter::FromArray($_REQUEST, 'profil');
            $etatCompte = GetParameter::FromArray($_REQUEST, 'statut');
            $ciblePrestation = GetParameter::FromArray($_REQUEST, 'ciblePrestation');
            $codeagent = GetParameter::FromArray($_REQUEST, 'codeagent');
            $villesRDV = GetParameter::FromArray($_REQUEST, 'villesRDV');
            $provenance = GetParameter::FromArray($_REQUEST, 'provenance');
            $ListePartenaire = GetParameter::FromArray($_REQUEST, 'ListePartenaire');


            $existe = false;
            if ($agent_id != null) {
                $parms = " WHERE id = '" . $agent_id . "' ";
            } else {
                $parms = " WHERE email = '" . $email . "' ";
            }

            $sqlSelect = " SELECT * FROM " . Config::TABLE_USER . "  $parms";
            $result = $fonction->_getSelectDatabases($sqlSelect);
            if ($result != NULL) {
                $existe = true;
            }

            if ($ListePartenaire != null && $ListePartenaire != "All") {
                list($idpartenaire, $codePartenaire, $nomPartenaire) = explode('-', $ListePartenaire, 3);
            } else {
                if ($ListePartenaire == "All") {
                    $codePartenaire = "All";
                    $idpartenaire = "99";
                    $nomPartenaire = "TOUS LES PARTENAIRES";
                } else {
                    $codePartenaire = null;
                    $idpartenaire = null;
                    $nomPartenaire = null;
                }
            }

            $retour = traitementGestionDesUtilisateur($existe, $typeCompte, $profil, $etatCompte, $ciblePrestation, $codeagent, $villesRDV, $nom, $prenom, $email, $telephone, $agent_id, $provenance, $codePartenaire);
            echo json_encode($retour);
            break;

        case "importBordereau":
            $etat = 1;

            $rdvLe = GetParameter::FromArray($_COOKIE, 'rdvLe');
            $rdvAu = GetParameter::FromArray($_COOKIE, 'rdvAu');
            $ListeGest = GetParameter::FromArray($_COOKIE, 'ListeGest');

            if (isset($_COOKIE["rdvLe"]) && isset($_COOKIE["rdvAu"]) && isset($_COOKIE["ListeGest"])) {

                // Gestion de la période
                if (!empty($rdvLe) && !empty($rdvAu)) {
                    $periode = date('d/m/Y', strtotime($rdvLe)) . " - " . date('d/m/Y', strtotime($rdvAu));
                    $lib_periode = date('Ymd', strtotime($rdvLe)) . " - " . date('Ymd', strtotime($rdvAu));
                } elseif (!empty($rdvLe)) {
                    $periode = date('d/m/Y', strtotime($rdvLe));
                    $lib_periode = date('Ymd', strtotime($rdvLe));
                } elseif (!empty($rdvAu)) {
                    $periode = date('d/m/Y', strtotime($rdvAu));
                    $lib_periode = date('Ymd', strtotime($rdvAu));
                }
                // Gestionnaire
                if (!empty($ListeGest)) {
                    [$idGest, $nomGest, $idVilleGest, $VilleGest] = explode('|', $ListeGest, 4);
                }
            } else {
                $idGest = null;
                $nomGest = null;
                $idVilleGest = null;
                $VilleGest = null;
                $lib_periode = date('Ymd');
            }

            $prefixe_ref = strtolower("yaav-rdv-" . $VilleGest . "-" . $idGest . "-");
            $reference = uniqid($prefixe_ref);


            $result = $fonction->_insertInfosBordereauRDV($idVilleGest, $VilleGest, $idGest, $nomGest, $rdvLe, $rdvAu, $reference, $etat);

            if ($result) {

                $dataATraiter = GetParameter::FromArray($_REQUEST, 'params');
                $data = json_decode($dataATraiter, true);
                if (!is_array($data)) {
                    http_response_code(400);
                    echo json_encode(["error" => "Données JSON invalides"]);
                    exit;
                } else {

                    $inserted = 0;

                    for ($i = 0; $i <= count($data) - 1; $i++) {
                        if ($i == 0) continue;

                        // print_r($data[$i]);

                        $ligneBordereau = new BordereauRDV($data[$i]);

                        if (isset($ligneBordereau->NumeroRdv) && $ligneBordereau->NumeroRdv != null) {

                            $sqlQuery = "SELECT * FROM laloyale_bdweb.tblrdv WHERE idrdv = '" . $ligneBordereau->NumeroRdv . "' ORDER BY idrdv ";
                            $result_rdv = $fonction->_getSelectDatabases($sqlQuery);
                            if ($result_rdv != null) {
                                $fonction->_insertBordereauRDV($ligneBordereau, $result_rdv[0]->idrdv, $reference);
                            }
                        } else {
                            $fonction->_insertBordereauRDV($ligneBordereau, null, $reference);
                        }
                    }

                    $response = ["success" => true, "inserted" => $i, "reference" => $reference];
                    echo json_encode($response);
                }
            } else {
                echo json_encode("-1");
            }

            break;

        case "confirmerRejetSinistre":

            $id_sinistre = GetParameter::FromArray($_REQUEST, 'id_sinistre');
            $traiterpar = GetParameter::FromArray($_REQUEST, 'traiterpar');
            $observation = GetParameter::FromArray($_REQUEST, 'observation');


            $sqlSelect = " SELECT * FROM tbl_sinistres  WHERE YEAR(created_at) = YEAR(CURDATE()) AND id = '" . $id_sinistre . "'  ORDER BY id DESC ";
            $retour = $fonction->_getSelectDatabases($sqlSelect);
            if ($retour != null) {
                $sinistre = $retour[0];

                $sqlUpdateSinistre = "UPDATE tbl_sinistres SET etape= ?, observationtraitement=?, traiterpar=?, updated_at =now(), traiterle = now() WHERE id = ?";
                $queryOptions = array(
                    "3",
                    addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
                    $traiterpar,
                    intval($id_sinistre)
                );

                $result = $fonction->_Database->Update($sqlUpdateSinistre, $queryOptions);
                if ($result != null) {
                    $retour = $sinistre->code;

                    $numero = "225" . substr($sinistre->celDecalarant, -10);
                    // $numero = "2250758401797";
                    $sms_envoi = new SMSService();
                    $ref_sms = "YAAV-SMS-" . $sinistre->id;

                    $message = "Cher client(e), votre pré-declaration de sinistre n° " . $sinistre->code . " a été rejetée." . PHP_EOL . " Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";
                    if (strlen($message) > 160) $message = substr($message, 0, 160);
                    $sms_envoi->sendOtpInfobip($numero, $message,  "YAKO AFRICA");

                    // $url_notification = $lienEnvoiMail . "action=confirmerRejetSinistre&data=[idsinistre:" . trim($id_sinistre) . "]";
                    // file_get_contents($url_notification);
                } else $retour = 0;
                echo json_encode($retour);
            } else echo json_encode("-1");

            break;
        case "validerSinistre":

            $id_sinistre = GetParameter::FromArray($_REQUEST, 'id_sinistre');
            $traiterpar = GetParameter::FromArray($_REQUEST, 'traiterpar');


            $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            $typeOpe = GetParameter::FromArray($_REQUEST, 'typeOpe');
            $ListeOpe = GetParameter::FromArray($_REQUEST, 'ListeOpe');
            $delaiTrait = GetParameter::FromArray($_REQUEST, 'delaiTrait');

            if ($id_sinistre != null) {

                $sqlSelect = " SELECT * FROM tbl_sinistres  WHERE YEAR(created_at) = YEAR(CURDATE()) AND id = '" . $id_sinistre . "'  ORDER BY id DESC ";
                $retour = $fonction->_getSelectDatabases($sqlSelect);
                if ($retour != null) {
                    $sinistre = $retour[0];


                    $retourDetail = $fonction->_GetDetailsTraitementPrestation($sinistre->id, "sinistre");
                    if ($retourDetail != null) {
                        $result = $retourDetail;
                    } else {

                        list($keyType, $typeOperation) = explode("-", $typeOpe);
                        $tablo = explode("-", $ListeOpe);
                        $count = count($tablo);
                        if ($count > 1) {
                            $CodeTypeAvenant = $tablo[0];
                            $DelaiTraitement = $tablo[1];
                            $operation = $tablo[2];
                        }

                        $result = $fonction->_InsertDetailPrestation($CodeTypeAvenant, $operation, $DelaiTraitement, $idcontrat, $sinistre->id, $maintenant, "sinistre");
                    }

                    $sqlUpdateSinistre = "UPDATE tbl_sinistres SET etape=? , traiterpar=? , estMigree=? , updated_at=NOW() , traiterle = NOW() , migreele = NOW() WHERE id =?";
                    $queryOptions = array(
                        "2",
                        $traiterpar,
                        '1',
                        intval($id_sinistre)
                    );

                    $result = $fonction->_Database->Update($sqlUpdateSinistre, $queryOptions);
                    if ($result != null) {
                        $retour = $sinistre->code;

                        $numero = "225" . substr($sinistre->celDecalarant, -10);
                        // $numero = "2250785817235";
                        $sms_envoi = new SMSService();
                        $ref_sms = "YAAV-SMS-" . $sinistre->id;
                        $message = "Cher client(e) , la pré-declaration de sinistre n° " . $sinistre->code . " a été acceptée. Merci de vous rendre en Agence YAKO AFRICA, muni des originaux des documents.";
                        if (strlen($message) > 160) $message = substr($message, 0, 160);
                        //$sms_envoi->sendOtpInfobip($numero, $message,  "YAKO AFRICA");

                        echo json_encode($retour);
                    } else echo json_encode("-1");
                } else echo json_encode("-1");
            } else echo json_encode("-1");
            break;

        case "tableauSuivi":
            $service = GetParameter::FromArray($_REQUEST, 'service');
            $filtreuse = GetParameter::FromArray($_REQUEST, 'filtreuse');

            if ($service != null && $filtreuse != null) {

                if ($service == "rdv") {

                    $sqlSelect = " SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
				    TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
			        LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $filtreuse 	ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";
                    $tableauSuivi = $fonction->_getSelectDatabases($sqlSelect);
                } else if ($service == "prestation") {
                    $tableauSuivi = "";
                } else if ($service == "sinistre") {
                    $tableauSuivi = "";
                } else {
                    $tableauSuivi = "";
                }
                echo json_encode($tableauSuivi);
                
            } else echo json_encode("-1");
            break;

        default:
            echo json_encode("0");
            break;
    }
}



function traitementGestionDesUtilisateur($existe, $typeCompte, $profil, $etatCompte, $ciblePrestation, $codeagent, $villesRDV, $nom, $prenom, $email, $telephone, $agent_id, $provenance, $codePartenaire)
{

    global $fonction, $lienEnvoiMail;


    $retour = "0";

    if (($typeCompte == "rdv" || $typeCompte == "gestionnaire") && $profil == "agent") {
        $login = $codeagent;
        $libelleTraitement = "RDVs";
    } else {
        $login = $email;
        $libelleTraitement = strtoupper($typeCompte) . 'S';
    }
    $motdepasse = "1234567";

    if ($existe) {

        $sqlUpdatePrestation = "UPDATE " . Config::TABLE_USER . " SET etat= ?, nom=?, prenom=?, email=?, telephone =? , typeCompte =? , profil=? ,cible=?, codeagent=?, ville=? , reseaux=? , partenaire=? WHERE id = ?";
        $queryOptions = array(
            $etatCompte,
            addslashes(htmlspecialchars(trim(ucfirst(strtoupper($nom))))),
            addslashes(htmlspecialchars(trim(ucfirst(strtoupper($prenom))))),
            $email,
            $telephone,
            $typeCompte,
            $profil,
            $ciblePrestation,
            $codeagent,
            $villesRDV,
            $provenance,
            $codePartenaire,
            intval($agent_id)
        );
        $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
        $retour = "le compte de l'utilisateur $nom $prenom a bien été mis à jour ";
    } else {

        $sqlInsertUtilisateur = "INSERT INTO " . Config::TABLE_USER . " (nom,prenom,email,telephone,typeCompte,profil,etat,cible,codeagent,ville,login,password,date,modifiele,reseaux,partenaire) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,now(),now(),?,?)";
        $queryOptions = array(
            addslashes(htmlspecialchars(trim(ucfirst(strtoupper($nom))))),
            addslashes(htmlspecialchars(trim(ucfirst(strtoupper($prenom))))),
            $email,
            $telephone,
            $typeCompte,
            $profil,
            $etatCompte,
            $ciblePrestation,
            $codeagent,
            $villesRDV,
            $login,
            $motdepasse,
            $provenance,
            $codePartenaire

        );
        $result = $fonction->_Database->Update($sqlInsertUtilisateur, $queryOptions);
        if ($result != null) {

            $agent_id = $result['LastInsertId'];
            $retour = "le compte de l'utilisateur $nom $prenom a bien été créer ";

            $numero = "225" . substr($telephone, -10);
            $ref_sms = "COMPTE-" . $agent_id;

            //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));
            $message = "Cher $profil , votre compte pour la gestion des $libelleTraitement a bien été créer ." . PHP_EOL . "Login : $login" . PHP_EOL . "mot de passe : $motdepasse.";
            $sms_envoi = new SMSService();
            if (strlen($message) > 160) $message = substr($message, 0, 160);
            $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");
        }
    }

    //$url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=ajouterUtilisateur&idrdv=" . trim($agent_id);
    $url_notification = $lienEnvoiMail . "action=ajouterUtilisateur&data=[agent_id:" . trim($agent_id) . "]";
    file_get_contents($url_notification);

    return $retour;
}

function traitementApresReceptionRDVAutres($rdv, $etat, $libelleTraitement, $observation, $resultatOpe)
{
    global $fonction, $maintenant, $lienEnvoiMail;

    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement=?, libelleTraitement=?, reponseGest=?, datetraitement=?, gestionnaire=?, updatedAt =? , etatSms =? WHERE idrdv = ?";
    $queryOptions = array(
        "3",
        $etat,
        $libelleTraitement,
        addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
        $maintenant,
        $rdv->gestionnaire,
        $maintenant,
        '1',
        intval($rdv->idrdv)
    );

    $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
    if ($result != null) {
        $retour = $rdv->idrdv;

        $numero = "225" . substr($rdv->tel, -10);
        $ref_sms = "RDV-" . $rdv->idrdv;

        //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));

        if ($etat == "3") {
            $message = "Cher client(e), après analyse, votre demande de " . strtoupper($rdv->motifrdv) . " du " . $rdv->daterdv . " n'a pas abouti. Plus d’infos sur votre espace client : urlr.me/9ZXGSr";
        } else {
            if ($resultatOpe == "transformation") {
                $message = "Cher client(e), votre demande de " . strtoupper($resultatOpe) . " du " . $rdv->daterdv . " a bien été autorisée. Plus d’infos sur votre espace client : urlr.me/9ZXGSr";
            } else {
                $message = "Cher client(e), votre demande de " . strtoupper($rdv->motifrdv) . " n° " . $rdv->idrdv . " du " . $rdv->daterdv . " a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";
            }
        }


        $sms_envoi = new SMSService();
        if (strlen($message) > 160) $message = substr($message, 0, 160);
        $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");
    } else $retour = 0;
    return $retour;
}



function traitementApresReceptionRDV($rdv, $etat, $typeOperation, $obervation, $operation = null)
{
    global $fonction, $maintenant, $lienEnvoiMail;

    $result_typeprestation = $fonction->getRetourneTypePrestation(" AND LOWER(libelle) like '%" . strtolower($rdv->motifrdv) . "%' ");
    //print_r($result_typeprestation);
    if ($result_typeprestation != null) {
        $typeprestation = $result_typeprestation[0]->libelle;
    } else {
        $typeprestation = $rdv->motifrdv;
    }

    if ($etat == "1") {
        $libelleTraitement = "Le client a la permission de faire une demande de " . $rdv->motifrdv;
    } else {
        $libelleTraitement = $typeOperation;
        $typeprestation = $operation;
    }

    $sqlInsertPrestation = "INSERT INTO tbl_prestations(idcontrat,typeprestation,prestationlibelle,etape,estMigree,created_at) VALUES (?,?,?,?,?, NOW() )";
    $queryOptionsPrestations = array(
        $rdv->police,
        $typeprestation,
        $typeprestation,
        '-1',
        '0'
    );
    $rrr = $fonction->_Database->Update($sqlInsertPrestation, $queryOptionsPrestations);

    $idprestation = $rrr["LastInsertId"];


    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement= ?, libelleTraitement=?, reponseGest=?, datetraitement=?, gestionnaire=?, updatedAt =? , etatSms =? , idCourrier=? , estPermit=? WHERE idrdv = ?";
    $queryOptions = array(
        "3",
        $etat,
        $libelleTraitement,
        addslashes(htmlspecialchars(trim(ucfirst(strtolower($obervation))))),
        $maintenant,
        $rdv->gestionnaire,
        $maintenant,
        '1',
        intval($idprestation),
        '1',
        intval($rdv->idrdv)
    );


    $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
    if ($result != null) {
        $retour = $rdv->idrdv;

        $numero = "225" . substr($rdv->tel, -10);
        $ref_sms = "RDV-" . $rdv->idrdv;

        //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));

        $message = "Cher client(e), votre demande de " . strtoupper($typeprestation) . " n° " . $rdv->idrdv . " du " . $rdv->daterdv . " a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";

        $sms_envoi = new SMSService();
        if (strlen($message) > 160) $message = substr($message, 0, 160);
        $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

        //$url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejetRDV&idrdv=" . trim($rdv->idrdv);
        $url_notification = $lienEnvoiMail . "action=confirmerRejetRDV&data=[idrdv:" . trim($rdv->idrdv) . "]";
        file_get_contents($url_notification);
    } else $retour = 0;

    return $retour;
}


function getAPI($tabloCritere, $url_api)
{
    //if ($url_api == NULL) $url_api = dbAcess::URL_API_ETAT_ENCAISSEMENT;
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
        //print_r($data);exit;
        return $data;
    } catch (Exception $e) {
        echo 'Exception reçue : ', $e->getMessage(), "\n";
        return false;
    }
}
