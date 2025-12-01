<?php
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

$sqlSelect = " SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
				TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
			LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $plus 	ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";
$resultat = $fonction->_getSelectDatabases($sqlSelect);
if ($resultat != NULL) $afficheuse = true;

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
                <h4>Synthèse des rendez-vous</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                        <li class="breadcrumb-item active">Tableau suivi des rendez-vous</li>
                    </ol>
                </nav>
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
                    <div id="chart1"> bvvvvvvvvvvvvvvvvv </div>
                </div>
                <div class="bg-white pd-20 card-box mb-30">
                    <div id="chart2"></div>
                </div>
                <div class="bg-white pd-20 card-box mb-30">
                    <div id="chart3"></div>
                </div>
            </div>


            <input type="hidden" id="afficheuse" name="afficheuse" value="<?php echo $afficheuse; ?>" hidden />
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
                        <h6><?= $libelle ?> (<span style="color:#F9B233;"><?= !empty($resultat) ? count($resultat) : 0 ?></span>) </h6>
                        </p>
                    </div>
                    <div class="card-body pb-20 radius-12 w-100 p-4">

                    </div>
                </div>
            <?php endif; ?>

            <?php if ($afficheuse) : ?>
                <div class="card-box mb-30">

                    <div class="pd-20 text-center">
                        <h4 style="color:#033f1f;">
                            Récapitulatif des RDV (<span style="color:#F9B233;"><?= !empty($resultat) ? count($resultat) : 0 ?></span>)
                        </h4>
                        <h6 style="color:#033f1f;"><?= $libelle ?></h6>
                    </div>

                    <div class="card-body pb-20 radius-12 w-100 p-4">
                        telecharger le rapport ici <div class="mt-2">
                            <a href="telecharger-rdv.php" target="_blank" class="btn btn-sm" style="background:#F9B233; color:white; text-decoration:none;">
                                Télécharger le rapport PDF
                            </a>
                        </div>
                    </div>
                    <div class="card-body pb-20 radius-12 w-100 p-4" style="background:whitesmoke;">

                        <?php
                        $colonnes = ['etat', 'motifrdv', 'nomgestionnaire', 'villeEffective', 'villes'];
                        $stats = $fonction->getStatsGenerales($resultat, $colonnes);

                        // Couleurs pour les badges
                        $badgeColors = ["primary", "secondary", "success", "warning", "danger", "info"];

                        foreach ($colonnes as $col) :

                            if ($col == 'etat') :
                        ?>
                                <div class="card-box pd-20 shadow-sm border rounded">
                                    <h5 class="mb-3">Statistiques par statut du RDV :</h5>
                                    <div class="row clearfix mb-4">


                                        <?php foreach ($stats[$col] as $valeur => $nb) :
                                            $retourEtat = Config::tablo_statut_rdv[$valeur] ?? ['color' => '#333', 'libelle' => $valeur];
                                        ?>
                                            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                <div class="card-box pd-20 text-center shadow-sm border rounded">
                                                    <input type="text" class="knob dial2" value="<?= $nb ?>" data-width="120" data-height="120" data-linecap="round" data-thickness="0.12" data-bgColor="#f1f1f1" data-fgColor="<?= $retourEtat['color'] ?>" data-angleOffset="180" readonly>
                                                    <h5 class="mt-2" style="color: <?= $retourEtat['color'] ?>;">
                                                        <?= $nb ?> RDV <?= $retourEtat['libelle'] ?>
                                                    </h5>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <hr>
                            <?php endif; ?>

                            <?php if ($col == 'motifrdv') : ?>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par motif de RDV :</h5>

                                            <div class="browser-visits">
                                                <ul>
                                                    <?php
                                                    $barColorsType = ["badge-primary", "badge-secondary", "badge-success", "badge-warning", "badge-danger", "badge-info", "badge-primary", "badge-secondary", "badge-success", "badge-warning", "badge-danger", "badge-info"];
                                                    foreach ($stats[$col] as $valeur => $nb) {
                                                        $color = "badge-" . $badgeColors[array_rand($badgeColors)];
                                                    ?>

                                                        <li class="d-flex flex-wrap align-items-center">
                                                            <div class="browser-name"><?= $valeur ?></div>
                                                            <div class="visit"><span class="badge <?= $color ?>"><?= $nb ?></span></div>
                                                        </li>

                                                    <?php
                                                    }
                                                    ?>
                                                </ul>

                                                <!-- <ul class="d-flex flex-wrap align-items-center">
                                                    <?php
                                                    foreach ($stats[$col] as $valeur => $nb) :
                                                        $color = "badge-" . $badgeColors[array_rand($badgeColors)];
                                                    ?>
                                                        <li class="me-2 mb-2">
                                                            <span class="badge <?= $color ?> p-2">
                                                                <?= $valeur ?> : <?= $nb ?>
                                                            </span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul> -->
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <hr>
                            <?php endif; ?>

                            <?php if ($col == 'nomgestionnaire') : ?>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par gestionnaire :</h5>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Gestionnaire</th>
                                                            <th>Total RDV</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($stats[$col] as $valeur => $nb) : ?>
                                                            <tr>
                                                                <td><?= $valeur ?></td>
                                                                <td><?= $nb ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            <?php endif; ?>

                            <?php if ($col == 'villes') : ?>
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par ville :</h5>
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Ville</th>
                                                            <th>Total RDV</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($stats[$col] as $valeur => $nb) : ?>
                                                            <tr>
                                                                <td><?= $valeur ?></td>
                                                                <td><?= $nb ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    </div>
                </div>

        </div>
    </div>

<?php endif; ?>

<?php include "include/footer.php"; ?>
</div>
</div>



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

<script>
    function retour() {
        window.history.back();
    }

    $(document).ready(function() {

        var filtre = document.getElementById("myDIV");
        filtre.style.display = "none";

        // let afficheuse = document.getElementById("afficheuse").value;

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



        // chart 8
        Highcharts.chart('chart8', {
            chart: {
                type: 'boxplot'
            },
            title: {
                text: 'Highcharts Box Plot Example'
            },
            legend: {
                enabled: false
            },
            xAxis: {
                categories: ['1', '2', '3', '4', '5'],
                title: {
                    text: 'Experiment No.'
                }
            },
            yAxis: {
                title: {
                    text: 'Observations'
                },
                plotLines: [{
                    value: 932,
                    color: 'red',
                    width: 1,
                    label: {
                        text: 'Theoretical mean: 932',
                        align: 'center',
                        style: {
                            color: 'gray'
                        }
                    }
                }]
            },
            series: [{
                name: 'Observations',
                data: [
                    [760, 801, 848, 895, 965],
                    [733, 853, 939, 980, 1080],
                    [714, 762, 817, 870, 918],
                    [724, 802, 806, 871, 950],
                    [834, 836, 864, 882, 910]
                ],
                tooltip: {
                    headerFormat: '<em>Experiment No {point.key}</em><br/>'
                }
            }, {
                name: 'Outlier',
                color: Highcharts.getOptions().colors[0],
                type: 'scatter',
                data: [
                    [0, 644],
                    [4, 718],
                    [4, 951],
                    [4, 969]
                ],
                marker: {
                    fillColor: 'white',
                    lineWidth: 1,
                    lineColor: Highcharts.getOptions().colors[0]
                },
                tooltip: {
                    pointFormat: 'Observation: {point.y}'
                }
            }]

        });

        Highcharts.chart('chart4', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Monthly Average Rainfall'
            },
            subtitle: {
                text: 'Source: WorldClimate.com'
            },
            xAxis: {
                categories: [
                    'Jan',
                    'Feb',
                    'Mar',
                    'Apr',
                    'May',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Oct',
                    'Nov',
                    'Dec'
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Rainfall (mm)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: 'Tokyo',
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

            }, {
                name: 'New York',
                data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

            }, {
                name: 'London',
                data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]

            }, {
                name: 'Berlin',
                data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]

            }]
        });

    })


    Highcharts.chart('chart1', {
        title: {
            text: 'Solar Employment Growth by Sector, 2010-2016'
        },
        subtitle: {
            text: 'Source: thesolarfoundation.com'
        },
        yAxis: {
            title: {
                text: 'Number of Employees'
            }
        },
        chart: {
            type: 'spline',
        },
        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                },
                pointStart: 2010
            },
            spline: {
                marker: {
                    enabled: false
                }
            }
        },
        series: [{
            name: 'Installation',
            data: [43934, 52503, 57177, 69658, 97031, 119931, 137133, 154175]
        }, {
            name: 'Manufacturing',
            data: [24916, 24064, 29742, 29851, 32490, 30282, 38121, 40434]
        }, {
            name: 'Sales & Distribution',
            data: [11744, 17722, 16005, 19771, 20185, 24377, 32147, 39387]
        }, {
            name: 'Project Development',
            data: [null, null, 7988, 12169, 15112, 22452, 34400, 34227]
        }, {
            name: 'Other',
            data: [12908, 5948, 8105, 11248, 8989, 11816, 18274, 18111]
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                }
            }]
        }
    });


    // chart 3
    Highcharts.chart('chart3', {
        chart: {
            type: 'area'
        },
        title: {
            text: 'US and USSR nuclear stockpiles'
        },
        subtitle: {
            text: 'Sources: <a href="https://thebulletin.org/2006/july/global-nuclear-stockpiles-1945-2006">' +
                'thebulletin.org</a> &amp; <a href="https://www.armscontrol.org/factsheets/Nuclearweaponswhohaswhat">' +
                'armscontrol.org</a>'
        },
        xAxis: {
            allowDecimals: false,
            labels: {
                formatter: function() {
                    return this.value;
                }
            }
        },
        yAxis: {
            title: {
                text: 'Nuclear weapon states'
            },
            labels: {
                formatter: function() {
                    return this.value / 1000 + 'k';
                }
            }
        },
        tooltip: {
            pointFormat: '{series.name} had stockpiled <b>{point.y:,.0f}</b><br/>warheads in {point.x}'
        },
        plotOptions: {
            area: {
                pointStart: 1940,
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        series: [{
            name: 'USA',
            data: [
                null, null, null, null, null, 6, 11, 32, 110, 235,
                369, 640, 1005, 1436, 2063, 3057, 4618, 6444, 9822, 15468,
                20434, 24126, 27387, 29459, 31056, 31982, 32040, 31233, 29224, 27342,
                26662, 26956, 27912, 28999, 28965, 27826, 25579, 25722, 24826, 24605,
                24304, 23464, 23708, 24099, 24357, 24237, 24401, 24344, 23586, 22380,
                21004, 17287, 14747, 13076, 12555, 12144, 11009, 10950, 10871, 10824,
                10577, 10527, 10475, 10421, 10358, 10295, 10104, 9914, 9620, 9326,
                5113, 5113, 4954, 4804, 4761, 4717, 4368, 4018
            ]
        }, {
            name: 'USSR/Russia',
            data: [null, null, null, null, null, null, null, null, null, null,
                5, 25, 50, 120, 150, 200, 426, 660, 869, 1060,
                1605, 2471, 3322, 4238, 5221, 6129, 7089, 8339, 9399, 10538,
                11643, 13092, 14478, 15915, 17385, 19055, 21205, 23044, 25393, 27935,
                30062, 32049, 33952, 35804, 37431, 39197, 45000, 43000, 41000, 39000,
                37000, 35000, 33000, 31000, 29000, 27000, 25000, 24000, 23000, 22000,
                21000, 20000, 19000, 18000, 18000, 17000, 16000, 15537, 14162, 12787,
                12600, 11400, 5500, 4512, 4502, 4502, 4500, 4500
            ]
        }]
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
</script>

</body>

</html>