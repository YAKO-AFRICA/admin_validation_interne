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

$tabloService = array("rdv" => "rdv", "prestation" => "prestation", "sinistre" => "sinistre");

$plus = "";
$afficheuse = false;

if (isset($_REQUEST['filtreliste'])) {

    $afficheuse = true;
    $retourPlus = $fonction->getFiltreuseRDV();
    $filtre = $retourPlus["filtre"];
    $libelle = $retourPlus["libelle"];
    if ($filtre) {
        list(, $conditions) = explode('AND', $filtre, 2);
        $plus = " WHERE $conditions ";
    }
} else {
    // je veux les donnees du mois en cours
    $plus = " WHERE YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE()) AND MONTH(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = MONTH(CURDATE()) ";
    $libelle = "RDV du mois en cours";
}

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

            <input type="hidden" id="afficheuse" name="afficheuse" value="<?php echo $afficheuse; ?>" hidden />
            <input type="hidden" id="service" name="service" value="rdv" hidden />
            <input type="hidden" id="filtreuse" name="filtreuse" value="<?php echo $plus; ?>" hidden />
            <input type="hidden" id="libelleFiltre" name="libelleFiltre" value="<?php echo $libelle; ?>" hidden />

            <i class="icon-copy ion-navicon-round" type="submit" onclick="myFunction()" title="FILTRE">FILTRE</i>
            <div class="card-box mb-10" id="myDIV">
                <div class="card-body">
                    <form method="POST">
                        <div class="card-box p-2 m-2" style="border:2px solid #F9B233; border-radius:10px;">
                            <!-- Filtre date, statut, ville et gestionnaire -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">
                                            Filtrer sur la date RDV
                                        </legend>
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
                                    </fieldset>
                                </div>
                            </div>
                            <!-- Autres filtres -->
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
                        <div id="afficheuseEtat"></div>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                <div id="afficheuseMotif"></div>
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
                                <div id="afficheuseVilles"></div>
                            </div>
                        </div>
                        <div class="bg-white pd-20 card-box mb-30">
                            <div id="chart7"></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include "include/footer.php"; ?>

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
    <script src="vendors/scripts/highchart-setting.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="include/fonction.js"></script>

    <script>
        $(document).ready(function() {

            var filtre = document.getElementById("myDIV");
            filtre.style.display = "none";

            let service = document.getElementById("service").value;
            let filtreuse = document.getElementById("filtreuse").value;
            let libelleFiltre = document.getElementById("libelleFiltre").value;

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
                    success: function(response) {
                        if (response != "-1") {
                            $("#totalResultat").html(response.length);

                            if (service == "rdv") {
                                const colonnes = ['etat', 'motifrdv', 'nomgestionnaire', 'villeEffective', 'villes'];
                                const stats = getStatsGenerales(response, colonnes);

                                afficheuseVilles(stats['villes']);
                                afficheuseMotif(stats['motifrdv']);
                                afficheuseEtat(stats['etat']);

                                const statsD = getStatsDelaiRDV(response);
                                afficheuseDelaiRDV(statsD);
                            }
                        }
                    },
                    error: function(response, status, etat) {
                        console.log(response, status, etat);
                    }
                });
            }

            $('#service2').change(function() {
                if ($(this).val() === "null") return;
                console.log("Afficher statistique de service : " + $(this).val());
            });
        });

        function myFunction() {
            var x = document.getElementById("myDIV");
            x.style.display = x.style.display === "none" ? "block" : "none";
        }

        function getStatsGenerales(rows, colonnes) {
            const stats = {};
            stats.total = rows.length;
            colonnes.forEach(col => { stats[col] = {}; });
            rows.forEach(row => {
                colonnes.forEach(col => {
                    let val = row[col] ?? "NON RENSEIGNÉ";
                    stats[col][val] = (stats[col][val] || 0) + 1;
                });
            });
            return stats;
        }

        function getStatsDelaiRDV(rows) {
            const stats = {};
            rows.forEach(row => {
                const delaiRdv1 = getDelaiRDV(row.daterdv);
                const delaiRdv2 = row.daterdveff ? getDelaiRDV(row.daterdveff) : delaiRdv1;
                [delaiRdv1, delaiRdv2].forEach(delai => {
                    const etat = delai.etat;
                    if (!stats[etat]) stats[etat] = { total: 0, couleur: delai.couleur, badge: delai.badge, libelle: delai.libelle, jours: [], lignes: [] };
                    stats[etat].total++;
                    stats[etat].jours.push(delai.jours);
                    stats[etat].lignes.push({ ...row, delai });
                });
            });
            return stats;
        }

        function getDelaiRDV(dateRDV) {
            if (!dateRDV) return { etat: "indisponible", couleur: "gray", badge: "", libelle: "Date non disponible", jours: null };
            if (dateRDV.includes("/")) { const [j,m,a] = dateRDV.split("/"); dateRDV = `${a}-${m}-${j}`; }
            const today = new Date(); today.setHours(0,0,0,0);
            const rdv = new Date(dateRDV); rdv.setHours(0,0,0,0);
            const jours = Math.round((rdv - today) / (1000*60*60*24));
            if (jours < 0) return { etat: "expire", couleur: "red", badge: "badge badge-danger", libelle: `Délai expiré depuis ${Math.abs(jours)} jour(s)`, jours: Math.abs(jours) };
            if (jours === 0) return { etat: "ok", couleur: "#f39c12", badge: "badge badge-warning", libelle: "Aujourd’hui", jours: 0 };
            return { etat: "prochain", couleur: "#033f1f", badge: "badge badge-success", libelle: `${jours} jour(s) restant(s)`, jours };
        }
    </script>
</body>
</html>
