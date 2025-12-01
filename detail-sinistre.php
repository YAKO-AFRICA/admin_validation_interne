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

if (isset($_COOKIE["idsinistre"])) {

    $afficheuse = TRUE;
    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idsinistre = GetParameter::FromArray($_COOKIE, 'idsinistre');
    $action = GetParameter::FromArray($_COOKIE, 'action');
    $code = GetParameter::FromArray($_COOKIE, 'code');


    $plus = " YEAR(created_at) = YEAR(CURDATE())";
    $sqlSelect = " SELECT * FROM tbl_sinistres  WHERE $plus AND id = '" . $idsinistre . "'  ORDER BY id DESC ";

    $retourSinistre = $fonction->_getSelectDatabases($sqlSelect);
    if ($retourSinistre != null) {
        $sinistre = $retourSinistre[0];
    } else {
        header('Location:liste-sinistres');
    }
} else {
    header("Location:deconnexion.php");
}


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
                <hr>
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
                <hr>
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


                        </div>
                        <div class="col-md-6">
                            <?php if ($sinistre->natureSinistre == "Deces") { ?>
                                <p><span class="text-color">Decès Accidentel:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->decesAccidentel == "1" ? "Oui" : "Non" ?? "--" ?></span></p>
                                <p><span class="text-color">Declaration Tardive:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->declarationTardive == "1" ? "Oui" : "Non" ?? "--" ?></span></p>
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
                <hr>
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


                <hr>
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4" style="color:#033f1f!important;">Listes des documents joints</h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                    </div>
                    <div class="row pd-20">
                        <?php
                        $retour_documents = $fonction->_getListeDocumentSinistre($sinistre->id);

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
                        ?>

                                <div class="col-md-4 border-right">
                                    <div class="d-flex align-items-center mt-3 document-ligne" id="line_<?= $ref_doc ?>">
                                        <input type="text" class="val_doc" name="val_doc" value="<?php echo $values; ?>" hidden>
                                        <input type="text" class="path_doc" name="path_doc" value="<?php echo $documents; ?>"
                                            hidden>

                                        <div class="fm-file-box text-success p-2">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0" style="font-size: 12px;">
                                                <a href="<?= $documents ?>" target="_blank"> <?= $nom_document ?> </a>
                                            </h6>
                                            <p class="mb-0 text-secondary" style="font-size: 0.6em;">
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
                        }
                        ?>
                    </div>

                </div>

                <?php

                if ($sinistre->etape != "1") {

                    $retourEtat = Config::tablo_statut_prestation[$sinistre->etape];
                ?>

                    <hr>
                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <h4 class="text-blue h4" style="color:#033f1f!important;">Informations sur le traitement du sinistre</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        </div>
                        <div class="row pd-20">

                            <div class="col-md-6">
                                <p><span class="text-color">Statut du sinistre :</span> <span class="text-infos " style="text-transform:uppercase; font-weight:bold;"><?php echo $retourEtat["libelle"]; ?></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><span class="text-color">Date de declaration :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= (empty($sinistre->created_at) ? "--" :  date("d/m/Y H:i:s", strtotime($sinistre->created_at))) ?></span></p>
                            </div>
                            <div class="col-md-3">
                                <p><span class="text-color">Date de traitement :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= (empty($sinistre->updated_at) ? "--" :  date("d/m/Y H:i:s", strtotime($sinistre->updated_at))) ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><span class="text-color">Traiter par :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->traiterpar ?? "--" ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><span class="text-color">Observation :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $sinistre->observationtraitement ?? "--" ?></span></p>
                            </div>
                        </div>
                        <?php
                        if ($sinistre->etape == "2") {
                        ?>
                            <div class="row pd-20">
                                <div class="col-md-6">
                                    <p><span class="text-color">Migration NSIL : </span><span class="text-infos"
                                            style="font-size:18px; font-weight:bold;"><?= $sinistre->estMigree == "1" ? "Oui" : "Non" ?? "--" ?></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="text-color">Date Migration NSIL : </span><span
                                            class="text-infos" style="font-size:18px; font-weight:bold; color:#033f1f"><?= $sinistre->migreele; ?></span>
                                    </p>
                                </div>
                                <?php
                                $detailPrestationNsil = $fonction->_GetDetailsTraitementPrestation($sinistre->id, "sinistre");
                                if ($detailPrestationNsil != null) {

                                ?>

                                    <div class="col-md-6">
                                        <p><span class="text-color">libelle Operation : </span><span class="text-infos"
                                                style="font-size:18px; font-weight:bold; color:#033f1f"><?= $detailPrestationNsil->libelleOperation; ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><span class="text-color">delai Traitement : </span><span class="text-infos"
                                                style="font-size:18px; font-weight:bold; color:#033f1f"><?= $detailPrestationNsil->delaiTraitement; ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><span class="text-color">id courrier NSIL : </span><span class="text-infos"
                                                style="font-size:18px; font-weight:bold; color:#033f1f"><?= $detailPrestationNsil->idTblCourrier; ?></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><span class="text-color">code courrier NSIL : </span><span class="text-infos"
                                                style="font-size:18px; font-weight:bold; color:#033f1f"><?= $detailPrestationNsil->codeCourrier; ?></span>
                                        </p>
                                    </div>

                                <?php
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                <?php

                }
                ?>

            </div>
        </div>

        <div class="footer-wrap pd-20 mb-20">
            <?php include "include/footer.php";    ?>
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

    <div class="modal fade" id="modaleAfficheDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content ">
                <div class="modal-body text-center">
                    <div class="card-body" id="iframeAfficheDocument">

                    </div>

                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" id="closeEchec" class="btn btn-secondary"
                            data-dismiss="modal">FERMER</button>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


    <script>
        $(document).ready(function() {

            $(".bx-show").click(function() {
                let path_document = $(this).data("path-doc");
                let val_doc = $(this).closest('.d-flex').find('.val_doc').val();
                let html = `<iframe src="${path_document}" width="100%" height="500"></iframe>`;
                $("#document").val(path_document);
                $("#val_doc2").val(val_doc);
                $("#iframeAfficheDocument").html(html);
                $('#modaleAfficheDocument').modal("show");
            });

        });

        function retour() {
            window.history.back();
        }
    </script>


</body>



</html>