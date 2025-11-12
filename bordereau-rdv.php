<?php
session_start();

if (empty($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("autoload.php");

$plus = " WHERE etape != '1' ";
$libelle = "";
$afficheuse = false;
$periode = "";
$VilleRDV = "";
$nomGest = "";
$effectue = 0;

if (isset($_REQUEST['filtreliste'])) {
	$afficheuse = true;

	// Récupération des champs
	$rdvSouhaitLe = $_REQUEST["rdvLe"] ?? null;
	$rdvSouhaitAu = $_REQUEST["rdvAu"] ?? null;
	$villesRDV    = $_REQUEST["villesRDV"] ?? null;
	$ListeGest    = $_REQUEST["ListeGest"] ?? null;

	// Gestion de la période
	if (!empty($rdvSouhaitLe) && !empty($rdvSouhaitAu)) {
		$periode = date('d/m/Y', strtotime($rdvSouhaitLe)) . " - " . date('d/m/Y', strtotime($rdvSouhaitAu));
	} elseif (!empty($rdvSouhaitLe)) {
		$periode = date('d/m/Y', strtotime($rdvSouhaitLe));
	} elseif (!empty($rdvSouhaitAu)) {
		$periode = date('d/m/Y', strtotime($rdvSouhaitAu));
	}

	// Gestion des villes
	if (!empty($villesRDV)) {
		[$idVilleRDV, $VilleRDV] = explode(';', $villesRDV, 2);
	}

	// Gestionnaire
	if (!empty($ListeGest)) {
		[$idGest, $nomGest, $idVilleGest, $VilleGest] = explode('|', $ListeGest, 4);
	}

	// Application du filtre
	$retourPlus = $fonction->getFiltreuseRDV();
	$filtre     = $retourPlus["filtre"] ?? "";
	$libelle    = $retourPlus["libelle"] ?? "";

	if (!empty($filtre)) {
		[, $conditions] = explode('AND', $filtre, 2);
		$plus = " WHERE $conditions ";
	}
}

$sqlSelect = "
    SELECT 
        tblrdv.*,
        CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
        users.telephone AS telgest,
        users.adresse AS localisation,
        users.email AS mailgest,
        TRIM(tblvillebureau.libelleVilleBureau) AS villes
    FROM tblrdv
    LEFT JOIN users ON tblrdv.gestionnaire = users.id
    LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
    $plus
    ORDER BY tblrdv.idrdv DESC
";

// echo $sqlSelect; exit;

$liste_prestations = $fonction->_getSelectDatabases($sqlSelect);
$effectue = is_array($liste_prestations) ? count($liste_prestations) : 0;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<?php include "include/entete.php"; ?>
	<title>Journal RDV</title>
</head>

<body>
	<?php include "include/header.php"; ?>

	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-12">
							<div class="title">
								<h4>JOURNAL RDV</h4>
							</div>
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="accueil-operateur.php">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">Journal RDV</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<?php if ($afficheuse): ?>
					<div class="mb-3">
						<button class="btn btn-warning" onclick="retour()">
							<i class="fa fa-arrow-left"></i> Retour
						</button>
					</div>

					<!-- Titre RDV -->
					<div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
						<div class="card-body text-center ">
							<h3 style="color:white">
								<span class="text-warning">
									Résultats de la recherche
								</span>
							</h3>
						</div>
					</div>
					<div class="card-box mb-30">

						<div class="row pd-20 text-center">
							<div class="col-md-12">
								<p><span class="text-color">Période :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= htmlspecialchars($periode) ?></span></p>
								<p><span class="text-color">Nom & Prenom du gestionnaire :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= htmlspecialchars($nomGest) ?></span></p>
								<p><span class="text-color">Ville :</span> <span class="text-infos" style="text-transform:uppercase; font-weight:bold;"><?= htmlspecialchars($VilleRDV) ?></span></p>

							</div>

						</div>
					</div>
					<div class="card-box mb-30">

						<div class="pd-20">

							<input type="hidden" id="rdvLe" value="<?= htmlspecialchars($rdvSouhaitLe) ?>" />
							<input type="hidden" id="rdvAu" value="<?= htmlspecialchars($rdvSouhaitAu) ?>" />
							<input type="hidden" id="villesRDV" value="<?= htmlspecialchars($villesRDV) ?>" />
							<input type="hidden" id="ListeGest" value="<?= htmlspecialchars($ListeGest) ?>" />

							<button class="btn btn-secondary p-2 m-2" id="addBordereau"
								style="background:#033f1f!important;float:right">
								<i class="icon-copy fa fa-user-plus"></i> Charger le bordereau de RDV
							</button>
						</div>

						<div class="pb-20">

							<div class="pd-20">
								<h4 class="text-blue h4" style="color:#033f1f!important;"><span style="color:#F9B233;"><?= $effectue ?> - </span> Total Ligne rdv affecté pour la periode <?= $periode ?> </h4>
								<div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
							</div>
							<table class="data-table table stripe hover nowrap">
								<thead>
									<tr>
										<th>#Ref</th>
										<th>Date demande</th>
										<th>Demandeur</th>
										<th>Id contrat</th>
										<th>Motif RDV</th>
										<th>Date RDV</th>
										<th>Ville RDV</th>
										<th>Gestionnaire</th>
										<th>État</th>
										<th class="datatable-nosort">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($liste_prestations)): ?>
										<?php foreach ($liste_prestations as $i => $rdv):
											$etat = $rdv->etat ?? 1;
											$retourEtat = Config::tablo_statut_rdv[$etat] ?? ["color_statut" => "", "libelle" => "Inconnu"];
										?>
											<tr>
												<td><?= $i + 1 ?></td>
												<td><?= htmlspecialchars($rdv->dateajou) ?></td>
												<td class="text-wrap">
													<?= htmlspecialchars($rdv->nomclient) ?><br>
													<small>Tél : <strong><?= htmlspecialchars($rdv->tel) ?></strong></small>
												</td>
												<td><?= htmlspecialchars($rdv->police) ?></td>
												<td><?= htmlspecialchars($rdv->motifrdv ?? "Non renseigné") ?></td>
												<td style="font-weight:bold; color:#F9B233;"><?= htmlspecialchars(@date('d/m/Y', strtotime($rdv->daterdveff))) ?> </td>
												<td style="font-weight:bold; color:#F9B233;"><?= htmlspecialchars($rdv->villes) ?>
												</td>
												<td style="font-weight:bold; color:#F9B233;">
													<?= htmlspecialchars($rdv->nomgestionnaire ?? "Non renseigné") ?></td>
												<td><span
														class="<?= $retourEtat["color_statut"] ?>"><?= $retourEtat["libelle"] ?></span>
												</td>
												<td>
													<button class="btn btn-warning btn-sm voir-detail" data-id="<?= $rdv->idrdv ?>"
														data-contrat="<?= $rdv->police ?>">
														<i class="fa fa-eye"></i> Détail
													</button>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else: ?>
										<tr>
											<td colspan="10" class="text-center text-danger">Aucun résultat trouvé.</td>
										</tr>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php else: ?>
					<!-- Bouton retour -->
					<div class="mb-3">
						<button class="btn btn-warning" onclick="retour()">
							<i class="fa fa-arrow-left"></i> Retour
						</button>
					</div>

					<!-- Titre RDV -->
					<div class="card mb-4 text-white" style="border:1px solid gray;background:#033f1f!important; color:white">
						<div class="card-body text-center ">
							<h3 style="color:white">
								<span class="text-warning">
									Ajouter un nouveau bordereau de RDV à un Gestionnaire
								</span>
							</h3>
						</div>
					</div>

					<div class="card-box pd-10 height-100-p mb-15" id="formFiltreRDV">

						<div class="card-body mb-30">
							<div class="card-body" style="border:2px solid #F9B233;">
								<form method="POST">

									<!-- Sélection des dates -->
									<div class="row">
										<div class="col-md-6">
											<h6 style="color: #033f1f !important;">Date de RDV</h6>
											<input type="date" name="rdvLe" class="form-control" id="rdvLe"
												value="">
										</div>
										<div class="col-md-6">
											<h6 style="color: #033f1f !important;">Au</h6>
											<input type="date" name="rdvAu" class="form-control" id="rdvAu"
												value="">
										</div>
									</div>

									<!-- Sélection ville et gestionnaire -->
									<div class="row mt-3">
										<div class="col-md-4 form-group">
											<h6 style="color: #033f1f !important;">Ville RDV</h6>
											<?= $fonction->getVillesBureau("", "") ?>
										</div>
										<div class="col-md-8 form-group">
											<h6 style="color: #033f1f !important;">Gestionnaire</h6>
											<select name="ListeGest" id="ListeGest" class="form-control"
												data-rule="required"></select>
										</div>
									</div>

									<!-- Bouton rechercher -->
									<div class="modal-footer" id="footer">
										<button type="submit" name="filtreliste" id="filtreliste" class="btn btn-danger"
											style="background:#F9B233 !important;border-color:#F9B233 !important;">
											RECHERCHER
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>

				<?php endif; ?>
			</div>
		</div>

		<div class="footer-wrap pd-20 mb-20">
			<?php include "include/footer.php"; ?>
		</div>
	</div>

	<!-- JS -->
	<script src="vendors/scripts/core.js"></script>
	<script src="vendors/scripts/script.min.js"></script>
	<script src="vendors/scripts/process.js"></script>
	<script src="vendors/scripts/layout-settings.js"></script>
	<script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
	<script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
	<script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.print.min.js"></script>
	<script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
	<script src="src/plugins/datatables/js/pdfmake.min.js"></script>
	<script src="src/plugins/datatables/js/vfs_fonts.js"></script>
	<script src="vendors/scripts/datatable-setting.js"></script>

	<script>
		function retour() {
			window.history.back();
		}

		$(document).ready(function() {

			var objetRDV = document.getElementById("villesRDV").value;
			if (objetRDV === "null") return;
			const [idvillesRDV, villesRDV] = objetRDV.split(";");
			getListeSelectAgentTransformations(idvillesRDV, villesRDV);
			//var dateRDVEffective = document.getElementById("daterdveff").value; //alert(objetRDV)




		});

		// Action détail RDV
		$(".btn-warning").on("click", function() {
			document.cookie = "idrdv=" + $(this).data("id");
			document.cookie = "idcontrat=" + $(this).data("contrat");
			document.cookie = "action=traiter";
			location.href = "detail-rdv";
		});

		// Bouton bordereau
		$("#addBordereau").click(function() {
			document.cookie = "search=" + $("#search").val();
			document.cookie = "libelle=" + $("#libelle").val();

			var objetRDV = document.getElementById("villesRDV").value;
			var rdvLe = document.getElementById("rdvLe").value;
			var rdvAu = document.getElementById("rdvAu").value;
			var ListeGest = document.getElementById("ListeGest").value;
			document.cookie = "rdvLe=" + rdvLe;
			document.cookie = "rdvAu=" + rdvAu;
			document.cookie = "villesRDV=" + objetRDV;
			document.cookie = "ListeGest=" + ListeGest;

			location.href = "ajout-bordereau";
		});

		// Quand la ville change 
		$('#villesRDV').change(function() {
			if ($(this).val() === "null") return;
			const [idvillesRDV, villesRDV] = $(this).val().split(";");
			console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ") ");
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
						html +=
							`<option value="${data.id}|${agent}|${idVilleEff}|${villesRDV}" id="ob-${indx}">${agent}</option>`;
					});

					$("#ListeGest").html(html);
					//verifierActivationBouton(); // Vérifie après chargement
				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});
		}
	</script>
</body>

</html>