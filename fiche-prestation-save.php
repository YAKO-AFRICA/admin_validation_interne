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

$tablo_doc_attendu = array();

if (isset($_COOKIE["id"])) {


    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idprestation = GetParameter::FromArray($_COOKIE, 'id');
    $action = GetParameter::FromArray($_COOKIE, 'action');
    $code = GetParameter::FromArray($_COOKIE, 'code');

    $prestation = $fonction->_getRetournePrestation(" WHERE id='" . $idprestation . "'  ");

    if ($prestation == null) {
        header('Location: liste-prestation.php');
    }

    $prestation = new tbl_prestations($prestation[0]);
    $retour_documents = $fonction->_getListeDocumentPrestation($idprestation);

    $afficheuse = TRUE;
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
                                    <li class="breadcrumb-item"><a href="accueil-operateur.php">Accueil</a></li>
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
                        <h3 class="text-center" style="color:white">Prestation n° <span style="color:#F9B233;"><?= strtoupper($code) ?></span></h3>
                    </div>
                </div>

                <div class="card-body height-100-p pd-20">

                    <div class="card-body radius-12 w-100 p-4" style="border:1px solid gray;background:gray;color:white">
                        <div class="pd-10 height-50-p mb-30">
                            <div class="row align-items-center">
                                <div class="col-md-4 text-center">
                                    <img src="vendors/images/banner-img.png" width="50%" alt="">
                                </div>

                                <div class="col-md-8">
                                    <div class="col-md-12">
                                        <!--p><span class="text-color">Sexe: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->sexe; ?></span></span></p-->
                                        <p><span class="text-color">Nom & Prenoms: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->souscripteur2; ?></span></span></p>
                                        <p><span class="text-color">Date de naissance :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->datenaissance; ?></span></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">Lieu de residence :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lieuresidence; ?></span></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">Numero de téléphone :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->cel; ?></p>
                                    </div>
                                    <div class="col-md-12">
                                        <p><span class="text-color">E-mail :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->email; ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card-body height-100-p pd-20">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-box mb-30 p-2" style="background-color:bisque ;color:white !important; font-weight:bold;">
                                            <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;">Liste des documents joints</h4>
                                            <hr>
                                            <?php
                                            $i = 0;

                                            if ($retour_documents != null) {
                                                for ($i = 0; $i <= count($retour_documents) - 1; $i++) {
                                                    $tablo = $retour_documents[$i];

                                                    $id_prestation = $tablo["idPrestation"];
                                                    $path_doc = trim($tablo["path"]);
                                                    $type_doc = trim($tablo["type"]);
                                                    $doc_name = trim($tablo["libelle"]);
                                                    $ref_doc = trim($tablo["id"]);
                                                    $datecreation_doc = trim($tablo["created_at"]);
                                                    $documents = Config::URL_PRESTATION_RACINE . $path_doc;

                                                    array_push($tablo_doc_attendu,  $ref_doc);

                                                    // Nom du document basé sur le type
                                                    switch ($type_doc) {
                                                        case 'RIB':
                                                            $nom_document = "RIB";
                                                            break;
                                                        case 'Police':
                                                            $nom_document = "Police du contrat d'assurance";
                                                            break;
                                                        case 'bulletin':
                                                            $nom_document = "Bulletin de souscription";
                                                            break;
                                                        case 'AttestationPerteContrat':
                                                            $nom_document = "Attestation de déclaration de perte";
                                                            break;
                                                        case 'CNI':
                                                            $nom_document = "CNI";
                                                            break;
                                                        case 'etatPrestation':
                                                            $nom_document = "Fiche de demande de prestation";
                                                            break;
                                                        default:
                                                            $nom_document = "Fiche d'identification du numéro de paiement";
                                                            break;
                                                    }

                                                    $values = $id_prestation . "-" . $ref_doc . "-" . $nom_document . "-" . $doc_name;

                                            ?>
                                                    <div class="d-flex align-items-center mt-3">
                                                        <input type="text" class="val_doc" name="val_doc" value="<?php echo $values; ?>" hidden>
                                                        <input type="text" class="path_doc" name="path_doc" value="<?php echo $documents; ?>" hidden>
                                                        <div class="fm-file-box text-success p-2">
                                                            <i class="fa fa-file-pdf-o"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-2">
                                                            <h6 class="mb-0" style="font-size: 12px;">
                                                                <a href="<?= $documents ?>" target="_blank"> <?= $nom_document ?> </a>
                                                            </h6>
                                                            <p class="mb-0 text-secondary" style="font-size: 0.8em;"> <?= $datecreation_doc ?> </p>
                                                        </div>
                                                        <button type="button" class="btn btn-warning bx bx-show"
                                                            data-doc-id="<?= $documents; ?>"
                                                            data-path-doc="<?= $documents; ?>"
                                                            style="background-color:#F9B233 !important;">
                                                            <i class="dw dw-eye"> voir</i>
                                                        </button>
                                                    </div>
                                            <?php

                                                }
                                            } else {
                                                echo '<div class="alert alert-danger" role="alert">  Attention ! <strong>Aucun document joint</strong>. </div>';
                                            }
                                            ?>

                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body height-100-p pd-20">
                            <input type="text" id="tablo_doc_attendu" name="tablo_doc_attendu" value="<?= json_encode($tablo_doc_attendu)  ?>" hidden>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="card-box mb-30">
                                            <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;"> Information sur la prestation </h4>
                                            <hr>
                                            <div class="row pd-20">
                                                <div class="col-md-6">
                                                    <p><span class="text-color">Date<br>demande: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lib_datedemande; ?></span></span></p>
                                                    <p><span class="text-color">Type de prestation: </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->typeprestation; ?></span></span></p>
                                                    <p><span class="text-color">Code<br>prestation :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->code; ?></span></p>
                                                    <p><span class="text-color">Id du contrat :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->idcontrat; ?></span></p>
                                                    <p><span class="text-color">Commentaire :</span> </span><span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->msgClient; ?></span></p>

                                                </div>
                                                <div class="col-md-6">
                                                    <p><span class="text-color">Montant<br>souhaité :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->montantSouhaite; ?> FCFA</span></p>
                                                    <p><span class="text-color">Moyen de paiement :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lib_moyenPaiement; ?></p>
                                                    <?php
                                                    if ($prestation->moyenPaiement == "Virement_Bancaire") {
                                                    ?>
                                                        <p><span class="text-color">IBAN du compte :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->IBAN; ?></p>

                                                    <?php
                                                    } else {
                                                    ?>
                                                        <p><span class="text-color">Operateur :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->lib_Operateur; ?></p>
                                                        <p><span class="text-color">Telephone de Paiement :</span> <span class="text-infos" style="font-size:18px; font-weight:bold;"><?= $prestation->telPaiement; ?></p>
                                                    <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="card-box mb-30" style="background-color:#033f1f ;color:white !important; font-weight:bold;">
                                            <h4 class="text-center p-2" style="color:white !important; font-weight:bold;"> Information sur le contrat via NSIL</h4>
                                            <hr>
                                            <div class="row pd-20">
                                                <div class="row" id="infos-contrat">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pd-20">
                    <div class="card-box height-100-p pd-20">
                        <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> traitement de la prestation </h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        <div class="row card-body" style="background: #D3D3D3;" id="infos_perso">
                            <div class="col-md-6">
                                <p style="float:left">Traité le : <span class="text-infos" id="dateheure" style="font-size:18px; font-weight:bold;"><?= $prestation->created_at; ?></span></p>
                                <span class="text-infos" style="color:#033f1f ; color:white !important"></span>
                            </div>
                            <div class="col-md-6">
                                <p style="float:right">Traité par : <span class="text-infos" style="font-size:18px; font-weight:bold;"><?php echo $_SESSION['utilisateur'];  ?></span></p>
                            </div>
                            <input type="text" id="id_prestation" name="id_prestation" value="<?= $prestation->id; ?>" hidden>
                            <input type="text" class="form-control" id="codeprest" name="codeprest" value="<?php echo $prestation->code; ?>" hidden>

                            <input type="text" id="valideur" name="valideur" value="<?php echo $_SESSION['utilisateur'];  ?>" hidden>
                        </div>
                        <div class="row">
                            <div class="row card-body" style="color: #033f1f;">
                                <div class=" offset-md-6 col-md-6">
                                    <div class="form-group">
                                        <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Etat de la demande de prestation :</label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                            <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">Rejété la demande</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                            <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">Accepter la demande</label>
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
                        <button type="submit" name="traitGen" id="traitGen" class="btn btn-success" style="background: #033f1f !important;">OK</button>
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
                        <div>

                            <!--button type="button" name="download_doc" id="download_doc" class="btn btn-success" style="background: #033f1f !important;">fffffff</button-->
                            <button type="button" name="valid_download" id="valid_download" class="btn btn-success" style="background: #033f1f !important;">VALIDER DOCUMENT</button>

                            <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                        </div>
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
                            Voulez vous rejeter la demande de prestation <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                        </h4>
                        <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                        <span style='color:seagreen'>le client sera notifier du rejet de la prestation</span>
                        </hr>
                        <input type="text" id="idprestation" name="idprestation" hidden>
                        <input type="text" id="motif" name="motif" hidden>
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


        <script>
            let document_valider = [];

            function retour() {
                window.history.back();
            }

            $(document).ready(function() {

                let etape = <?php echo $prestation->etape ?>;
                let idcontrat = "2259833";


                console.log(etape, idcontrat)


                if (idcontrat != "") {
                    remplirModalEtatComtrat(idcontrat)
                }
                /**/


                if (etape == '2') {
                    getMenuValider()
                } else if (etape == '3') {
                    getMenuRejeter()
                }
            })


            $('input[name="customRadio"][value="' + <?php echo $prestation->etape; ?> + '"]').attr('checked', 'checked');
            $('input[name="customRadio"]').change(function() {
                // Lorsqu'on change de valeur dans la liste
                var valeur = $(this).val();

                // On supprime le commentaire et vérifie la valeur
                if (valeur == '3') {
                    // La prestation est rejetée
                    getMenuRejeter();

                } else if (valeur == '2') {
                    // La prestation est validée
                    let tablo_attendu = <?php echo json_encode($tablo_doc_attendu); ?>;
                    console.log(tablo_attendu);
                    console.log(document_valider);

                    // On vérifie si le nombre de documents validés correspond au nombre attendu
                    if (document_valider.length === tablo_attendu.length) {
                        $("#color_button").text("green");
                        $("#nom_button").text("Valider la prestation");
                        getMenuValider();

                    } else {
                        $("#afficheuse").empty();
                        $('input[name="customRadio"][value="1"]').prop('checked', true); // Remplace attr('checked', 'checked') par prop
                        alert("Désolé, veuillez vérifier que tous les documents joints soient corrects, puis valider la prestation ou cliquer sur rejeter la prestation.");
                    }

                } else {
                    // La prestation est en attente
                    alert('La prestation est en attente.');
                    $("#afficheuse").empty();
                    $("#color_button").text("#F9B233");
                    $("#optionTraitement").empty();
                    // Si tu veux désactiver un bouton, tu peux décommenter cette ligne :
                    // $('#envoyer').attr('disabled', 'disabled');
                }
            });



            $("#optionTraitement").on("click", "#confirmerRejet", function(evt) {

                var id_prestation = document.getElementById("id_prestation").value;
                var codeprest = document.getElementById("codeprest").value;
                var valideur = document.getElementById("valideur").value;
                //var motifRejet = document.getElementById("motifRejet").value;
                //var observation = document.getElementById("obervation").value;

                let observation = $('#obervation').val();
                let motifs = $('#motifRejet').val();

                if (!motifs || motifs.length === 0) {
                    alert("Veuillez sélectionner au moins un motif de rejet.");
                    return;
                }
                console.log(motifs);

                if (motifs.length > 0) {

                    let libMotif = ""

                    motifs.forEach(function(motif) {
                        // Sépare chaque motif sur le symbole "|"
                        // console.log(motif);
                        let parts = motif.split('|');
                        let id = parts[0];
                        let libelle = parts[1];
                        console.log("ID:", id, "Libellé:", libelle);
                        //libMotif += "Motif " + (index + 1) + " (ID " + id + ") : " + libelle + "\n";

                    });

                    //alert(libMotif)
                    /*var valMotif = motifRejet.split('|');
                    var idMotif = valMotif[0]
                    var libMotif = valMotif[1]*/

                    $("#a_afficher_1").text(codeprest)
                    $("#a_afficher_2").text(libMotif)


                    $("#idprestation").val(id_prestation)
                    $("#traiterpar").val(valideur)
                    $("#motif").val(motifs)
                    $("#observation").val(observation)

                    $('#confirmation-modal').modal('show')

                } else {
                    alert("Veuillez renseigner le motif de rejet svp !!");
                    document.getElementById("motifRejet").focus();
                    return false;
                }
            })


            $("#validerRejet").click(function(evt) {
                //alert("validerRejet ")

                var idprestation = document.getElementById("idprestation").value;
                var motif = document.getElementById("motif").value;
                var traiterpar = document.getElementById("traiterpar").value;
                var observation = document.getElementById("observation").value;

                console.log(idprestation, motif, traiterpar, observation);



                $.ajax({
                    url: "config/routes.php",
                    data: {
                        idprestation: idprestation,
                        motif: motif,
                        traiterpar: traiterpar,
                        observation: observation,
                        etat: "confirmerRejet"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,
                    success: function(response, status) {
                        console.log(response)

                        let a_afficher = ""
                        if (response != '-1' && response != '0') {
                            let code = response;

                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>La prestation <span class="text-success">` + code + `</span> a bien été rejetée  !</h2></div>`

                        } else {
                            a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du rejet de la prestation <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                        }

                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")

                    },
                    error: function(response, status, etat) {
                        console.log(etat, response)
                        //   etat =  '-1';
                    }
                })

            })


            $("#annulerRejet").click(function(evt) {

                var idprestation = document.getElementById("idprestation").value;
                var motif = document.getElementById("motif").value;
                var traiterpar = document.getElementById("traiterpar").value;
                $('#confirmation-modal').modal('hide')
            })


            $("#optionTraitement").on("click", "#valider", function(evt) {

                var id_prestation = document.getElementById("id_prestation").value;
                var codeprest = document.getElementById("codeprest").value;
                var valideur = document.getElementById("valideur").value;

                var idcontrat = document.getElementById("idcontrat").value;
                var typeOpe = document.getElementById("typeOpe").value;
                var ListeOpe = document.getElementById("ListeOpe").value;
                var delaiTrait = document.getElementById("delaiTrait").value;


                console.log(id_prestation, codeprest, valideur);
                console.log(idcontrat, typeOpe, ListeOpe, delaiTrait);

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        idprestation: id_prestation,
                        code: codeprest,
                        traiterpar: valideur,
                        idcontrat: idcontrat,
                        typeOpe: typeOpe,
                        ListeOpe: ListeOpe,
                        delaiTrait: delaiTrait,
                        etat: "validerprestation"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,
                    success: function(response, status) {
                        console.log(response)

                        let result = response["result"];
                        let total = response["total"];
                        let resultat = response["data"];
                        //console.log(result)

                        if (result == "SUCCESS") {
                            //console.log(resultat)
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>La prestation <span class="text-success">` + codeprest + `</span> a bien été accepté !</h2> </div>`
                        } else {
                            a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>Desole une erreur est survenue lors de l'acceptation de la prestation <span class="text-danger">` + codeprest + `</span>  !</h2> </div>`
                        }

                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")

                    },
                    error: function(response, status, etat) {
                        console.log(etat, response)
                        //   etat =  '-1';
                    }
                })
            })


            $("#traitGen").click(function() {

                $('#modaleAfficheDocument').modal('hide')
                window.history.back();
            })


            $(".bx-show").click(function(evt) {
                var path_document = $(this).data("path-doc");
                var val_doc = $(this).closest('.d-flex').find('.val_doc').val();

                console.log("val_doc: ", val_doc);
                console.log("path_document: ", path_document);

                if (path_document && path_document.length > 0) {
                    // Créer un iframe pour afficher le document PDF
                    let html = `<iframe src="${path_document}" width="100%" height="500"></iframe>`;

                    // Créer un lien de téléchargement pour le document
                    let html_download = `<a href="${path_document}" download target="_blank" class="text-white">
                             <i>TELECHARGER DOCUMENT</i></a>`;

                    // Mettre à jour le contenu de la modale avec l'iframe et le lien de téléchargement
                    $("#lien_document").text(path_document);
                    $("#document").val(path_document);
                    $("#val_doc2").val(val_doc);
                    $("#iframeAfficheDocument").html(html);

                    // Afficher la modale contenant l'iframe
                    $('#modaleAfficheDocument').modal("show");
                } else {
                    // Si le chemin du document est vide ou invalide
                    alert("Aucun document trouvé.");
                }
            });


            $("#afficheuse").on("change", "#typeOpe", function(evt) {
                var val = evt.target.value.split('-');
                var key = val[0]
                var lib = val[1]
                console.log(val)

                $("#opeType").val(lib)

                $.ajax({
                    url: "https://api.laloyalevie.com/enov/op-type-operation-list",
                    // url: "http://192.168.11.163/enovapi/public/enov/op-type-operation-list",
                    data: {
                        type: key
                    },
                    dataType: "json",
                    method: "post",
                    success: function(response, statut) {
                        console.log(response)
                        var apt = "<option> Choix de l\'operation " + lib + "</option>"
                        //var apt = ""
                        $.each(response, function(key, value) {
                            // console.log(value.MonLibelle)
                            // console.log(value.CodeTypeAvenant)
                            let valueOpe = value.CodeTypeAvenant + '-' + value.DelaiTraitement + ' jours' + '-' + value.MonLibelle
                            apt += '<option  value="' + valueOpe + '">' + value.MonLibelle + '-(' + value.DelaiTraitement + ' jours)' + '</option>'
                        })
                        $("#ListeOpe").html(apt)
                    },
                    error: function(response, statut, err) {
                        console.log(err)
                        console.log(response)
                    }
                })
            })


            $("#afficheuse").on("change", "#ListeOpe", function(evt) {
                var val = evt.target.value.split('-');
                var key = val[0]
                var lib = val[1]

                $("#delaiTrait").val(lib)
            })


            $("#modaleAfficheDocument").on("click", "#valid_download", function(evt) {

                // Récupération des valeurs des éléments d'entrée
                var doc = document.getElementById("document").value; // Récupère la valeur de l'élément avec l'ID 'document'
                var val_doc = document.getElementById("val_doc2").value; // Récupère la valeur de l'élément avec l'ID 'val_doc2'

                // Découper la valeur de 'val_doc' en un tableau
                var tab = val_doc.split('-');
                var id_prestation = tab[0]; // Id prestation
                var ref_doc = tab[1]; // Référence du document
                var doc_name = tab[2]; // Nom du document
                var type_doc = tab[3]; // Type du document

                // Décoder le tableau PHP passé en JavaScript via json_encode()
                let tablo_attendu = <?php echo json_encode($tablo_doc_attendu); ?>;

                //console.log(tablo_attendu); // Affiche dans la console le tableau attendu

                // Vérifier si 'ref_doc' est présent dans 'document_valider' (tableau)

                let index = document_valider.indexOf(ref_doc);
                if (index !== -1) {
                    console.log("L'élément " + ref_doc + " se trouve à l'indice " + index);
                    alert(doc_name + " a dejà été validé");
                } else {
                    console.log("L'élément " + ref_doc + " n'est pas dans le tableau.");

                    // Ajouter la référence du document dans le tableau 'document_valider'
                    document_valider.push(ref_doc);
                    alert(doc_name + " a bien été validé");
                }



                // Fermer la modale
                $('#modaleAfficheDocument').modal("hide");

                // Optionnel : ouvrir le document dans une nouvelle fenêtre (désactivé pour l'instant)
                // window.open(doc); 
            });



            $("#closeNotif").click(function() {
                $('#notification').modal('hide')
                window.history.back();
            })


            $("#closeNotif").click(function() {
                $('#notification').modal('hide')
                window.history.back();
            })


            $("#closeEchec").click(function() {
                $('#echecNotification').modal('hide')
                location.reload();
            })


            $("#afficheuse").on("change", "#motifRejet", function(evt) {
                objet = $(this).val();
                console.log(objet + " ici")

                if (objet != '') {

                    var valM = objet.split('|');
                    var keyM = valM[0]
                    var libM = valM[1]

                    let libelleMR = `<div class="col-md-12">
							<label for="infoRdv" style="background-color:red ;color:white;font-size:20px; font-weight:bold;"> Motif de rejet : ${libM}</label>
						</div>`
                    $("#libMotif").html(libelleMR)
                }
            })



            function getMenuValider() {

                let notif = `
                            <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Verification pour migration NSIL </h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                                
                            <div class="row">
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">id contrat :</label>
                                    <input type="text" class="form-control" id="actionType" name="actionType" value="valider" hidden>
                                    <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="<?php echo $prestation->idcontrat; ?>" disabled>
                                    </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;">Type d\'opération (<span style="color:red;">*</span>)</label>
                                        <select name="typeOpe" id="typeOpe" class="form-control "  data-rule="required">
                                            <option value="">...</option>
                                            <option value="TECH-Technique">Technique</option>
                                            <option value="AVT-Administratif">Administratif</option>
                                            
                                        </select>
                                </div>
                                <div class="form-group col-md-5 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;" >Liste d\'opération <span id="opeType"></span> (<span style="color:red;">*</span>)</label>
                                        <select name="ListeOpe" id="ListeOpe" class="form-control " data-rule="required">
                                            
                                        </select>
                                </div>
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">Délai de traitement :</label>
                                    <input type="text" class="form-control" id="delaiTrait" name="delaiTrait" value="" disabled>
                                </div>
                            </div>
                        `

                let bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white"> Accepter la prestation</button>`


                $("#color_button").text(`#033f1f`)
                $("#nom_button").text(`Enregistrer le traitement`)
                $("#afficheuse").html(notif)
                $("#optionTraitement").html(bouton_valider)
            }


            function getListeMotifRejet() {
                $.ajax({
                    url: "config/routes.php",
                    method: "post",
                    dataType: "json",
                    data: {
                        etat: "listeMotifRejet"
                    },
                    success: function(response) {
                        if (response != '-1') {
                            let html = `<label for="motifRejet" class="col-form-label">Motif de rejet :</label>
                            <select name="motifRejet[]" id="motifRejet" class="form-control" multiple data-msg="Objet" data-rule="required">
                                <option value="">...</option>`;

                            $.each(response, function(indx, data) {
                                let valus = data.code + '|' + data.libelle;
                                html += `<option value="${valus}" id="ob-${indx}">${data.libelle}</option>`;
                            });

                            html += `<option value="99|autres">AUTRE MOTIF</option></select>`;

                            $("#afficheuseMotifRejet").html(html);

                            // Initialisation de Select2
                            $('#motifRejet').select2({
                                placeholder: "Sélectionnez un ou plusieurs motifs",
                                width: '100%'
                            });
                        }
                    },
                    error: function(response) {
                        console.error("Erreur AJAX :", response);
                    }
                });
            }

            function getMenuRejeter() {
                let notif = `
        <div class="row">
            <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
            
            <div class="form-group col-md-8 col-sm-12" id="afficheuseMotifRejet"></div>

            <div class="form-group col-md-4 col-sm-12">
                <label for="obervation" class="col-form-label">
                    Veuillez renseigner vos observations sur le rejet de la demande de prestation :
                </label>
                <textarea class="form-control" id="obervation" name="obervation"></textarea>
            </div>

            <span id="libMotif"></span>
        </div>`;

                let bouton_rejet = `
        <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
            Rejeter la prestation
        </button>`;

                $("#afficheuse").html(notif);
                $("#color_button").text("red");
                $("#optionTraitement").html(bouton_rejet);

                getListeMotifRejet();
            }




            function getRecupRadio(radios) {

                var found = 1;
                for (var i = 0; i < radios.length; i++) {
                    if (radios[i].checked) {
                        //alert(radios[i].value);
                        found = 0;
                        return radios[i].value;
                        break;
                    }
                }
                if (found == 1) {
                    return "1";
                }
            }


            function remplirModalEtatComtrat(idProposition) {

                let retour = getAPIVerificationProfil(idProposition);
                let details;
                let confirmer
                let nonRegle
                let enc
                let infos = ""
                if (retour) {
                    details = retour["details"]
                    confirmer = retour['regle']
                    enc = retour['enc']
                    nonRegle = enc["nonRegle"]

                    let contactsPersonne = retour["contactsPersonne"]
                    console.log(details)
                    //console.log(contactsPersonne)


                    let numero = ''

                    $.each(contactsPersonne, function(indxct, contactP) {

                        if (contactP.contact != "" && contactP.contact != null) {
                            numero += `
                            <div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Numero ${indxct} : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${contactP.contact}</span></div>
                            `
                        }
                    })

                    $.each(details, function(indx, data) {

                        infos += `
					<div class="row card-body">
						<div class="form-group  col-md-12" style="font-size:11px; font-weight:bold;"> Nom et prenom  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.nomSous+" "+data.PrenomSous}</span></div>
						<div class="form-group  col-md-12" style="font-size:11px; font-weight:bold;"> Date de naissance : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DateNaissance}</span></div>
			
						${numero}	
                    </div>	
                    <div class="row card-body">
						<div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Produit  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.produit}</span></div>
						<div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Capital : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.CapitalSouscrit)}</span></div>
						<div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Prime  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalPrime)}</span></div>
						
                        <div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Effet Reel  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DateEffetReel}</span></div>
						<div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Fin Adhesion  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.FinAdhesion}</span></div>
					    <div class="form-group  col-md-4" style="font-size:11px; font-weight:bold;"> Duree du contrat : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DureeCotisationAns}</span></div>
						
                    </div>						
					<div class="row card-body">
						<div class="form-group  col-md-6" style="font-size:11px; font-weight:bold;"> Nbre d'emission  : <span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEmission}</span></div>
						<div class="form-group  col-md-6" style="font-size:11px; font-weight:bold;"> Total Emission  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEmission)} FCFA</span></div>
						
                        <div class="form-group  col-md-6" style="font-size:11px; font-weight:bold;"> Nbre Encaissment  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEncaissment}</span></div>
						<div class="form-group  col-md-6" style="font-size:11px; font-weight:bold;"> Total Encaissement  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEncaissement)} FCFA</span></div>
						
                        <div class="form-group  col-md-6" style="font-size:11px; font-weight:bold;"> Nbre Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreImpayes} </span></div>
						<div class="form-group  col-md-6" style="font-size:11px; font-weight:bold;"> Total Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalImpayes)} FCFA</span></div>
					</div>`
                    })
                    $("#infos-contrat").html(infos);

                }

            }



            function myFunction() {
                var x = document.getElementById("myDIV");
                if (x.style.display === "none") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
            }

            function getAPIVerificationProfil(keys) {
                let resultat;
                $.ajax({
                    url: "https://api.laloyalevie.com/oldweb/encaissement-bis",
                    data: {
                        idContrat: keys
                    },
                    dataType: "json",
                    method: "post",
                    async: false,
                    success: function(response, status) {
                        console.log(response)
                        resultat = response
                    },
                    error: function(response, status, etat) {
                        resultat = '-1';
                    }
                })
                return resultat
            }
        </script>


</body>

<!--script>
	var oTable = $('#listeDemande').DataTable({
		order: [
			[0, 'desc']
		],
		buttons: [
			'copy', 'excel', 'pdf'
		]
	});
</script-->

</html>