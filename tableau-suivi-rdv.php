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
    //echo $plus; exit;
    $sqlSelect = " 	SELECT 	tblrdv.*, 	CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
				TRIM(tblvillebureau.libelleVilleBureau) AS villes FROM tblrdv	LEFT JOIN users ON tblrdv.gestionnaire = users.id
			LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau 	 $plus 	ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') DESC	";

    $resultat = $fonction->_getSelectDatabases($sqlSelect);
} else {
    $plus = " WHERE etape != '1' ";
    $afficheuse = false;
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
                <h4>Synthèse des rendez-vous</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                        <li class="breadcrumb-item active">Tableau suivi des rendez-vous</li>
                    </ol>
                </nav>
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

<script>
    function retour() {
        window.history.back();
    }

    $(document).ready(function() {

        let afficheuse = document.getElementById("afficheuse").value;

        if (afficheuse == 1) {
            var filtre = document.getElementById("myDIV");
            filtre.style.display = "none";
        }

        var objetRDV = document.getElementById("villesRDV").value;
        if (objetRDV === "null") return;

        const [idvillesRDV, villesRDV] = objetRDV.split(";");

        getListeSelectAgentTransformations(idvillesRDV, villesRDV);
        //var dateRDVEffective = document.getElementById("daterdveff").value;
        //alert(objetRDV)
    })

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