<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

include("autoload.php");

$idville = GetParameter::FromArray($_REQUEST, 'idville');

if ($idville !== null) {
    $critereRecherche = " AND idville='" . addslashes($idville) . "'";

    $resultat       = $fonction->pourcentageRDVBy("ville", $critereRecherche);
    $resultatUser   = $fonction->pourcentageRDVBy("user", " AND ville = '$idville'");
    $resultatStatut = $fonction->pourcentageRDVBy("statut", " AND idTblBureau = '$idville'");
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
    <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">0%</div>
            <div class="loading-text">Chargement...</div>
        </div>
    </div>
    <!-- ============================================== -->

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">

            <div class="page-header">
                <h4>Synthèse des rendez-vous</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                        <li class="breadcrumb-item active">Synthèse des rendez-vous</li>
                    </ol>
                </nav>
            </div>

            <?php if ($idville !== null && isset($resultat[$idville])) :
                $info = $resultat[$idville];
            ?>

                <!-- ================== INFOS VILLE ================== -->
                <div class="card-box mb-30">
                    <div class="pd-20">
                        <div class="row">

                            <div class="col-md-7 stat-box" >
                                <p >Ville : <strong><?= $info['libelle'] ?></strong></p>
                                <p>ID Ville : <strong><?= $info['keyword'] ?></strong></p>
                                <p>Localisation : <strong><?= $info['localisation'] ?></strong></p>
                            </div>

                            <div class="col-md-5 stat-box">
                                <p>Total rendez-vous :
                                    <strong><?= intval($info['nb_ligne_element']) ?></strong>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ================== TABLE ÉTAT ================== -->
                <div class="card-box mb-30">
                    <div class="header-title text-center">Répartition par statut</div>

                    <div class="card-body">
                        <p class="text-right">
                            <strong>Total : <?= array_sum(array_column($resultatStatut, 'nb_ligne_element')) ?></strong>
                        </p>

                        <table id="tableStatut" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Statut</th>
                                    <th>Nombre</th>
                                    <th>Taux</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultatStatut as $etat): ?>
                                    <tr>
                                        <td><?= $etat['libelle'] ?></td>
                                        <td><span class="badge-auto" style="background:<?= $etat['color'] ?>"><?= $etat['nb_ligne_element'] ?></span></td>
                                        <td><?= $etat['lib_pourcentage'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ================== TABLE UTILISATEURS ================== -->
                <div class="card-box mb-30">
                    <div class="header-title text-center">Répartition par utilisateur</div>

                    <div class="card-body">
                        <p class="text-right">
                            <strong>Total : <?= array_sum(array_column($resultatUser, 'nb_ligne_element')) ?></strong>
                        </p>

                        <table id="tableUser" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nom & Prénom</th>
                                    <th>Code agent</th>
                                    <th>Compteur RDV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($resultatUser as $usr): ?>
                                    <tr>
                                        <td><?= $usr['nomuser'] ?></td>
                                        <td><?= $usr['codeagent'] ?></td>
                                        <td><span class="badge-auto" style="background:#0a4"><?= $usr['nb_ligne_element'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="alert alert-warning">Aucune ville sélectionnée.</div>
            <?php endif; ?>

        </div>
    </div>

    <?php include "include/footer.php"; ?>

    <!-- ================= JS ================= -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#tableStatut').DataTable();
            $('#tableUser').DataTable();

            let i = 0;
            let interval = setInterval(() => {
                if (i >= 100) return clearInterval(interval);
                i += 2;
                $('#bar1').css('width', i + '%');
                $('#percent1').text(i + '%');
            }, 30);

            setTimeout(() => $(".pre-loader").fadeOut(), 1500);
        });
    </script>

</body>

</html>