<?php

use PSpell\Config;

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("autoload.php");

$mois = $fonction->retourneMoisCourant();
$tabSemaine = $fonction->retourneSemaineCourante();
// print_r($tabSemaine);
// //exit;
// print_r($mois);

$tabloService = array("rdv" => "rdv", "prestation" => "prestation", "sinistre" => "sinistre");

$plus = "";
$afficheuse = false;

if (isset($_REQUEST['filtreliste'])) {

    $afficheuse = true;
    $retourPlus = $fonction->getFiltreuseRDV();
    $filtre = $retourPlus["filtre"];
    $libelle = $retourPlus["libelle"];
    //echo $filtre; exit;
    if ($filtre) {
        list(, $conditions) = explode('AND', $filtre, 2);
        $plus = " WHERE $conditions ";
    }
} else {
    // je veux les donnees du mois en cours
    $plus = " WHERE YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE()) AND MONTH(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = MONTH(CURDATE()) ";
    $libelle = "RDV du mois en cours";
}

// $sqlSelect = " SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
// 				TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
// 			LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $plus 	ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";
// $resultat = $fonction->_getSelectDatabases($sqlSelect);
// if ($resultat != NULL) $afficheuse = true;
$afficheuse = true;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/entete.php"; ?>

    <style>
        .stat-box strong {
            font-size: 18px;
        }

        .badge-auto {
            padding: 4px 8px;
            color: #fff;
            border-radius: 4px;
            font-weight: bold;
        }

        .table td {
            font-size: 13px;
        }

        .header-title {
            background-color: #033f1f;
            color: white;
            padding: 8px;
            font-weight: bold;
            font-size: 15px;
        }
    </style>
</head>

<body>

    <?php include "include/header.php"; ?>

    <!-- ================= PRELOADER ================= -->
    <!-- <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">0%</div>
            <div class="loading-text">Chargement...</div>
        </div>
    </div> -->
    <!-- ============================================== -->

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">

            <div class="page-header">

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <div class="title">
                            <h4>TABLEAU SUIVI</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Tableau suivi</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-6 col-sm-12 text-right">
                        <!-- <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownService" data-toggle="dropdown" aria-expanded="false">
                                    Services
                                </button>

                            <div class="dropdown-menu dropdown-menu-right p-3" aria-labelledby="dropdownService" style="min-width: 240px;">
                                <select id="service2" name="service2" class="form-control">
                                    <option value="" selected>Selectionnez un service</option>
                                    <option value="all">Tous</option>
                                    <option value="rdv">RDV</option>
                                    <option value="prestation">Prestations</option>
                                    <option value="sinistre">Sinistres</option>
                                </select>
                            </div>

                        </div> -->

                        <select id="service2" name="service2" class="btn btn-outline-primary">
                            <option value="" selected>Selectionnez un service</option>
                            <option value="all">Tous</option>
                            <option value="rdv">RDV</option>
                            <option value="prestation">Prestations</option>
                            <option value="sinistre">Sinistres</option>
                        </select>

                    </div>
                </div>
            </div>



            <div class="card-box mb-30" hidden>
                <div class="dropdown">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        January 2020
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Export List</a>
                        <a class="dropdown-item" href="#">Policies</a>
                        <a class="dropdown-item" href="#">View Assets</a>
                    </div>
                </div>

                <div class="bg-white pd-20 card-box mb-30">
                    <div id="chart8"></div>
                </div>

                <div class="bg-white pd-20 card-box mb-30">
                    <div id="chart4"></div>
                </div>


                <div class="bg-white pd-20 card-box mb-30">
                    <div id="chart2"></div>
                </div>
                <div class="bg-white pd-20 card-box mb-30">
                    <div id="chart3"></div>
                </div>
            </div>


            <input type="hidden" id="afficheuse" name="afficheuse" value="<?php echo $afficheuse; ?>" hidden />
            <input type="hidden" id="service" name="service" value="rdv" hidden />
            <input type="hidden" id="filtreuse" name="filtreuse" value="<?php echo $plus; ?>" hidden />
            <input type="hidden" id="libelleFiltre" name="libelleFiltre" value="<?php echo $libelle; ?>" hidden />

            <i class="icon-copy ion-navicon-round" type="submit" onclick="myFunction()" title="FILTRE">FILTRE</i>
            <div class="card-box mb-10" id="myDIV">
                <div class="card-body">
                    <form method="POST">
                        <div class="card-box p-2 m-2" style="border:2px solid #F9B233; border-radius:10px;">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">
                                            Filtrer sur la date RDV
                                        </legend>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="rdvLe" class="form-label">Date début ( <span style="color:red;">*</span> )</label>
                                                        <input type="date" class="form-control" id="rdvLe" name="rdvLe" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="rdvAu" class="form-label">Date fin ( <span style="color:red;">*</span> )</label>
                                                        <input type="date" class="form-control" id="rdvAu" name="rdvAu" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="rdvLe" class="form-label">Date début traitement ( <span style="color:red;">*</span> )</label>
                                                        <input type="date" class="form-control" id="traiterLe" name="traiterLe">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="rdvAu" class="form-label">Date fin traitement ( <span style="color:red;">*</span> )</label>
                                                        <input type="date" class="form-control" id="traiterAu" name="traiterAu">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">
                                            Filtrer sur statut du RDV / motif du RDV
                                        </legend>

                                        <div class="row g-3">
                                            <div class="col-md-6 form-group">
                                                <h6 style="color: #033f1f !important;">Statut RDV</h6>
                                                <?php echo $fonction->getSelectTypeEtapeRDV(); ?>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <h6 style="color: #033f1f !important;">Motif RDV</h6>
                                                <?php echo $fonction->getSelectTypeRDVFiltre(); ?>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">
                                            Filtrer sur Ville Reception / Gestionnaire Transformation
                                        </legend>

                                        <div class="row g-3">
                                            <div class="col-md-6 form-group">
                                                <h6 style="color: #033f1f !important;">Ville RDV</h6>
                                                <?= $fonction->getVillesBureau("", "") ?>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <h6 style="color: #033f1f !important;"> Gestionnaire </h6>
                                                <select name="ListeGest" id="ListeGest" class="form-control" data-rule="required"></select>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer" id="footer">
                            <button type="submit" name="filtreliste" id="filtreliste" class="btn btn-secondary" style="background:#033f1f; color: white">RECHERCHER</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <?php if ($afficheuse) : ?>
                <div class="card-box mb-30">
                    <div class="pd-20 text-center">
                        <h4 style="color:#033f1f;">Statistique des RDV </h4>

                        <p style="color:#033f1f; font-weight: bold">
                        <h6><span id="libelleFiltreAffiche"></span> (<span style="color:#F9B233;" id="totalResultat"></span>) </h6>
                        </p>
                    </div>
                    <div class="card-body pb-20 radius-12 w-100 p-4">


                        <div id="afficheuseEtat">
                        </div>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseMotif">
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="chart1"></div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="row mb-4">
                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="chart5"></div>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseVilles">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>



        </div>
    </div>



    <?php include "include/footer.php"; ?>



    <!-- ================= JS ================= -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/jQuery-Knob-master/jquery.knob.min.js"></script>
    <script src="src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
    <script src="src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
    <script src="src/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
    <script src="src/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="vendors/scripts/dashboard2.js"></script>

    <script src="src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
    <script src="vendors/scripts/highchart-setting.js"></script>

    <script>
        function retour() {
            window.history.back();
        }

        $(document).ready(function() {

            var filtre = document.getElementById("myDIV");
            filtre.style.display = "none";

            // let afficheuse = document.getElementById("afficheuse").value;
            let service = document.getElementById("service").value;
            let filtreuse = document.getElementById("filtreuse").value;
            let libelleFiltre = document.getElementById("libelleFiltre").value;

            let service2 = document.getElementById("service2").value;


            //alert(service2);

            //alert(filtreuse + "  " + libelleFiltre + "  " + service);

            if (filtreuse != null) {
                $('#libelleFiltreAffiche').html(libelleFiltre);
                $.ajax({
                    url: "config/routes.php",
                    data: {
                        service: service,
                        filtreuse: filtreuse,
                        etat: "tableauSuivi"
                    },
                    dataType: "json",
                    method: "post",
                    success: function(response, status) {
                        //console.log(response);

                        if (response != "-1") {
                            $("#totalResultat").html(response.length);


                            if (service == "rdv") {
                                const colonnes = ['etat', 'motifrdv', 'nomgestionnaire', 'villeEffective', 'villes'];
                                const stats = getStatsGenerales(response, colonnes);

                                const tabloEtat = stats['etat'];
                                const tabloMotif = stats['motifrdv'];
                                const tabloNomGestionnaire = stats['nomgestionnaire'];
                                const tabloVilleEffective = stats['villeEffective'];
                                const tabloVilles = stats['villes'];

                                afficheuseVilles(tabloVilles);
                                afficheuseMotif(tabloMotif);
                                afficheuseEtat(tabloEtat);


                                // $.each(stats, function(indx, tablo) {

                                //     console.log(indx);
                                //     if (indx == "etat" && tablo.length > 0) {


                                //         console.log(tablo);
                                //         // let htmlEtat = ``;

                                //         // $.each(tablo, function(index, data) {

                                //         //     htmlEtat += `<div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                //         //     <div class="card-box pd-20 text-center shadow-sm border rounded">
                                //         //         <input type="text" class="knob dial2" value="${data}" data-width="120" data-height="120" data-linecap="round" data-thickness="0.12" data-bgColor="#f1f1f1" data-fgColor="" data-angleOffset="180" readonly>
                                //         //             <h5 class="mt-2" style="color: ;">
                                //         //                 ${data} RDV ${index}
                                //         //             </h5>
                                //         //     </div>
                                //         // </div>`;
                                //         //     formGraphEtat(data);
                                //         // })

                                //         // $("#afficheuseEtat").html(htmlEtat);

                                //     }

                                // });

                            }
                        }


                    },
                    error: function(response, status, etat) {
                        console.log(response, status, etat);
                    }
                });

            }

            // if (afficheuse == 1) {
            //     var filtre = document.getElementById("myDIV");
            //     filtre.style.display = "none";
            // }

            var objetRDV = document.getElementById("villesRDV").value;
            if (objetRDV === "null") return;

            const [idvillesRDV, villesRDV] = objetRDV.split(";");

            getListeSelectAgentTransformations(idvillesRDV, villesRDV);
            //var dateRDVEffective = document.getElementById("daterdveff").value;
            //alert(objetRDV)

        })

        $('#service2').change(function() {

            if ($(this).val() === "null") return;

            let service = $(this).val();
            console.log("Afficher statistique de service : " + service + "  ");


        });


        // Quand la ville change
        $('#villesRDV').change(function() {

            if ($(this).val() === "null") return;

            const [idvillesRDV, villesRDV] = $(this).val().split(";");

            console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  ");
            getListeSelectAgentTransformations(idvillesRDV, villesRDV);

        });


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

        function myFunction() {
            var x = document.getElementById("myDIV");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }

        function getStatsGenerales(rows, colonnes) {
            const stats = {};
            stats.total = rows.length;

            // Initialiser les clés pour chaque colonne
            colonnes.forEach(col => {
                stats[col] = {};
            });

            // Parcourir les lignes
            rows.forEach(row => {
                colonnes.forEach(col => {
                    let val = row[col] ?? null;
                    if (val === null) val = "NON RENSEIGNÉ";

                    if (!stats[col][val]) {
                        stats[col][val] = 0;
                    }
                    stats[col][val]++;
                });
            });

            return stats;
        }

        function formGraphEtat(valueEtat) {
            $(".dial2").knob();
            $({
                animatedVal: 0
            }).animate({
                animatedVal: valueEtat
            }, {
                duration: 3000,
                easing: "swing",
                step: function() {
                    $(".dial2").val(Math.ceil(this.animatedVal)).trigger("change");
                }
            });
        }

        function afficheuseVilles(tabloVilles) {
            //console.log(tabloVilles);
            let optionVilles = ``;
            let tablo_graph = [];

            $.each(tabloVilles, function(indx, data) {

                tablo_graph.push([indx, data, false], );
                optionVilles += `<tr>
                                    <td>${indx}</td>
                                    <td>${data}</td>
                                </tr>`;
            });

            //console.log(tablo_graph);
            let htmlVilles = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par ville :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionVilles + `
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseVilles").html(htmlVilles);
            // chart 5
            Highcharts.chart('chart5', {
                title: {
                    text: 'Statistiques par ville'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                series: [{
                    type: 'pie',
                    allowPointSelect: true,
                    keys: ['name', 'y', 'selected', 'sliced'],
                    data: tablo_graph,
                    showInLegend: true
                }]
            });

        }

        function afficheuseMotif(tabloMotif) {
            console.log(tabloMotif);

            let optionMotif = ``;
            let tablo_graph = [];

            $.each(tabloMotif, function(indx, data) {

                tablo_graph.push([indx, data, false], );
                optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td>${data}</td>
                                </tr>`;
            });

            let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Motif :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionMotif + `
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseMotif").html(htmlMotif);

            Highcharts.chart('chart1', {
                title: {
                    text: 'Statistiques par Motif'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                series: [{
                    type: 'pie',
                    allowPointSelect: true,
                    keys: ['name', 'y', 'selected', 'sliced'],
                    data: tablo_graph,
                    showInLegend: true
                }]
            });
        }

        function afficheuseEtat(tabloEtat) {




            const tablo_statut_rdv = {
                "1": {
                    lib_statut: "En attente",
                    libelle: "En attente",
                    statut_traitement: "1",
                    color_statut: "badge badge-secondary",
                    color: "gray",
                    url: "liste-rdv-attente",
                    icone: "micon dw dw-edit"
                },

                "2": {
                    lib_statut: "Transmis",
                    libelle: "TRANSMIS",
                    statut_traitement: "2",
                    color_statut: "badge badge-secondary",
                    color: "blue",
                    url: "liste-rdv-transmis",
                    icone: "micon fa fa-forward fa-2x"
                },

                "0": {
                    lib_statut: "Rejete",
                    libelle: "REJETE",
                    statut_traitement: "0",
                    color_statut: "badge badge-danger",
                    color: "red",
                    url: "",
                    icone: "micon fa fa-close"
                },

                "3": {
                    lib_statut: "Traiter",
                    libelle: "TRAITER",
                    statut_traitement: "3",
                    color_statut: "badge badge-success",
                    color: "#033f1f",
                    url: "liste-rdv-traite",
                    icone: "micon fa fa-check"
                },

                "-1": {
                    lib_statut: "Saisie inachevée",
                    libelle: "SAISIE INACHEVEE",
                    statut_traitement: "-1",
                    color_statut: "badge badge-dark",
                    color: "black",
                    url: "liste-rdv-rejet",
                    icone: "micon fa fa-close"
                }
            };

            let optionEtat = ``;

            // ---- CALCUL TOTAL ----
            let total = 0;
            $.each(tabloEtat, function(indx, data) {
                total += parseInt(data);
            });

            // ---- CARTES INDIVIDUELLES ----
            $.each(tabloEtat, function(indx, data) {

                let valeurDDD = tablo_statut_rdv[indx] ?? 0;
                console.log(valeurDDD);

                optionEtat += `
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="card-box pd-20 text-center shadow-sm border rounded">
                            <input type="text" 
                                class="knob dial2"      value="0" 
                                data-valFinal="${data}" data-max="${total}"   data-width="120"  data-height="120" 
                                data-linecap="round" data-thickness="0.12"   data-bgColor="#f1f1f1"  data-fgColor="${valeurDDD.color}"    data-angleOffset="180" 
                                readonly>

                            <h5 class="mt-2" style="color: ${valeurDDD.color};">
                                ${data} RDV ${valeurDDD.libelle}
                            </h5>
                        </div>
                    </div>`;
            });

            // ---- CARTE TOTAL ----
            // optionEtat += `
            //     <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
            //         <div class="card-box pd-20 text-center shadow-sm border rounded">
            //             <input type="text" class="knob dial2 total-knob" value="0"  data-valFinal="${total}"  data-max="${total}" data-width="120" 
            //                 data-height="120"  data-linecap="round"  data-thickness="0.12"  data-bgColor="#f1f1f1" 
            //                 data-fgColor="green"  data-angleOffset="180"  readonly>

            //             <h5 class="mt-2" style="color: green; font-weight:bold;">
            //                 TOTAL : ${total}
            //             </h5>
            //         </div>
            //     </div>`;

            // ---- INJECTION HTML ----
            $("#afficheuseEtat").html(`<div class="row mb-4"> 
                            ${optionEtat}
                            
                        </div>`);

            // ---- INIT KNOBS ----
            $(".dial2").knob();

            // ---- ANIMATION ----
            $(".dial2").each(function() {
                let $this = $(this);
                let finalVal = parseInt($this.data("valfinal"));

                $({
                    val: 0
                }).animate({
                    val: finalVal
                }, {
                    duration: 2000,
                    easing: "swing",
                    step: function() {
                        $this.val(Math.ceil(this.val)).trigger("change");
                    }
                });
            });
        }
    </script>

</body>

</html>