<?php
session_start();


if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}/**/


include("autoload.php");

if (isset($_REQUEST['filtreliste'])) {
	$retourPlus = $fonction->getFiltreuse();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];
} else {
	$filtre = '';
}

$etat = GetParameter::FromArray($_REQUEST, 'i');
if (isset($etat) && $etat !== null && in_array($etat, array_keys(Config::tablo_statut_prestation))) {
	$etat = $etat;
	$retourEtat = Config::tablo_statut_prestation[$etat];
	$libelleTraitement = " - ".$retourEtat["libelle"];
} else {
	$etat = null;
	$libelleTraitement = " - Total(s)";
}



$liste_sinistre = $fonction->getSelectSinistreAfficher($etat);
if ($liste_sinistre != null) $effectue = count($liste_sinistre);
else $effectue = 0;



?>

<!DOCTYPE html>
<html>

<head>
	<?php include "include/entete.php"; ?>
</head>

<body>

	<?php include "include/header.php";  ?>

	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">

				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Pre-declarations de sinistre</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a
											href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page">
										Pre-déclarations de sinistre</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<?php
					$retourStatut = $fonction->getParametreGlobalSinistre();
					echo $retourStatut;
					?>
				</div>

				<div class="card-box mb-30">
					<div class="pd-20">
						<h4 class="text-center" style="color:#033f1f; "> Liste des Pre-declarations de sinistre <?php echo ucfirst($libelleTraitement) ?></h4>
					</div>
					<div class="pb-20">
						<div class="col text-center">
							<h5><?= "Total Ligne  : " ?> <span style="color:#F9B233;"><?= $effectue ?></span></h5>
						</div>
					</div>
					<div class="pb-20">
						<table class="table hover data-table-export nowrap" id="liste-rdv-attente" style="width:100%; font-size:10px;">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th hidden>Id</th>
									<th>Date</th>
									<th>code</th>
									<th>Id contrat</th>
									<th>Declarant</th>
									<th>Assure(e)</th>
									<th>Sinistre</th>

									<th class="table-plus datatable-nosort">Etat</th>
									<th class="table-plus datatable-nosort">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php

								if ($liste_sinistre != null) {

									$effectue = count($liste_sinistre);
									for ($i = 0; $i <= ($effectue - 1); $i++) {


										$sinistre = $liste_sinistre[$i];

										if (isset($sinistre->etape) && $sinistre->etape !== null && in_array($sinistre->etape, array_keys(Config::tablo_statut_prestation)))  $etat = $sinistre->etape;
										else $etat = 1;
										$retourEtat = Config::tablo_statut_prestation[$etat];
										if (isset($sinistre->created_at) && $sinistre->created_at !== null) $dateDeclaration = date("d/m/Y H:i:s", strtotime($sinistre->created_at));
										else $dateDeclaration = "Non mentionné";

								?>
										<tr id="ligne-<?= $i ?>" style="color: #033f1f !important; ">
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $sinistre->id; ?></td>
											<td><?php echo $dateDeclaration; ?></td>
											<td id="code-<?= $i ?>"><?= htmlspecialchars($sinistre->code ?? '') ?></td>
											<td id="idcontrat-<?= $i ?>"><?= htmlspecialchars($sinistre->idcontrat ?? '') ?></td>
											<td class="text-wrap"><?php echo $sinistre->nomDecalarant . " " . $sinistre->prenomDecalarant; ?>

												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Filiation :<span style="font-weight:bold;"><?php echo $sinistre->filiation; ?></span>
												</p>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Téléphone :<span style="font-weight:bold;"><?php echo $sinistre->celDecalarant; ?></span>
												</p>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Email :<span style="font-weight:bold;"><?php echo $sinistre->emailDecalarant; ?></span>
												</p>
											</td>
											<td class="text-wrap"><?php echo $sinistre->nomAssuree . " " . $sinistre->prenomAssuree; ?>

												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													date naissance :<span style="font-weight:bold;"><?php echo $sinistre->datenaissanceAssuree; ?></span>
												</p>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													lieu naissance :<span style="font-weight:bold;"><?php echo $sinistre->lieunaissanceAssuree; ?></span>
												</p>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													lieu residence :<span style="font-weight:bold;"><?php echo $sinistre->lieuresidenceAssuree; ?></span>
												</p>
											</td>

											<td>
												<?php echo $sinistre->natureSinistre; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													cause :<span style="font-weight:bold;"><?php echo $sinistre->causeSinistre; ?></span>
												</p>

											</td>
											<td> <span class="<?php echo $retourEtat["color_statut"]; ?>"><?php echo $retourEtat["libelle"] ?></span></td>
											<td>
												<button class="btn btn-warning btn-sm view" id="view-<?= $i ?>" style="background-color:#F9B233;color:white"><i class="fa fa-eye"></i> Détail</button>
												<?php if ($sinistre->etape == "1"): ?>
													<button class="btn btn-success btn-sm traiter" id="traiter-<?= $i ?> " style="background-color:#033f1f; color:white"><i class="fa fa-mouse-pointer"></i> Traiter</button>
												<?php endif; ?>
											</td>
										</tr>
								<?php
									}
								}
								?>
							</tbody>
						</table>
					</div>
				</div>

			</div>
		</div>
		<div class="footer-wrap pd-20 mb-20">
			<?php include "include/footer.php";    ?>
		</div>
	</div>


	<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body text-center font-18">
					<h4 class="padding-top-30 mb-30 weight-500">
						Voulez vous rejeter la demande de sinistre <span id="a_afficher_1" name="a_afficher_1"
							style="color:#033f1f!important; font-weight:bold;"> </span> ?
					</h4>
					<span style='color:red;'>Attention cette action est irreversible !!</span><br>
					<span style='color:seagreen'>le client sera notifier du rejet de la demande de rdv</span>
					</hr>
					<input type="text" id="idprestation" name="idprestation" hidden>
					<input type="text" id="observations" name="observations" hidden>

					<div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
						<div class="col-6">
							<button type="button" id="annulerRejet" name="annulerRejet"
								class="btn btn-secondary border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-times"></i></button>
							NON
						</div>
						<div class="col-6">
							<button type="button" id="validerRejet" name="validerRejet"
								class="btn btn-danger border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-check"></i></button>
							OUI
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-body text-center">
					<div class="card-body" id="msgEchec">
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="retourNotification" name="retourNotification" class="btn btn-success"
						style="background: #033f1f !important;">OK</button>
					<button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
				</div>
			</div>
		</div>
	</div>

	<!-- js -->
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

	<script>
		$(document).ready(function() {



			// Voir detail
			$(document).on('click', '.view', function() {
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				document.cookie = "idsinistre=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=detail";
				location.href = "detail-sinistre";
			});

			// Traiter
			$(document).on('click', '.traiter', function() {
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				document.cookie = "idsinistre=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = "fiche-sinistre";
			});


		})


		$("#retourNotification").click(function() {
			$('#notificationValidation').modal('hide');
			location.reload(); // recharge la page au lieu de forcer vers detail-rdv
		});



		function extraireIndex(id) {
			let result = id.split('-');
			return result[1];
		}

		function extraireInfosRdv(index) {
			return {
				idrdv: $("#id-" + index).html(),
				idcontrat: $("#idcontrat-" + index).html(),
				daterdv: $("#daterdv-" + index).html()
			};
		}
	</script>



</body>


</html>