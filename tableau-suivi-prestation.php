<?php

use PSpell\Config;

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("autoload.php");

// $mois = $fonction->retourneMoisCourant();
// $tabSemaine = $fonction->retourneSemaineCourante();
// $tabloService = array("rdv" => "rdv", "prestation" => "prestation", "sinistre" => "sinistre");

$plus = "";
$afficheuse = true;

if (isset($_REQUEST['filtreliste'])) {

    $retourPlus = $fonction->getFiltreuse();
    $filtre = $retourPlus["filtre"];
    $libelle = $retourPlus["libelle"];
    if ($filtre) {
        list(, $conditions) = explode('AND', $filtre, 2);
        $plus = " WHERE $conditions ";
    }
} else {
    // je veux les donnees du mois en cours
    $plus = " WHERE YEAR(tbl_prestations.created_at) = YEAR(CURDATE()) AND MONTH(tbl_prestations.created_at) = MONTH(CURDATE()) ";
    $libelle = "Prestations du mois en cours : " . date('m/Y');
}


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
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>TABLEAU SUIVI PRESTATION</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Tableau suivi prestation</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <input type="hidden" id="afficheuse" name="afficheuse" value="<?= htmlspecialchars($afficheuse ? 1 : 0); ?>" hidden />
            <input type="hidden" id="service" name="service" value="prestation" hidden />
            <input type="hidden" id="filtreuse" name="filtreuse" value="<?php echo htmlspecialchars($plus); ?>" hidden />
            <input type="hidden" id="libelleFiltre" name="libelleFiltre" value="<?= htmlspecialchars($libelle); ?>" hidden />
            <hr>
            <i class="icon-copy ion-navicon-round" type="submit" onclick="myFunction()" title="FILTRE"> 👉 FILTRE POUR LE TABLEAU SUIVI DES PRESTATIONS </i>
            <div class="card-box mb-10" id="myDIV">
                <div class="card-body">
                    <form method="POST">
                        <div class="card-box p-2 m-2" style="border:2px solid #F9B233; border-radius:10px;">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">
                                            Filtrer sur la date demande / traitement prestation
                                        </legend>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="DateDebutPrest" class="form-label">Date début demande</label>
                                                        <input type="date" class="form-control" id="DateDebutPrest" name="DateDebutPrest" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="DateFinPrest" class="form-label">Date fin demande</label>
                                                        <input type="date" class="form-control" id="DateFinPrest" name="DateFinPrest" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="DateDebutTrait" class="form-label">Date début traitement prestation </label>
                                                        <input type="date" class="form-control" id="DateDebutTrait" name="DateDebutTrait">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="DateFinTrait" class="form-label">Date fin traitement prestation</label>
                                                        <input type="date" class="form-control" id="DateFinTrait" name="DateFinTrait">
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
                                            Filtrer sur type de demande / Etape prestation
                                        </legend>

                                        <div class="row g-3">
                                            <div class="col-md-6 form-group">
                                                <h6 style="color: #033f1f !important;">Type demande prestation</h6>
                                                <?php echo $fonction->getSelectTypePrestationFiltre(); ?>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <h6 style="color: #033f1f !important;">Etape demande prestation</h6>
                                                <?php echo $fonction->getSelectTypeEtapePrestation(); ?>
                                            </div>

                                        </div>
                                    </fieldset>
                                </div>
                            </div>



                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">
                                            Filtrer sur Migration NSIL
                                        </legend>

                                        <div class="row g-3">
                                            <div class="col-md-4 form-group">
                                                <h6 style="color: #033f1f !important;">Migration NSIL</h6>
                                                <select name="migration" id="migration" class="form-control" data-msg="Objet" data-rule="required">
                                                    <option value="">...</option>
                                                    <option value="1">Oui</option>
                                                    <option value="0">En attente</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8 form-group">
                                                <h6 style="color: #033f1f !important;"> Date declaration NSIL </h6>
                                                <input type="date" class="form-control" name="DateNIL" id="DateNIL" />
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
                        <h4 style="color:#033f1f; margin:0;">
                            Statistique des Prestations
                        </h4>
                        <h6 style="color:#033f1f; font-weight:bold; margin-top:6px;">
                            <span id="libelleFiltreAffiche"></span>
                            ( <span id="totalResultat" style="color:#F9B233; font-weight:bold; font-size:16px;"></span> )
                        </h6>
                    </div>

                    <div class="card-body pb-20 radius-12 w-100 p-4">

                        <div class="card-body pb-20 radius-12 w-100 p-4">
                            <div class="mt-2">
                                <button class="btn btn-sm" style="background:#033f1f; color:white; text-decoration:none;" id="telechargerExcel">
                                    Telecharger le rapport Excel
                                </button>
                            </div>
                        </div>

                        <div class="bg-white pd-20 card-box mb-30">
                            <div id="afficheuseEtat">
                            </div>
                        </div>

                        <hr>
                        <div class="card-body pd-20 card-box mb-30">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique prestation par Type Prestation</h4>
                            <div class="bg-white pd-20 card-box mb-30">
                                <div class="row mb-4">
                                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                        <div id="afficheuseMotif">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                        <div id="chartMotif" style="width: 100%; height: 300px"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-4">

                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="bg-white pd-20 card-box mb-30">
                                        <span id="titreTypePrestationAdministratif"> </span>
                                        <hr>
                                        <div class="row mb-4">
                                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                                <div id="afficheuseTypePrestationAdministratif"></div>
                                            </div>
                                            <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                                <div id="chartTypePrestationAdministratif" style="width: 100%; height: 300px"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div class="bg-white pd-20 card-box mb-30">
                                        <span id="titreTypePrestationTechniques"> </span>
                                        <hr>
                                        <div class="row mb-4">
                                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                                <div id="afficheuseTypePrestationNONAdministratif"></div>
                                            </div>
                                            <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                                <div id="chartTypePrestationNONAdministratif" style="width: 100%; height: 300px">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="bg-white pd-20 card-box mb-30">
                                <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique prestation par Gestionnaire</h4>
                                <div class="row mb-4">
                                    <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                        <div class="bg-white pd-20 card-box mb-30">
                                            <div id="chartRDVGestionnaire"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                        <div id="afficheuseRDVGestionnaire">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body pb-20 radius-12 w-100 p-4">

                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Générale sur les prestations Administratives </h4>

                            <div id="afficheuseStatAdministrative">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="afficheuseEtatAdministratif">
                                    </div>
                                </div>
                                <hr>

                                <hr>
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div class="row mb-4">
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <div id="chartEstMigreeAdministratif"></div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <div id="afficheuseGestionnaireAdministratif">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="card-body pb-20 radius-12 w-100 p-4">

                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Générale sur les prestations NON Administratives </h4>

                            <div id="afficheuseStatNONAdministrative">
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div id="afficheuseEtatTechnique">
                                    </div>
                                </div>
                                <hr>
                                <div class="bg-white pd-20 card-box mb-30">


                                </div>
                                <hr>
                                <div class="bg-white pd-20 card-box mb-30">
                                    <div class="row mb-4">
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <div id="chartEstMigreeNONAdministratif"></div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                            <div id="afficheuseGestionnaireNONAdministratif">
                                            </div>
                                        </div>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


        <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>



        <!-- <script src="include/fonction.js"></script> -->

        <script>
            let service = document.getElementById("service").value;
            let filtreuse = document.getElementById("filtreuse").value;
            let libelleFiltre = document.getElementById("libelleFiltre").value;

            console.log(filtreuse);

            let resultatRecherche = [];

            $(document).ready(function() {

                var filtre = document.getElementById("myDIV");
                filtre.style.display = "none";


                if (filtreuse != null) {

                    let colors = [
                        "#033f1f", "#F9B233", "#3b82f6", "#ef4444", "#22c55e", "#eab308", "#a855f7", "#14b8a6", "#f97316",
                        "#10b981", "#6366f1", "#84cc16", "#f43f5e", "#0ea5e9", "#475569", "#d946ef",
                        "#059669", "#941010", "#7c3aed", "#be123c", "#38bdf8", "#4ade80", "#facc15",
                        "#fb923c", "#1e40af", "#6b7280"
                    ];

                    const tablo_statut_prestation = {
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
                            lib_statut: "Accepte(s)",
                            libelle: "ACCEPTE(S)",
                            statut_traitement: "2",
                            color_statut: "badge badge-success",
                            color: "#033f1f",
                            url: "liste-rdv-transmis",
                            icone: "micon fa fa-forward fa-2x"
                        },

                        "3": {
                            lib_statut: "Rejete(s)",
                            libelle: "REJETE(S)",
                            statut_traitement: "0",
                            color_statut: "badge badge-warning",
                            color: "#F9B233",
                            url: "",
                            icone: "micon fa fa-close"
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

                            console.log(response);


                            if (response != "-1") {
                                let tableauSuivi = response["tableauSuivi"];
                                let tableauSuiviAdminstratif = response["tableauSuiviAdminstratif"];
                                let tableauSuiviNonAdminstratif = response["tableauSuiviNonAdminstratif"];
                                let tableauSuiviPrestationRDV = response["tableauSuiviPrestationRDV"];
                                resultatRecherche = tableauSuivi;

                                $("#totalResultat").html(tableauSuivi.length);

                                const colonnes = ['etape', 'typeprestation', 'prestationlibelle', 'Operateur', 'traiterpar', 'estMigree'];
                                const stats = getStatsGenerales(tableauSuivi, colonnes);

                                const tabloEtat = stats['etape'];
                                const tabloTypePrestation = stats['typeprestation'];
                                const tabloPrestationLibelle = stats['prestationlibelle'];
                                const tabloOperateur = stats['Operateur'];
                                const tabloTraitement = stats['traiterpar'];
                                const tabloMigree = stats['estMigree'];

                                // console.log(tabloEtat);
                                // console.log(tabloTypePrestation);
                                // console.log(tabloPrestationLibelle);
                                // console.log(tabloTraitement);
                                // console.log(tabloMigree);

                                afficheuseEtat(tabloEtat);
                                afficheuseMotif(tabloTypePrestation, colors);
                                afficheuseRDVGestionnaire(tabloTraitement, colors);
                                //afficheuseVilles(tabloPrestationLibelle, colors);


                                const colonnesAdministratif = ['etape', 'typeprestation', 'prestationlibelle', 'traiterpar', 'estMigree'];
                                const statsAdministratif = getStatsGenerales(tableauSuiviAdminstratif, colonnesAdministratif);

                                const tabloEtatAdministratif = statsAdministratif['etape'];
                                const tabloTypePrestationAdministratif = statsAdministratif['typeprestation'];
                                const tabloEstMigreePrestationAdministratif = statsAdministratif['estMigree'];
                                const tabloGestionnairePrestationAdministratif = statsAdministratif['traiterpar'];

                                console.log(statsAdministratif);
                                afficheuseEtatAdministratif(tabloEtatAdministratif, tablo_statut_prestation);
                                afficheuseTypePrestationAdministratif(tabloTypePrestationAdministratif, colors);
                                afficheuseEstMigreePrestationAdministratif(tabloEstMigreePrestationAdministratif, colors);
                                afficheuseGestionnairePrestationAdministratif(tabloGestionnairePrestationAdministratif, colors);

                                const colonnesNONAdministratif = ['etape', 'typeprestation', 'prestationlibelle', 'Operateur', 'traiterpar', 'estMigree'];
                                const statsNONAdministratif = getStatsGenerales(tableauSuivi, colonnes);

                                console.log(statsNONAdministratif);
                                const tabloEtatNONAdministratif = statsNONAdministratif['etape'];
                                const tabloTypePrestationNONAdministratif = statsNONAdministratif['typeprestation'];
                                const tabloPrestationLibelleNONAdministratif = statsNONAdministratif['prestationlibelle'];
                                const tabloOperateurNONAdministratif = statsNONAdministratif['Operateur'];
                                const tabloTraitementNONAdministratif = statsNONAdministratif['traiterpar'];
                                const tabloMigreeNONAdministratif = statsNONAdministratif['estMigree'];

                                afficheuseEtatNONAdministratif(tabloEtatNONAdministratif, tablo_statut_prestation);
                                afficheuseTypePrestationNONAdministratif(tabloPrestationLibelleNONAdministratif, colors);
                                afficheuseRDVGestionnaireNONAdministratif(tabloTraitementNONAdministratif, colors);
                                //afficheuseVillesNONAdministratif(tabloPrestationLibelleNONAdministratif, colors);


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
            })

            $('#telechargerExcel').click(function() {
                console.log("Afficher statistique de service : " + libelleFiltre + "  ");
                console.log("Afficher statistique de service : " + filtreuse + "  ");


                if (resultatRecherche != "-1" && resultatRecherche.length > 0) {

                    let data = JSON.stringify(resultatRecherche);
                    //console.log(data);
                    //formater le nom du fichier excel avec date et heure actuelle
                    let date = new Date();
                    let jour = date.getDate();
                    let mois = date.getMonth() + 1;
                    let annee = date.getFullYear();
                    let heure = date.getHours();
                    let minute = date.getMinutes();
                    let second = date.getSeconds();
                    let nomFichier = "tableau-suivi-prestation-" + annee + "-" + mois + "-" + jour + "-" + heure + minute + second + ".xlsx";
                    exportExcelFormat(resultatRecherche, nomFichier);
                }
            });







            function myFunction() {
                var x = document.getElementById("myDIV");
                if (x.style.display === "none") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
            }


            function retour() {
                window.history.back();
            }




            function exportExcelFormat(tablo, fileName = "export.xlsx") {

                const ws = XLSX.utils.json_to_sheet(tablo);

                // Largeurs automatiques
                ws['!cols'] = Object.keys(tablo[0]).map(col => ({
                    wch: 20
                }));

                // Style en-têtes
                Object.keys(tablo[0]).forEach((k, i) => {
                    let cell = ws[XLSX.utils.encode_cell({
                        r: 0,
                        c: i
                    })];
                    cell.s = {
                        font: {
                            bold: true,
                            color: {
                                rgb: "FFFFFF"
                            }
                        },
                        fill: {
                            fgColor: {
                                rgb: "4F81BD"
                            }
                        },
                        alignment: {
                            horizontal: "center"
                        }
                    };
                });

                // Bordures
                const range = XLSX.utils.decode_range(ws['!ref']);
                for (let R = range.s.r; R <= range.e.r; ++R) {
                    for (let C = range.s.c; C <= range.e.c; ++C) {

                        let cellAddr = XLSX.utils.encode_cell({
                            r: R,
                            c: C
                        });
                        let cell = ws[cellAddr];
                        if (!cell) continue;

                        cell.s = cell.s || {};
                        cell.s.border = {
                            top: {
                                style: "thin",
                                color: {
                                    rgb: "000000"
                                }
                            },
                            bottom: {
                                style: "thin",
                                color: {
                                    rgb: "000000"
                                }
                            },
                            left: {
                                style: "thin",
                                color: {
                                    rgb: "000000"
                                }
                            },
                            right: {
                                style: "thin",
                                color: {
                                    rgb: "000000"
                                }
                            }
                        };
                    }
                }

                // Workbook
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Feuille1");
                XLSX.writeFile(wb, fileName);
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


            function afficheuseVilles(tabloVilles, colors) {
                //console.log(tabloVilles);
                let optionVilles = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;

                $.each(tabloVilles, function(indx, data) {

                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    optionVilles += `<tr>
                                    <td>${indx}</td>
                                    <td><span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px">${data}</span></td>
                                </tr>`;
                    idcolor++;
                });

                //console.log(tablo_graph);
                let htmlVilles = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par libelle prestation  :</h5>
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
                Highcharts.chart('chartVilles', {
                    colors: tablo_color,
                    title: {
                        text: 'Statistiques par libelle prestation'
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

            function afficheuseMotif(tabloMotif, colors) {
                //console.log(tabloMotif);
                let optionMotif = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;
                let total = 0;

                $.each(tabloMotif, function(indx, data) {
                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    total += parseInt(data);
                    optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                    idcolor++;
                });

                let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Type de Prestation ( Total ${total} ) :</h5>
                                            <div class="table-responsive" style="height:280px;">
                                                <table class="table table-striped table-bordered mb-0" style="font-size:10px">
                                                    <thead></thead>
                                                    <tbody>
                                                    ` + optionMotif + `
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
                $("#afficheuseMotif").html(htmlMotif);

                if (tablo_graph.length == 0) {
                    tablo_graph.push(["Aucun", 1, false], );
                } else {
                    Highcharts.chart('chartMotif', {
                        colors: tablo_color,
                        title: {
                            text: 'Statistiques par Type Prestation'
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
                        lib_statut: "Accepte(s)",
                        libelle: "ACCEPTE(S)",
                        statut_traitement: "2",
                        color_statut: "badge badge-success",
                        color: "#033f1f",
                        url: "liste-rdv-transmis",
                        icone: "micon fa fa-forward fa-2x"
                    },

                    "3": {
                        lib_statut: "Rejete(s)",
                        libelle: "REJETE(S)",
                        statut_traitement: "0",
                        color_statut: "badge badge-warning",
                        color: "#F9B233",
                        url: "",
                        icone: "micon fa fa-close"
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
                    //console.log(valeurDDD);
                    optionEtat += `
                    <div class="col-xl-3 mb-30">
							<div class="card-box height-100-p widget-style1 text-white"
								style="background-color:${valeurDDD.color}; font-weight:bold; ">
								<div class="d-flex flex-wrap align-items-center">
									<div class="progress-data">	</div>
									<div class="widget-data">
										<div class="h4 mb-0 text-white"> ${data}</div>
										<div class="weight-600 font-14">Prestation ${valeurDDD.libelle}</div>
									</div>
								</div>
							</div>
						</div>
                    `;
                });
                // ---- INJECTION HTML ----
                $("#afficheuseEtat").html(`<div class="row mb-4"> ${optionEtat} </div>`);
            }

            function afficheuseRDVGestionnaire(tabloGestionnaire, colors) {

                let optionGestionnaire = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;
                $.each(tabloGestionnaire, function(indx, data) {
                    //console.log(indx);

                    // if (indx == "NON RENSEIGNÉ") {

                    //     indx = "Pas de gestionnaire";
                    // }
                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    optionGestionnaire += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                    idcolor++;
                });

                let htmlGestionnaire = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Gestionnaire Prestation :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionGestionnaire + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
                $("#afficheuseRDVGestionnaire").html(htmlGestionnaire);

                Highcharts.chart('chartRDVGestionnaire', {
                    chart: {
                        type: 'bar', // ou 'column' ou 'pie' selon tes besoins
                        options3d: {
                            enabled: true,
                            alpha: 15,
                            beta: 15,
                            depth: 50,
                            viewDistance: 25
                        }
                    },
                    title: {
                        text: 'Statistiques par Gestionnaire Prestation'
                    },
                    xAxis: {
                        type: "category"
                    },
                    plotOptions: {
                        column: {
                            depth: 25
                        }
                    },
                    legend: {
                        enabled: true
                    },
                    series: [{
                        name: 'RDV Gestionnaire',
                        data: tablo_graph, // 👉 data: [{name,y,color}]
                        colorByPoint: true,
                        colors: tablo_color
                    }]
                });
            }

            ////////////////////////// ADMINISTRATIF //////////////////////////
            function afficheuseEtatAdministratif(tabloEtat, tablo_statut_prestation) {
                let optionEtat = ``;
                // ---- CALCUL TOTAL ----
                let total = 0;
                $.each(tabloEtat, function(indx, data) {
                    total += parseInt(data);
                });

                // ---- CARTES INDIVIDUELLES ----
                $.each(tabloEtat, function(indx, data) {
                    let valeurDDD = tablo_statut_prestation[indx] ?? 0;
                    //console.log(valeurDDD);
                    optionEtat += `
                    <div class="col-xl-3 mb-30">
							<div class="card-box height-100-p widget-style1 text-white"
								style="background-color:${valeurDDD.color}; font-weight:bold; ">
								<div class="d-flex flex-wrap align-items-center">
									<div class="progress-data">	</div>
									<div class="widget-data">
										<div class="h4 mb-0 text-white"> ${data}</div>
										<div class="weight-600 font-14">Prestation ${valeurDDD.libelle}</div>
									</div>
								</div>
							</div>
						</div>
                    `;
                });
                // ---- INJECTION HTML ----
                $("#afficheuseEtatAdministratif").html(`<div class="row mb-4"> ${optionEtat} </div>`);
            }

            function afficheuseTypePrestationAdministratif(tabloMotif, colors) {
                //console.log(tabloMotif);
                let optionMotif = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;
                let total = 0;
                //let colors = ["red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray", "black", "yellow", "red", "green", "blue", "orange", "brown", "gold", "violet", "cyan", "magenta", "gray", "black", "yellow"];
                $.each(tabloMotif, function(indx, data) {

                    //console.log(indx);
                    total += parseInt(data);
                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                    idcolor++;
                });

                let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <div class="table-responsive" style="height:280px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead></thead>
                                                    <tbody>
                                                    ` + optionMotif + `
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;

                //$("#afficheuseTypePrestationAdministratif").html(htmlMotif);
                $("#titreTypePrestationAdministratif").html(`<h5 class="mb-3">Statistiques par Type de Prestations Administratif ( Total ${total} ) : </h5>`);
                $("#afficheuseTypePrestationAdministratif").html(htmlMotif);

                if (tablo_graph.length == 0) {
                    tablo_graph.push(["Aucun", 1, false], );
                } else {
                    Highcharts.chart('chartTypePrestationAdministratif', {
                        colors: tablo_color,
                        title: {
                            text: 'Graphique par Type Prestations Administratives'
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
            }

            function afficheuseEstMigreePrestationAdministratif(tabloGestionnaire, colors) {

                let optionGestionnaire = ``;
                let idcolor = 0;
                let tablo_graph = [];
                let tablo_color = [];

                $.each(tabloGestionnaire, function(indx, data) {
                    //console.log(indx);

                    if (indx == "0") {
                        indx = "Non Migree";
                    } else if (indx == "1") {
                        indx = "Migree";
                    } else {
                        indx = "Aucun";
                    }

                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    idcolor++;
                });

                Highcharts.chart('chartEstMigreeAdministratif', {
                    chart: {
                        type: 'bar', // ou 'column' ou 'pie' selon tes besoins
                        options3d: {
                            enabled: true,
                            alpha: 15,
                            beta: 15,
                            depth: 50,
                            viewDistance: 25
                        }
                    },
                    title: {
                        text: 'Statistiques Prestations Administratives Migrees / Non Migrees'
                    },
                    xAxis: {
                        type: "category"
                    },
                    plotOptions: {
                        column: {
                            depth: 25
                        }
                    },
                    legend: {
                        enabled: true
                    },
                    series: [{
                        name: 'Prestations Administratives Migrees / Non Migrees',
                        data: tablo_graph, // 👉 data: [{name,y,color}]
                        colorByPoint: true,
                        colors: tablo_color
                    }]
                });
            }

            function afficheuseGestionnairePrestationAdministratif(tabloGestionnaire, colors) {

                let optionGestionnaire = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;
                $.each(tabloGestionnaire, function(indx, data) {
                    //console.log(indx);

                    // if (indx == "NON RENSEIGNÉ") {

                    //     indx = "Pas de gestionnaire";
                    // }
                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    optionGestionnaire += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                    idcolor++;
                });

                let htmlGestionnaire = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Gestionnaire Prestation :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionGestionnaire + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
                $("#afficheuseGestionnaireAdministratif").html(htmlGestionnaire);
            }

            /////////////////// NON ADMINISTRATIF //////////////////////////

            function afficheuseEtatNONAdministratif(tabloEtat, tablo_statut_prestation) {

                let optionEtat = ``;
                // ---- CALCUL TOTAL ----
                let total = 0;
                $.each(tabloEtat, function(indx, data) {
                    total += parseInt(data);
                });

                // ---- CARTES INDIVIDUELLES ----
                $.each(tabloEtat, function(indx, data) {
                    let valeurDDD = tablo_statut_prestation[indx] ?? 0;
                    //console.log(valeurDDD);
                    optionEtat += `
                    <div class="col-xl-3 mb-30">
							<div class="card-box height-100-p widget-style1 text-white"
								style="background-color:${valeurDDD.color}; font-weight:bold; ">
								<div class="d-flex flex-wrap align-items-center">
									<div class="progress-data">	</div>
									<div class="widget-data">
										<div class="h4 mb-0 text-white"> ${data}</div>
										<div class="weight-600 font-14">Prestation ${valeurDDD.libelle}</div>
									</div>
								</div>
							</div>
						</div>
                    `;
                });
                // ---- INJECTION HTML ----
                $("#afficheuseEtatTechnique").html(`<div class="row mb-4"> ${optionEtat} </div>`);
            }

            function afficheuseTypePrestationNONAdministratif(tabloMotif, colors) {
                //console.log(tabloMotif);
                let optionMotif = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;
                let total = 0;

                $.each(tabloMotif, function(indx, data) {

                    //console.log(indx);
                    if (indx == "Autre") {

                    } else {
                        total += parseInt(data);
                        tablo_graph.push([indx, data, false], );
                        tablo_color.push(colors[idcolor]);
                        optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                        idcolor++;
                    }

                });

                let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <div class="table-responsive" style="height:280px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead></thead>
                                                    <tbody>` + optionMotif + `</tbody>
                                                </table>
                                            </div>
                                        </div>`;
                $("#titreTypePrestationTechniques").html(`<h5 class="mb-3">Statistiques par Type de Prestations Techniques ( Total ${total} ) : </h5>`);
                $("#afficheuseTypePrestationNONAdministratif").html(htmlMotif);

                if (tablo_graph.length == 0) {
                    tablo_graph.push(["Aucun", 1, false], );
                } else {
                    Highcharts.chart('chartTypePrestationNONAdministratif', {
                        colors: tablo_color,
                        title: {
                            text: 'Graphique par Type Prestations Administratives'
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
            }

            function afficheuseEstMigreePrestationNONAdministratif(tabloGestionnaire, colors) {

                let optionGestionnaire = ``;
                let idcolor = 0;
                let tablo_graph = [];
                let tablo_color = [];

                $.each(tabloGestionnaire, function(indx, data) {
                    //console.log(indx);

                    if (indx == "0") {
                        indx = "Non Migree";
                    } else if (indx == "1") {
                        indx = "Migree";
                    } else {
                        indx = "Aucun";
                    }

                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    idcolor++;
                });

                Highcharts.chart('chartEstMigreeNONAdministratif', {
                    chart: {
                        type: 'bar', // ou 'column' ou 'pie' selon tes besoins
                        options3d: {
                            enabled: true,
                            alpha: 15,
                            beta: 15,
                            depth: 50,
                            viewDistance: 25
                        }
                    },
                    title: {
                        text: 'Statistiques Prestations Administratives Migrees / Non Migrees'
                    },
                    xAxis: {
                        type: "category"
                    },
                    plotOptions: {
                        column: {
                            depth: 25
                        }
                    },
                    legend: {
                        enabled: true
                    },
                    series: [{
                        name: 'Prestations Administratives Migrees / Non Migrees',
                        data: tablo_graph, // 👉 data: [{name,y,color}]
                        colorByPoint: true,
                        colors: tablo_color
                    }]
                });
            }

            function afficheuseGestionnairePrestationNONAdministratif(tabloGestionnaire, colors) {

                let optionGestionnaire = ``;
                let tablo_graph = [];
                let tablo_color = [];
                let idcolor = 0;
                $.each(tabloGestionnaire, function(indx, data) {
                    //console.log(indx);

                    // if (indx == "NON RENSEIGNÉ") {

                    //     indx = "Pas de gestionnaire";
                    // }
                    tablo_graph.push([indx, data, false], );
                    tablo_color.push(colors[idcolor]);
                    optionGestionnaire += `<tr>
                                    <td>${indx}</td>
                                    <td> <span class="badge badge-pill" style="background-color:${colors[idcolor]};color:white ; font-size:12px"> ${data} </span> </td>
                                </tr>`;
                    idcolor++;
                });

                let htmlGestionnaire = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Gestionnaire Prestation :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionGestionnaire + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
                $("#afficheuseGestionnaireNONAdministratif").html(htmlGestionnaire);
            }
        </script>

</body>

</html>