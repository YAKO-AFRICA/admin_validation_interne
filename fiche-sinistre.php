<?php

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("autoload.php");


$plus = "";
$resultat = "";
$afficheuse = FALSE;
$retour_documents = 0;
$tablo_doc_attendu = array();

if (isset($_COOKIE["idsinistre"])) {

    $afficheuse = TRUE;
    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idsinistre = GetParameter::FromArray($_COOKIE, 'idsinistre');
    $action = GetParameter::FromArray($_COOKIE, 'action');
    $code = GetParameter::FromArray($_COOKIE, 'code');


    $plus = " YEAR(tbl_sinistres.created_at) = YEAR(CURDATE())";
    $sqlSelect = " SELECT tbl_sinistres.* , CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire  FROM tbl_sinistres LEFT JOIN users ON tbl_sinistres.traiterpar = users.id  WHERE $plus AND tbl_sinistres.id = '" . $idsinistre . "'  ORDER BY tbl_sinistres.id DESC ";

    $retourSinistre = $fonction->_getSelectDatabases($sqlSelect);
    if ($retourSinistre != null) {
        $sinistre = $retourSinistre[0];
        $retour_documents = $fonction->_getListeDocumentSinistre($sinistre->id);
    } else {
        header('Location:liste-sinistres');
    }
} else {
    header("Location:deconnexion.php");
}

//exit;

?>

<!DOCTYPE html>
<html>

<head>
    <?php include "include/entete.php"; ?>
</head>

<body>

    <?php include "include/header.php";  ?>

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4>TRAITEMENT DES DEMANDES</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                    <li class="breadcrumb-item " aria-current="page">Liste des demandes</li>
                                    <li class="breadcrumb-item active" aria-current="page">Traitement demande</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="card-body height-100-p pd-20">
                    <div class="card-box mb-30">
                        <div style="float:left" class="p-2">
                            <button class="btn btn-warning p-2 m-2" onclick="retour()"><i class='fa fa-arrow-left'>Retour</i></button>
                        </div>
                    </div>
                    <div class="card-body radius-12 w-100 p-4" style="border:1px solid gray;background:#033f1f">
                        <h3 class="text-center" style="color:white">Declaration de Sinistre n° <span style="color:#F9B233;"><?= strtoupper($sinistre->code) . " du " . date('d/m/Y H:i:s', strtotime($sinistre->created_at)) ?></span></h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h4 class="text-blue h4" style="color:#033f1f!important;">Detail du declarant</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            </div>
                            <div class="row pd-20">
                                <div class="col-md-6">
                                    <p><span class="text-color">Filiation :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->filiation ?? '' ?></span></p>
                                    <p><span class="text-color">Nom & Prenom :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->nomDecalarant . " " . $sinistre->prenomDecalarant ?? '' ?></span></p>
                                    <p><span class="text-color">Date de naissance :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->datenaissanceDecalarant ?? '' ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="text-color">Lieu de residence :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->lieuresidenceDecalarant ?? '' ?></span></p>
                                    <p><span class="text-color">Numero de téléphone :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->celDecalarant ?? ''  ?></span></p>
                                    <p><span class="text-color">E-mail :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->emailDecalarant ?? '' ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h4 class="text-blue h4" style="color:#033f1f!important;">Detail de l'assuré(e)</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            </div>
                            <div class="row pd-20">
                                <div class="col-md-6">
                                    <p><span class="text-color">Genre :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->genreAssuree == "M" ? "Masculin" : "Feminin"  ?? '' ?></span></p>
                                    <p><span class="text-color">Nom & Prenom :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->nomAssuree . " " . $sinistre->prenomAssuree ?? '' ?></span></p>
                                    <p><span class="text-color">Date de naissance :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->datenaissanceAssuree ?? '' ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="text-color">Lieu de residence :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->lieuresidenceAssuree ?? '' ?></span></p>
                                    <p><span class="text-color">Profession :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->professionAssuree ?? ''  ?></span></p>
                                    <p><span class="text-color">Lieu naissance :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->lieunaissanceAssuree ?? '' ?></span></p>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h4 class="text-blue h4" style="color:#033f1f!important;">Détail du sinistre</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            </div>
                            <div class="row pd-20">
                                <div class="col-md-6">
                                    <p><span class="text-color">Id contrat</span> : <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->idcontrat ?></span></p>
                                    <p><span class="text-color">Date du sinistre</span> : <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->dateSinistre) ? "--" : date("d/m/Y", strtotime($sinistre->dateSinistre)) ?></span></p>
                                    <p><span class="text-color">Nature du sinistre :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->natureSinistre ?? "--" ?></span></p>
                                    <p><span class="text-color">Cause du sinistre:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->causeSinistre ?? "--" ?></span></p>
                                    <p><span class="text-color">Decès Accidentel:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->decesAccidentel == "1" ? "Oui" : "Non" ?? "--" ?></span></p>
                                    <p><span class="text-color">Declaration Tardive:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->declarationTardive == "1" ? "Oui" : "Non" ?? "--" ?></span></p>


                                </div>
                                <div class="col-md-6">
                                    <?php if ($sinistre->natureSinistre == "Deces") { ?>
                                        <?php if ($sinistre->declarationTardive == "1") { ?>
                                            <p><span class="text-color">Date Inhumation:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->dateInhumation) ? "--" : date("d/m/Y", strtotime($sinistre->dateInhumation)) ?></span></p>
                                            <p><span class="text-color">Lieu Inhumation:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->lieuInhumation) ? "--" : $sinistre->lieuInhumation ?></span></p>

                                        <?php } ?>
                                        <p><span class="text-color">La conservation a t'elle eu lieu ? </span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->lieuConservation) ? "NON" : "OUI" ?></span></p>

                                        <?php if (!empty($sinistre->lieuConservation)) { ?>
                                            <p><span class="text-color">Lieu Conservation:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->lieuConservation) ? "--" : $sinistre->lieuConservation ?></span></p>
                                        <?php } ?>
                                        <p><span class="text-color">Date Levee:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->dateLevee) ? "--" : date("d/m/Y", strtotime($sinistre->dateLevee)) ?></span></p>
                                        <p><span class="text-color">Lieu Levee:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($sinistre->lieuLevee) ? "--" : $sinistre->lieuLevee ?></span></p>

                                    <?php } ?>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h4 class="text-blue h4" style="color:#033f1f!important;">Informations sur le paiement du sinistre</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            </div>
                            <div class="row pd-20">

                                <div class="col-md-6">
                                    <p><span class="text-color">Moyen de paiement :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= (empty($sinistre->moyenPaiement) ? "--" : str_replace("_", " ", $sinistre->moyenPaiement)) ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="text-color">Montant du BON :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= (empty($sinistre->montantBON) ? "--" :  number_format($sinistre->montantBON, 0, ',', ' ')) ?></span></p>
                                </div>
                                <?php
                                if ($sinistre->moyenPaiement == "Virement_Bancaire") {
                                ?>
                                    <div class="col-md-6">
                                        <p><span class="text-color">IBAN du compte :</span> <span class="text-infos"
                                                style="font-size:18px; font-weight:bold;"><?= $sinistre->codebanque . " - " . $sinistre->numcompte . " - " . $sinistre->clerib; ?>
                                        </p>
                                    </div>
                                <?php } else { ?>
                                    <div class="col-md-6">
                                        <p><span class="text-color">Operateur :</span> <span class="text-infos"
                                                style="font-size:18px; font-weight:bold;"><?= empty($sinistre->Operateur) ? "--" : str_replace("_", " ", $sinistre->Operateur); ?>
                                        </p>
                                        <p><span class="text-color">Telephone de Paiement :</span> <span class="text-infos"
                                                style="font-size:18px; font-weight:bold;"><?= $sinistre->telPaiement; ?>
                                        </p>
                                    </div>
                                <?php }  ?>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h4 class="text-blue h4" style="color:#033f1f!important;">Listes des documents joints</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            </div>
                            <div class="text-center mb-2">
                                <span id="compteur_valides" class="badge bg-success">Validés : 0</span>
                                <span id="compteur_restants" class="badge bg-danger">Restants :
                                    <?php if ($retour_documents != null) echo count($retour_documents);
                                    else echo "0"; ?></span>
                            </div>
                            <div class="row pd-20">
                                <?php


                                if ($retour_documents != null) {

                                    for ($i = 0; $i <= count($retour_documents) - 1; $i++) {
                                        $tablo = $retour_documents[$i];


                                        $idSinistre = $tablo["idSinistre"];
                                        $path_doc = trim($tablo["path"]);
                                        $nom_document = trim($tablo["libelle"]);
                                        $filename = trim($tablo["filename"]);
                                        $ref_doc = trim($tablo["id"]);
                                        $datecreation_doc = trim($tablo["created_at"]);
                                        $documents = Config::URL_PRESTATION_RACINE . $path_doc;
                                        $values = $idSinistre . "-" . $ref_doc . "-" . $nom_document . "-" . $filename;
                                        array_push($tablo_doc_attendu,  $ref_doc);
                                ?>

                                        <div class="col-md-6 border-right">
                                            <div class="d-flex align-items-center mt-3 document-ligne" id="line_<?= $ref_doc ?>">
                                                <input type="text" class="val_doc" name="val_doc" value="<?php echo $values; ?>" hidden>
                                                <input type="text" class="path_doc" name="path_doc" value="<?php echo $documents; ?>"
                                                    hidden>

                                                <div class="fm-file-box text-success p-2">
                                                    <i class="fa fa-file-pdf-o"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="mb-0" style="font-size: 18px;">
                                                        <a href="<?= $documents ?>" target="_blank"> <?= $nom_document ?> </a>
                                                    </h6>
                                                    <p class="mb-0 text-secondary" style="font-size: 0.8em;">
                                                        <?= $datecreation_doc ?> </p>
                                                </div>
                                                <button type="button" class="btn btn-warning bx bx-show"
                                                    data-doc-id="<?= $documents; ?>" data-path-doc="<?= $documents; ?>"
                                                    style="background-color:#F9B233 !important;">
                                                    <i class="dw dw-eye"></i>
                                                </button>

                                            </div>
                                            <span id="checking_<?= $ref_doc ?>"> </span>
                                        </div>
                                <?php
                                    }
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">  Attention ! <strong>Aucun document joint</strong>. </div>';
                                }

                                ?>
                                <input type="text" id="tablo_doc_attendu" name="tablo_doc_attendu"
                                    value="<?= json_encode($tablo_doc_attendu)  ?>" hidden>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="pd-20">
                    <div class="card-box height-100-p pd-20">
                        <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> traitement de la pre-declaration de sinistre </h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        <div class="row card-body" style="background: #D3D3D3;" id="infos_perso">
                            <div class="col-md-6">
                                <p style="float:left">Traité le : <span class="text-infos" id="dateheure" style="font-size:18px; font-weight:bold;"><?= $sinistre->created_at; ?></span></p>
                                <span class="text-infos" style="color:#033f1f ; color:white !important"></span>
                            </div>
                            <div class="col-md-6">
                                <p style="float:right">Traité par : <span class="text-infos" style="font-size:18px; font-weight:bold;"><?php echo $_SESSION['utilisateur'];  ?></span></p>
                            </div>
                            <input type="text" id="id_sinistre" name="id_sinistre" value="<?= $sinistre->id; ?>" hidden>
                            <input type="text" class="form-control" id="codesinistre" name="codesinistre" value="<?php echo $sinistre->code; ?>" hidden>

                            <input type="text" id="valideur" name="valideur" value="<?php echo $_SESSION['id'];  ?>" hidden>
                        </div>
                        <div class="row">
                            <div class="row card-body" style="color: #033f1f;">
                                <div class=" offset-md-6 col-md-6">
                                    <div class="form-group">
                                        <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Etat de la pré-declaration de sinistre :</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                            <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">Rejété la pré-declaration</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                            <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">Accepter la pré-declaration</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="afficheuse">

                        </div>
                        <div class="row" id="idNumId">

                        </div>

                        <div class="modal-footer" id="footer">
                            <label class="" id="lib"></label>
                            <div id="optionTraitement">
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="footer-wrap pd-20 mb-20">
                <?php include "include/footer.php";    ?>
            </div>


        </div>

        <div class="modal fade" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-body text-center">
                        <div class="card-body">
                            <p id="msgEchec" style="font-weight:bold; font-size:20px; color:red"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="notificationValidationOK" id="notificationValidationOK" class="btn btn-success" style="background: #033f1f !important;">OK</button>
                        <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modaleAfficheDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-body text-center">
                        <div class="card-body" id="iframeAfficheDocument">

                        </div>
                        <input type="text" class="form-control" id="val_doc2" name="val_doc3" hidden>
                        <input type="text" class="form-control" id="document" name="document" hidden>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="valid_download" id="valid_download" class="btn btn-success"
                            style="background: #033f1f !important;">VALIDER DOCUMENT</button>
                        <button type="button" name="annuler_download" id="annuler_download" class="btn btn-danger"
                            style="background:red !important;">REJETER DOCUMENT</button>
                        <button type="button" id="closeAfficheDocument" name="closeAfficheDocument"
                            class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="notification" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-body text-center">
                        <div class="form-group">
                            <h2><span id="a_afficher"></span></h2>
                        </div>
                        <div class="card-body radius-12 w-100">
                            <div class="alert alert-success" role="alert">
                                <h2><span id="a_afficher2"></span></h2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div id="closeNotif">
                            <button type="button" id="closeNotif" class="btn btn-secondary"
                                data-dismiss="modal">FERMER</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="echecNotification" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content ">
                    <div class="modal-body text-center">

                        <div class="card-body radius-12 w-100">
                            <div class="alert alert-warning" role="alert">
                                <h2><span id="a_afficherE2"></span></h2><br>
                                <h2><span id="a_afficherE"></span></h2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center font-18">
                        <h4 class="padding-top-30 mb-30 weight-500">
                            Voulez vous rejeter la pré-declaration de sinistre <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                        </h4>
                        <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                        <span style='color:seagreen'>le client sera notifier du rejet de la pré-declaration</span>
                        </hr>
                        <input type="text" id="id_sinistre" name="id_sinistre" hidden>
                        <input type="text" id="traiterpar" name="traiterpar" hidden>
                        <input type="text" id="observation" name="observation" hidden>



                        <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                            <div class="col-6">
                                <button type="button" id="annulerRejet" name="annulerRejet" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                                NON
                            </div>
                            <div class="col-6">
                                <button type="button" id="validerRejet" name="validerRejet" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
                                OUI
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
            <div id="customToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body" id="toastMessage">
                        Action effectuée avec succès.
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Fermer"></button>
                </div>
            </div>
        </div>


        <!-- js -->
        <script src="vendors/scripts/core.js"></script>
        <script src="vendors/scripts/script.min.js"></script>
        <script src="vendors/scripts/process.js"></script>
        <script src="vendors/scripts/layout-settings.js"></script>
        <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
        <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
        <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
        <!-- buttons for Export datatable -->
        <script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.print.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
        <script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
        <script src="src/plugins/datatables/js/pdfmake.min.js"></script>
        <script src="src/plugins/datatables/js/vfs_fonts.js"></script>
        <!-- Datatable Setting js -->
        <script src="vendors/scripts/datatable-setting.js"></script>
        <!-- CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


        <script>
            let document_rejeter = [];
            let document_valider = [];
            let tablo_document = [];

            let etape = <?php echo $sinistre->etape ?>;
            let idcontrat = "<?php echo $sinistre->idcontrat; ?>";
            var tablo_documents = <?= json_encode($tablo_doc_attendu) ?>;



            // console.log(etape + " " + idcontrat);
            // console.log(tablo_documents);



            $(document).ready(function() {

                $('input[name="customRadio"][value="' + <?php echo $sinistre->etape; ?> + '"]').attr('checked', 'checked');

                $('input[name="customRadio"]').change(function() {
                    // Lorsqu'on change de valeur dans l'etat de la pre declaration
                    var valeur = $(this).val();
                    // On supprime le commentaire et vérifie la valeur
                    if (valeur == '3') {
                        // La pre declaration est rejetée

                        // console.log(tablo_documents);
                        // console.log(document_rejeter);
                        getMenuRejeter();

                    } else if (valeur == '2') {
                        // La pre declaration est valider

                        if (tablo_documents.length == 0) {
                            $("#afficheuse").empty();
                            $('input[name="customRadio"][value="1"]').prop('checked',
                                true); // Remplace attr('checked', 'checked') par prop
                            //alert("Désolé, aucun document joint , veuillez rejeter la pre-declaration.");
                            showToast("Désolé, aucun document joint , veuillez rejeter la pré-declaration.",
                                "warning");


                        } else {
                            let tablo_attendu = <?php echo json_encode($tablo_doc_attendu); ?>;
                            // On vérifie si le nombre de documents validés correspond au nombre attendu
                            if (document_valider.length === tablo_attendu.length) {

                                $("#color_button").text("green");
                                $("#nom_button").text("Valider la prestation");
                                getMenuValider();

                                chargerListeOperations();

                                // --- Recharger la liste dès que l’utilisateur modifie le type d’opération ---
                                $("#afficheuse").on("input change", "#typeOpe", function() {
                                    chargerListeOperations();
                                });

                            } else {
                                $("#afficheuse").empty();
                                $('input[name="customRadio"][value="1"]').prop('checked',
                                    true); // Remplace attr('checked', 'checked') par prop
                                alert("Désolé, veuillez vérifier que tous les documents joints soient corrects, puis valider la pré-declaration ou cliquer sur rejeter la pré-declaration.");
                                showToast(
                                    "Désolé, veuillez vérifier que tous les documents joints soient corrects, puis valider la pré-declaration ou cliquer sur rejeter la pré-declaration.",
                                    "warning");
                            }

                        }
                    } else {
                        // La pre declaration est en attente
                        $("#afficheuse").empty();
                        $("#color_button").text("#F9B233");
                        $("#optionTraitement").empty();
                        showToast("La pré-declaration est en attente", "secondary");
                    }
                });


            })

            $(".bx-show").click(function() {
                let path_document = $(this).data("path-doc");
                let val_doc = $(this).closest('.d-flex').find('.val_doc').val();

                let html = `<iframe src="${path_document}" width="100%" height="500"></iframe>`;
                $("#document").val(path_document);
                $("#val_doc2").val(val_doc);
                $("#iframeAfficheDocument").html(html);
                $('#modaleAfficheDocument').modal("show");
            });


            $("#modaleAfficheDocument").on("click", "#annuler_download", function() {

                //alert("Annulation de la validation");

                let doc = $("#document").val();
                let val_doc = $("#val_doc2").val();

                let tab = val_doc.split('-');
                let id_sinistre = tab[0];
                let ref_doc = tab[1];
                let doc_name = tab[2];
                let type_doc = tab[3];


                //console.log(val_doc);

                if (!document_valider.includes(ref_doc)) {
                    alert(doc_name + " n'a pas été validé");
                    showToast(doc_name + " n'a pas encore été validé", "warning");
                    $("#checking_" + ref_doc).html(
                        `<i class="fa fa-times text-danger"> ${doc_name} n'est pas validé</i>`);
                } else {
                    // Retire ref_doc du tableau
                    document_rejeter.push(ref_doc);
                    document_valider = document_valider.filter(item => item !== ref_doc);
                    //alert(doc_name + " a été annulé");
                    showToast(doc_name + " a été annulé", "secondary");

                    // Met à jour l'affichage
                    $("#checking_" + ref_doc).html(
                        `<i class="fa fa-undo text-danger"> ${doc_name} a été annulé</i>`);
                    $("#line_" + ref_doc).closest(".d-flex")
                        .removeClass("border border-success bg-light")
                        .fadeIn();

                    updateCompteurs(document_valider, tablo_documents.length);

                    $("#afficheuse").empty();
                    $('input[name="customRadio"][value="1"]').prop('checked',
                        true); // Remplace attr('checked', 'checked') par prop

                }
                $('#modaleAfficheDocument').modal("hide");

            });

            $("#modaleAfficheDocument").on("click", "#valid_download", function() {

                let doc = $("#document").val();
                let val_doc = $("#val_doc2").val();

                let tab = val_doc.split('-');
                let id_sinistre = tab[0];
                let ref_doc = tab[1];
                let doc_name = tab[2];
                let type_doc = tab[3];


                if (document_valider.includes(ref_doc)) {
                    //alert(doc_name + " a déjà été validé");
                    showToast(doc_name + " a déjà été validé", "info");
                    $("#checking_" + ref_doc).html(
                        `<i class="fa fa-check text-warning"> ${doc_name} a déjà été validé</i>`);
                } else {
                    document_valider.push(ref_doc);
                    //alert(doc_name + " validé avec succès");
                    showToast(doc_name + " validé avec succès", "success");
                    $("#checking_" + ref_doc).html(
                        `<i class="fa fa-check text-success"> ${doc_name} a bien été validé</i>`);
                    $("#line_" + ref_doc).closest(".d-flex")
                        .addClass("border border-success bg-light")
                        .fadeOut(2000);
                    //$("#line_" + ref_doc).fadeOut();

                    updateCompteurs(document_valider, tablo_documents.length);
                }
                $('#modaleAfficheDocument').modal("hide");
            });



            $("#validerRejet").click(function(evt) {
                //alert("validerRejet ")

                var id_sinistre = document.getElementById("id_sinistre").value;
                var traiterpar = document.getElementById("traiterpar").value;
                var observation = document.getElementById("observation").value;

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        id_sinistre: id_sinistre,
                        traiterpar: traiterpar,
                        observation: observation,
                        etat: "confirmerRejetSinistre"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,
                    success: function(response, status) {
                        //console.log(response)

                        let a_afficher = ""
                        if (response != '-1' && response != '0') {
                            let code = response;

                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>La pré-declaration <span class="text-success">` + code + `</span> a bien été rejetée  !</h2></div>`

                        } else {
                            a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du rejet de la pré-declaration <span class="text-success">` + code +
                                `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                        }
                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")

                    },
                    error: function(response, status, etat) {
                        console.log(etat, response)
                    }
                })
            });


            $("#afficheuse").on("change", "#ListeOpe", function(evt) {
                var val = evt.target.value.split('-');
                var key = val[0]
                var lib = val[1]
                $("#delaiTrait").val(lib)
            });

            $("#optionTraitement").on("click", "#confirmerRejet", function(evt) {

                var id_sinistre = document.getElementById("id_sinistre").value;
                var codesinistre = document.getElementById("codesinistre").value;
                var valideur = document.getElementById("valideur").value;
                let observation = $('#obervation').val();


                if (!observation || observation.length === 0) {
                    alert("Veuillez renseigner vos observations pour le rejet de la pré-declaration SVP !!");
                    document.getElementById("obervation").focus();
                    return false;
                }

                if (observation.length > 0) {

                    let libMotif = ""
                    let motifs = ""


                    $("#a_afficher_1").text(codesinistre)
                    $("#a_afficher_2").text(libMotif)


                    $("#id_sinistre").val(id_sinistre)
                    $("#traiterpar").val(valideur)
                    $("#observation").val(observation)
                    $('#confirmation-modal').modal('show')


                } else {
                    alert("Veuillez renseigner vos observations pour le rejet de la pré-declaration SVP !!");
                    document.getElementById("obervation").focus();
                    return false;
                }
            });

            $("#optionTraitement").on("click", "#validerSinistre", function(evt) {

                alert("validerSinistre ")

                if (document_valider.length == tablo_documents.length) {

                    const spinner = document.getElementById("spinner");
                    spinner.style.display = "block"; // Afficher le spinner

                    var id_sinistre = document.getElementById("id_sinistre").value;
                    var traiterpar = document.getElementById("traiterparSinistre").value;

                    console.log(id_sinistre, traiterpar);

                    var idcontrat = document.getElementById("idcontrat").value;
                    var typeOpe = document.getElementById("typeOpe").value;
                    var ListeOpe = document.getElementById("ListeOpe").value;
                    var delaiTrait = document.getElementById("delaiTrait").value;

                    console.log(idcontrat, typeOpe, ListeOpe, delaiTrait);

                    if (typeOpe.length <= 0 || typeOpe == "") {
                        alert("Veuillez renseigner le type de l'operation svp !!");
                        document.getElementById("typeOpe").focus();
                        return false;
                    }

                    if (ListeOpe.length <= 0 || ListeOpe == "") {
                        alert("Veuillez renseigner le libelle de l'operation svp !!");
                        document.getElementById("ListeOpe").focus();
                        return false;
                    }


                    console.log(id_sinistre, traiterpar);

                    $.ajax({
                        url: "config/routes.php",
                        data: {
                            id_sinistre: id_sinistre,
                            traiterpar: traiterpar,
                            idcontrat: idcontrat,
                            typeOpe: typeOpe,
                            ListeOpe: ListeOpe,
                            delaiTrait: delaiTrait,
                            etat: "validerSinistre"
                        },
                        dataType: "json",
                        method: "post",
                        //async: false,
                        success: function(response, status) {

                            spinner.style.display = "none"; // Cacher le spinner
                            if (response != "-1" && response != "0") {

                                a_afficher = `<div class="alert alert-success" role="alert">
								<h2>La pré-declaration <span class="text-success">` + response + `</span> a bien été accepté !</h2> </div>`



                            } else {
                                a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>Desole une erreur est survenue lors de l'acceptation de la prestation <span class="text-danger">` +
                                    response + `</span>  !</h2> </div>`
                            }

                            $("#msgEchec").html(a_afficher)
                            $('#notificationValidation').modal("show")
                        },
                        error: function(response, status, etat) {
                            console.log(etat, response)
                            //   etat =  '-1';
                        }
                    })


                } else {
                    alert("Veuillez renseigner tous les documents svp !!");
                    document.getElementById("typeOpe").focus();
                    return false;
                }

            });










            $("#notificationValidationOK").click(function() {

                $('#notificationValidation').modal('hide')
                //location.href = "detail-sinistre";
                retour();
            })



            function getMenuRejeter() {


                let notif = `
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <div class="form-group col-md-7 col-sm-12" id="afficheuseMotifRejet"></div>
                    <div class="form-group col-md-12 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner vos observations sur le rejet de la pré-declaration de sinistre (<span style="color:red">*</span>) :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation" required><?php echo $sinistre->observationtraitement; ?></textarea>
                    </div>
                </div>`;

                let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    Rejeter la pré-declaration
                </button>`;

                $("#afficheuse").html(notif);
                $("#color_button").text("red");
                $("#optionTraitement").html(bouton_rejet);
            }



            function getMenuValider() {
                const cible = "Technique";
                const idContrat = "<?php echo isset($sinistre->idcontrat) ? $sinistre->idcontrat : ''; ?>";
                var traiterparSinistre = "<?php echo $_SESSION['utilisateur']; ?>";

                let valueType = (cible === "administratif") ? "AVT-Administratif" : "TECH-Technique";

                let notif = `
                    <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Vérification pour migration NSIL</h4>
                    <div style="border-top: 4px solid #033f1f;width:100%;text-align:center;"></div>

                    <div class="row">
                        <div class="form-group col-md-2 col-sm-12">
                            <label for="tel" class="col-form-label">ID contrat :</label>
                            <input type="hidden" id="actionType" name="actionType" value="valider">
                            <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="${idContrat}" disabled>
                            <input type="text" class="form-control" id="traiterparSinistre" name="traiterparSinistre" value="${traiterparSinistre}" hidden>
                        </div>

                        <div class="form-group col-md-3 col-sm-12">
                            <label class="form-label" style="font-size:16px; font-weight:bold;">Type d’opération (<span style="color:red;">*</span>)</label>
                            <select name="typeOpe" id="typeOpe" class="form-control" required>
                                <option value="" disabled>...</option>
                                <option value="TECH-Technique" ${valueType === "TECH-Technique" ? "selected" : "disabled"}>Technique</option>
                                <option value="AVT-Administratif" ${valueType === "AVT-Administratif" ? "selected" : "disabled"}>Administratif</option>
                            </select>
                        </div>

                        <div class="form-group col-md-5 col-sm-12">
                            <label class="form-label" style="font-size:16px; font-weight:bold;">Liste d’opérations <span id="opeType"></span> (<span style="color:red;">*</span>)</label>
                            <select name="ListeOpe" id="ListeOpe" class="form-control" required></select>
                        </div>

                        <div class="form-group col-md-2 col-sm-12">
                            <label class="col-form-label">Délai de traitement :</label>
                            <input type="text" class="form-control" id="delaiTrait" name="delaiTrait" value="" readonly>
                        </div>
                    </div>`;

                let bouton_valider = `
                    <button type="submit" name="validerSinistre" id="validerSinistre" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white">
                        Accepter la pré-declaration
                    </button>
                    <div id="spinner" style="display:none; margin-top:10px;">
                        <div class="spinner-border" style="color:#076633;" role="status"></div>
                    </div>`;

                $("#color_button").text("#033f1f");
                $("#nom_button").text("Enregistrer le traitement");
                $("#afficheuse").html(notif);
                $("#optionTraitement").html(bouton_valider);

            }



            function updateCompteurs(document_valider, total_documents = 5) {
                // console.log("compteur ", document_valider, total_documents, document_valider.length)

                //total_documents = tablo_documents.length;
                let nb_valides = document_valider.length;
                let nb_restants = total_documents - nb_valides;
                $("#compteur_valides").text("Validés : " + nb_valides);
                $("#compteur_restants").text("Restants : " + nb_restants);
            }


            function retour() {
                window.history.back();
            }

            function showToast(message, type = 'success') {
                const toastEl = document.getElementById('customToast');
                const toastBody = document.getElementById('toastMessage');

                // Définir le message
                toastBody.textContent = message;

                // Réinitialiser les classes
                toastEl.className = 'toast align-items-center text-white border-0';

                // Appliquer la couleur selon le type
                switch (type) {
                    case 'success':
                        toastEl.classList.add('bg-success');
                        break;
                    case 'error':
                        toastEl.classList.add('bg-danger');
                        break;
                    case 'info':
                        toastEl.classList.add('bg-info');
                        break;
                    case 'warning':
                        toastEl.classList.add('bg-warning', 'text-dark');
                        break;
                    default:
                        toastEl.classList.add('bg-secondary');
                }

                // Afficher le toast
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }


            function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
                number = parseFloat(number).toFixed(decimals);

                let parts = number.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

                return parts.join(dec_point);
            }

            function chargerListeOperations() {
                const val = $("#typeOpe").val().split("-");
                const key = val[0];
                const lib = val[1] || "";

                if (!key) return;

                $("#opeType").text(lib);
                $("#ListeOpe").html(`<option value="">Chargement des opérations...</option>`);

                $.ajax({
                    url: "https://api.laloyalevie.com/enov/op-type-operation-list",
                    method: "POST",
                    dataType: "json",
                    data: {
                        type: key
                    },
                    success: function(response) {
                        console.log(response);
                        let options = `<option value="">Choisir une opération ${lib}</option>`;
                        $.each(response, function(i, value) {

                            //trier ou MonLibelle contient sinistre
                            if (value.MonLibelle.toLowerCase().includes("sinistre")) {
                                console.log(value.MonLibelle);
                                const code = value.CodeTypeAvenant || "";
                                const delai = value.DelaiTraitement || "";
                                const libelle = value.MonLibelle || "";
                                const valueOpe = `${code}-${delai} jours-${libelle}`;
                                options += `<option value="${valueOpe}">${libelle} (${delai} jours)</option>`;
                            }

                        });
                        $("#ListeOpe").html(options);
                    },
                    error: function(xhr, statut, err) {
                        console.error("Erreur AJAX :", err);
                        $("#ListeOpe").html(`<option value="">Erreur de chargement</option>`);
                    }
                });
            }
        </script>


</body>


</html>