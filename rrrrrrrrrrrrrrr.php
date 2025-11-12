   
   
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
    $idrdv = GetParameter::FromArray($_COOKIE, 'id');
    $action = GetParameter::FromArray($_COOKIE, 'action');


    $sqlSelect = "SELECT tblrdv.* ,  TRIM(libelleVilleBureau) as villes  FROM tblrdv INNER JOIN tblvillebureau on tblrdv.idTblBureau = tblvillebureau.idVilleBureau WHERE tblrdv.idrdv = '" . $idrdv . "' ";

    $retour_rdv = $fonction->_getSelectDatabases($sqlSelect);

    if ($retour_rdv == null) {
        header('Location: liste-rdv-attente');
        exit;
    }

    $rdv = $retour_rdv[0];

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
                        <h3 class="text-center" style="color:white">Traitement de la demande de rdv N° <span style="color:#F9B233;"><?= strtoupper($rdv->idrdv) . " du  " . $rdv->daterdv ?></span></h3>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">

                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Information sur la demande de rdv</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:whitesmoke;color:#033f1f">
                                <div class="row">
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="demandeur" class="col-form-label">Titre du demandeur:</label>
                                        <input type="text" class="form-control" id="demandeur" value="<?php if (isset($rdv->titre)) echo $rdv->titre; ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">
                                        <label for="prestation" class="col-form-label"> Prestation souhaitée:</label>
                                        <input type="text" class="form-control" id="prestation" value="<?php if (isset($rdv->motifrdv)) echo $rdv->motifrdv; ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-4 col-sm-12">

                                        <label for="nom" class="col-form-label">Id contrat / Numéro de police(s):</label>
                                        <input type="text" class="form-control" id="police" value="<?php if (isset($rdv->police)) echo $rdv->police; ?>" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-5 col-sm-12">
                                        <label for="nom" class="col-form-label">Nom & Prénom(s):</label>
                                        <input type="text" class="form-control" id="nom" value="<?php if (isset($rdv->nomclient)) echo $rdv->nomclient; ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-2 col-sm-12">
                                        <label for="datenaissance" class="col-form-label">Date de naissance:</label>
                                        <input type="text" class="form-control" id="datenaissance" value="<?php if (isset($rdv->datenaissance)) echo $rdv->datenaissance; ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-2 col-sm-12">
                                        <label for="tel" class="col-form-label">Téléphone:</label>
                                        <input type="text" class="form-control" id="tel" value="<?php if (isset($rdv->tel)) echo $rdv->tel; ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="email" class="col-form-label">Email :</label>
                                        <input type="text" class="form-control" id="email" value="<?php if (isset($rdv->email)) echo $rdv->email; ?>" disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="email" class="col-form-label">Ville choisie :</label>
                                        <input type="text" class="form-control" id="villeCh" value="<?php if (isset($rdv->villes)) echo $rdv->villes; ?>" disabled>
                                        <input type="text" class="form-control" id="idVille" value="<?php echo $rdv->idVilleBureau; ?>" hidden>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="daterdv" class="col-form-label">Date RDV souhaitée :</label>
                                        <input type="text" class="form-control" id="daterdv" value="<?php if (isset($rdv->daterdv)) echo $rdv->daterdv; ?>" disabled>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="email" class="col-form-label">Ville effective :</label>
                                        <?php echo $fonction->getVillesBureau($rdv->idTblBureau); ?>
                                    </div>
                                    <div class="form-group col-md-3 col-sm-12">
                                        <label for="daterdveff" class="col-form-label">Date RDV Effective :<bold style="color: #e35f5B;">*</bold></label>
                                        <input type="date" class="form-control" placeholder="Select Date" id="daterdveff" onblur="checkDate('1')" value="<?php if (isset($rdv->daterdv)) echo date("Y-d-m", strtotime($rdv->daterdv)); ?>" required>
                                        <input type="text" class="form-control" id="localisation" value="<?php echo $rdv->localisation; ?>" hidden>
                                    </div>

                                    <!--div class="form-group col-md-12 col-sm-12">
                                <label for="message" class="col-form-label">Message:</label>
                                <textarea class="form-control" id="message" disabled><?php if (isset($rdv->messageclient)) echo $rdv->messageclient; ?></textarea>
                            </div-->

                                    <input type="hidden" class="form-control" id="id" value="<?php if (isset($rdv->idrdv)) echo $rdv->idrdv; ?>" disabled>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="card-box mb-30 p-2" style="background-color:whitesmoke ;  font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Information sur le contrat via NSIL</h4>
                            <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>

                            <div class="card-body radius-12 w-100 p-4" style="border:1px solid whitesmoke;background:whitesmoke;color:#033f1f">
                                <div class="row p-2" id="infos-contrat">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pd-20">
                    <div class="card-box height-100-p pd-20">
                        <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> traitement du rdv </h4>
                        <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                        <div class="row card-body" style="background: #D3D3D3;" id="infos_perso">
                            <div class="col-md-6">
                                <p style="float:left">Traité le : <span class="text-infos" id="dateheure" style="font-size:18px; font-weight:bold;"></span></p>
                                <span class="text-infos" style="color:#033f1f ; color:white !important"></span>
                            </div>
                            <div class="col-md-6">
                                <p style="float:right">Traité par : <span class="text-infos" style="font-size:18px; font-weight:bold;"><?php echo $_SESSION['utilisateur'];  ?></span></p>
                            </div>
                            <input type="text" id="valideur" name="valideur" value="<?php echo $_SESSION['utilisateur'];  ?>" hidden>
                        </div>
                        <div class="row">
                            <div class="row card-body" style="color: #033f1f;">
                                <div class=" offset-md-6 col-md-6">
                                    <div class="form-group">
                                        <label for="message" class="col-form-label" style="font-size:18px; font-weight:bold;">Etat de la demande : </label>
                                    </div>
                                    <div class="form-group row">
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio1" name="customRadio" class="custom-control-input" value="3" style="color:red">
                                            <label class="custom-control-label" for="customRadio1" style="color:red; font-weight:bold">Rejété le rdv</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio2" name="customRadio" class="custom-control-input" value="1">
                                            <label class="custom-control-label" for="customRadio2" style="color:gray!important; font-weight:bold;">En attente</label>
                                        </div>
                                        <div class="custom-control custom-radio mb-5 col">
                                            <input type="radio" id="customRadio3" name="customRadio" class="custom-control-input" value="2">
                                            <label class="custom-control-label" for="customRadio3" style="color: #033f1f; font-weight:bold;">Valider le rdv</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="afficheuse"></div>
                        <div class="row" id="idNumId"></div>

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
                        <div class="card-body">
                            <p id="msgEchec" style="font-weight:bold; font-size:20px; color:red"></p>
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
            let etape = <?php echo $rdv->etat ?>;
            let idcontrat = "<?php echo $rdv->police; ?>";

            //alert(etape + " " + idcontrat);



            $(document).ready(function() {

                if (idcontrat != "") {
                    //remplirModalEtatComtrat(idcontrat)
                }

                $('input[name="customRadio"][value="' + <?php echo $rdv->etat; ?> + '"]').attr('checked', 'checked');

                /*if (etape == '2') {
                    getMenuValider()
                } else if (etape == '3') {
                    getMenuRejeter()
                }*/


                $('input[name="customRadio"]').change(function() {
                    // Lorsqu'on change de valeur dans la liste
                    var valeur = $(this).val();

                    // On supprime le commentaire et vérifie la valeur
                    if (valeur == '3') {
                        // La prestation est rejetée
                        getMenuRejeter();

                    } else if (valeur == '2') {

                        // La prestation est validée
                        alert('Le rdv est valider !');
                        getMenuValider();

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


                $('#villesRDV').change(function() {

                    objet = $(this).val();
                    console.log(objet)
                    if (objet == "null") return;

                    let tablo = objet.split(";");
                    var idvillesRDV = tablo[0];
                    var villesRDV = tablo[1];
                    console.log(villesRDV)

                    //getSelectCOmmerciale(idvillesRDV, villesRDV)
                })

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


            $("#optionTraitement").on("click", "#confirmerRejet", function(evt) {

                var id_prestation = document.getElementById("id_prestation").value;
                var codeprest = document.getElementById("codeprest").value;
                var valideur = document.getElementById("valideur").value;

                let observation = $('#obervation').val();
                let motifs = $('#motifRejet').val();

                if (!motifs || motifs.length === 0) {
                    alert("Veuillez sélectionner au moins un motif de rejet.");
                    return;
                }
                //console.log(motifs);

                if (motifs.length > 0) {

                    let libMotif = ""

                    motifs.forEach(function(motif) {
                        // Sépare chaque motif sur le symbole "|"
                        // console.log(motif);
                        let parts = motif.split('|');
                        let id = parts[0];
                        let libelle = parts[1];
                        //console.log("ID:", id, "Libellé:", libelle);
                    });
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

                //console.log(idprestation, motif, traiterpar, observation);

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
                        //console.log(response)

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

            $("#afficheuse").on("change", "#ListeGest", function(evt) {


                var val = evt.target.value.split('|');

                var idgestionaire = val[0]
                var agent = val[1]
                var idVilleEff = val[2]
                var villesRDV = val[3]


                console.log(idgestionaire, agent, idVilleEff, villesRDV)

            })


            $("#afficheuse").on("change", "#ListeOpe", function(evt) {
                var val = evt.target.value.split('-');
                var key = val[0]
                var lib = val[1]

                $("#delaiTrait").val(lib)
            })

            $("#closeAfficheDocument").click(function() {

                $('#modaleAfficheDocument').modal('hide')
            })

            $("#retourNotification").click(function() {

                $('#notificationValidation').modal('hide')
                //location.reload();
                location.href = "detail-prestation";

            })


            function retour() {
                window.history.back();
            }


            function getMenuValider() {


                var objetRDV = document.getElementById("villesRDV").value;
                var dateRDVEffective = document.getElementById("daterdveff").value;

                let tablo = objetRDV.split(";");
                var idvillesRDV = tablo[0];
                var villesRDV = tablo[1];

                let notif = `
                            <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Affecter le RDV n° <?php echo $rdv->idrdv; ?> à un gestionnaire pour la Transformation</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                                <div class="card-body p-2">
								    <h4 class="text-blue h4" style="font-size:16px; color:#033f1f"> Total(s) des RDV(s) du ${dateRDVEffective}  </h4>
                                    <div class="row" id="afficheusers"></div>
								</div>
								<hr />
                                
                            <div class="row">
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">id contrat / police :</label>
                                    <input type="text" class="form-control" id="actionType" name="actionType" value="valider" hidden>
                                    <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="<?php echo $rdv->police; ?>" disabled>
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="tel" class="col-form-label">ville RDV Effective :</label>
                                    <input type="text" class="form-control" id="villesRDVEffective" name="villesRDVEffective" value="${villesRDV}" disabled>
                                </div>
                                
                                <div class="form-group col-md-5 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;" >Liste des gestionnaires ${villesRDV}  (<span style="color:red;">*</span>)</label>
                                        <select name="ListeGest" id="ListeGest"  class="form-control " data-rule="required">
                                            
                                        </select>
                                </div>
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">Date RDV effective :</label>
                                    <input type="text" class="form-control" id="dateRDVEffective" name="dateRDVEffective" value="${dateRDVEffective}" disabled>
                                </div>
                            </div>
                        `

                //getCompteurSelectAgentTransformations(idvillesRDV, villesRDV, dateRDVEffective);
                getListCompteurGestionnaire(idvillesRDV, dateRDVEffective, villesRDV);

                getListeSelectAgentTransformations(idvillesRDV, villesRDV)
                let bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white"> Accepter la prestation</button>`


                $("#color_button").text(`#033f1f`)
                $("#nom_button").text(`Enregistrer le traitement`)
                $("#afficheuse").html(notif)
                $("#optionTraitement").html(bouton_valider)
            }



            function getMenuRejeter() {


                let notif = `
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <div class="form-group col-md-7 col-sm-12" id="afficheuseMotifRejet"></div>
                    <div class="form-group col-md-5 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner vos observations sur le rejet de la demande de prestation :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation"> <?php echo $prestation->observationtraitement; ?> </textarea>
                    </div>
                    <span id="libMotif"></span> </div>`;

                let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    Rejeter la prestation
                </button>`;

                $("#afficheuse").html(notif);
                $("#color_button").text("red");
                $("#optionTraitement").html(bouton_rejet);

                getListeMotifRejet();
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
                            numero += `
                            Numero ${indxct} : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${contactP.contact}</span><br>
                            `
                        }
                    })

                    $.each(details, function(indx, data) {

                        let prime = number_format(data.TotalPrime, 2, ',', ' ');
                        let capital = number_format(data.CapitalSouscrit, 2, ',', ' ');

                        infos += `
                        <div class="row w-100">
                            <div class="form-group  col-md-8" style="font-size:12px; font-weight:bold;"> Nom et prenom  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.nomSous+" "+data.PrenomSous}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Date de naissance : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DateNaissance}</span></div>
                            <!--div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;">  ${numero}	</div-->
                        </div>	
                        <div class="row  w-100">
                            <div class="form-group  col-md-8" style="font-size:12px; font-weight:bold;"> Produit  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.produit}</span>
                            <p class="mb-0 " style="font-size: 0.7em;"> Capital :<span class="text-infos" style="font-weight:bold;"> ${capital} FCFA</span></p>
                            <p class="mb-0 " style="font-size: 0.7em;">Prime :<span class="text-infos" style="font-weight:bold;"> ${prime} FCFA</span> </p>
                            </div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> 
                                <p class="mb-0 " style="font-size: 0.8em;">Effet Reel :<span class="text-infos" style="font-weight:bold;"> ${data.DateEffetReel} </span></p>
                                <p class="mb-0 " style="font-size: 0.8em;">Fin Adhesion :<span class="text-infos" style="font-weight:bold;"> ${data.FinAdhesion} </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Duree du contrat :<span class="text-infos" style="font-weight:bold;"> ${data.DureeCotisationAns} ans </span> </p>
                            </div>
                        </div>						
                        <div class="row  w-100">
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre d'emission  : <span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEmission}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Emission  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEmission)} FCFA</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre Encaissment  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEncaissment}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Encaissement  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEncaissement)} FCFA</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreImpayes} </span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalImpayes)} FCFA</span></div>
                        </div>`
                    })

                    console.log(details)
                    $("#infos-contrat").html(infos);
                } else {
                    $("#infos-contrat").html(" pas d'informations disponible");
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

                $.ajax({
                    url: "config/routes.php",
                    data: {

                        idVilleEff: idVilleEff,
                        etat: "afficherGestionnaire"
                    },
                    dataType: "json",
                    method: "post",

                    success: function(response, status) {

                        let html = `<option value="">[Les agents de Transformations de ` + villesRDV + `]</option>`

                        $.each(response, function(indx, data) {
                            let agent = data.gestionnairenom
                            html += `<option value="${data.id+`|`+agent+`|`+idVilleEff+`|`+villesRDV}" id="ob-${indx}">${agent}</option>`
                        })
                        $("#ListeGest").html(html);

                    },
                    error: function(response, status, etat) {
                        console.log(response, status, etat)
                    }
                })
            }

            function getListCompteurGestionnaire(idVilleEff, daterdveff, villesRDV, idusers = "") {

                $.ajax({
                    url: "config/routes.php",
                    data: {

                        idVilleEff: idVilleEff,
                        daterdveff: daterdveff,
                        idusers: idusers,
                        etat: "ListCompteurGestionnaire"
                    },
                    dataType: "json",
                    method: "post",

                    success: function(response, status) {

                        let afficheusers = ``;
                        $.each(response, function(indx, data) {
                            let agent = data.gestionnairenom

                            afficheusers += ` <div class="form-group col-md-4 col-sm-12">
                                    <label for="tel" class="col-form-label">${agent} :</label>
                                    <span class="badge badge-pill" style="background-color:blue;color:white">${villesRDV}</span>
                                </div>`
                        })
                        $("#afficheusers").html(afficheusers);

                    },
                    error: function(response, status, etat) {
                        console.log(response, status, etat)
                    }
                })
            }



            function getCompteurSelectAgentTransformations(idVilleEff, villesRDV, daterdveff = "", daterdv = "", idusers = "", id = "") {

                $.ajax({
                    url: "config/routes.php",
                    data: {

                        id: id,
                        idusers: idusers,
                        idVilleEff: idVilleEff,
                        daterdveff: daterdveff,
                        daterdv: daterdv,
                        etat: "compteurGestionnaire"
                    },
                    dataType: "json",
                    method: "post",

                    success: function(response, status) {

                        console.log(response)

                        /*let html = `<option value="">[Les agents de Transformations de ` + villesRDV + `]</option>`
                        $.each(response, function(indx, data) {
                            let agent = data.gestionnairenom

                            html += `<option value="${data.id+`|`+agent+`|`+idVilleEff+`|`+villesRDV}" id="ob-${indx}">${agent}</option>`
                        })
                        $("#ListeGest").html(html);*/

                    },
                    error: function(response, status, etat) {
                        console.log(response, status, etat)
                    }
                })
            }


            function updateCompteurs(document_valider, total_documents = 5) {
                let nb_valides = document_valider.length;
                let nb_restants = total_documents - nb_valides;
                $("#compteur_valides").text("Validés : " + nb_valides);
                $("#compteur_restants").text("Restants : " + nb_restants);
            }

            function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
                number = parseFloat(number).toFixed(decimals);

                let parts = number.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

                return parts.join(dec_point);
            }
        </script>


</body>

</html>
   
   <script>
        let etape = <?php echo $rdv->etat ?>;
        let idcontrat = "<?php echo $rdv->police; ?>";

        //alert(etape + " " + idcontrat);



        $(document).ready(function() {

            if (idcontrat != "") {
                remplirModalEtatComtrat(idcontrat)
            }

            $('input[name="customRadio"][value="' + <?php echo $rdv->etat; ?> + '"]').attr('checked', 'checked');


            $('input[name="customRadio"]').change(function() {
                // Lorsqu'on change de valeur dans la liste
                var valeur = $(this).val();

                // On supprime le commentaire et vérifie la valeur
                if (valeur == '3') {
                    // La prestation est rejetée
                    getMenuRejeter();

                } else if (valeur == '2') {

                    // La prestation est validée
                    alert('Le rdv est valider !');
                    getMenuValider();

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



        })


        $('#villesRDV').change(function() {

            objet = $(this).val();
            console.log(objet)
            if (objet == "null") return;

            let tablo = objet.split(";");
            var idvillesRDV = tablo[0];
            var villesRDV = tablo[1];
            console.log(villesRDV)

            //getSelectCOmmerciale(idvillesRDV, villesRDV)
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


        $("#optionTraitement").on("click", "#confirmerRejet", function(evt) {

            var id_prestation = document.getElementById("id_prestation").value;
            var codeprest = document.getElementById("codeprest").value;
            var valideur = document.getElementById("valideur").value;

            let observation = $('#obervation').val();
            let motifs = $('#motifRejet').val();

            if (!motifs || motifs.length === 0) {
                alert("Veuillez sélectionner au moins un motif de rejet.");
                return;
            }
            //console.log(motifs);

            if (motifs.length > 0) {

                let libMotif = ""

                motifs.forEach(function(motif) {
                    // Sépare chaque motif sur le symbole "|"
                    // console.log(motif);
                    let parts = motif.split('|');
                    let id = parts[0];
                    let libelle = parts[1];
                    //console.log("ID:", id, "Libellé:", libelle);
                });
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

            //console.log(idprestation, motif, traiterpar, observation);

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
                    //console.log(response)

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

        $("#afficheuse").on("change", "#ListeGest", function(evt) {


            var val = evt.target.value.split('|');

            var idgestionaire = val[0]
            var agent = val[1]
            var idVilleEff = val[2]
            var villesRDV = val[3]


            console.log(idgestionaire, agent, idVilleEff, villesRDV)

        })


        $("#retourNotification").click(function() {

            $('#notificationValidation').modal('hide')
            //location.reload();
            location.href = "detail-prestation";
        })


        function retour() {
            window.history.back();
        }


        function getMenuValider() {


            var objetRDV = document.getElementById("villesRDV").value;
            var dateRDVEffective = document.getElementById("daterdveff").value;

            let tablo = objetRDV.split(";");
            var idvillesRDV = tablo[0];
            var villesRDV = tablo[1];

            let notif = `
                            <h4 class="text-center p-2" style="color:#033f1f;  font-weight:bold; "> Affecter le RDV n° <?php echo $rdv->idrdv; ?> à un gestionnaire pour la Transformation</h4>
                                <div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
                                <div class="card-body p-2">
								    <h4 class="text-blue h4" style="font-size:16px; color:#033f1f"> Total(s) des RDV(s) du ${dateRDVEffective}  </h4>
                                    <div class="row" id="afficheusers"></div>
								</div>
								<hr />
                                
                            <div class="row">
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">id contrat / police :</label>
                                    <input type="text" class="form-control" id="actionType" name="actionType" value="valider" hidden>
                                    <input type="text" class="form-control" id="idcontrat" name="idcontrat" value="<?php echo $rdv->police; ?>" disabled>
                                </div>
                                <div class="form-group col-md-3 col-sm-12">
                                    <label for="tel" class="col-form-label">ville RDV Effective :</label>
                                    <input type="text" class="form-control" id="villesRDVEffective" name="villesRDVEffective" value="${villesRDV}" disabled>
                                </div>
                                
                                <div class="form-group col-md-5 col-sm-12">
                                    <label for="validationTextarea" class="form-label" style="font-size:16px; font-weight:bold;" >Liste des gestionnaires ${villesRDV}  (<span style="color:red;">*</span>)</label>
                                        <select name="ListeGest" id="ListeGest"  class="form-control " data-rule="required">
                                            
                                        </select>
                                </div>
                                <div class="form-group col-md-2 col-sm-12">
                                    <label for="tel" class="col-form-label">Date RDV effective :</label>
                                    <input type="text" class="form-control" id="dateRDVEffective" name="dateRDVEffective" value="${dateRDVEffective}" disabled>
                                </div>
                            </div>
                        `

            //getCompteurSelectAgentTransformations(idvillesRDV, villesRDV, dateRDVEffective);
            getListCompteurGestionnaire(idvillesRDV, dateRDVEffective, villesRDV);

            getListeSelectAgentTransformations(idvillesRDV, villesRDV)
            let bouton_valider = `<button type="submit" name="valider" id="valider" class="btn btn-success" style="background:#033f1f;font-weight:bold; color:white"> Accepter la prestation</button>`


            $("#color_button").text(`#033f1f`)
            $("#nom_button").text(`Enregistrer le traitement`)
            $("#afficheuse").html(notif)
            $("#optionTraitement").html(bouton_valider)
        }



        function getMenuRejeter() {


            let notif = `
                <div class="row">
                    <input type="hidden" class="form-control" id="actionType" name="actionType" value="rejeter">
                    <div class="form-group col-md-7 col-sm-12" id="afficheuseMotifRejet"></div>
                    <div class="form-group col-md-5 col-sm-12">
                        <label for="obervation" class="col-form-label">
                            Veuillez renseigner vos observations sur le rejet de la demande de prestation :
                        </label>
                        <textarea class="form-control" id="obervation" name="obervation"> <?php echo $prestation->observationtraitement; ?> </textarea>
                    </div>
                    <span id="libMotif"></span> </div>`;

            let bouton_rejet = `
                <button type="submit" name="confirmerRejet" id="confirmerRejet" class="btn btn-warning" style="background:#F9B233;font-weight:bold; color:white">
                    Rejeter la prestation
                </button>`;

            $("#afficheuse").html(notif);
            $("#color_button").text("red");
            $("#optionTraitement").html(bouton_rejet);

            getListeMotifRejet();
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
                        numero += `
                            Numero ${indxct} : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${contactP.contact}</span><br>
                            `
                    }
                })

                $.each(details, function(indx, data) {

                    let prime = number_format(data.TotalPrime, 2, ',', ' ');
                    let capital = number_format(data.CapitalSouscrit, 2, ',', ' ');

                    infos += `
                        <div class="row w-100">
                            <div class="form-group  col-md-8" style="font-size:12px; font-weight:bold;"> Nom et prenom  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.nomSous+" "+data.PrenomSous}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Date de naissance : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.DateNaissance}</span></div>
                            <!--div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;">  ${numero}	</div-->
                        </div>	
                        <div class="row  w-100">
                            <div class="form-group  col-md-8" style="font-size:12px; font-weight:bold;"> Produit  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.produit}</span>
                            <p class="mb-0 " style="font-size: 0.7em;"> Capital :<span class="text-infos" style="font-weight:bold;"> ${capital} FCFA</span></p>
                            <p class="mb-0 " style="font-size: 0.7em;">Prime :<span class="text-infos" style="font-weight:bold;"> ${prime} FCFA</span> </p>
                            </div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> 
                                <p class="mb-0 " style="font-size: 0.8em;">Effet Reel :<span class="text-infos" style="font-weight:bold;"> ${data.DateEffetReel} </span></p>
                                <p class="mb-0 " style="font-size: 0.8em;">Fin Adhesion :<span class="text-infos" style="font-weight:bold;"> ${data.FinAdhesion} </span> </p>
                                <p class="mb-0 " style="font-size: 0.8em;">Duree du contrat :<span class="text-infos" style="font-weight:bold;"> ${data.DureeCotisationAns} ans </span> </p>
                            </div>
                        </div>						
                        <div class="row  w-100">
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre d'emission  : <span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEmission}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Emission  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEmission)} FCFA</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre Encaissment  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreEncaissment}</span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Encaissement  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalEncaissement)} FCFA</span></div>
                            
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Nbre Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${data.NbreImpayes} </span></div>
                            <div class="form-group  col-md-4" style="font-size:12px; font-weight:bold;"> Total Impayes  : </span><span class="text-infos" style="font-size:14px; font-weight:bold;">${parseInt(data.TotalImpayes)} FCFA</span></div>
                        </div>`
                })

                console.log(details)
                $("#infos-contrat").html(infos);
            } else {
                $("#infos-contrat").html(" pas d'informations disponible");
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
            $.ajax({
                url: "config/routes.php",
                method: "POST",
                dataType: "json",
                data: {
                    idVilleEff: idVilleEff,
                    etat: "afficherGestionnaire"
                },
                success: function(response) {
                    let html = `<option value="">[Les agents de Transformations de ${villesRDV}]</option>`;
                    if (Array.isArray(response) && response.length > 0) {
                        response.forEach((data, index) => {
                            const agent = data.gestionnairenom;
                            const value = `${data.id}|${agent}|${idVilleEff}|${villesRDV}`;
                            html += `<option value="${value}" id="ob-${index}">${agent}</option>`;
                        });
                    } else {
                        html += `<option value="">Aucun agent disponible</option>`;
                    }
                    $("#ListeGest").html(html);
                },
                error: function(xhr, status, error) {
                    console.error("Erreur liste agents :", error);
                    $("#ListeGest").html(`<option value="">Erreur lors du chargement</option>`);
                }
            });
        }


        function getListCompteurGestionnaire(idVilleEff, daterdveff, villesRDV, idusers = "") {

            $.ajax({
                url: "config/routes.php",
                data: {

                    idVilleEff: idVilleEff,
                    daterdveff: daterdveff,
                    idusers: idusers,
                    etat: "ListCompteurGestionnaire"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    let afficheusers = ``;
                    $.each(response, function(indx, data) {
                        let agent = data.gestionnairenom

                        afficheusers += ` <div class="form-group col-md-4 col-sm-12">
                                    <label for="tel" class="col-form-label">${agent} :</label>
                                    <span class="badge badge-pill" style="background-color:blue;color:white">${villesRDV}</span>
                                </div>`
                    })
                    $("#afficheusers").html(afficheusers);

                },
                error: function(response, status, etat) {
                    console.log(response, status, etat)
                }
            })
        }



        function getCompteurSelectAgentTransformations(idVilleEff, villesRDV, daterdveff = "", daterdv = "", idusers = "", id = "") {

            $.ajax({
                url: "config/routes.php",
                data: {

                    id: id,
                    idusers: idusers,
                    idVilleEff: idVilleEff,
                    daterdveff: daterdveff,
                    daterdv: daterdv,
                    etat: "compteurGestionnaire"
                },
                dataType: "json",
                method: "post",

                success: function(response, status) {

                    console.log(response)

                    /*let html = `<option value="">[Les agents de Transformations de ` + villesRDV + `]</option>`
                    $.each(response, function(indx, data) {
                        let agent = data.gestionnairenom

                        html += `<option value="${data.id+`|`+agent+`|`+idVilleEff+`|`+villesRDV}" id="ob-${indx}">${agent}</option>`
                    })
                    $("#ListeGest").html(html);*/

                },
                error: function(response, status, etat) {
                    console.log(response, status, etat)
                }
            })
        }


        function updateCompteurs(document_valider, total_documents = 5) {
            let nb_valides = document_valider.length;
            let nb_restants = total_documents - nb_valides;
            $("#compteur_valides").text("Validés : " + nb_valides);
            $("#compteur_restants").text("Restants : " + nb_restants);
        }

        function number_format(number, decimals = 0, dec_point = '.', thousands_sep = ',') {
            number = parseFloat(number).toFixed(decimals);

            let parts = number.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_sep);

            return parts.join(dec_point);
        }
    </script>