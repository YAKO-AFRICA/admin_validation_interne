<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("autoload.php");
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <?php include "include/entete.php"; ?>
    <link rel="stylesheet" href="src/plugins/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="src/plugins/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="vendors/styles/style.css">
    <style>
        .pre-loader { /* Préchargeur */
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; display: flex; justify-content: center; align-items: center;
            z-index: 9999;
        }
        .loader-progress { width: 80%; height: 8px; background: #ddd; margin-bottom: 10px; border-radius: 4px; }
        .bar { width: 0; height: 100%; background: #033f1f; border-radius: 4px; }
        .percent { text-align: center; font-weight: bold; margin-bottom: 10px; }
    </style>
</head>

<body>
    <?php include "include/header.php"; ?>

    <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-progress" id="progress_div"><div class="bar" id="bar1"></div></div>
            <div class="percent" id="percent1">0%</div>
            <div class="loading-text">Chargement...</div>
        </div>
    </div>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-4"><img src="vendors/images/banner-img.png" alt=""></div>
                    <div class="col-md-8">
                        <h4 class="font-20 weight-500 mb-10">
                            Bienvenue <?= $_SESSION['utilisateur']; ?> sur la plateforme de gestion des <?= strtolower($_SESSION['typeCompte']) == "gestionnaire" || strtolower($_SESSION['typeCompte']) == "rdv" ? "rendez-vous" : strtolower($_SESSION['typeCompte'].'s') ?>,
                        </h4>
                        <p class="font-18 max-width-600">Récapitulatif des demandes de <?= strtolower($_SESSION['typeCompte']) == "gestionnaire" || strtolower($_SESSION['typeCompte']) == "rdv" ? "rendez-vous" : strtolower($_SESSION['typeCompte'].'s') ?>.</p>
                    </div>
                </div>
            </div>

            <?php
            if ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_PRESTATION) {
                $data = $fonction->_recapGlobalePrestations();
                echo "<script>let prestationsData = ".json_encode($data).";</script>";
            ?>
                <div class="row">
                    <?= $fonction->getParametreGlobalPrestations(); ?>
                </div>

                <div class="row p-2">
                    <div class="col-md-5">
                        <div class="card-box pd-20">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion par statut</h4>
                            <canvas id="chartStat" style="width:100%; height:300px;"></canvas>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card-box pd-20">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Proportion par type</h4>
                            <canvas id="chartType" style="width:100%; height:300px;"></canvas>
                        </div>
                    </div>
                </div>

            <?php
            } elseif ($_SESSION['typeCompte'] == Config::TYPE_SERVICE_RDV) {
            ?>
                <div class="row p-2">
                    <div class="col-md-12">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Rendez-vous par ville</h4>
                        <div>Total général: <span id="totalVille">0</span></div>
                        <table id="tableRDV_Ville" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Ville</th>
                                    <th>Nombre de RDV</th>
                                    <th>Taux (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <canvas id="chartRDV_Ville" style="width:100%; height:300px;"></canvas>
                    </div>
                </div>

                <div class="row p-2">
                    <div class="col-md-12">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Rendez-vous par service</h4>
                        <div>Total général: <span id="totalService">0</span></div>
                        <table id="tableRDV_Type" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Nombre de RDV</th>
                                    <th>Taux (%)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <canvas id="chartRDV_Type" style="width:100%; height:300px;"></canvas>
                    </div>
                </div>
            <?php } ?>

        </div>

        <div class="footer-wrap pd-20 mb-20">
            <?php include "include/footer.php"; ?>
        </div>
    </div>

    <!-- JS -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
    $(document).ready(function() {
        // Preloader simulation
        let width = 0;
        let interval = setInterval(function() {
            if(width >= 100) clearInterval(interval);
            else {
                width += 5;
                $('#bar1').css('width', width + '%');
                $('#percent1').text(width + '%');
            }
        }, 50);
        setTimeout(()=>$('.pre-loader').fadeOut(), 1200);

        let typeCompte = "<?= $_SESSION['typeCompte'] ?>";

        function randomColor() {
            return '#' + Math.floor(Math.random()*16777215).toString(16);
        }

        if(typeCompte === "rdv") {
            $.ajax({
                url: "config/routes.php",
                type: "POST",
                data: { type:"rdv", etat:"intro" },
                dataType: "json",
                success: function(resp) {
                    // Ville
                    let totalVille = 0;
                    let labelsVille=[], dataVille=[], colorsVille=[];
                    let tbodyVille = '';
                    resp.retourStatVille.forEach(r=>{
                        totalVille += r.nb_ligne_element;
                        labelsVille.push(r.keyword);
                        dataVille.push(r.nb_ligne_element);
                        let c = randomColor(); colorsVille.push(c);
                        tbodyVille += `<tr>
                            <td>${r.keyword}</td>
                            <td><span class="badge" style="background:${c};color:white">${r.nb_ligne_element}</span></td>
                            <td>${r.pourcentage} %</td>
                        </tr>`;
                    });
                    $('#tableRDV_Ville tbody').html(tbodyVille);
                    $('#totalVille').text(totalVille);
                    $('#tableRDV_Ville').DataTable();

                    new Chart('chartRDV_Ville',{
                        type:'bar',
                        data:{labels:labelsVille,datasets:[{label:'Nombre RDV',data:dataVille,backgroundColor:colorsVille}]},
                        options:{responsive:true,plugins:{legend:{display:true}}}
                    });

                    // Service
                    let totalService = 0;
                    let labelsService=[], dataService=[], colorsService=[];
                    let tbodyService='';
                    resp.retourStatutType.forEach(r=>{
                        totalService += r.nb_ligne_element;
                        labelsService.push(r.libelle);
                        dataService.push(r.nb_ligne_element);
                        let c = randomColor(); colorsService.push(c);
                        tbodyService += `<tr>
                            <td>${r.libelle}</td>
                            <td><span class="badge" style="background:${c};color:white">${r.nb_ligne_element}</span></td>
                            <td>${r.pourcentage} %</td>
                        </tr>`;
                    });
                    $('#tableRDV_Type tbody').html(tbodyService);
                    $('#totalService').text(totalService);
                    $('#tableRDV_Type').DataTable();

                    new Chart('chartRDV_Type',{
                        type:'pie',
                        data:{labels:labelsService,datasets:[{data:dataService,backgroundColor:colorsService}]},
                        options:{responsive:true,plugins:{legend:{display:true}}}
                    });
                }
            });
        }
    });
    </script>
</body>
</html>
