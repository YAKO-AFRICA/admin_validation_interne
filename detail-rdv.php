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

if (isset($_COOKIE["idrdv"])) {


    $idcontrat = GetParameter::FromArray($_COOKIE, 'idcontrat');
    $idrdv = GetParameter::FromArray($_COOKIE, 'idrdv');
    $action = GetParameter::FromArray($_COOKIE, 'action');



    $retour_rdv = $fonction->_getRetourneDetailRDV($idrdv);
    if ($retour_rdv == null) {
        header('Location: liste-rdv-attente');
        exit;
    }

    $rdv = $retour_rdv[0];
    $retourEtat = Config::tablo_statut_rdv[$rdv->etat];
    $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';
    if ($rdv->etatSms == "1") {
        $lib_etatSms = "Oui";
        $color_etatSms = "badge badge-success";
    } else {
        $lib_etatSms = "Non";
        $color_etatSms = "badge badge-danger";
    }

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
                <!-- Page Header -->
                <div class="page-header mb-3">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="text-primary font-weight-bold">Traitement des demandes</h4>
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb bg-transparent p-0">
                                    <li class="breadcrumb-item"><a href="accueil-operateur.php">Accueil</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Détail Rdv</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Bouton retour -->
                <div class="mb-3">
                    <button class="btn btn-warning" onclick="retour()">
                        <i class="fa fa-arrow-left"></i> Retour
                    </button>
                </div>

                <!-- Titre RDV -->
                <div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
                    <div class="card-body text-center ">
                        <h3 style="color:white">Détail RDV N°
                            <span class="text-warning">
                                <?= strtoupper($rdv->idrdv) . " du  " . $rdv->daterdv ?>
                            </span>
                        </h3>
                    </div>
                </div>


                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4" style="color:#033f1f!important;">Detail du client</h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                    </div>
                    <div class="row pd-20">
                        <div class="col-md-6">
                            <p><span class="text-color">Titre :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->titre ?? '' ?></span></p>
                            <p><span class="text-color">Nom & Prenom :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomclient ?? '' ?></span></p>
                            <p><span class="text-color">Date de naissance :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->datenaissance ?? '' ?></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="text-color">Lieu de residence :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->lieuresidence ?? '' ?></span></p>
                            <p><span class="text-color">Numero de téléphone :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->tel ?? ''  ?></span></p>
                            <p><span class="text-color">E-mail :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->email ?? '' ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="card-box mb-30">
                    <div class="pd-20">
                        <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Rendez-vous</h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                    </div>
                    <div class="row pd-20">
                        <div class="col-md-6">
                            <p><span class="text-color"> Date de prise de rdv</span> : <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->dateajou ?></span></p>
                            <p><span class="text-color">Ville du Rdv choisie :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeChoisie ?? "--" ?></span></p>
                            <p><span class="text-color">Date de Rdv choisie:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->daterdv ?? "--" ?></span></p>
                            <p><span class="text-color">Ville du Rdv effective:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeEffective ?? "--" ?></span></p>
                            <p><span class="text-color">Date de Rdv effective:</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($rdv->daterdveff) ? "--" : date("d/m/Y", strtotime($rdv->daterdveff)) ?></span></p>

                        </div>
                        <div class="col-md-6">
                            <p><span class="text-color">Traiter le :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->datetraitement ?? "--" ?></span></p>
                            <p><span class="text-color">Traiter par :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= ($rdv->etat == "2" || $rdv->etat == "3") ? strtoupper($rdv->nomgestionnaire) :  strtoupper($rdv->nomAdmin . " " . $rdv->prenomAdmin) ?? "--" ?></span></p>
                            <p><span class="text-color">Motif du Rdv :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->motifrdv ?? "---" ?></span></p>
                            <p><span class="text-color">ID contrat / N° de police(s) :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->police ?? "---" ?></span></p>
                            <p><span class="text-color">Etat du rdv :</span> <span class="<?php echo $retourEtat["color_statut"]; ?>"><?php echo $retourEtat["libelle"] ?></span></p>
                            <?php

                            if ($rdv->etat == "0") {
                            ?>
                                <p><span class="text-color">Motif d'annulation :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->reponse ?? "--" ?></span></p>
                            <?php
                            }
                            ?>
                        </div>

                    </div>
                </div>

                <?php

                if ($rdv->etat == "2" || $rdv->etat == "3") {
                ?>
                    <div class="card-box mb-30">
                        <div class="pd-20">
                            <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Transmission du Rendez-vous effectif</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        </div>
                        <div class="row pd-20">
                            <div class="col-md-6">
                                <p><span class="text-color">Transmis le :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= empty($rdv->transmisLe) ? "" : date("d/m/Y à H:i:s", strtotime($rdv->transmisLe)) ?></span></p>
                                <p><span class="text-color">Transmis à :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomgestionnaire ?? "--" ?></span></p>
                                <p><span class="text-color">Villes : </span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->villeEffective ?? "" ?></span></p>

                            </div>
                            <div class="col-md-6">
                                <p><span class="text-color">Transmis par :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->nomAdmin . " " . $rdv->prenomAdmin ?></span></p>
                                <p><span class="text-color">Sms envoyé ? :</span> <span style="text-transform:uppercase; font-weight:bold;" class="<?php echo $color_etatSms; ?>"><?php echo $lib_etatSms ?></span></p>
                                <p><span class="text-color">Issue apres Rdv :</span> <span class="text-infos"><?= $rdv->estPermit == 1 && $rdv->etatTraitement == 1 ? "<span class='btn btn-success btn-sm'>Accordé pour $rdv->motifrdv </span>" : ($rdv->estPermit == 1 && $rdv->etatTraitement != 1 ? "<span class='btn btn-danger '>Non Accordé pour $rdv->motifrdv</span>" : "") ?></span></p>
                                <p><span class="text-color">Reponse Apres entretien :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->libelleTraitement ?? "--" ?></span></p>
                            </div>
                        </div>
                        <div class="row pd-20">
                            <div class="col-md-12">
                                <p><span class="text-color">Observation :</span> lor <span class="text-infos" style="font-weight:bold;"><?= $rdv->reponseGest ?? "" ?></span></p>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($rdv->etatCourrier != "") {
                    ?>

                        <div class="card-box mb-30">
                            <div class="pd-20">
                                <h4 class="text-blue h4" style="color:#033f1f!important;">Détail Courrier</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            </div>
                            <div class="row pl-20">
                                <div class="col-md-6">
                                    <p><span class="text-color">Etat du courrier :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->etatCourrier == "-1" ? "Pas encore déposé" : ($rdv->etatCourrier == "0" ? "Réjété" : ($rdv->etatCourrier == "1" ? "En attente de traitement" : ($rdv->etatCourrier == "2" ? "Validé" : ""))) ?></span></p>
                                    <p><span class="text-color">Reponse apres traitement :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->reponseCourrier  ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><span class="text-color">Date de depôt de courrier :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->etatCourrier == "-1" ? ($rdv->createdCourrier == "" ? "" : date("d/m/Y", strtotime($rdv->createdCourrier))) : ($rdv->deposeCourrier == "" ? "" : date("d/m/Y", strtotime($rdv->deposeCourrier))) ?></span></p>
                                    <p><span class="text-color">Date de traitement de courrier :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= $rdv->traiteCourrier == "" ? "" : date("d/m/Y", strtotime($rdv->traiteCourrier)) ?></span></p>
                                </div>
                            </div>
                            <div class="row pd-20 d-flex justify-content-end">
                                <button class="btn btn-warning" onclick="retour()">
                                    <i class="fa fa-arrow-left"></i> Retour
                                </button>
                            </div>
                        </div>
                <?php
                    }
                }

                ?>
            </div>

            <div class="footer-wrap pd-20 mb-20">
                <?php include "include/footer.php"; ?>
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
                    <button type="button" name="valid_download" id="valid_download" class="btn btn-success" style="background: #033f1f !important;">VALIDER DOCUMENT</button>
                    <button type="button" name="annuler_download" id="annuler_download" class="btn btn-danger" style="background:red !important;">REJETER DOCUMENT</button>
                    <button type="button" id="closeAfficheDocument" name="closeAfficheDocument" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
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

    <div class="modal fade" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content ">
                <div class="modal-body text-center">
                    <div class="card-body" id="msgEchec">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="retourNotification" name="retourNotification" class="btn btn-success" style="background: #033f1f !important;">OK</button>
                    <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
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
        $(document).ready(function() {

            const etape = <?= $rdv->etat ?>;
            const idcontrat = "<?= $rdv->police ?>";
            const idVilleEff = "<?= $rdv->idTblBureau ?>";

            var objetRDV = document.getElementById("villesRDV").value;
            var dateRDVEffective = document.getElementById("daterdveff").value;

            if (idcontrat !== "") {
                //remplirModalEtatComtrat(idcontrat);
            }

            console.log(objetRDV + " " + dateRDVEffective);

            checkDate('0');

            $('input[name="customRadio"]').change(function() {
                const valeur = $(this).val();

                if (valeur === '3') {
                    getMenuRejeter();
                } else if (valeur === '2') {
                    getMenuValider();
                } else {
                    alert('La prestation est en attente.');
                    $("#afficheuse").empty();
                    $("#color_button").text("#F9B233");
                    $("#optionTraitement").empty();
                }
            });



            // Quand la ville change
            $('#villesRDV').change(function() {

                if ($(this).val() === "null") return;

                const dateRDVEffective = $(this).val();
                const [idvillesRDV, villesRDV] = $(this).val().split(";");

                console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  " + dateRDVEffective);

                const valeur = $('input[name="customRadio"]:checked').val();
                if (valeur === '2') {
                    getMenuValider();
                }
                //verifierActivationBouton();
                checkDate('0');
            });

            // Quand la date change
            $('#daterdveff').change(function() {

                const dateRDVEffective = $(this).val();
                const objetRDV = $('#villesRDV').val();
                if (objetRDV === "null") return;

                const [idvillesRDV, villesRDV] = objetRDV.split(";");

                console.log("Nouvelle date RDV effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  " + dateRDVEffective);
                const valeur = $('input[name="customRadio"]:checked').val();
                if (valeur === '2') {
                    getMenuValider();
                }
            });



        });



        $("#afficheuse").on("change", "#ListeGest", function(evt) {

        })

        $(document).on("click", "#valider", function(evt) {

            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();
            const gestionnaire = $('#ListeGest').val();

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = gestionnaire.split("|");

            ///console.log(" valider rdv " + objetRDV + " " + dateRDV + " " + etat + " " + gestionnaire);

            $.ajax({
                url: "config/routes.php",
                method: "post",
                dataType: "json",
                data: {
                    idrdv: "<?= $rdv->idrdv ?>",
                    idcontrat: "<?= $rdv->police ?>",
                    gestionnaire: gestionnaire,
                    objetRDV: objetRDV,
                    daterdveff: dateRDV,
                    etat: "transmettreRDV",
                },
                success: function(response) {

                    if (response != '-1') {
                        let a_afficher = `
                        <div class="alert alert-success" role="alert" style="text-align: center; font-size: 18px ; color: #033f1f; font-weight: bold">
                            Le rdv n° ${response} a bien été transmis au gestionnaire ${nomgestionnaire} pour reception le ${dateRDV} à ${villesGestionnaire}
                        </div>`;

                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")
                    } else {
                        let a_afficher = `
                        <div class="alert alert-danger" role="alert">
                           Desole , le rdv n° ${response} n'a pas été transmis au gestionnaire ${nomgestionnaire} 
                        </div>`;


                        $("#msgEchec").html(a_afficher)
                        $('#notificationValidation').modal("show")
                    }

                },
                error: function(response) {
                    console.error("Erreur AJAX :", response);
                }
            })
        })

        $("#retourNotification").click(function() {

            $('#notificationValidation').modal('hide')
            location.href = "detail-rdv";

        })

        // Quand un gestionnaire est sélectionné
        $(document).on('change', '#ListeGest', function() {
            verifierActivationBouton();
        });

        function retour() {
            window.history.back();
        }


        // Génère le formulaire de validation
        function getMenuValider() {

            const objetRDV = $("#villesRDV").val();
            const dateRDVEffective = $("#daterdveff").val();

            const [idvillesRDV, villesRDV] = objetRDV.split(";");

            const notif = `
                <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Affecter le RDV n° <?php echo $rdv->idrdv; ?> à un gestionnaire pour la Transformation</h4>
                <div style="border-top: 4px solid #033f1f;width: 100%;text-align: center;"></div>
                <div class="row">
                    <div class="form-group col-md-2 col-sm-12">
                        <label class="col-form-label">id contrat / police :</label>
                        <input type="text" class="form-control" name="actionType" value="valider" hidden>
                        <input type="text" class="form-control" name="idcontrat" value="<?php echo $rdv->police; ?>" disabled>
                    </div>
                    <div class="form-group col-md-3 col-sm-12">
                        <label class="col-form-label">Date RDV effective :</label>
                        <input type="date" class="form-control" id="dateRDVEffective" value="${dateRDVEffective}" disabled>
                    </div>
                    <div class="form-group col-md-3 col-sm-12">
                        <label class="col-form-label">Ville RDV Effective :</label>
                        <input type="text" class="form-control" id="villesRDVEffective" value="${villesRDV}" disabled>
                    </div>
                    <div class="form-group col-md-4 col-sm-12">
                        <label class="form-label" style="font-size:16px; font-weight:bold;">Liste des gestionnaires ${villesRDV} (<span style="color:red;">*</span>)</label>
                        <select name="ListeGest" id="ListeGest" class="form-control" data-rule="required"></select>
                    </div>
                </div>`;

            const bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white" disabled>Transmettre le RDV</button>`;

            $("#color_button").text(`#033f1f`);
            $("#nom_button").text(`Enregistrer le traitement`);
            $("#afficheuse").html(notif);
            $("#optionTraitement").html(bouton_valider);

            getListeSelectAgentTransformations(idvillesRDV, villesRDV);
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

                    if (resultat.error != false) {
                        console.log(response)
                    }
                },
                error: function(response, status, etat) {
                    resultat = '-1';
                }
            })
            return resultat
        }

        function getListeSelectAgentTransformations(idVilleEff, villesRDV) {

            console.log("selction de gestionnaire de transformation", idVilleEff, villesRDV)
            $.ajax({
                url: "config/routes.php",
                data: {
                    idVilleEff: idVilleEff,
                    etat: "afficherGestionnaire"
                },
                dataType: "json",
                method: "post",
                success: function(response, status) {
                    let html = `<option value="">[Les agents de Transformations de ${villesRDV}]</option>`;

                    $.each(response, function(indx, data) {
                        let agent = data.gestionnairenom;
                        html += `<option value="${data.id}|${agent}|${idVilleEff}|${villesRDV}" id="ob-${indx}">${agent}</option>`;
                    });

                    $("#ListeGest").html(html);
                    //verifierActivationBouton(); // Vérifie après chargement
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat);
                }
            });
        }


        function getListCompteurGestionnaire(idusers = "") {



            var daterdveff = document.getElementById("daterdveff").value;
            var objetRDV = document.getElementById("villesRDV").value;
            let tablo = objetRDV.split(";");
            var idVilleEff = tablo[0];
            var villesRDV = tablo[1];

            console.log(idVilleEff, daterdveff, villesRDV, idusers)


            $.ajax({
                url: "config/routes.php",
                data: {

                    idVilleEff: idVilleEff,
                    daterdveff: daterdveff,
                    idusers: idusers,
                    etat: "ListCompteurGestionnaireByNISSA"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    let afficheusers = ``;
                    console.log(response)
                    if (response != "-1") {
                        if (response.length > 0) {

                            $.each(response, function(indx, data) {

                                console.log(data)
                                let agent = data.gestionnairenom
                                let totalrdv = data.totalrdv
                                let agentid = data.id
                                let codeagent = data.codeagent

                                afficheusers += ` <div class="form-group col-md-4 col-sm-12">
                                    <label for="tel" class="col-form-label" style="font-size:14px; font-weight:bold;">${agent} :</label>
                                    <span class="badge badge-pill" style="background-color:#F9B233;color:white">${totalrdv}</span>
                                </div>`
                                let afficheusersAll = `
                                <div class="card-body p-2" style="background-color:#D3D3D3">
                                    <h4 class="text-blue h4" style="font-size:16px; color:#033f1f"> Total(s) des RDV(s) par gestionnaire du <b>${daterdveff}</b> </h4>
                                    <div class="row"> ` + afficheusers + `</div>
                                </div>  `
                                $("#afficheuseusers").html(afficheusersAll);
                            })
                        }
                    } else {

                        afficheusers += ` <div class="card-body p-2" style="background-color:#D3D3D3">
                            <h4 class="text-blue h4" style="font-size:16px; color:#033f1f"> pas d'informations disponible sur les gestionnaires de <b>${villesRDV}</b></h4></div>`
                        $("#afficheuseusers").html(afficheusers);
                    }
                },
                error: function(response, status, etat) {
                    console.log(response, status, etat)
                }
            })
        }

        function checkDate(parms) {


            var objetVillesRDV = document.getElementById("villesRDV").value;
            var daterdv = document.getElementById("daterdv").value;
            var daterdveff = document.getElementById("daterdveff").value;

            let tablo = objetVillesRDV.split(";");
            var idVilleEff = tablo[0];
            var villesRDV = tablo[1];

            console.log("checkDate : " + daterdveff + " : " + idVilleEff + " - " + villesRDV)

            let a_afficher = ""

            $.ajax({
                url: "config/routes.php",
                data: {
                    parms: parms,
                    idVilleEff: idVilleEff,
                    daterdveff: daterdveff,
                    daterdv: daterdv,
                    etat: "compteurRdv"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    console.log(response)
                    let daterdvR = response.daterdv;
                    let idVilleEffR = response.idVilleEff;
                    let total = response.total;
                    let dataR = response.data;


                    if (total != "0") {
                        total = parseInt(dataR["totalrdv"]);
                        a_afficher = "Il y a <span style='color: red; font-weight: bold;'>" + number_format(total) + "</span> RDV(s) programmé(s) a cette date <span style='color:#033f1f ; font-weight: bold;'>" + daterdvR + "</span> pour la ville <span style='color:#033f1f ; font-weight: bold;'>" + villesRDV + "</span>"
                    } else {
                        a_afficher = "Pas de RDV programmé a cette date <span style='color:#033f1f ; font-weight: bold;'>" + daterdvR + "</span> pour la ville <span style='color:#033f1f ; font-weight: bold;'>" + villesRDV + "</span>"
                    }
                    $("#infos-compteurRDV").html(`<div class="alert alert-info" role="alert"> ` + a_afficher + `</div>`);

                },
                error: function(response, status, etat) {
                    console.log(response, status, etat)
                }
            })

        }


        function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
            number = parseFloat(number).toFixed(decimals);

            let parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

            return parts.join(dec_point);
        }

        function remplirModalEtatComtrat(idProposition) {

            let retour = getAPIVerificationProfil(idProposition);
            let details;
            let confirmer
            let nonRegle
            let enc
            let infos = ""
            //console.log(retour)

            if (retour) {

                //console.log(retour)

                details = retour["details"]
                confirmer = retour['regle']
                enc = retour['enc']
                nonRegle = enc["nonRegle"]

                let contactsPersonne = retour["contactsPersonne"]

                let numero = ''

                $.each(contactsPersonne, function(indxct, contactP) {

                    if (contactP.contact != "" && contactP.contact != null) {
                        numero += `Numero ${indxct} : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${contactP.contact}</span><br>`
                    }
                })

                $.each(details, function(indx, data) {

                    let prime = number_format(data.TotalPrime, 2, ',', ' ');
                    let capital = number_format(data.CapitalSouscrit, 2, ',', ' ');

                    infos += `
                        <div class="row w-100">
                            <div class="form-group  col-md-7" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.7em;"> Nom et prenom : <span class="text-infos" style="font-weight:bold;"> ${data.nomSous+" "+data.PrenomSous} </span></p>
                            </div>
                            <div class="form-group  col-md-5" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.7em;"> Date de naissance : <span class="text-infos" style="font-weight:bold;"> ${data.DateNaissance} </span></p>
                            </div>
                        </div>
                        <div class="row  w-100">
                            <div class="form-group  col-md-7" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.7em;"> Produit :<span class="text-infos" style="font-weight:bold;"> ${data.produit} FCFA</span></p>
                                <p class="mb-0 " style="font-size: 0.7em;"> Capital :<span class="text-infos" style="font-weight:bold;"> ${data.CapitalSouscrit} FCFA</span></p>
                                <p class="mb-0 " style="font-size: 0.7em;">Prime :<span class="text-infos" style="font-weight:bold;"> ${data.TotalPrime} FCFA</span> </p>
                            </div>
                            <div class="form-group  col-md-5" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.8em;">Effet Reel :<span class="text-infos" style="font-weight:bold;"> ${data.DateEffetReel} </span></p>
                                <p class="mb-0 " style="font-size: 0.8em;">Fin Adhesion :<span class="text-infos" style="font-weight:bold;"> ${data.FinAdhesion} </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Duree du contrat :<span class="text-infos" style="font-weight:bold;"> ${data.DureeCotisationAns} ans </span> </p>
                            </div>
                        </div>
                        <div class="row  w-100">
                            <div class="form-group  col-md-7" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.8em;">Nbre d'emission :<span class="text-infos" style="font-weight:bold;"> ${data.NbreEmission} </span></p>
                                <p class="mb-0 " style="font-size: 0.8em;">Nbre Encaissment :<span class="text-infos" style="font-weight:bold;"> ${data.NbreEncaissment} </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Nbre Impayes :<span class="text-infos" style="font-weight:bold;"> ${data.NbreImpayes} </span> </p>
                            </div>
                            <div class="form-group  col-md-5" style="font-size:14px; font-weight:bold;">
                                <p class="mb-0 " style="font-size: 0.8em;">Total Emission :<span class="text-infos" style="font-weight:bold;"> ${parseInt(data.TotalEmission)} FCFA </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Total Encaissment :<span class="text-infos" style="font-weight:bold;"> ${parseInt(data.TotalEncaissement)} FCFA </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Total Impayes :<span class="text-infos" style="font-weight:bold;"> ${parseInt(data.TotalImpayes)} FCFA </span> </p>
                            </div>
                        </div>`
                })

                console.log(details)
                $("#infos-contrat").html(infos);
            } else {
                $("#infos-contrat").html(" pas d'informations disponible");
            }
        }

        function verifierActivationBouton() {
            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();
            const gestionnaire = $('#ListeGest').val();

            const toutRempli = objetRDV && objetRDV !== "null" && dateRDV && gestionnaire && etat === "2";
            $("#valider").prop("disabled", !toutRempli);
        }
    </script>


</body>

</html>