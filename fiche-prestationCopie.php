<?php
// Sécurité renforcée
session_start();
if (!isset($_SESSION['id']) || empty($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

require_once("autoload.php");

// Initialisation des variables
$prestation = null;
$tablo_doc_attendu = [];
$afficheuse = false;

// Vérification des cookies et récupération des données
if (!isset($_COOKIE["id"])) {
    header("Location:deconnexion.php");
    exit;
}

// Validation et filtrage des entrées
$idcontrat = filter_input(INPUT_COOKIE, 'idcontrat');
$idprestation = filter_input(INPUT_COOKIE, 'id', FILTER_VALIDATE_INT);
$code = filter_input(INPUT_COOKIE, 'code');

if (!$idprestation) {
    header('Location: liste-prestation.php');
    exit;
}

// Récupération des données
$prestation_data = $fonction->_getRetournePrestation(" WHERE id='" . $idprestation . "'");
if (empty($prestation_data)) {
    header('Location: liste-prestation.php');
    exit;
}

$prestation = new tbl_prestations($prestation_data[0]);
$retour_documents = $fonction->_getListeDocumentPrestation($idprestation);
$afficheuse = true;

// Préparation des documents attendus
foreach ($retour_documents as $doc) {
    $tablo_doc_attendu[] = $doc["id"];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php include "include/entete.php"; ?>
    <title>Traitement des demandes - <?= htmlspecialchars($code) ?></title>
    <style>
        .document-card {
            transition: all 0.3s ease;
        }

        .document-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .text-color {
            color: #033f1f;
            font-weight: bold;
        }

        .text-infos {
            color: #333;
        }

        .bg-custom-primary {
            background-color: #033f1f;
        }

        .bg-custom-secondary {
            background-color: #F9B233;
        }

        .toast-container {
            z-index: 9999;
        }
    </style>
</head>

<body>
    <?php include "include/header.php"; ?>

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <!-- En-tête de page -->
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>TRAITEMENT DES DEMANDES</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="accueil-operateur.php">Accueil</a></li>
                                <li class="breadcrumb-item">Liste des demandes</li>
                                <li class="breadcrumb-item active">Traitement demande</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Section principale -->
            <div class="card-body height-100-p pd-20">
                <div class="card-box mb-30">
                    <button class="btn btn-warning p-2 m-2" onclick="history.back()">
                        <i class="fa fa-arrow-left"></i> Retour
                    </button>
                </div>

                <!-- En-tête de la prestation -->
                <div class="card-body radius-12 w-100 p-4 bg-custom-primary text-white">
                    <h3 class="text-center">Prestation n° <span class="text-warning"><?= strtoupper(htmlspecialchars($code)) ?></span></h3>
                </div>
            </div>

            <!-- Informations sur le demandeur et le contrat -->
            <div class="row">
                <!-- Colonne gauche - Informations demandeur -->
                <div class="col-md-5">
                    <div class="card-box mb-30 p-2">
                        <h4 class="text-center p-2 text-color">Information sur le demandeur</h4>
                        <div class="divider bg-custom-primary"></div>

                        <div class="card-body radius-12 w-100 p-4 bg-light">
                            <div class="row">
                                <div class="col-12">
                                    <p><span class="text-color">Nom & Prénoms: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->souscripteur2) ?></span>
                                    </p>

                                    <p><span class="text-color">Date de naissance: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->datenaissance) ?></span>
                                    </p>

                                    <p><span class="text-color">Lieu de résidence: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->lieuresidence) ?></span>
                                    </p>

                                    <p><span class="text-color">Numéro de téléphone: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->cel) ?></span>
                                    </p>

                                    <p><span class="text-color">E-mail: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->email) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des documents joints -->
                    <div class="card-box mb-30 p-2">
                        <h4 class="text-center p-2 text-color">Liste des documents joints</h4>
                        <div class="divider bg-custom-primary"></div>

                        <div class="card-body radius-12 w-100 p-4 bg-light">
                            <div class="text-center mb-3">
                                <span id="compteur_valides" class="badge bg-success">Validés: 0</span>
                                <span id="compteur_restants" class="badge bg-danger">Restants: <?= count($retour_documents) ?></span>
                            </div>

                            <?php if (!empty($retour_documents)): ?>
                                <?php foreach ($retour_documents as $doc): ?>

                                    <?php
                                    //print_r($doc);
                                    $documents = Config::URL_PRESTATION_RACINE . trim($doc["path"]);
                                    $nom_document = trim($doc["type"]);
                                    ?>
                                    <div class="document-card d-flex align-items-center mt-3 p-2 border rounded" id="line_<?= $doc["id"] ?>">
                                        <input type="hidden" class="val_doc"
                                            value="<?= "{$doc["idPrestation"]}-{$doc["id"]}-{$nom_document}-{$doc["type"]}" ?>">
                                        <input type="hidden" class="path_doc" value="<?= htmlspecialchars($documents) ?>">

                                        <div class="fm-file-box text-danger p-2">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </div>

                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0">
                                                <a href="<?= htmlspecialchars($documents) ?>" target="_blank">
                                                    <?= htmlspecialchars($nom_document) ?>
                                                </a>
                                            </h6>
                                            <small class="text-muted"><?= $doc["created_at"] ?></small>
                                        </div>

                                        <button type="button" class="btn btn-warning btn-sm view-doc"
                                            data-doc-id="<?= $doc["id"] ?>"
                                            data-path="<?= htmlspecialchars($documents) ?>">
                                            <i class="dw dw-eye"></i> Voir
                                        </button>
                                    </div>
                                    <div id="checking_<?= $doc["id"] ?>"></div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">Aucun document joint</div>
                            <?php endif; ?>

                            <input type="hidden" id="tablo_doc_attendu" value="<?= htmlspecialchars(json_encode($tablo_doc_attendu)) ?>">
                        </div>
                    </div>
                </div>

                <!-- Colonne droite - Informations contrat et prestation -->
                <div class="col-md-7">
                    <!-- Informations sur le contrat -->
                    <div class="card-box mb-30 p-2">
                        <h4 class="text-center p-2 text-color">Information sur le contrat via NSIL</h4>
                        <div class="divider bg-custom-primary"></div>

                        <div class="card-body radius-12 w-100 p-4 bg-light">
                            <div class="row" id="infos-contrat">
                                <!-- Contenu chargé via AJAX -->
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur la prestation -->
                    <div class="card-box mb-30 p-2">
                        <h4 class="text-center p-2 text-color">Détails de la prestation</h4>
                        <div class="divider bg-custom-primary"></div>

                        <div class="card-body radius-12 w-100 p-4 bg-light">
                            <div class="row text-color">
                                <div class="col-md-6">
                                    <p><span class="text-color">Date demande: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->lib_datedemande) ?></span>
                                    </p>

                                    <p><span class="text-color">Type de prestation: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->typeprestation) ?></span>
                                    </p>

                                    <p><span class="text-color">Code prestation: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->code) ?></span>
                                    </p>

                                    <p><span class="text-color">Id du contrat: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->idcontrat) ?></span>
                                    </p>

                                    <p><span class="text-color">Commentaire: </span>
                                        <span class="text-infos"><?= $prestation->msgClient ?? "" ?></span>
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <p><span class="text-color">Montant souhaité: </span>
                                        <span class="text-infos"><?= $prestation->montantSouhaite ?> FCFA</span>
                                    </p>

                                    <p><span class="text-color">Moyen de paiement: </span>
                                        <span class="text-infos"><?= htmlspecialchars($prestation->lib_moyenPaiement) ?></span>
                                    </p>

                                    <?php if ($prestation->moyenPaiement == "Virement_Bancaire"): ?>
                                        <p><span class="text-color">IBAN du compte: </span>
                                            <span class="text-infos"><?= htmlspecialchars($prestation->IBAN) ?></span>
                                        </p>
                                    <?php else: ?>
                                        <p><span class="text-color">Opérateur: </span>
                                            <span class="text-infos"><?= htmlspecialchars($prestation->lib_Operateur) ?></span>
                                        </p>

                                        <p><span class="text-color">Téléphone de Paiement: </span>
                                            <span class="text-infos"><?= htmlspecialchars($prestation->telPaiement) ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section de traitement -->
            <div class="pd-20">
                <div class="card-box height-100-p pd-20">
                    <h4 class="text-center p-2 text-color">Traitement de la prestation</h4>
                    <div class="divider bg-custom-primary"></div>

                    <div class="row card-body bg-light" id="infos_perso">
                        <div class="col-md-6">
                            <p>Traité le: <span class="text-infos"><?= htmlspecialchars($prestation->created_at) ?></span></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p>Traité par: <span class="text-infos"><?= htmlspecialchars($_SESSION['utilisateur']) ?></span></p>
                        </div>

                        <input type="hidden" id="id_prestation" value="<?= $prestation->id ?>">
                        <input type="hidden" id="codeprest" value="<?= htmlspecialchars($prestation->code) ?>">
                        <input type="hidden" id="valideur" value="<?= htmlspecialchars($_SESSION['utilisateur']) ?>">
                    </div>

                    <!-- Options de traitement -->
                    <div class="row mt-3">
                        <div class="col-md-8 offset-md-2">
                            <div class="form-group">
                                <label class="col-form-label fw-bold">État de la demande:</label>
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="customRadio" id="customRadio1" value="3">
                                        <label class="form-check-label text-danger fw-bold" for="customRadio1">Rejeter</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="customRadio" id="customRadio2" value="1">
                                        <label class="form-check-label text-secondary fw-bold" for="customRadio2">En attente</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="customRadio" id="customRadio3" value="2">
                                        <label class="form-check-label text-success fw-bold" for="customRadio3">Accepter</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zone dynamique pour les options de traitement -->
                    <div id="afficheuse" class="mt-4"></div>

                    <!-- Boutons d'action -->
                    <div class="modal-footer" id="footer">
                        <div id="optionTraitement" class="d-flex justify-content-end w-100"></div>
                    </div>
                </div>
            </div>

            <!-- Pied de page -->
            <div class="footer-wrap pd-20 mb-20">
                <?php include "include/footer.php"; ?>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <?php include "include/modals.php"; ?>

    <!-- Toast de notification -->
    <div class="position-fixed top-0 start-50 translate-middle-x p-3 toast-container">
        <div id="customToast" class="toast align-items-center text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Code JavaScript amélioré
        $(document).ready(function() {
            // Initialisation
            const prestation = {
                id: <?= $prestation->id ?>,
                code: "<?= $prestation->code ?>",
                etape: <?= $prestation->etape ?>,
                idcontrat: "<?= $prestation->idcontrat ?>"
            };

            const utilisateur = "<?= $_SESSION['utilisateur'] ?>";
            const tabloDocuments = <?= json_encode($tablo_doc_attendu) ?>;
            let documentsValides = [];
            let documentsRejetes = [];

            // Initialiser l'interface
            initInterface();

            // Écouteurs d'événements
            $('input[name="customRadio"]').change(onStatusChange);
            $(document).on('click', '.view-doc', onViewDocument);
            $(document).on('click', '#valid_download', onValidateDocument);
            $(document).on('click', '#annuler_download', onCancelValidation);
            $(document).on('click', '#confirmerRejet', onConfirmRejection);
            $(document).on('click', '#validerRejet', onFinalizeRejection);
            $(document).on('click', '#valider', onValidatePrestation);

            // Fonctions
            function initInterface() {
                // Sélectionner le statut actuel
                $(`input[name="customRadio"][value="${prestation.etape}"]`).prop('checked', true);

                // Charger les infos du contrat si disponible
                if (prestation.idcontrat) {
                    loadContractInfo(prestation.idcontrat);
                }

                // Initialiser les compteurs
                updateCounters();
            }

            function onStatusChange() {
                const status = $(this).val();

                switch (status) {
                    case '3': // Rejet
                        showRejectionOptions();
                        break;
                    case '2': // Validation
                        if (canValidatePrestation()) {
                            showValidationOptions();
                        } else {
                            resetToPendingStatus();
                            showToast("Veuillez valider tous les documents avant d'accepter la prestation", "warning");
                        }
                        break;
                    default: // En attente
                        clearActionOptions();
                }
            }

            function canValidatePrestation() {
                return tabloDocuments.length > 0 &&
                    documentsValides.length === tabloDocuments.length;
            }

            function showRejectionOptions() {
                // Charger les motifs de rejet
                loadRejectionReasons()
                    .then(reasons => {
                        const html = `
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="fw-bold">Motif de rejet:</label>
                                    <select id="motifRejet" class="form-control" multiple>
                                        ${reasons.map(reason => 
                                            `<option value="${reason.code}|${reason.libelle}">${reason.libelle}</option>`
                                        ).join('')}
                                        <option value="99|Autre motif">Autre motif</option>
                                    </select>
                                </div>
                                <div class="form-group mt-3">
                                    <label class="fw-bold">Observations:</label>
                                    <textarea id="observation" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                        </div>`;

                        $('#afficheuse').html(html);
                        $('#motifRejet').select2({
                            placeholder: "Sélectionnez un ou plusieurs motifs"
                        });

                        // Bouton de confirmation
                        $('#optionTraitement').html(`
                        <button id="confirmerRejet" class="btn btn-danger">
                            <i class="fa fa-times"></i> Confirmer le rejet
                        </button>`);
                    });
            }

            function showValidationOptions() {
                const html = `
                <div class="row">
                    <div class="col-md-12">
                        <fieldset class="border p-3 mb-3">
                            <legend class="fw-bold">Déclaration de conformité</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="certification">
                                <label class="form-check-label" for="certification">
                                    Je certifie que tous les documents sont conformes
                                </label>
                            </div>
                        </fieldset>
                        
                        <h5 class="text-center fw-bold">Vérification pour migration NSIL</h5>
                        <div class="divider bg-custom-primary mb-3"></div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label class="fw-bold">ID Contrat:</label>
                                <input type="text" class="form-control" value="${prestation.idcontrat}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Type d'opération:</label>
                                <select id="typeOpe" class="form-control">
                                    <option value="">...</option>
                                    <option value="TECH-Technique">Technique</option>
                                    <option value="AVT-Administratif">Administratif</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="fw-bold">Liste d'opération:</label>
                                <select id="ListeOpe" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </div>`;

                $('#afficheuse').html(html);

                // Bouton de validation
                $('#optionTraitement').html(`
                <button id="valider" class="btn btn-success">
                    <i class="fa fa-check"></i> Valider la prestation
                </button>
                <div id="spinner" class="d-none ms-2">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`);
            }

            // ... (autres fonctions restantes à implémenter de la même manière)

            // Fonction utilitaire pour afficher les toasts
            function showToast(message, type = 'success') {
                const toastEl = $('#customToast');
                const toastBody = $('#toastMessage');

                // Configurer le toast en fonction du type
                toastEl.removeClass('bg-success bg-danger bg-warning bg-info');
                toastEl.addClass(`bg-${type}`);

                if (type === 'warning') {
                    toastEl.addClass('text-dark');
                } else {
                    toastEl.removeClass('text-dark');
                }

                // Afficher le message
                toastBody.text(message);

                // Initialiser et afficher le toast
                const toast = new bootstrap.Toast(toastEl[0]);
                toast.show();
            }
        });
    </script>
</body>

</html>