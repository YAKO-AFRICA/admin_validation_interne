<?php

// Set the timezone to UTC
//date_default_timezone_set('UTC');

session_start();


include("../autoload.php");
/*$fonction = new  fonction();
$dbAcces = new dbAcess();
*/

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

                    echo json_encode("1");
                } else echo json_encode("-1");
            } else echo json_encode("-1");

            break;

        case "intro":
            $retourStatut = $fonction->_recapGlobalePrestations();
            $global = $fonction->pourcentageAllTypePrestation();

            $result = array(
                "retourStatut" => $retourStatut,
                "global" => $global
            );
            echo json_encode($result);
            break;

        case "introRejet":
            $retourStatut = null;
            $MotifRejet = $fonction->pourcentageAllMotifRejetPrestation();

            $result = array(
                "retourStatut" => $retourStatut,
                "MotifRejet" => $MotifRejet
            );
            echo json_encode($result);
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

                        $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejet&idprestation=" . trim($idprestation);
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

                //print_r($prestation);

                $retourDetail = $fonction->_GetDetailsTraitementPrestation($prestation->id);
                if ($retourDetail != null) {

                    $fonction->_UpdateDetailPrestationValider($CodeTypeAvenant, $operation, $DelaiTraitement, $idcontrat, $prestation->id, $maintenant, $retourDetail->idDetail);
                    $result = $retourDetail->idDetail;
                } else {
                    $result = $fonction->_InsertDetailPrestation($CodeTypeAvenant, $operation, $DelaiTraitement, $idcontrat, $prestation->id, $maintenant);
                }

                if ($result != null) {

                    $numero = "225" . substr($prestation->cel, -10);
                    //$numero = "2250758407197";
                    $fonction->_UpdatePrestationValiderNSIL($prestation, $traiterpar);
                    $resultat = array("result" => "SUCCESS", "total" => '0', "data" =>  "validation de la prestation");

                    $sms_envoi = new SMSService();
                    $ref_sms = "YAAV-SMS-" . $prestation->id;
                    $message = "Cher client(e), votre prestation n° " . $prestation->code . " a été acceptée." . PHP_EOL . " Consultez son état sur votre espace personnel : urlr.me/9ZXGSr";
                    if (strlen($message) > 160) $message = substr($message, 0, 160);

                    //$sms_envoi->sendOtpInfobip($numero, $message,  "YAKO AFRICA");

                    $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=validerprestation&idprestation=" . trim($idprestation);
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


        case "listeMotifRejet":


            $retourListeMotifRejet = $fonction->_GetListeMotifRejet();

            if (!empty($retourListeMotifRejet)) {
                // Réindexation du tableau si nécessaire
                $retourListeMotifRejet = array_values($retourListeMotifRejet);

                //print_r($retourListeMotifRejet);
                // Encodage en JSON
                echo json_encode($retourListeMotifRejet);
            } else {
                // En cas de données vides, renvoyer un tableau vide
                echo json_encode([]);
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
            if ($idVilleEff != null) {
                $params = " AND idVilleBureau != '$idVilleEff' ";
            }

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

        case "compteurRdv":

            $daterdveff = trim($_REQUEST['daterdveff']);
            $idVilleEff = trim($_REQUEST['idVilleEff']);
            $daterdv = trim($_REQUEST['daterdv']);
            $parms = trim($_REQUEST['parms']);

            if ($parms == '1') {
                $daterdv = $daterdveff;
                /*list($annee, $mois, $jour) = explode('-', $daterdveff, 3);
		        $daterdv = trim($jour . '/' . $mois . '/' . $annee);*/
            } else {
                /**/
                list($jour, $mois, $annee) = explode('/', $daterdv, 3);
                $daterdv = date_create($annee . '-' . $mois . '-' . $jour);
                $daterdv = date_format($daterdv, "Y-m-d");
                $daterdv = $daterdv;
            }

            $dateAfficher = date('d/m/Y', strtotime($daterdv));

            $sqlQuery = "SELECT  COUNT(*) AS totalrdv, tblrdv.villeEffective,  tblrdv.daterdveff,  MIN(tblrdv.daterdv) AS daterdv_min FROM tblrdv WHERE tblrdv.villeEffective = '$idVilleEff'   AND DATE(tblrdv.daterdveff) = '" . $daterdv . "' GROUP BY tblrdv.villeEffective, tblrdv.daterdveff ORDER BY totalrdv DESC";
            $resultat = $fonction->_getSelectDatabases($sqlQuery);
            if ($resultat != NULL) {

                $retour = array("daterdv" => $dateAfficher, "idVilleEff" => $idVilleEff, "total" => count($resultat), "data" => $resultat[0]);
            } else {
                $retour = array("daterdv" => $dateAfficher, "idVilleEff" => $idVilleEff, "total" => "0", "data" => null);
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
                //$resultat = $fonction->_transmettreRDV($idrdv, $idcontrat, $gestionnaire, $objetRDV, $dateRDV);
                //echo json_encode($resultat);

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

                    $fonction->_InsertHistorisqueRdv($idrdv);
                    $result = $fonction->_TransmettreRDVGestionnaire($etat, $reponse, $dateRDVEff, $datetraitement, $idgestionnaire, $idrdv, $idVilleEff, $traiterpar);


                    $dateeffective = date('d/m/Y', strtotime($dateRDVEff));

                    $sqlQuery2 = "SELECT id , email , codeagent , telephone,  TRIM(CONCAT(nom ,' ', prenom)) as gestionnairenom FROM users WHERE  id='" . $idgestionnaire . "' ";
                    $result2 = $fonction->_getSelectDatabases($sqlQuery2);
                    if ($result2 != NULL) {
                        $retourGestionnaire = $result2[0];
                        $telGestionnaire = $retourGestionnaire->telephone;
                        $emailGestionnaire = $retourGestionnaire->email;
                        $nomGestionnaire = $retourGestionnaire->gestionnairenom;

                        $message = "Cher(e) client(e), votre RDV est prévu le $dateeffective à $VilleEff . Un conseiller client vous recevra. Pour plus d'informations, Consultez votre espace personnel : urlr.me/9ZXGSr . ";
                    } else {
                        $message = "Suite à votre demande de rendez-vous, un conseiller client vous recevra le " . $dateeffective . " afin d'apporter une solution à votre préoccupation.";
                        //$message = "Cher(e) client(e), suite à votre demande de rendez-vous, un conseiller vous recevra le " . $dateeffective . "." . PHP_EOL ."Pour toute information complémentaire, veuillez contacter le " . $telGestionnaire . ".";
                    }

                    $numero = "225" . substr($rdv->tel, -10);
                    //$numero = "2250758407197";
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
                        $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=transmettreRDV&idrdv=" . trim($idrdv);
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
                    //$numero = "2250758407197";
                    $ref_sms = "RDV-" . $idrdv;

                    //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));
                    $message = "Cher client(e), votre demande de rdv du " . $rdv->daterdv . " a été rejetée." . PHP_EOL . "Consultez les détails du rejet sur votre espace personnel : urlr.me/9ZXGSr";


                    $sms_envoi = new SMSService();
                    if (strlen($message) > 160) $message = substr($message, 0, 160);
                    //$retour = $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

                    $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejetRDV&idrdv=" . trim($idrdv);
                    file_get_contents($url_notification);
                } else $retour = 0;


                echo json_encode($retour);
            } else echo json_encode("-1");

            break;
        case "getJourReception":

            $idvilles = GetParameter::FromArray($_REQUEST, 'idvilles');

            if (isset($idvilles) && $idvilles != null) {
                $sqlSelect = "SELECT tbloptionrdv.* , tblvillebureau.libelleVilleBureau as villes FROM tbloptionrdv  INNER JOIN tblvillebureau on tbloptionrdv.codelieu = tblvillebureau.idVilleBureau WHERE tbloptionrdv.codelieu = '" . $idvilles . "' ORDER BY tbloptionrdv.codejour ASC ";
            } else {
                $sqlSelect = "SELECT tbloptionrdv.* , tblvillebureau.libelleVilleBureau as villes FROM tbloptionrdv INNER JOIN tblvillebureau on tbloptionrdv.codelieu = tblvillebureau.idVilleBureau ORDER BY tbloptionrdv.codejour ASC ";
            }

            $option_rdv = $fonction->_getSelectDatabases($sqlSelect);
            if ($option_rdv != null) {
                // Filtrer les événements en fonction du `codejour` et `villes`
                $filteredEvents = [];
                $tablo = [];
                foreach ($option_rdv as $event) {

                    $tablo['location'] = $event->villes;
                    $tablo['jour'] = $event->codejour;
                    $tablo['idvilles'] = $event->codelieu;
                    array_push($filteredEvents, $tablo);
                    // Vérifier si l'événement correspond au `codejour` et aux `villes`
                    // if ($event['jour'] == $event->codejour && $event['location'] == $event->$villes) {
                    //     $filteredEvents[] = $event;
                    // }
                }

                if (!empty($filteredEvents)) {
                    $response['events'] = $filteredEvents;
                    $response['success'] = true;
                }

                // Retourner la réponse en JSON
                echo json_encode($response);
                //echo json_encode($option_rdv[0]);
            } else
                echo json_encode("-1");

            break;

        case "importBordereau":

            // $idrdv = GetParameter::FromArray($_REQUEST, 'idrdv');
            // $idcontrat = GetParameter::FromArray($_REQUEST, 'idcontrat');
            // $gestionnaire = GetParameter::FromArray($_REQUEST, 'gestionnaire');
            // $objetRDV = GetParameter::FromArray($_REQUEST, 'objetRDV');
            // $dateRDV = GetParameter::FromArray($_REQUEST, 'dateRDV');
            // $etat = GetParameter::FromArray($_REQUEST, 'etat');

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

            $prefixe_ref = strtolower("yaav-rdv-" . $VilleGest . "-" . $idGest . "-" );
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
            /*
           

            if (isset($_GET['compteur'])) {

                $idrdv = trim($_POST['id']);
                $daterdveff = trim($_POST['daterdveff']);
                $idVilleEff = trim($_POST['idVilleEff']);
                $idgestionnaire = trim($_POST['idusers']);
                $daterdv = trim($_REQUEST['daterdv']);

                $villes = retourneVilles($idVilleEff);
               
                list($jour, $mois, $annee) = explode('/', $daterdveff, 3);
                $daterdv = date_create($annee . '-' . $mois . '-' . $jour);
                //print_r($daterdv["date"]) ;
                $daterdv = date_format($daterdv, "Y-m-d");
	
                $daterdv = $daterdveff;
                $sqlQuery = "SELECT count(*) as totalrdv , tblrdv.gestionnaire , tblrdv.villeEffective , TRIM(CONCAT(users.nom ,' ', users.prenom)) as utilisateur FROM tblrdv LEFT OUTER JOIN users ON tblrdv.gestionnaire = users.id WHERE  tblrdv.gestionnaire='$idgestionnaire' AND date(tblrdv.daterdveff)='" . $daterdv . "' GROUP BY gestionnaire,villeEffective ORDER BY totalrdv DESC";
                #$sqlQuery = "SELECT count(*) as totalrdv , tblrdv.gestionnaire , tblrdv.villeEffective , TRIM(CONCAT(users.nom ,' ', users.prenom)) as utilisateur FROM tblrdv LEFT OUTER JOIN users ON tblrdv.gestionnaire = users.id WHERE  tblrdv.gestionnaire='$idgestionnaire' AND date(tblrdv.daterdv)='" . $daterdv . "'   GROUP BY gestionnaire,villeEffective ORDER BY totalrdv DESC";
                $sqlSelect = $connect->query($sqlQuery);
                $retourcompteur = $sqlSelect->fetchAll(PDO::FETCH_ASSOC);
                if ($retourcompteur != false || !empty($retourcompteur)) {
                    array_push($retourcompteur, array("villes" => $villes));
                } else {
                    $utilisateur = retourneGestionnaire($idgestionnaire);
                    $retourcompteur = array("0" => array("totalrdv" => 0, "gestionnaire" => $idgestionnaire, "villeEffective" => $idVilleEff, "villes" => $villes, "utilisateur" => $utilisateur));
                }
                $retour = array("daterdv" => $daterdveff, "idVilleEff" => $idVilleEff, "data" => $retourcompteur);
                echo json_encode($retour);

            }
            */
    }
}


/*if ($action == "connexion") {
    $paramSession = trim("admin|admin|admin|ADMINISTRATEUR");
    $_SESSION["agent_id"] = trim("admin");
    $_SESSION["paramSessionUser"] = trim($paramSession);
    echo json_encode("1");
}*/


function nettoyerCles($array)
{
    $result = [];
    foreach ($array as $key => $value) {
        $keyNettoyee = trim($key);
        $result[$keyNettoyee] = $value;
    }
    return $result;
}
