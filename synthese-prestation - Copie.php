<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}


include("autoload.php");



?>

<!DOCTYPE html>
<html>

<head>
    <?php include "include/entete.php"; ?>
</head>

<body>

    <?php
    include "include/header.php";
    ?>

    <div class="pre-loader">
        <div class="pre-loader-box">
            <!--div class="loader-logo"><img src="vendors/images/logo-icon.png" alt="" style="width:30%;height:40%;"></div-->
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">15%</div>
            <div class="loading-text">
                chargement...
            </div>
        </div>
    </div>

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

                <?php
                if ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_PRESTATION) {

                    $data = $fonction->_recapGlobalePrestations();
                    echo "<script>let prestationsData = " . json_encode($data) . ";</script>";

                ?>

                    <div class="row p-2">
                        <div class="col-md-7">
                            <div class="card-box height-100-p pd-20">
                                <h2 class="h4 mb-20 p-2 card-body" style="background-color:#033f1f; font-weight:bold;color:white"> proportion par statut de traitement des demandes de prestation</h2>
                                <div id="container" style="width: 100%; height: 300px;"></div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="card-box height-100-p pd-20">

                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead style="color:white; font-size:20px; background-color:#033f1f">
                                            <tr>
                                                <th class="table-plus datatable-nosort">libelle</th>
                                                <th>nombre</th>
                                                <th class="datatable-nosort">pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="afficheuseStat" style="font-size:20px"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row p-2">

                        <div class="col-md-7">
                            <div class="card-box height-100-p pd-20">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead style="color:white; font-size:20px; background-color:#033f1f">
                                            <tr>
                                                <th class="table-plus datatable-nosort">libelle</th>
                                                <th>nombre</th>
                                                <th class="datatable-nosort">pourcentage</th>
                                            </tr>
                                        </thead>
                                        <tbody id="afficheuseStatType" style="font-size:20px"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="card-box height-100-p pd-20">
                                <h2 class="h4 mb-20 p-2 card-body" style="background-color:#033f1f; font-weight:bold;color:white"> proportion par type de demande de prestation</h2>

                                <div class="card center">
                                    <canvas id="myChartType" style="width:100%;max-width:600px; height:230px; ">

                                    </canvas>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php
                }
                ?>


            </div>
        </div>

        <div class="footer-wrap pd-20 mb-20">
            <?php include "include/footer.php";    ?>
        </div>



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

        <script src="vendors/scripts/datatable-setting.js"></script>
        <!-- Datatable Setting js -->
        <script src="vendors/scripts/datatable-setting.js"></script>
        <script src="src/plugins/apexcharts/apexcharts.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>



        <!-- Inclure Highcharts -->
        <script src="https://code.highcharts.com/highcharts.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categories = [];
                const data = [];

                for (let key in prestationsData) {
                    if (prestationsData.hasOwnProperty(key)) {
                        let prestation = prestationsData[key];
                        categories.push(prestation.libelle + " ( " + prestation.nb_ligne_element + " ) ");
                        data.push({
                            y: parseFloat(prestation.pourcentage),
                            color: prestation.color
                        });
                    }
                }

                Highcharts.chart('container', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Répartition par etape de prestation (%)'
                    },
                    xAxis: {
                        categories: categories,
                        title: {
                            text: 'Etape de prestation'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Pourcentage (%)'
                        }
                    },
                    series: [{
                        name: 'Pourcentage',
                        data: data
                    }],
                    tooltip: {
                        pointFormat: '<b>{point.y:.1f}%</b>'
                    },
                    legend: {
                        enabled: false
                    }
                });
            });
        </script>


        <script>
            $(document).ready(function() {

                let typeCompte = "<?php echo $_SESSION['typeCompte'] ?>";

                if (typeCompte == "prestation") {
                    introPretations()
                }


            })


            function introPretations() {
                let tablo = [];
                let tabloStat = [];
                let tabloVal = [];
                let barColors = [];

                let tabloType = [];
                let tabloStatType = [];
                let tabloValType = [];
                let barColorsType = [];

                let aTraiter = 0;


                $.ajax({
                    url: "config/routes.php",
                    data: {
                        etat: "intro"
                    },
                    dataType: "json",
                    method: "post",

                    success: function(response, status) {

                        let retourStatut = response['retourStatut']
                        let retourType = response['global']
                        //console.log(retourStatut);


                        $.each(retourStatut, function(indx, element) {

                            if (element.etat == "1") aTraiter = element.nb_ligne_element;
                            tabloStat.push(element.keyword);
                            tabloVal.push(element.nb_ligne_element);
                            barColors.push(element.color);

                            tablo += `<tr>
                            <td style="font-size:14px"><i class="bx bxs-circle me-2"  ></i>${element.keyword}</td>
                            <td style="font-size:14px"><span class="badge ${element.bagde} badge-pill">${element.nb_ligne_element}</span></td>
                            <td style="font-size:14px"><span class="badge ${element.bagde} badge-pill">${element.pourcentage} %</span></td>
                            
                        </tr>`
                        })

                        templateDiagrammeBar(tabloStat, tabloVal, barColors, "Production par statut de traitement")
                        $("#afficheuseStat").html(tablo);

                        $("#a_traiter").text(aTraiter + ' demandes non traitées');

                        $.each(retourType, function(indxType, elementType) {

                            tabloStatType.push(elementType.libelle);
                            tabloValType.push(elementType.nb_ligne_element);
                            barColors.push(elementType.color);


                            tabloType += `<tr>
                            <td style="font-size:14px"><i class="bx bxs-circle me-2"  ></i>${elementType.libelle}</td>
                            <td style="font-size:14px"><span class="badge badge-pill" style="background-color:${elementType.color};color:white">${elementType.nb_ligne_element}</span></td>
                            <td style="font-size:14px"><span class="badge badge-pill" style="background-color:${elementType.color};color:white" >${elementType.pourcentage} %</span></td>
                            </tr> `
                        })

                        templateDiagrammeBarType(tabloStatType, tabloValType, barColorsType, "Production par type de prestations")
                        $("#afficheuseStatType").html(tabloType);

                    },
                    error: function(response, status, etat) {
                        //var a_afficher = "traitement enregistrer avec succes !!"
                        // $("#a_afficher2").text(a_afficher)
                        // $('#notification').modal("show")
                    }
                })
            }

            function templateDiagrammeBar(arg1, arg2, arg3 = null, textlegend = "Ma production recouvrement") {
                var xValues = arg1;
                var yValues = arg2;
                var barColors = "";



                if (arg3 == null) barColors = ["red", "green", "blue", "orange", "brown", "gold"];
                barColors = arg3;
                //var barColors = arg3;

                //console.log(barColors)

                new Chart("myChart3", {
                    type: "bar",
                    data: {
                        labels: xValues,
                        datasets: [{
                            backgroundColor: barColors,
                            data: yValues
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,

                            text: textlegend
                        }
                    }
                });
            }

            function templateDiagrammeBarType(arg1, arg2, arg3 = null, textlegend = "Production Agent") {
                var xValues = arg1;
                var yValues = arg2;
                var barColors;
                //var barColors = arg3;
                barColors = ["red", "green", "blue", "orange", "brown", "gold", "violet", "red", "green", "blue", "orange", "brown", "gold", "violet"];
                //barColors = arg3;

                //console.log(barColors)
                new Chart("myChartType", {
                    type: "pie",
                    data: {
                        labels: xValues,
                        datasets: [{
                            backgroundColor: barColors,
                            data: yValues
                        }]
                    },
                    options: {
                        legend: {
                            display: true
                        },
                        title: {
                            display: true,
                            text: textlegend
                        }
                    }
                });
            }
        </script>

</body>

</html>