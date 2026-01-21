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
if (isset($etat) && $etat !== null && in_array($etat, array_keys(Config::tablo_statut_rdv))) {
	$etat = $etat;
	$retourEtat = Config::tablo_statut_rdv[$etat];
	$libelleTraitement = " - " . $retourEtat["libelle"];
	$couleur = $retourEtat["color"];
} else {
	$etat = null;
	$libelleTraitement = " - Total(s)";
	$couleur = "#000000";
}

$liste_rdvs = $fonction->getSelectRDVAfficher($etat);
if ($liste_rdvs != null) $effectue = count($liste_rdvs);
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
								<h4><?= Config::lib_pageListeRDV ?></h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a
											href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page">
										<?= Config::lib_pageListeRDV ?></li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<?php
				$retourStatut = $fonction->afficheuseGlobalStatistiqueRDV();
				echo $retourStatut;
				?>

				<div class="card-box mb-30">
					<div class="pd-20">
						<h4 class="text-center" style="color:info; "> Liste des Rendez-vous <span style="color:<?= $couleur ?>;"><?= $libelleTraitement ?></span> </h4>
					</div>
					<div class="pb-20">
						<div class="col text-center">
							<h5><?= "Total Ligne  : " ?> <span style="color:<?= $couleur ?> !important;"><?= $effectue ?></span> </h5>
						</div>
					</div>

					<div class="pb-20">
						<table class="table hover data-table-export nowrap" id="liste-rdv-attente" style="width:100%; font-size:10px;">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th>Id RDV</th>
									<th>Date prise RDV</th>
									<th>Nom & prénom(s)</th>
									<th>Id contrat</th>
									<th>Motif</th>
									<th>Date RDV</th>
									<th>Lieu RDV</th>
									<?php if (!empty($liste_rdvs) && ($liste_rdvs[0]->etat == "2" || $liste_rdvs[0]->etat == "3")): ?>
										<th>Détail</th>
									<?php elseif (!empty($liste_rdvs) && $liste_rdvs[0]->etat == "0"): ?>
										<th>Motif rejet</th>
									<?php else: ?>
										<th>Délais</th>
									<?php endif; ?>
									<th>État</th>
									<th class="table-plus datatable-nosort">Action</th>
								</tr>
							</thead>

							<tbody>

								<?php if (!empty($liste_rdvs)) : ?>

									<?php foreach ($liste_rdvs as $i => $rdv) : ?>

										<?php
										// État
										$etat = (!empty($rdv->etat) && isset(Config::tablo_statut_rdv[$rdv->etat]))	? $rdv->etat : 1;
										// $retourEtat = Config::tablo_statut_rdv[$rdv->etat];
										$retourEtat = Config::tablo_statut_rdv[$etat];
										//print_r(Config::tablo_statut_rdv[$rdv->etat]);
										$dateCompare = null;
										$lib_delai = null;
										$couleur_fond = null;
										$badge_delai = null;
										// Détermination de la date RDV affichée
										$dateRdv = $rdv->daterdv;
										if ($rdv->etat == "2" || $rdv->etat == "3") {
											if (isset($rdv->daterdveff) && $rdv->daterdveff != "") $dateRdv = date("d/m/Y", strtotime($rdv->daterdveff));

											if ($rdv->etat == "2") $dateCompare = $rdv->transmisLe;
											if ($rdv->etat == "3") $dateCompare = $rdv->traiterLe;
										}

										$delai = $fonction->getDelaiRDV($dateRdv, $dateCompare);
										if ($rdv->etat == "1") {
											$lib_delai = $delai['libelle'];
											$couleur_fond = $delai['couleur'] ?? 'transparent';
											$badge_delai = $delai['badge'] ?? 'badge badge-secondary';
										}
										?>

										<tr id="ligne-<?= $i ?>" style="color: <?= htmlspecialchars($couleur_fond) ?>;">
											<td class="table-plus"><?= $i + 1 ?></td>
											<td id="id-<?= $i ?>"><?= htmlspecialchars($rdv->idrdv) ?></td>
											<td>
												<?= $rdv->dateajou ?>
											</td>
											<td class="text-wrap">
												<?= htmlspecialchars($rdv->nomclient) ?>
												<p class="mb-0 text-dark" style="font-size:0.7em;">
													Téléphone :
													<span style="font-weight:bold;"><?= htmlspecialchars($rdv->tel) ?></span>
												</p>
											</td>
											<td id="idcontrat-<?= $i ?>"><?= htmlspecialchars($rdv->police ?? '') ?></td>
											<td><?= htmlspecialchars($rdv->motifrdv ?? '') ?></td>
											<td id="daterdv-<?= $i ?>" style="font-weight:bold;">
												<?= htmlspecialchars($dateRdv) ?>
											</td>
											<td style="color:#F9B233; font-weight:bold;">
												<?= $villes = !empty($rdv->villes) ? strtoupper($rdv->villes) : "Non mentionné" ?>
											</td>
											<td class="text-wrap">
												<?php if ($rdv->etat == "1"): ?>
													<span class="<?= htmlspecialchars($badge_delai) ?>"><?= $lib_delai ?></span>
												<?php elseif ($rdv->etat == "2"): ?>
													<p class="mb-0 text-dark" style="font-size:0.7em;">
														Gestionnaire :
														<span style="font-weight:bold;"><?= htmlspecialchars($rdv->nomgestionnaire ?? "N/A") ?></span>
													</p>
													<p class="mb-0 text-dark" style="font-size:0.7em;">
														Date Transmission :
														<span style="font-weight:bold;"><?= !empty($rdv->transmisLe) ? date('d/m/Y', strtotime($rdv->transmisLe)) : "" ?></span>
													</p>
													<p class="mb-0 text-dark" style="font-size:0.7em;">
														<?php if ($rdv->etat == "2" && ($dateRdv < date('Y-m-d'))): ?>
															<span style="font-weight:bold; color:red;">Date RDV Expiré </span>
														<?php endif; ?>
													</p>
												<?php elseif ($rdv->etat == "3"): ?>
													<p class="mb-0 text-dark" style="font-size:0.7em;">
														Gestionnaire :
														<span style="font-weight:bold;"><?= htmlspecialchars($rdv->nomgestionnaire ?? "N/A") ?></span>
													</p>
													<p class="mb-0 text-dark" style="font-size:0.7em;">
														Date Traitement :
														<span style="font-weight:bold;"><?= !empty($rdv->traiterLe) ? date('d/m/Y H:i', strtotime($rdv->traiterLe)) : "" ?></span>

													</p>
													<p class="mb-0 text-dark" style="font-size:0.7em;">
														Traitement :
														<span style="font-weight:bold;">
															<?php if (isset($rdv->etatTraitement) && $rdv->etatTraitement != null && $rdv->etatTraitement != "0"): ?>
																<?= $rdv->libelleTraitement ?>
															<?php else: ?>
																traitement non mentionné
															<?php endif; ?>
														</span>
													</p>
												<?php else: ?>
													<?= $rdv->reponse ?>
												<?php endif; ?>
											</td>

											<td>
												<span class="<?= htmlspecialchars($retourEtat["color_statut"]) ?>">
													<?= htmlspecialchars($retourEtat["libelle"]) ?>
												</span>
											</td>

											<td>
												<button class="btn btn-warning btn-sm view"
													id="view-<?= $i ?>"
													style="background-color:#F9B233;color:white">
													<i class="fa fa-eye"></i> Détail
												</button>

												<?php if ($rdv->etat == "1"): ?>
													<button class="btn btn-success btn-sm traiter"
														id="traiter-<?= $i ?>"
														style="background-color:#033f1f; color:white">
														<i class="fa fa-mouse-pointer"></i> Traiter
													</button>
												<?php elseif ($rdv->etat == "2" && ($dateRdv < date('Y-m-d'))): ?>

													<button class="btn btn-info btn-sm traiter"
														id="traiter-<?= $i ?>"
														style="background-color:info; color:white">
														<i class="fa fa-mouse-pointer"></i> retraiter le rdv
													</button>

												<?php endif; ?>

											</td>

										</tr>

									<?php endforeach; ?>

								<?php endif; ?>
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
						Voulez vous rejeter la demande de rdv <span id="a_afficher_1" name="a_afficher_1"
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

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

	<script>
		$(document).ready(function() {



			// Voir detail
			$(document).on('click', '.view', function() {
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				document.cookie = "idrdv=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=detail";
				location.href = "detail-rdv";
			});

			// Traiter
			$(document).on('click', '.traiter', function() {
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				document.cookie = "idrdv=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = "fiche-rdv";
			});


			// $(".fa-trash").click(function(evt) {
			// 	const ind = extraireIndex(evt.target.id);
			// 	if (!ind) return;
			// 	const {
			// 		idrdv,
			// 		daterdv
			// 	} = extraireInfosRdv(ind);
			// 	$("#idprestation").val(idrdv);
			// 	$("#a_afficher_1").text(`n° ${idrdv} du ${daterdv}`);
			// 	$('#confirmation-modal').modal('show');
			// });


		})

		// $("#validerRejet").click(function() {
		// 	const idrdv = $("#idprestation").val();
		// 	const valideur = "<?= $_SESSION['id'] ?>";
		// 	$.ajax({
		// 		url: "config/routes.php",
		// 		method: "POST",
		// 		dataType: "json",
		// 		data: {
		// 			idrdv,
		// 			motif: "",
		// 			traiterpar: valideur,
		// 			observation: "Aucune observation",
		// 			etat: "confirmerRejetRDV"
		// 		},
		// 		success: function(response) {
		// 			const msg = response !== '-1' && response !== '0' ?
		// 				`<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été rejetée !</h2></div>` :
		// 				`<div class="alert alert-danger" role="alert"><h2>Erreur lors du rejet de la RDV <span class="text-danger">${idrdv}</span>.</h2></div>`;
		// 			$("#msgEchec").html(msg);
		// 			$('#notificationValidation').modal("show");
		// 		},
		// 		error: function(err) {
		// 			console.error("Erreur AJAX rejet RDV", err);
		// 		}
		// 	});
		// });


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