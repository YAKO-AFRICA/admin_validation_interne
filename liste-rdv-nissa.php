<?php
session_start();


if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}/**/


include("autoload.php");

$libelle = '';
if (isset($_REQUEST['filtreliste']) || isset($_REQUEST['etat'])) {

	$retourPlus = $fonction->getFiltreuseRDV();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];
	if ($retourPlus["filtre"] != null) {
		list($ii, $pars1) = explode('AND', $retourPlus["filtre"], 2);
		$plus = " WHERE $pars1 ";
	}
	//print_r($_REQUEST);exit;
} else {
	$plus = '';
}



//$plus = $filtre;

$sqlSelect = "SELECT tblrdv.* ,  concat (users.nom,' ',users.prenom) as nomgestionnaire  FROM tblrdv LEFT JOIN users on tblrdv.gestionnaire = users.id  $plus ORDER BY idrdv DESC";
//echo $sqlSelect; exit;

/*
$sqlSelect = "SELECT tblrdv.*,  CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire FROM tblrdv LEFT JOIN users ON tblrdv.gestionnaire = users.id 
WHERE tblrdv.etat = '1' AND STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) ORDER BY STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y') ASC ";
*/
//echo $sqlSelect; exit;
$liste_rdvs = $fonction->_getSelectDatabases($sqlSelect);
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
											href="accueil-operateur.php"><?= Config::lib_pageAccueil ?></a></li>
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
						<h4 class="text-center title-section">
							Liste des Rendez-vous
							<br>
							<span class="subtitle"><?= $libelle ?></span>
						</h4>
					</div>

					<div class="pb-20">
						<div class="col text-center">
							<h5 class="total-ligne">
								Total Ligne : <span class="highlight"><?= $effectue ?></span>
							</h5>
						</div>
					</div>

					<div class="pb-20">
						<table class="table hover  data-table-export nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th hidden>Id</th>
									<th>Date</th>
									<th>Nom & prénom(s)</th>
									<th>Id contrat</th>
									<th>Motif</th>
									<th>Date RDV souhaité</th>
									<th>lieu RDV</th>
									<th class="table-plus datatable-nosort">Etat</th>
									<th class="table-plus datatable-nosort">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($liste_rdvs != null) {


									$effectue = count($liste_rdvs);
									for ($i = 0; $i <= ($effectue - 1); $i++) {

										$rdv = $liste_rdvs[$i];
										$idTblBureau = $rdv->idTblBureau;

										$sqlQuery = "SELECT TRIM(libelleVilleBureau) as villes FROM tblvillebureau where idVilleBureau = '" . $idTblBureau . "'";
										$retourVilles = $fonction->_getSelectDatabases($sqlQuery);
										if ($retourVilles != null) $villes = trim($retourVilles[0]->villes);
										else $villes = "NAN";


										if (isset($rdv->etat) && $rdv->etat !== null && in_array($rdv->etat, array_keys(Config::tablo_statut_rdv)))  $etat = $rdv->etat;
										else $etat = 1;
										$retourEtat = Config::tablo_statut_rdv[$etat];

								?>
										<tr>
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $rdv->idrdv; ?></td>
											<td><?php echo $rdv->dateajou; ?></td>
											<td class="text-wrap"><?php echo $rdv->nomclient; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Téléphone :<span style="font-weight:bold;"><?php echo $rdv->tel; ?></span>
												</p>
											</td>

											<td class="text-wrap" id="idcontrat-<?= $i ?>"><?php echo $rdv->police; ?></td>

											<td class="text-wrap"><?php echo $rdv->motifrdv; ?> </td>
											<td id="daterdv-<?= $i ?>"><?php echo $rdv->daterdv; ?></td>
											<td class="text-wrap" style="font-weight:bold; color:#F9B233!important;">
												<?php echo $villes; ?></td>
											<td>
												<span
													class="<?php echo $retourEtat["color_statut"]; ?>"><?php echo $retourEtat["libelle"] ?></span>
											</td>


											<td class="table-plus">
												<label class="btn btn-secondary" style="background-color:#F9B233 ;"
													for="click-<?= $i ?>"><i class="fa fa-eye" id="click-<?= $i ?>"> Détail
													</i></label>
												<?php if ($rdv->etat == "1") { ?>
													<label class="btn btn-secondary" style="background-color: #033f1f ;"
														for="click-<?= $i ?>"><i class="fa fa-mouse-pointer" id="click-<?= $i ?>">
															Traiter</i></label>
												<?php  } ?>
												<label class="btn btn-secondary" style="background-color: #e74c3c ;"
													for="click-<?= $i ?>"><i class="fa fa-trash" id="click-<?= $i ?>"> Rejeter
													</i></label>

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
				<div class="tab-content">
				</div>
			</div>
			<hr>

		</div>
		<div class="footer-wrap pd-20 mb-20">
			<?php include "include/footer.php";    ?>
		</div>
	</div>
	</div>


	<div class="modal fade" id="confirmation-modal22" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body text-center font-18">
					<h4 class="padding-top-30 mb-30 weight-500">Voulez vous supprimer la demande n° : <span
							id="a_afficher3" style="color: #F9B233;"> </span>?</h4>
					<input type="text" hidden class="form-control" name="idobjet" id="idobjet">

					<div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
						<div class="col-6">
							<button type="button" id="validerSuprime"
								class="btn btn-danger border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-check"></i></button>
							OUI
						</div>
						<div class="col-6">
							<button type="button" id="annulerSuprime"
								class="btn btn-secondary border-radius-100 btn-block confirmation-btn"
								data-dismiss="modal"><i class="fa fa-times"></i></button>
							NON
						</div>

					</div>
				</div>
			</div>
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

	<script>
		$(document).ready(function() {


			$(".fa-eye, .fa-mouse-pointer").click(function(evt) {
				const ind = extraireIndex(evt.target.id);
				if (!ind) return;
				const {
					idrdv,
					idcontrat
				} = extraireInfosRdv(ind);
				document.cookie = "idrdv=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = evt.target.classList.contains('fa-eye') ? "detail-rdv" : "fiche-rdv";
			});



			$(".fa-trash").click(function(evt) {
				const ind = extraireIndex(evt.target.id);
				if (!ind) return;
				const {
					idrdv,
					daterdv
				} = extraireInfosRdv(ind);
				$("#idprestation").val(idrdv);
				$("#a_afficher_1").text(`n° ${idrdv} du ${daterdv}`);
				$('#confirmation-modal').modal('show');
			});


		})

		$("#validerRejet").click(function() {
			const idrdv = $("#idprestation").val();
			const valideur = "<?= $_SESSION['id'] ?>";
			$.ajax({
				url: "config/routes.php",
				method: "POST",
				dataType: "json",
				data: {
					idrdv,
					motif: "",
					traiterpar: valideur,
					observation: "Aucune observation",
					etat: "confirmerRejetRDV"
				},
				success: function(response) {
					const msg = response !== '-1' && response !== '0' ?
						`<div class="alert alert-success" role="alert"><h2>Le RDV <span class="text-success">${idrdv}</span> a bien été rejetée !</h2></div>` :
						`<div class="alert alert-danger" role="alert"><h2>Erreur lors du rejet de la RDV <span class="text-danger">${idrdv}</span>.</h2></div>`;
					$("#msgEchec").html(msg);
					$('#notificationValidation').modal("show");
				},
				error: function(err) {
					console.error("Erreur AJAX rejet RDV", err);
				}
			});
		});


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




<script>
	/*var oTable = $('#listeReclams').DataTable({
		order: [
			[0, 'desc']
		],
		buttons: [
			'copy', 'excel', 'pdf'
		]
	});*/
</script>

</html>