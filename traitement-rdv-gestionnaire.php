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


    $sqlSelect = "SELECT tblrdv.* , TRIM(libelleVilleBureau) as villes , concat(users.nom,' ',users.prenom) as nomgestionnaire , users.codeagent as codeagent FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau INNER JOIN users ON tblrdv.gestionnaire = users.id WHERE tblrdv.idrdv = '" . $idrdv . "'";
    $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);
    if ($retour_rdv == null) {
        // header('Location: liste-rdv-attente');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $rdv = $retour_rdv[0];
    

    /*if ($rdv->etat != "1") {
        header('Location: detail-rdv');
        exit;
    }*/

    $daterdv = isset($rdv->daterdv) ? date('Y-m-d', strtotime(str_replace('/', '-', $rdv->daterdv))) : '';
    $daterdveff = isset($rdv->daterdveff) ? date('Y-m-d', strtotime($rdv->daterdveff)) : '';

    $reply = $fonction->getRetourneVillesBureau($rdv->idTblBureau);

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
                                    <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                    <li class="breadcrumb-item">Liste des demandes</li>
                                    <li class="breadcrumb-item active" aria-current="page">Traitement demande</li>
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
                        <h3 style="color:white">Traitement de la demande de RDV N°
                            <span class="text-warning">
                                <?= strtoupper($rdv->idrdv) . " du  " . $rdv->daterdv ?>
                            </span>
                        </h3>
                    </div>
                </div>

                <div class="row">
                    <!-- Informations RDV -->
                    <div class="col-md-8">
                        <div class="card mb-4 bg-light">
                            <div class="card-header bg-white">
                                <h4 class="text-center text-info font-weight-bold" style="color:#033f1f!important;">Information sur la demande de RDV</h4>
                            </div>
                            <div class="card-body text-dark">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>Titre du demandeur :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->titre ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Prestation souhaitée :</label>
                                        <input type="text" class="form-control" id="motifrdv" name="motifrdv" value="<?= $rdv->motifrdv ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>ID contrat / N° de police(s) :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->police ?? '' ?>" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-5">
                                        <label>Nom & Prénom(s) :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->nomclient ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Date de naissance :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->datenaissance ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>Téléphone :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->tel ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Email :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->email ?? '' ?>" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Ville choisie :</label>
                                        <input type="text" class="form-control" value="<?= $rdv->villes ?? '' ?>" disabled>
                                        <input type="hidden" id="idVilleBureau" name="idVilleBureau" value="<?= $rdv->idVilleBureau ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV souhaitée :</label>
                                        <input type="text" class="form-control" id="daterdv" name="daterdv" value="<?= $rdv->daterdv ?? '' ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Ville effective :</label>
                                        <?= $fonction->getVillesBureau($rdv->idTblBureau, "disabled") ?>

                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Date RDV Effective <span class="text-danger">*</span> :</label>
                                        <input type="date" class="form-control" id="daterdveff" name="daterdveff" onblur="checkDate('1')" value="<?= $daterdveff ?>" readonly>

                                    </div>
                                    <div class="form-group col-md-12" hidden>
                                        <label>Gestionnaire <span class="text-danger">*</span> :</label>
                                        <input type="text" class="form-control" id="gestionnaire" name="gestionnaire" value="<?= $rdv->gestionnaire . '|' . $rdv->nomgestionnaire . '|' . $rdv->idTblBureau . '|' . $rdv->villes ?>" readonly>
                                    </div>
                                    <input type="hidden" value="<?= $rdv->idrdv ?? '' ?>">
                                </div>
                                <div id="infos-compteurRDV"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Contrat NSIL -->
                    <div class="col-md-4">
                        <div class="card mb-4 bg-light">
                            <div class="card-header bg-white">
                                <h4 class="text-center font-weight-bold" style="color:#033f1f!important;">Information sur le contrat via NSIL</h4>
                            </div>
                            <div class="card-body" style="color:#033f1f!important;">
                                <div class="row" id="infos-contrat"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Traitement RDV -->
                <div class="card mb-4">
                    <div class="card-header text-center bg-white">
                        <h4 class="font-weight-bold" style="border:1px solid gray;background:#033f1f!important; color:white;">Traitement du RDV</h4>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6">
                                <p>Traité le : <span id="dateheure" class="font-weight-bold"> <?= date('d/m/Y H:i:s') ?> </span></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <p>Traité par : <span class="font-weight-bold"><?= $_SESSION['utilisateur'] ?></span></p>
                            </div>
                        </div>
                        <input type="hidden" id="valideur" value="<?= $_SESSION['utilisateur'] ?>">

                        <div class="row card-body" style="color: #033f1f;">
                            <div class=" offset-md-6 col-md-6">
                                <div class="form-group">
                                    <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Voulez vous autoriser le client à déposer son courrier ? </label>
                                </div>
                                <div class="form-group row">
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                        <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">NON</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                        <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                    </div>
                                    <div class="custom-control custom-radio mb-5 col">
                                        <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                        <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">OUI , le client est permit à déposer son courrier pour <span style="color:red"> <?= strtoupper($rdv->motifrdv) ?> </span> </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="afficheuseusers"></div>
                        <div id="afficheuse" class="mt-3"></div>

                        <div class="modal-footer mt-4">
                            <label id="lib"></label>
                            <div id="optionTraitement"></div>
                        </div>
                    </div>
                </div>
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
                        Voulez vous enregistrer la demande de rdv <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                    </h4>
                    <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                    <span style='color:seagreen'>le client sera notifier du resultat de la demande de rdv</span>
                    </hr>
                    <input type="text" id="idprestation" name="idprestation" hidden>
                    <input type="text" id="motif" name="motif" hidden>
                    <input type="text" id="traiterpar" name="traiterpar" hidden>
                    <input type="text" id="observations" name="observations" hidden>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


    <script>
        $(document).ready(function() {

            const etape = <?= $rdv->etat ?>;
            const idcontrat = "<?= $rdv->police ?>";
            const idVilleEff = "<?= $rdv->idTblBureau ?>";

            var objetRDV = document.getElementById("villesRDV").value;
            var dateRDVEffective = document.getElementById("daterdveff").value;

            if (idcontrat !== "") {
                remplirModalEtatComtrat(idcontrat);
            }


            console.log(objetRDV + " " + dateRDVEffective);

            $('input[name="customRadio"]').change(function() {
                const valeur = $(this).val();

                if (valeur === '3') {
                    getMenuValider();
                } else if (valeur === '2') {
                    getMenuRejeter();
                } else {
                    alert('Le RDV est en attente.');
                    $("#afficheuse").empty();
                    $("#color_button").text("#F9B233");
                    $("#optionTraitement").empty();
                }
            });
        });



        $(document).on("click", "#valider", function(evt) {

            const objetRDV = $('#villesRDV').val();
            const dateRDV = $('#daterdveff').val();
            const etat = $('input[name="customRadio"]:checked').val();

            const resultatOpe = $('#resultatOpe').val();
            const obervation = $('#obervation').val();
            //const gestionnaire = $('#ListeGest').val();


            console.log(" ===> " + objetRDV + " " + dateRDV + " " + etat + " // " + resultatOpe);

            //const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesGestionnaire] = gestionnaire.split("|");
            //console.log(" valider rdv " + objetRDV + " " + dateRDV + " " + etat + " " + gestionnaire);


            $.ajax({
                url: "config/routes.php",
                method: "post",
                dataType: "json",
                data: {
                    idrdv: "<?= $rdv->idrdv ?>",
                    idcontrat: "<?= $rdv->police ?>",
                    //gestionnaire: gestionnaire,
                    objetRDV: objetRDV,
                    daterdveff: dateRDV,
                    resultatOpe: resultatOpe,
                    obervation: obervation,
                    etat: "operationRDVReception",
                },
                success: function(response) {

                    console.log("response",response);

                    if (response != '-1' && response != '0') {
                        let code = response;

                        if(code == "transformation"){
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2> Merci de vous connecter a la plateforme de <span class="text-success">` + code + `</span> afin de poursuivre le traitement !!</h2></div>`
                        }else {
                            a_afficher = `<div class="alert alert-success" role="alert">
								<h2>Traitement de la demande de rdv <span class="text-success">` + code + `</span> a bien été enregistrée  !</h2></div>`
                        }

                    } else {
                        a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du traitement de la demande de rdv <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                    }
                    $("#msgEchec").html(a_afficher)
                    $('#notificationValidation').modal("show")

                },
                error: function(response) {
                    console.error("Erreur AJAX :", response);
                }
            })

        })


        $(document).on("click", "#confirmerRejet", function(evt) {


            let observation = $('#obervation').val();
            const dateRDV = $('#daterdveff').val();
            var valideur = document.getElementById("valideur").value;

            if (observation.length > 0) {
                let libMotif = "Motif de rejet : " + observation;

                let idrdv = "<?= $rdv->idrdv ?>"
                let idcontrat = "<?= $rdv->police ?>"

                let aff = `n° ${idrdv} du ${dateRDV}`

                $("#observations").val(observation)
                $("#idprestation").val(idrdv)
                $("#traiterpar").val(valideur)

                $("#a_afficher_1").text(aff)
                $("#a_afficher_2").text(libMotif)
                $('#confirmation-modal').modal('show')
            } else {
                alert("Veuillez renseigner le motif après reception du rdv svp !!");
                return;
            }
        })

        $("#validerRejet").click(function(evt) {
            //alert("validerRejet ")

            var idrdv = document.getElementById("idprestation").value;
            var motif = document.getElementById("motif").value;
            var traiterpar = document.getElementById("traiterpar").value;
            var observation = document.getElementById("observations").value;

            let valideur = <?= $_SESSION['id'] ?>

            console.log(idrdv, motif, valideur, traiterpar, observation);
            $.ajax({
                url: "config/routes.php",
                data: {
                    idrdv: idrdv,
                    motif: motif,
                    traiterpar: valideur,
                    observation: observation,
                    etat: "confirmerRejetRDV"
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
								<h2>Traitement de la demande de rdv <span class="text-success">` + code + `</span> a bien été enregistrée  !</h2></div>`

                    } else {
                        a_afficher = `<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors du traitement de la demande de rdv <span class="text-success">` + code + `</span> !</h2><br> Veuillez reessayer plus tard </div>`

                    }
                    $("#msgEchec").html(a_afficher)
                    $('#notificationValidation').modal("show")

                },
                error: function(response, status, etat) {
                    console.log(etat, response)
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


        function getMenuValider() {

            let notif = `
                    <!-- Le reste de ton contenu ici -->
                    <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Observation apres reception du RDV </h4>
                                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                                
                            <div class="row">
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">id contrat :</label>
                                    <input type="text" class="form-control" id="actionType" name="actionType" value="valider" hidden>
                                    <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="<?php echo $rdv->police; ?>" disabled>
                                    </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;">Resultat de l\'opération (<span style="color:red;">*</span>)</label>
                                        <select name="resultatOpe" id="resultatOpe" class="form-control "  data-rule="required">
                                            <option value="">...</option>
                                            <option value="transformation">Transformations</option>
                                            <option value="partielle">Le client a demandé un rachat partiel</option>
                                            <option value="avance">Le client a demandé une avance / pret</option>
                                            <option value="renonce">Le client a decidé de conserver son contrat</option>
                                            <option value="absent">Le client ne s'est pas presenté</option>
                                            <option value="autres">Autres</option>
                                        </select>
                                </div>
                                <div class="form-group col-md-6 col-sm-12">
                                    <label for="tel" class="col-form-label">commentaires :</label>
                                    <textarea class="form-control" id="obervation" name="obervation"></textarea>
                                </div>
                                
                            </div>`;

            let bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white"> Enregistrer le traitement</button> 
                <div id="spinner" style="display: none; margin-top: 10px;">
                  <div class="spinner-border" style="color: #076633;" role="status">
                    <span class="visually-hidden"></span>
                  </div>
                </div>
                `


            $("#color_button").text(`#033f1f`)
            $("#nom_button").text(`Enregistrer le traitement`)
            $("#afficheuse").html(notif)
            $("#optionTraitement").html(bouton_valider)

        }
        // Génère le formulaire de validation
        function getMenuValiderold() {

            const objetRDV = $("#villesRDV").val();
            const dateRDVEffective = $("#daterdveff").val();
            const motifrdv = $("#motifrdv").val();
            const gestionnaire = $("#gestionnaire").val();

            const [idvillesRDV, villesRDV] = objetRDV.split(";");

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesRDVGestionnaire] = gestionnaire.split("|");

            const notif = `
                <div id="afficheuseCompteurUsers"></div>
                <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Vous autorisez le client a faire son depot de courrier : <br>Libelle du courrier : ${motifrdv} </h4>
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
                        <label class="form-label" style="font-size:16px; font-weight:bold;">gestionnaires RDV (<span style="color:red;">*</span>)</label>
                        <input type="text" class="form-control" id="gestionnaire" name="gestionnaire" value="${nomgestionnaire}" disabled>
                    </div>
                    <div class="form-group col-md-6 col-sm-12">
                        <label class="form-label" style="font-size:16px; font-weight:bold;">Libelle du courrier</label>
                        <input type="text" class="form-control" id="motifrdv" name="motifrdv" value="${motifrdv}" disabled>
                    </div>
                </div>`;

            const bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white" disabled>Transmettre le RDV </button>`;

            $("#color_button").text(`#033f1f`);
            $("#nom_button").text(`Enregistrer le traitement`);
            $("#afficheuse").html(notif);
            $("#optionTraitement").html(bouton_valider);

            //getListCompteurGestionnaire();
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


        function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
            number = parseFloat(number).toFixed(decimals);

            let parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

            return parts.join(dec_point);
        }

        function formatDateJJMMAA(dateString) {
            if (!dateString) return '';
            const [year, month, day] = dateString.split("-");
            return `${day}/${month}/${year}`;
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

        function getMenuRejeter() {
            const objetRDV = $("#villesRDV").val();
            const dateRDVEffective = $("#daterdveff").val();
            const motifrdv = $("#motifrdv").val();
            const gestionnaire = $("#gestionnaire").val();

            const [idvillesRDV, villesRDV] = objetRDV.split(";");

            const [idgestionnaire, nomgestionnaire, idvilleGestionnaire, villesRDVGestionnaire] = gestionnaire.split("|");

            let notif = `
                <h4 class="text-center p-2" style="color:#033f1f; font-weight:bold;">Vous autorisez le client a faire son depot de courrier : <br>Libelle du courrier : ${motifrdv} </h4>
                
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <div class="form-group col-md-12 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner le motif après reception <span style="color:red;">*</span> :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation"></textarea>
                    </div>
                    <span id="libMotif"></span> </div>`;

            let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    ENREGISTRER 
                </button>`;

            $("#afficheuse").html(notif);
            $("#color_button").text("red");
            $("#optionTraitement").html(bouton_rejet);
        }
    </script>


</body>

</html>