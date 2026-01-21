<?php


include("autoload.php");

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$urlService = $protocol . $_SERVER['HTTP_HOST'];

$services = "YAKO AFRICA ASSURANCES VIE";
$lienYako = "www.yakoafricassur.com";
$lienService =  "";
$mail = false;
$subject = "";
$mailCopie = "";

$action = (isset($_REQUEST["action"]) ? trim($_REQUEST["action"]) : NULL);
$data = (isset($_REQUEST["data"]) ? trim($_REQUEST["data"]) : NULL);


if ($data != null) {
    $data = str_replace("[", "", $data);
    $data = str_replace("]", "", $data);

    list($champ, $refchamp) = explode(":", $data, 2);
} else {
    $champ = null;
    $refchamp = null;
}



if ($refchamp != null && $champ == "idprestation") {


    $idprestation = $refchamp;
    $retour = $fonction->_getRetournePrestation(" WHERE id='" . trim($idprestation) . "'");
    if ($retour != null) {

        $prestation = new tbl_prestations($retour[0]);

        $retour_documents = $fonction->_getListeDocumentPrestation($idprestation);
        $listes_documents = getRetourneListeDocumentPrestation($retour_documents);

        if ($prestation->prestationlibelle == "Autre") {
            $ajoutOptions = '';
        } else {
            $ajoutOptions = `<li>Montant souhaité : <b>" . $prestation->montantSouhaite . " FCFA</b></li>
                                            <li>Moyen de paiement : <b>" . $prestation->lib_moyenPaiement . "</b></li>`;
        }

        switch ($action) {

            case "confirmerRejet":

                $ListeMotifRejet = $fonction->_GetListeMotifRejetPrestation($prestation->code, null, true);
                $subject = "Prestation -  " . strtoupper($prestation->code);

                $titre = "<label style='color:red'> La demande de prestation <b>" . $prestation->code . "</b>  a été rejetée</label>,</br>";
                $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($prestation->souscripteur2) . "</span> !
                                    </div>  <br>
                                               " . $titre . " <br>                                
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Type de prestation : <b>" . $prestation->typeprestation . "</b></li>
                                            <li>Code prestation : <b>" . $prestation->code . "</b></li>
                                            <li>Id du contrat : <b>" . $prestation->idcontrat . "</b></li>
                                            " . $ajoutOptions . "
                                        </ul>
                                        <br>
                                        <div class='card-body p-2' style='background-color:bisque ; font-weight:bold;'>
                                        <h4 class='text-center p-4' style='color:#033f1f ; font-weight:bold;'>Motif de rejet</h4> <hr>$ListeMotifRejet</div>                          
                                </div>
                                ";

                $message = format_mail_by_NISSA($prestation->souscripteur2, $text_form, $titre);
                $to = $prestation->email;
                $mail = true;

                break;

            case "validerprestation":


                $subject = "Prestation -  " . strtoupper($prestation->code);
                $titre = "<label style='color:green'> La demande de prestation <b>" . $prestation->code . "</b>  est en cours de traitement </label></br>";

                $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($prestation->souscripteur2) . "</span> !
                                    </div>  <br>
                                        " . $titre . " <br>                                   
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Type de prestation : <b>" . $prestation->typeprestation . "</b></li>
                                            <li>Code prestation : <b>" . $prestation->code . "</b></li>
                                            <li>Id du contrat : <b>" . $prestation->idcontrat . "</b></li>
                                             " . $ajoutOptions . "
                                        </ul>
                                        <br>
                                        " . $listes_documents . "                          
                                </div>";

                $message = format_mail_by_NISSA($prestation->souscripteur2, $text_form, $titre);
                $to = $prestation->email;
                $mail = true;
                break;
        }
    }
} elseif ($champ == "idrdv" && $refchamp != null) {

    $idrdv = $refchamp;
    $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";
    $sqlSelect = "
			SELECT 
				tblrdv.*,
				CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.email AS emailgestionnaire, users.codeagent AS codeagentgestionnaire,
				TRIM(tblvillebureau.libelleVilleBureau) AS villes
			FROM tblrdv
			LEFT JOIN users ON tblrdv.gestionnaire = users.id
			LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
			WHERE tblrdv.idrdv = '$idrdv' 
			
			ORDER BY tblrdv.idrdv DESC	";
    $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);

    if ($retour_rdv != null) {
        $rdv = $retour_rdv[0];

        switch ($action) {
            case "confirmerRejetRDV":

                $subject = "RDV -  " . strtoupper($rdv->codedmd) . " du  " . $rdv->daterdv;
                $titre = "<label style='color:red'> La demande de Rendez-vous <b>" . $rdv->codedmd . " - n° " . $idrdv . "</b>  a été rejetée</label>,</br>";
                $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($rdv->nomclient) . "</span> !
                                    </div>  <br>
                                               " . $titre . " <br>                                
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Date RDV : <b>" . $rdv->daterdv . "</b></li>
                                            <li>Motif : <b>" . $rdv->motifrdv . "</b></li>
                                            <li>Code RDV : <b>" . $rdv->codedmd . "</b></li>
                                            <li>Id du contrat : <b>" . $rdv->police . "</b></li>
                                        </ul>
                                        <br>
                                        <div class='card-body p-2' style='background-color:bisque ; font-weight:bold;'>
                                        <h4 class='text-center p-4' style='color:#033f1f ; font-weight:bold;'>Motif de rejet</h4> <hr>" . $rdv->reponse . "</div>                          
                                </div>
                                ";

                $message = format_mail_by_NISSA($rdv->nomclient, $text_form, $titre);
                $to = $rdv->email;
                $mail = true;

                break;
            case "transmettreRDV":

                $result = $fonction->getRetourneContactInfosGestionnaire($rdv->codeagentgestionnaire);
                $email_final = trim($result["email_final"]);
                $telephoneGestionnaire = trim($result["telephone"]);
                $contactGestionnaire = trim($result["contacts_html"]);
                $emailgestionnaire = $rdv->emailgestionnaire;
                if(empty($email_final)) $email_final = $emailgestionnaire;

                $mailCopie = ", " . $rdv->nomgestionnaire . " <" . $email_final . ">";

                $subject = "RDV -  " . strtoupper($rdv->codedmd) . " du  " . $rdv->daterdv;
                $titre = "<label style='color:green'> La demande de Rendez-vous <b>" . $rdv->codedmd . " - n° " . $idrdv . "</b>  a ete transmise</label>,</br>";
                $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($rdv->nomclient) . "</span> !
                                    </div>  <br>
                                               " . $titre . " <br>                                
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Date RDV : <b>" . date('d/m/Y', strtotime($rdv->daterdveff)) . "</b></li>
                                            <li>Motif : <b>" . $rdv->motifrdv . "</b></li>
                                            <li>Code RDV : <b>" . $rdv->codedmd . "</b></li>
                                            <li>Id du contrat : <b>" . $rdv->police . "</b></li>
                                            <li>Ville RDV : <b>" . $rdv->villes . "</b></li>
                                            
                                        </ul>
                                        <br>
                                        <div class='card-body p-2' style='background-color:bisque ; font-weight:bold;'>
                                        <h2 class='text-center p-4' style='color:#033f1f ; font-weight:bold;'> Contact du gestionnaire </h2> <hr>
                                        <ul>
                                            <li>Gestionnaire : <b>" . $rdv->nomgestionnaire . "</b></li>
                                            <li>Telephone : <b>" . $telephoneGestionnaire . "</b></li>
                                        </ul>
                                        </div>                          
                                </div>
                                ";

                $message = format_mail_by_NISSA($rdv->nomclient, $text_form, $titre);

                print_r($message);
                $to = $rdv->email;
                $mail = true;

                //getByNissa($rdv, $idrdv, $email_final, $telephoneGestionnaire);
                break;
        }
    }
    // $retourRDV = 
} elseif ($champ == "agent_id" && $refchamp != null) {


    //ajouterUtilisateur
    $agent_id = $refchamp;
    if ($agent_id != null) {
        $sqlSelect = " SELECT * FROM " . Config::TABLE_USER . " WHERE id = '$agent_id' ";
        $result = $fonction->_getSelectDatabases($sqlSelect);
        if ($result != NULL) {

            $agent = $result[0];

            switch ($action) {

                case "ajouterUtilisateur":
                    $subject = "utilisateur  -  " . $agent->nom . " " . $agent->prenom;

                    $titre = "<label style='color:green'> Le compte de l'" . $agent->profil . " a bien éte crée </label>,</br>";
                    $text_form = "<div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($agent->nom . " " . $agent->prenom) . "</span> !
                                    </div>  <br>
                                               " . $titre . " <br>                                
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Lien de connexion : <b><a href='https://yakoafricassur.com/admin/login.php' target='_blank'>Cliquez ici</a></b></li>
                                            <li>Date  : <b>" . $agent->date . "</b></li>
                                            <li>Login : <b>" . $agent->login . "</b></li>
                                            <li>Mot de passe : <b>1234567</b></li>
                                            <li>Profil : <b>" . $agent->profil . "</b></li>
                                            <li>Type de compte : <b>" . $agent->typeCompte . "</b></li>
                                        </ul>                       
                                </div>
                                ";

                    $message = format_mail_by_NISSA($agent->nom . " " . $agent->prenom, $text_form, $titre);
                    $to = $agent->email;
                    $mail = true;

                    break;
            }
        }
    }
}



if ($mail && $to != "") {

    $boundary = md5(uniqid(microtime(), TRUE));

    // Headers
    $headers = 'From: ' . $services . ' <support.enov@yakoafricassur.com>' . "\r\n";
    $headers .= 'Cc: HELP DESK YAKO AFRICA <support.enov@yakoafricassur.com> , SUPPORT YNOV <no-reply@yakoafricassur.com>' . $mailCopie;
    $headers .= 'Mime-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: multipart/mixed;boundary=' . $boundary . "\r\n";
    $headers .= "\r\n";

    // Message
    //$msg = 'Texte affiché par des clients mail ne supportant pas le type MIME.'."\r\n\r\n";
    // Message HTML

    $msg = '--' . $boundary . "\r\n";
    $msg .= 'Content-type: text/html; charset=utf-8' . "\r\n\r\n";
    $msg .= $message;

    //print $msg . PHP_EOL;

    // Fin
    $msg .= '--' . $boundary . "\r\n";

    // Function mail()

    if (mail($to, $subject, $msg, $headers)) // Envoi du message
    {
        $to_log = __FUNCTION__ . ' | Message de notification a bien été envoyé à ' . $to . " ( " . $message . " ) ";
        // print $to_log;

        //$fonction->_UpdateStatutEnvoiMail($prestation, "1", $contenu = "");
        //echo json_encode($resultat);
    } else // Non envoyé
    {
        $to_log = __FUNCTION__ . ' | Le message de notification n\'a pas pu être envoyé à ' . $to . " ( " . $message . " ) ";
        // print $to_log;
    }

    //print $to;
    $log_message = $message . PHP_EOL . $to_log;
    file_put_contents(__DIR__ . "/config/log/notification_mail_" . @date('Y-m-d') . ".txt", $log_message . PHP_EOL, FILE_APPEND);
} else {
    $to_log = "desole le mail a echoue";
}




function getRetourneListeDocumentPrestation($retour_documents)
{

    $i = 0;
    $docs = "";
    $docs_etatPrestation = "";
    if ($retour_documents !== null) {
        for ($i = 0; $i <= count($retour_documents) - 1; $i++) {

            $tablo = $retour_documents[$i];

            $id_prestation = $tablo["idPrestation"];
            $path_doc = trim($tablo["path"]);
            $type_doc = trim($tablo["type"]);
            $doc_name = trim($tablo["libelle"]);
            $ref_doc = trim($tablo["id"]);
            $datecreation_doc = trim($tablo["created_at"]);
            $documents = Config::URL_PRESTATION_RACINE . $path_doc;
            $values = $id_prestation . "-" . $documents;

            if ($type_doc == "RIB") $nom_document = "RIB";
            elseif ($type_doc == "Police") $nom_document = "Police du contrat d'assurance";
            elseif ($type_doc == "bulletin") $nom_document = "bulletin de souscription";
            elseif ($type_doc == "AttestationPerteContrat") $nom_document = "Attestation de declaration de perte";
            elseif ($type_doc == "CNI") $nom_document = "CNI";
            elseif ($type_doc == "etatPrestation") {
                $nom_document = "Fiche de demande de prestation";
                $docs_etatPrestation = '<ul> <li>' . $nom_document . ' : <b><a href="' . $documents . '" target="_blank"> ' . $nom_document . ' </a></b></li></ul> ';
            } else $nom_document = "Fiche d'identification du numero de paiement";

            $docs .= '<ul> <li>' . $nom_document . ' : <b><a href="' . $documents . '" target="_blank"> ' . $nom_document . ' </a></b></li></ul> ';
        }
        $listes_documents = '<div class="card-body p-2" style="background-color:bisque ; font-weight:bold;">
            <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;">Documents joints</h4> <hr>' . $docs . ' </div> ';
        return $listes_documents;
    }

    return null;
}


function format_mail_by_NISSA($nom_destinataire, $text_form, $docs = null)
{
    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #555;
            }
            .title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }
            .content {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .important {
                font-weight: bold;
                color: #2C7B77;
            }
            .link {
                color: #2F67F6;
                text-decoration: none;
            }
            .button {
                background-color: #033f1f;
                color: #ffffff;
                padding: 10px 20px;
                text-align: center;
                border-radius: 3px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>                   

        <div style='background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;'>

            <div class='row'>
                <div class='card-box mb-30'>
                    <div class='row center-block'>
                        <div class='col-lg-3'></div>

                        <div class='col-lg-6'>
                            <center>
                                <img src='www.yakoafricassur.com/gestion-demande-compte/vendors/images/entete-yako-africa.png' width='100%' alt='' srcset=''>
                            </center>
                        </div>
                        <div class='col-lg-3'></div>
                    </div>
                </div>
            </div>
            <table align='center' border='0' cellpadding='0' cellspacing='0' role='presentation'  style='background:#fff;background-color:#fff;width:100%;'>
                <tbody>
                    <tr>
                        <td  style='border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;'>
                            <div class='mj-column-per-100 outlook-group-fix' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;'>

                            <table border='0' cellpadding='0' cellspacing='0' role='presentation'  style='vertical-align:bottom;' width='100%'>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                        <table align='center' border='0' cellpadding='0' cellspacing='0'
                                            role='presentation'
                                            style='border-collapse:collapse;border-spacing:0px; background-color:#033f1f;'>
                                            <tbody>
                                                <tr>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;padding-bottom:40px;word-break:break-word;'>
                                        <div
                                            style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:28px;font-weight:bold;line-height:1;text-align:center;color:#555;'>
                                            Chèr(e) &nbsp; " . $nom_destinataire . "  !!

                                        </div>
                                    </td>
                                </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                " . $text_form . "  
        
                                
                            </td>
                        </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                <div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;'>
                                    Best regards,<br><br> YAKOAFRICASSUR<br>DSI conception<br>
                                    <a href='https://www.yakoafricassur.com'
                                        style='color:#2F67F6'>yakoafricassur.com</a>
                                </div>
                                 <div class='content'>
            Cordialement,<br>
            L'équipe de YAKOAFRICASSUR<br>
            <a href='https://www.yakoafricassur.com' class='link'>www.yakoafricassur.com</a>
        </div>

                            </td>
                        </tr>

                    </table>

                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
    </body>
    </html>
";

    return $message;
}



function format_mail(tbl_prestations $prestation, $message, $docs = null)
{
    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: #555;
            }
            .title {
                font-size: 24px;
                font-weight: bold;
                color: #333;
                margin-bottom: 10px;
            }
            .content {
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            .important {
                font-weight: bold;
                color: #2C7B77;
            }
            .link {
                color: #2F67F6;
                text-decoration: none;
            }
            .button {
                background-color: #033f1f;
                color: #ffffff;
                padding: 10px 20px;
                text-align: center;
                border-radius: 3px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>                   

        <div style='background:#fff;background-color:#fff;Margin:0px auto;max-width:600px;'>

            <div class='row'>
                <div class='card-box mb-30'>
                    <div class='row center-block'>
                        <div class='col-lg-3'></div>

                        <div class='col-lg-6'>
                            <center>
                                <img src='www.yakoafricassur.com/gestion-demande-compte/vendors/images/entete-yako-africa.png' width='100%' alt='' srcset=''>
                            </center>
                        </div>
                        <div class='col-lg-3'></div>
                    </div>
                </div>
            </div>
            <table align='center' border='0' cellpadding='0' cellspacing='0' role='presentation'  style='background:#fff;background-color:#fff;width:100%;'>
                <tbody>
                    <tr>
                        <td  style='border:#dddddd solid 1px;border-top:0px;direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;'>
                            <div class='mj-column-per-100 outlook-group-fix' style='font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:bottom;width:100%;'>

                            <table border='0' cellpadding='0' cellspacing='0' role='presentation'  style='vertical-align:bottom;' width='100%'>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                        <table align='center' border='0' cellpadding='0' cellspacing='0'
                                            role='presentation'
                                            style='border-collapse:collapse;border-spacing:0px; background-color:#033f1f;'>
                                            <tbody>
                                                <tr>


                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align='center'
                                        style='font-size:0px;padding:10px 25px;padding-bottom:40px;word-break:break-word;'>
                                        <div
                                            style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:28px;font-weight:bold;line-height:1;text-align:center;color:#555;'>
                                            Chèr(e) &nbsp; " . strtoupper($prestation->souscripteur) . "  !!

                                        </div>
                                    </td>
                                </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                <div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:16px;line-height:22px;text-align:left;color:#555;'>
                                    <div class='content'>
                                    <div class='title'>
                                        Bonjour <span class='important'>" . strtoupper($prestation->souscripteur2) . "</span> !
                                    </div>  <br>
                                        " . $message . "<br>
                                        
                                        Resume de la demande  :<br>
                                        <ul>
                                            <li>Type de prestation : <b>" . $prestation->typeprestation . "</b></li>
                                            <li>Code prestation : <b>" . $prestation->code . "</b></li>
                                            <li>Id du contrat : <b>" . $prestation->idcontrat . "</b></li>
                                            <li>Montant souhaité : <b>" . $prestation->montantSouhaite . "</b></li>
                                            <li>Moyen de paiement : <b>" . $prestation->lib_moyenPaiement . "</b></li>
                                            
                                        </ul>
                                        <br>
                                        " . $docs . "                          
                                </div>
        
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td align='left' style='font-size:0px;padding:10px 25px;word-break:break-word;'>

                                <div
                                    style='font-family:'Helvetica Neue',Arial,sans-serif;font-size:14px;line-height:20px;text-align:left;color:#525252;'>
                                    Best regards,<br><br> YAKOAFRICASSUR<br>DSI conception<br>
                                    <a href='https://www.yakoafricassur.com'
                                        style='color:#2F67F6'>yakoafricassur.com</a>
                                </div>
                                 <div class='content'>
            Cordialement,<br>
            L'équipe de YAKOAFRICASSUR<br>
            <a href='https://www.yakoafricassur.com' class='link'>www.yakoafricassur.com</a>
        </div>

                            </td>
                        </tr>

                    </table>

                </div>
            </td>
        </tr>
    </tbody>
</table>
</div>
    </body>
    </html>
";

    return $message;
}




function getByNissa($rdv, $idrdv, $email_final, $telephoneGestionnaire)
{
    global $urlService;
    $subjectGestionnaire = "Affectation RDV -  " . strtoupper($rdv->codedmd) . " du  " .  date('d/m/Y', strtotime($rdv->daterdveff));
    $titreGestionnaire = " <label style='color:green'> La demande de Rendez-vous <b>" . $rdv->codedmd . " - n° " . $idrdv . "</b>  vous a été affecté </label>,";
    $text_formGestionnaire = "
                            <div style='font-family: \"Helvetica Neue\", Arial, sans-serif; font-size:16px; line-height:22px; text-align:left; color:#555;'>
                                <div class='content'>
                                    <div class='card-body p-2' style='font-size:20px; font-weight:bold; margin-bottom:10px;'>
                                        Bonjour <span class='important'>" . strtoupper(htmlspecialchars($rdv->nomgestionnaire)) . "</span> !
                                    </div>
                                    <br>
                                    <label style='color:green; font-size:16px;'>
                                        La demande de Rendez-vous 
                                        <b>" . htmlspecialchars($rdv->codedmd) . " - n° " . htmlspecialchars($idrdv) . " du " .  date('d/m/Y', strtotime($rdv->daterdveff)) . "</b>
                                        vous a été affectée
                                    </label>
                                    <br><br>
                                    Résumé de la demande :
                                    <br>
                                    <ul>
                                        <li>Nom et Prenom  : <b>" . htmlspecialchars($rdv->nomclient) . "</b></li>
                                        <li>Contact  : <b>" . htmlspecialchars($rdv->tel) . "</b></li>
                                        <li>Date RDV : <b>" . htmlspecialchars(date('d/m/Y', strtotime($rdv->daterdveff))) . "</b></li>
                                        <li>Motif : <b>" . htmlspecialchars($rdv->motifrdv) . "</b></li>
                                        <li>Code RDV : <b>" . htmlspecialchars($rdv->codedmd) . "</b></li>
                                        <li>Id du contrat : <b>" . htmlspecialchars($rdv->police) . "</b></li>
                                        <li>Ville RDV : <b>" . htmlspecialchars($rdv->villes) . "</b></li>
                                    </ul>
                                    <br>
                                    <div class='card-body p-2 text-center' 
                                        style='background-color: bisque; font-weight:bold; padding:15px; text-align:center;'>
                                        Merci de vous connecter à la plateforme de gestion des RDV pour le traitement.
                                        <br><br>
                                        <a href='$urlService' target='_blank'
                                        style='color:#d35400; text-decoration:underline; font-size:16px;'>
                                            Plateforme de gestion des RDV
                                        </a>
                                        <br>
                                    </div>
                                </div>
                            </div>
                            ";
    $messageMail = formulaireMail($rdv->nomclient, $text_formGestionnaire, $titreGestionnaire);
    notificationMail($subjectGestionnaire, $rdv->nomgestionnaire, $email_final, $messageMail);
    print_r($messageMail);
}
