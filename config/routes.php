<?php

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

                    //print_r($retourUsers);

                    $_SESSION["id"] = $retourUsers->id;
                    $_SESSION["typeCompte"] = $retourUsers->typeCompte;
                    $_SESSION["utilisateur"] = $retourUsers->userConnect;
                    $_SESSION["profil"] = $retourUsers->profil;
                    $_SESSION["infos"] = $retourUsers->infos;

                    echo json_encode($retourUsers->infos);
                } else echo json_encode("-1");
            } else echo json_encode("-1");

            break;

        case 'motdepasseOublie':

            $loginPO = GetParameter::FromArray($_REQUEST, 'login');
            $email = GetParameter::FromArray($_REQUEST, 'email');

            if (isset($loginPO) && $loginPO != null) {
                $plus = " AND login = '$loginPO' AND etat = '1' ";
                $retourUsers = $fonction->_GetUsers($plus);
                if ($retourUsers != null) {

                    list($pre_email, $post_email) = explode('@', $email, 2);

                    if (preg_match("/^yakoafricassur/", $post_email)) {

                        $retour_recup_users = $fonction->_getSelectDatabases("SELECT * FROM tbl_recup_users WHERE id_users = '" . $retourUsers->id . "' ");

                        if ($retour_recup_users == null) {
                            $retour = $fonction->insertRecuperationMotPasse($retourUsers, $email);
                        } else {
                            $fonction->updateRecuperationMotPasse($retourUsers, $email);
                            $retour =  $retour_recup_users[0]->id;
                        }

                        $lib = "recuperation-mail?i=" . trim($retour) . "&p=rp-" . date('YmdHis');

                        $url_notification = "http://localhost/mes-projets/yako-africa/admin-prestation/" . $lib;
                        file_get_contents($url_notification);

                        echo json_encode($retour);
                    } else {
                        echo json_encode("-2");
                    }
                } else {
                    echo json_encode("-1");
                }
            } else {
                echo json_encode("-1");
            }

            break;

        case "intro":

            $type = GetParameter::FromArray($_REQUEST, 'type');
            //print_r($_REQUEST);

            if ($type == Config::TYPE_SERVICE_PRESTATION) {
                $retourStatut = $fonction->_recapGlobalePrestations();
                $global = $fonction->pourcentageAllTypePrestation();

                $result = array(
                    "retourStatut" => $retourStatut,
                    "global" => $global
                );
                echo json_encode($result);
            } elseif ($type == Config::TYPE_SERVICE_RDV) {

                // $retourStatut = $fonction->pourcentageRDVBy("statut");
                // $retourStatutVille = $fonction->pourcentageRDVBy("ville");
                // $retourStatutuser = $fonction->pourcentageRDVBy("user");
                // $retourStatutType = $fonction->pourcentageRDVBy("type");
                //  $result = array(
                //     "retourStatut" => $retourStatut,
                //     "retourStatVille" => $retourStatutVille,
                //     "retourStatuser" => $retourStatutuser,
                //     "retourStatutType" => $retourStatutType
                // );
                // echo json_encode($result);
                //$global = $fonction->pourcentageAllTypeRDV();
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


                // $result_typeprestation = $fonction->getRetourneTypePrestation(" AND LOWER(libelle) like '%" . strtolower($rdv->motifrdv) . "%' ");
                // //print_r($result_typeprestation);
                // if ($result_typeprestation != null) {
                //     $typeprestation = $result_typeprestation[0]->libelle;
                // } else {
                //     $typeprestation = $rdv->motifrdv;
                // }


                // $sqlInsertPrestation = "INSERT INTO tbl_prestations(idcontrat,typeprestation,prestationlibelle,etape,estMigree,created_at) VALUES (?,?,?,?,?, NOW() )";
                // $queryOptionsPrestations = array(
                //     $rdv->police,
                //     $typeprestation,
                //     $typeprestation,
                //     '-1',
                //     '0'
                // );
                // $rrr = $fonction->_Database->Update($sqlInsertPrestation, $queryOptionsPrestations);

                // $idprestation = $rrr["LastInsertId"];

                // $sqlUpdatePrestation = "UPDATE tblrdv SET etat= ?, reponse=?, datetraitement=?, gestionnaire=?, updatedAt =? , etatSms =? , idCourrier=? , estPermit=? WHERE idrdv = ?";
                // $queryOptions = array(
                //     $etat,
                //     addslashes(htmlspecialchars(trim(ucfirst(strtolower($observation))))),
                //     $maintenant,
                //     $gestionnaire,
                //     $maintenant,
                //     '1',
                //     intval($idprestation),
                //     '1',
                //     intval($idrdv)
                // );


                // $result = $fonction->_Database->Update($sqlUpdatePrestation, $queryOptions);
                // if ($result != null) {
                //     $retour = $idrdv;

                //     $numero = "225" . substr($rdv->tel, -10);
                //     $numero = "2250758407197";
                //     $ref_sms = "RDV-" . $idrdv;

                //     //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));
                //     $message = "Cher client(e), votre demande de " . strtoupper($rdv->motifrdv) . " n° " . $idrdv . " du " . $rdv->daterdv . " a été autorisée . Pour finaliser votre démarche, connectez-vous à votre espace client : urlr.me/9ZXGSr";

                //     $message = "Cher client(e), votre demande de " . strtoupper($rdv->motifrdv) . " n° " . $idrdv . " du " . $rdv->daterdv . " a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";


                //     $sms_envoi = new SMSService();
                //     if (strlen($message) > 160) $message = substr($message, 0, 160);
                //     //$sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

                //     $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejetRDV&idrdv=" . trim($idrdv);
                //     file_get_contents($url_notification);
                // } else $retour = 0;

                // echo json_encode($retour);

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
                    "partielle" => array("etat" => "2", "libelle" => "Le client a demandé un rachat partielle", "operation" => "Rachat partiel"),
                    "avance" => array("etat" => "2", "libelle" => "Le client a demandé une avance / pret", "operation" => "Avance ou prêt"),
                    "renonce" => array("etat" => "3", "libelle" => "Le client a changé d'avis", "operation" => "Renonce"),
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
        default:
            echo json_encode("0");
            break;
    }
}


function traitementApresReceptionRDVAutres($rdv, $etat, $libelleTraitement, $observation, $resultatOpe)
{
    global $fonction, $maintenant;

    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement=?, libelleTraitement=?, reponse=?, datetraitement=?, gestionnaire=?, updatedAt =? , etatSms =? WHERE idrdv = ?";
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
        $numero = "2250758407197";
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
        //$ff = $sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");
        //print_r($ff);

        $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejetRDV&idrdv=" . trim($rdv->idrdv);
        file_get_contents($url_notification);
    } else $retour = 0;
    return $retour;
}



function traitementApresReceptionRDV($rdv, $etat, $typeOperation, $obervation, $operation = null)
{
    global $fonction, $maintenant;

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


    $sqlUpdatePrestation = "UPDATE tblrdv SET etat = ?, etatTraitement= ?, libelleTraitement=?, reponse=?, datetraitement=?, gestionnaire=?, updatedAt =? , etatSms =? , idCourrier=? , estPermit=? WHERE idrdv = ?";
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
        $numero = "2250758407197";
        $ref_sms = "RDV-" . $rdv->idrdv;

        //$dateeffective = date('d/m/Y', strtotime($rdv->daterdv));

        $message = "Cher client(e), votre demande de " . strtoupper($typeprestation) . " n° " . $rdv->idrdv . " du " . $rdv->daterdv . " a été autorisée. Finaliser votre démarche dans votre espace client : urlr.me/9ZXGSr";

        $sms_envoi = new SMSService();
        if (strlen($message) > 160) $message = substr($message, 0, 160);
        //$sms_envoi->sendOtpInfobip($numero, $message, "YAKO AFRICA");

        $url_notification = "https://admin.prestations.yakoafricassur.com/notification-mail.php?action=confirmerRejetRDV&idrdv=" . trim($rdv->idrdv);
        file_get_contents($url_notification);
    } else $retour = 0;

    return $retour;
}
