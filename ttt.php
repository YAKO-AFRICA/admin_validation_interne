<?php
// tableau_suivi_prestation.php
session_start();
require_once "autoload.php";

if (empty($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

/**
 * Filtrage / initialisation
 */
$mois = $fonction->retourneMoisCourant();
$tabSemaine = $fonction->retourneSemaineCourante();
$afficheuse = true;
$plus = "";
$libelle = "";

if (!empty($_REQUEST['filtreliste'])) {
    $retourPlus = $fonction->getFiltreuse();
    $filtre = $retourPlus['filtre'] ?? '';
    $libelle = $retourPlus['libelle'] ?? 'Filtre personnalisé';

    if ($filtre) {
        // enlever un éventuel AND initial et nettoyer un peu
        $filtre = preg_replace('/^\s*AND\s*/i', '', trim($filtre));
        $plus = " WHERE $filtre ";
    }
} else {
    // données du mois en cours
    $plus = " WHERE YEAR(tbl_prestations.created_at) = YEAR(CURDATE())
              AND MONTH(tbl_prestations.created_at) = MONTH(CURDATE()) ";
    $libelle = "Prestations du mois en cours";
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/entete.php"; ?>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Tableau suivi prestation</title>

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

        .card-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>
    <?php include "include/header.php"; ?>

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <!-- Header / Breadcrumb -->
            <div class="page-header">
                <div class="row">
                    <div class="col-12">
                        <div class="title">
                            <h4>TABLEAU SUIVI PRESTATION</h4>
                        </div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="intro">Accueil</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Tableau suivi prestation</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Hidden inputs (sécurisés) -->
            <input type="hidden" id="afficheuse" name="afficheuse" value="<?= htmlspecialchars($afficheuse ? 1 : 0) ?>" />
            <input type="hidden" id="service" name="service" value="prestation" />
            <input type="hidden" id="filtreuse" name="filtreuse" value="<?= htmlspecialchars($plus) ?>" />
            <input type="hidden" id="libelleFiltre" name="libelleFiltre" value="<?= htmlspecialchars($libelle) ?>" />

            <!-- Filtre toggle -->
            <button class="btn btn-light mb-2" id="btnToggleFilter" title="Afficher / Masquer le filtre">FILTRE</button>

            <!-- Formulaire de filtre -->
            <div class="card-box mb-10" id="myDIV">
                <div class="card-body">
                    <form method="POST" id="formFiltre">
                        <div class="card-box p-2 m-2" style="border:2px solid #F9B233; border-radius:10px;">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">Filtrer sur la date demande / traitement prestation</legend>
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
                                                        <label for="DateDebutTrait" class="form-label">Date début traitement prestation</label>
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

                            <!-- type et étape -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">Filtrer sur type de demande / Etape prestation</legend>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <h6 style="color:#033f1f">Type demande prestation</h6>
                                                <?= $fonction->getSelectTypePrestationFiltre(); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 style="color:#033f1f">Etape demande prestation</h6>
                                                <?= $fonction->getSelectTypeEtapePrestation(); ?>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <!-- Migration NSIL -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <fieldset class="border rounded p-3">
                                        <legend class="w-auto px-2 font-weight-bold">Filtrer sur Migration NSIL</legend>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <h6 style="color:#033f1f">Migration NSIL</h6>
                                                <select name="migration" id="migration" class="form-control">
                                                    <option value="">...</option>
                                                    <option value="1">Oui</option>
                                                    <option value="0">En attente</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <h6 style="color:#033f1f">Date déclaration NSIL</h6>
                                                <input type="date" class="form-control" name="DateNIL" id="DateNIL" />
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="filtreliste" id="filtreliste" class="btn btn-secondary" style="background:#033f1f; color:white">RECHERCHER</button>
                        </div>
                    </form>
                </div>
            </div>

            <hr>

            <?php if ($afficheuse): ?>
                <div class="card-box mb-30">
                    <div class="pd-20 text-center">
                        <h4 style="color:#033f1f;">Statistique des Prestations</h4>
                        <h6><span id="libelleFiltreAffiche"></span> (<span style="color:#F9B233;" id="totalResultat"></span>)</h6>
                    </div>

                    <div class="card-body pb-20 radius-12 w-100 p-4">
                        <div class="mt-2 mb-3">
                            <button class="btn btn-sm" style="background:#033f1f; color:white" id="telechargerExcel">Télécharger le rapport Excel</button>
                        </div>

                        <div class="bg-white pd-20 card-box mb-30">
                            <div id="afficheuseEtat"></div>
                        </div>

                        <hr>

                        <div class="bg-white pd-20 card-box mb-30">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique prestation par Type Prestation</h4>
                            <div class="row mb-4">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                                    <div id="afficheuseMotif"></div>
                                </div>
                                <div class="col-lg-8 col-md-6 col-sm-12 mb-3">
                                    <div id="chartMotif"></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="bg-white pd-20 card-box mb-30">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique prestation par Libelle Prestation</h4>
                            <div class="row mb-4">
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div id="chartVilles"></div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                                    <div id="afficheuseVilles"></div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="bg-white pd-20 card-box mb-30">
                            <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique prestation par Gestionnaire</h4>
                            <div class="row mb-4">
                                <div class="col-lg-7 col-md-6 col-sm-12 mb-3">
                                    <div id="chartRDVGestionnaire"></div>
                                </div>
                                <div class="col-lg-5 col-md-6 col-sm-12 mb-3">
                                    <div id="afficheuseRDVGestionnaire"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Administratif -->
                    <div class="card-body pb-20 radius-12 w-100 p-4">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Générale sur les prestations Administratives</h4>
                        <div id="afficheuseStatAdministrative">
                            <div class="bg-white pd-20 card-box mb-30">
                                <div id="afficheuseEtatAdministratif"></div>
                            </div>
                            <hr>
                            <div class="bg-white pd-20 card-box mb-30">
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <div id="afficheuseTypePrestationAdministratif"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="chartTypePrestationAdministratif"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="bg-white pd-20 card-box mb-30">
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <div id="chartEstMigreeAdministratif"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="afficheuseGestionnaireAdministratif"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Non administratif -->
                    <div class="card-body pb-20 radius-12 w-100 p-4">
                        <h4 class="mb-20 p-2" style="background-color:#033f1f;color:white;font-weight:bold;">Statistique Générale sur les prestations NON Administratives</h4>
                        <div id="afficheuseStatNONAdministrative">
                            <div class="bg-white pd-20 card-box mb-30">
                                <div id="afficheuseEtatTechnique"></div>
                            </div>
                            <hr>
                            <div class="bg-white pd-20 card-box mb-30">
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <div id="afficheuseTypePrestationNONAdministratif"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="chartTypePrestationNONAdministratif"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="bg-white pd-20 card-box mb-30">
                                <div class="row mb-4">
                                    <div class="col-lg-6">
                                        <div id="chartEstMigreeNONAdministratif"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="afficheuseGestionnaireNONAdministratif"></div>
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

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>

    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>

    <script src="src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

    <script>
        /* ============ Script principal amélioré ============ */
        (function($) {
            'use strict';

            // Variables globales locales
            const service = $('#service').val();
            const filtreuse = $('#filtreuse').val();
            const libelleFiltre = $('#libelleFiltre').val();
            let resultatRecherche = [];

            // Palette courte + fonction générateur si besoin
            const defaultColors = [
                "#3b82f6", "#ef4444", "#22c55e", "#eab308", "#a855f7", "#14b8a6", "#f97316",
                "#10b981", "#6366f1", "#84cc16", "#f43f5e", "#0ea5e9", "#475569", "#d946ef",
                "#059669", "#941010", "#7c3aed", "#be123c", "#38bdf8", "#4ade80", "#facc15",
                "#fb923c", "#1e40af", "#6b7280"
            ];

            function colorForIndex(i) {
                return defaultColors[i % defaultColors.length];
            }

            // Objet statuts (centralisé)
            const TABLO_STATUT_RDV = {
                "1": {
                    lib_statut: "En attente",
                    libelle: "En attente",
                    statut_traitement: "1",
                    color_statut: "badge badge-secondary",
                    color: "#6b7280",
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
                    color: "#000000",
                    url: "liste-rdv-rejet",
                    icone: "micon fa fa-close"
                }
            };

            // Toggle filtre
            $('#btnToggleFilter').on('click', function() {
                $('#myDIV').toggle();
            });

            // Au chargement
            $(document).ready(function() {
                $('#myDIV').hide(); // par défaut caché

                $('#libelleFiltreAffiche').text(libelleFiltre);

                if (typeof filtreuse !== 'undefined' && filtreuse !== '') {
                    loadStats();
                }
            });

            // Chargement des données via AJAX
            function loadStats() {
                $.ajax({
                    url: "config/routes.php",
                    method: "POST",
                    dataType: "json",
                    data: {
                        service: service,
                        filtreuse: filtreuse,
                        etat: "tableauSuivi"
                    },
                    success: function(response) {
                        if (!response || response === "-1") {
                            console.warn('Aucune donnée reçue');
                            return;
                        }

                        // Récupération des tableaux renvoyés par le backend
                        const tableauSuivi = response.tableauSuivi || [];
                        const tableauSuiviAdminstratif = response.tableauSuiviAdminstratif || [];
                        const tableauSuiviNonAdminstratif = response.tableauSuiviNonAdminstratif || [];
                        const tableauSuiviPrestationRDV = response.tableauSuiviPrestationRDV || [];

                        resultatRecherche = tableauSuivi;
                        $('#totalResultat').text(tableauSuivi.length);

                        // Colonnes à agréger
                        const colonnes = ['etape', 'typeprestation', 'prestationlibelle', 'Operateur', 'traiterpar', 'estMigree'];

                        // Stats générales
                        const stats = getStatsGenerales(tableauSuivi, colonnes);
                        afficheuseEtat(stats['etape']);
                        afficheuseMotif(stats['typeprestation']);
                        afficheuseRDVGestionnaire(stats['traiterpar']);
                        afficheuseVilles(stats['prestationlibelle']);

                        // Administratif
                        const colonnesAdministratif = ['etape', 'typeprestation', 'prestationlibelle', 'traiterpar', 'estMigree'];
                        const statsAdministratif = getStatsGenerales(tableauSuiviAdminstratif, colonnesAdministratif);
                        afficheuseEtatAdministratif(statsAdministratif['etape']);
                        afficheuseTypePrestationAdministratif(statsAdministratif['typeprestation']);
                        afficheuseEstMigreePrestationAdministratif(statsAdministratif['estMigree']);
                        afficheuseGestionnairePrestationAdministratif(statsAdministratif['traiterpar']);

                        // NON Administratif (si tu veux faire différemment, adapte)
                        const statsNONAdmin = getStatsGenerales(tableauSuiviNonAdminstratif, colonnes);
                        afficheuseEtatNONAdministratif(statsNONAdmin['etape']);
                        afficheuseTypePrestationNONAdministratif(statsNONAdmin['typeprestation']);
                        afficheuseEstMigreePrestationNONAdministratif(statsNONAdmin['estMigree']);
                        afficheuseGestionnairePrestationNONAdministratif(statsNONAdmin['traiterpar']);
                    },
                    error: function(xhr, status, err) {
                        console.error('Erreur AJAX', status, err);
                    }
                });
            }

            // Export Excel - utilise SheetJS (XLSX)
            $('#telechargerExcel').on('click', function() {
                if (!Array.isArray(resultatRecherche) || resultatRecherche.length === 0) {
                    alert('Aucun résultat à exporter.');
                    return;
                }

                const now = new Date();
                const pad = (n) => n.toString().padStart(2, '0');
                const filename = `tableau-suivi-prestation-${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}-${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}.xlsx`;

                exportExcelFormat(resultatRecherche, filename);
            });

            // ========== UTILITAIRES ==========

            function getStatsGenerales(rows, colonnes = []) {
                const stats = {};
                stats.total = rows.length || 0;
                colonnes.forEach(c => stats[c] = {});

                rows.forEach(row => {
                    colonnes.forEach(col => {
                        let val = row[col];
                        if (val === undefined || val === null || val === '') val = "NON RENSEIGNÉ";
                        if (!stats[col][val]) stats[col][val] = 0;
                        stats[col][val]++;
                    });
                });

                return stats;
            }

            // Générateur de tableau HTML (nom, valeur) -> retourne string
            function buildTableRowsFromObject(obj, colorFunc) {
                let html = '';
                let idx = 0;
                for (const key in obj) {
                    const value = obj[key];
                    const color = colorFunc ? colorFunc(idx) : colorForIndex(idx);
                    html += `<tr><td>${escapeHtml(key)}</td><td><span class="badge badge-pill" style="background-color:${color};color:white;font-size:12px">${value}</span></td></tr>`;
                    idx++;
                }
                return html;
            }

            function escapeHtml(unsafe) {
                if (unsafe === null || unsafe === undefined) return '';
                return String(unsafe)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // ========== AFFICHAGES ET GRAPHIQUES ==========

            function afficheuseVilles(tabloVilles = {}) {
                const tableRows = buildTableRowsFromObject(tabloVilles, colorForIndex);
                $('#afficheuseVilles').html(`<div class="card-box pd-20 shadow-sm border rounded"><h5 class="mb-3">Statistiques par libelle prestation :</h5><div class="table-responsive" style="height:400px;"><table class="table table-striped table-bordered mb-0"><thead>${tableRows}</thead><tbody></tbody></table></div></div>`);

                // Chart
                const dataSeries = [];
                let i = 0;
                for (const k in tabloVilles) {
                    dataSeries.push({
                        name: k,
                        y: parseInt(tabloVilles[k]),
                        color: colorForIndex(i)
                    });
                    i++;
                }
                if (dataSeries.length === 0) dataSeries.push({
                    name: 'Aucun',
                    y: 1
                });

                Highcharts.chart('chartVilles', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: 'Statistiques par libelle prestation'
                    },
                    series: [{
                        name: 'Total',
                        data: dataSeries
                    }]
                });
            }

            function afficheuseMotif(tabloMotif = {}) {
                const tableRows = buildTableRowsFromObject(tabloMotif, colorForIndex);
                $('#afficheuseMotif').html(`<div class="card-box pd-20 shadow-sm border rounded"><h5 class="mb-3">Statistiques par Type de Prestation :</h5><div class="table-responsive" style="height:400px;"><table class="table table-striped table-bordered mb-0"><thead>${tableRows}</thead><tbody></tbody></table></div></div>`);

                const dataSeries = [];
                let i = 0;
                for (const k in tabloMotif) {
                    dataSeries.push({
                        name: k,
                        y: parseInt(tabloMotif[k]),
                        color: colorForIndex(i)
                    });
                    i++;
                }
                if (dataSeries.length === 0) dataSeries.push({
                    name: 'Aucun',
                    y: 1
                });

                Highcharts.chart('chartMotif', {
                    chart: {
                        type: 'pie'
                    },
                    title: {
                        text: 'Statistiques par Type Prestation'
                    },
                    series: [{
                        name: 'Total',
                        data: dataSeries
                    }]
                });
            }

            function afficheuseEtat(tabloEtat = {}) {
                // Calcul total et cartes
                let cards = '';
                let i = 0;
                for (const k in tabloEtat) {
                    const val = tabloEtat[k];
                    const statut = TABLO_STATUT_RDV[k] || {
                        libelle: k,
                        color: colorForIndex(i)
                    };
                    const color = statut.color || colorForIndex(i);
                    cards += `<div class="col-xl-3 mb-30"><div class="card-box height-100-p widget-style1 text-white" style="background-color:${color};font-weight:bold;"><div class="d-flex align-items-center"><div class="widget-data"><div class="h4 mb-0 text-white">${val}</div><div class="weight-600 font-14">Prestation ${escapeHtml(statut.libelle)}</div></div></div></div></div>`;
                    i++;
                }
                $('#afficheuseEtat').html(`<div class="row mb-4">${cards}</div>`);
            }

            function afficheuseRDVGestionnaire(tabloGestionnaire = {}) {
                const tableRows = buildTableRowsFromObject(tabloGestionnaire, colorForIndex);
                $('#afficheuseRDVGestionnaire').html(`<div class="card-box pd-20 shadow-sm border rounded"><h5 class="mb-3">Statistiques par Gestionnaire Prestation :</h5><div class="table-responsive" style="height:400px;"><table class="table table-striped table-bordered mb-0"><thead>${tableRows}</thead><tbody></tbody></table></div></div>`);

                const dataSeries = [];
                let i = 0;
                for (const k in tabloGestionnaire) {
                    dataSeries.push([k, parseInt(tabloGestionnaire[k])]);
                    i++;
                }

                Highcharts.chart('chartRDVGestionnaire', {
                    chart: {
                        type: 'column',
                        options3d: {
                            enabled: true,
                            alpha: 15,
                            beta: 15,
                            depth: 50
                        }
                    },
                    title: {
                        text: 'Statistiques par Gestionnaire Prestation'
                    },
                    xAxis: {
                        type: 'category'
                    },
                    series: [{
                        name: 'RDV Gestionnaire',
                        data: dataSeries
                    }]
                });
            }

            // ADMINISTRATIF & NON ADMINISTRATIF: fonctions réutilisables qui utilisent celles ci-dessus
            function afficheuseEtatAdministratif(obj) {
                afficheuseEtat(obj);
                $('#afficheuseEtatAdministratif').html($('#afficheuseEtat').html());
            }

            function afficheuseTypePrestationAdministratif(obj) {
                afficheuseMotif(obj);
                $('#afficheuseTypePrestationAdministratif').html($('#afficheuseMotif').html());
            }

            function afficheuseEstMigreePrestationAdministratif(obj) {
                // transforme clefs 0/1 => non/migree
                const normalized = {};
                for (const k in obj) {
                    const label = (k === '1' || k === 1) ? 'Migree' : (k === '0' || k === 0) ? 'Non Migree' : k;
                    normalized[label] = obj[k];
                }
                // Chart
                const dataSeries = Object.keys(normalized).map((k, idx) => ({
                    name: k,
                    y: parseInt(normalized[k]),
                    color: colorForIndex(idx)
                }));
                Highcharts.chart('chartEstMigreeAdministratif', {
                    chart: {
                        type: 'column',
                        options3d: {
                            enabled: true,
                            alpha: 15,
                            beta: 15,
                            depth: 50
                        }
                    },
                    title: {
                        text: 'Prestations Administratives Migrees / Non Migrees'
                    },
                    xAxis: {
                        type: 'category'
                    },
                    series: [{
                        name: 'Prestations',
                        data: dataSeries
                    }]
                });
            }

            function afficheuseGestionnairePrestationAdministratif(obj) {
                $('#afficheuseGestionnaireAdministratif').html(`<div class="card-box pd-20 shadow-sm border rounded"><h5 class="mb-3">Statistiques par Gestionnaire Prestation :</h5><div class="table-responsive" style="height:400px;"><table class="table table-striped table-bordered mb-0"><thead>${buildTableRowsFromObject(obj, colorForIndex)}</thead><tbody></tbody></table></div></div>`);
            }

            // NON Administratif (utilise mêmes helpers)
            function afficheuseEtatNONAdministratif(obj) {
                afficheuseEtat(obj);
                $('#afficheuseEtatTechnique').html($('#afficheuseEtat').html());
            }

            function afficheuseTypePrestationNONAdministratif(obj) {
                afficheuseMotif(obj);
                $('#afficheuseTypePrestationNONAdministratif').html($('#afficheuseMotif').html());
            }

            function afficheuseEstMigreePrestationNONAdministratif(obj) {
                // similaire à administratif
                const normalized = {};
                for (const k in obj) {
                    const label = (k === '1' || k === 1) ? 'Migree' : (k === '0' || k === 0) ? 'Non Migree' : k;
                    normalized[label] = obj[k];
                }
                const dataSeries = Object.keys(normalized).map((k, idx) => ({
                    name: k,
                    y: parseInt(normalized[k]),
                    color: colorForIndex(idx)
                }));
                Highcharts.chart('chartEstMigreeNONAdministratif', {
                    chart: {
                        type: 'column',
                        options3d: {
                            enabled: true,
                            alpha: 15,
                            beta: 15,
                            depth: 50
                        }
                    },
                    title: {
                        text: 'Prestations NON Administratives Migrees / Non Migrees'
                    },
                    xAxis: {
                        type: 'category'
                    },
                    series: [{
                        name: 'Prestations',
                        data: dataSeries
                    }]
                });
            }

            function afficheuseGestionnairePrestationNONAdministratif(obj) {
                $('#afficheuseGestionnaireNONAdministratif').html(`<div class="card-box pd-20 shadow-sm border rounded"><h5 class="mb-3">Statistiques par Gestionnaire Prestation :</h5><div class="table-responsive" style="height:400px;"><table class="table table-striped table-bordered mb-0"><thead>${buildTableRowsFromObject(obj, colorForIndex)}</thead><tbody></tbody></table></div></div>`);
            }

            // ========== Export XLSX (SheetJS) ==========
            function exportExcelFormat(tablo, fileName = "export.xlsx") {
                try {
                    const ws = XLSX.utils.json_to_sheet(tablo);
                    // colonnes automatiques
                    ws['!cols'] = Object.keys(tablo[0] || {}).map(() => ({
                        wch: 20
                    }));

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Feuille1");
                    XLSX.writeFile(wb, fileName);
                } catch (e) {
                    console.error('Erreur export Excel', e);
                    alert('Erreur lors de la génération du fichier Excel.');
                }
            }

        })(jQuery);
    </script>

</body>

</html>