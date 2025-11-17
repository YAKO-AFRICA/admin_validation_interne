<?php
session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}


include("autoload.php");

if (isset($_REQUEST['filtreliste'])) {
	$retourPlus = $fonction->getFiltreuse();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];
} else {
	$filtre = '';
}

$params = " WHERE tbl_prestations.etape NOT IN ('0', '-1') $filtre ";


$liste_prestations = $fonction->_getRetourneAllListePrestation($params);
if ($liste_prestations != null) $effectue = count($liste_prestations);
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
								<h4>Toutes les prestations</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="accueil-operateur.php"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page"> Toutes les prestations</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>
				<?php
				if (isset($_SESSION['cible']) && $_SESSION['cible'] != "administratif") {
				?>

					<i class="icon-copy ion-navicon-round" type="submit" onclick="myFunction()" title="FILTRE">FILTRE</i>
					<div class="card-box mb-10" id="myDIV">
						<?php echo $fonction->setFiltrePrestationTechnique(); ?>
					</div>
					<hr>
				<?php
				} else { ?>
					<div class="card-box mb-10" id="myDIV"></div>
				<?php
				}
				?>

				<div class="row">
					<?php
					echo $fonction->getParametreGlobalPrestations();
					?>
				</div>

				<div class="card-box mb-30">
					<div class="pd-20">
						<h4 class="text-center" style="color:#033f1f; "> Toutes les demandes des prestations </h4>
					</div>
					<div class="pb-20">
						<div class="col text-center">
							<h5><?= "Total Ligne  : " ?> <span style="color:#F9B233;"><?= $effectue ?></span></h5>
						</div>
					</div>
					<div class="pb-20">
						<table class="table hover  data-table-export nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th hidden>Idprestation</th>
									<th>Date</th>
									<th>Code</th>
									<th>Id</th>
									<th>Souscripteur</th>
									<!-- <th>Téléphone</th> -->
									<th>Detail prestation</th>
									<!-- <th>Montant souhaite</th> -->
									<th>Etat </th>
									<th class="datatable-nosort"></th>

								</tr>
							</thead>
							<tbody>
								<?php
								if ($liste_prestations != null) {

									$effectue = count($liste_prestations);
									for ($i = 0; $i <= ($effectue - 1); $i++) {

										$prestations = new tbl_prestations($liste_prestations[$i]);

								?>
										<!-- <tr>
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $prestations->id; ?></td>
											<td class="text-wrap"><?php echo $prestations->created_at; ?></td>
											<td id="code-<?= $i ?>"><?php echo $prestations->code; ?></td>
											<td id="idcontrat-<?= $i ?>"><?php echo $prestations->idcontrat; ?></td>
											<td class="text-wrap"><?php echo $prestations->souscripteur2; ?></td>
											<td><?php echo $prestations->cel; ?></td>
											<td class="text-wrap"><?php echo $prestations->typeprestation; ?></td>
											<td class="text-wrap"><?php echo $prestations->montantSouhaite; ?></td>
											<td>
												<span class="<?php echo $prestations->color_statut; ?>"><?php echo $prestations->lib_statut; ?></span>
											</td>


											<td class="table-plus">
												<label class="btn btn-secondary" style="background-color:#F9B233 ;" for="click-<?= $i ?>"><i class="fa  fa-eye" id="click-<?= $i ?>"> Détail prestation </i></label>
												<?php if ($prestations->etape == "1") { ?>
													<label class="btn btn-secondary" style="background-color: #033f1f ;" for="click-<?= $i ?>"><i class="fa  fa-mouse-pointer" id="click-<?= $i ?>"> Traiter la prestation </i></label>
												<?php  } ?>
											</td>

										</tr> -->
										<tr id="ligne-<?= $i ?>" style="color: #033f1f !important; ">

											<td><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $prestations->id; ?></td>
											<td><?php echo $prestations->created_at; ?></td>
											<td id="code-<?= $i ?>"><?php echo $prestations->code; ?></td>
											<td id="idcontrat-<?= $i ?>"><?php echo $prestations->idcontrat; ?></td>
											<td class="text-wrap">
												<?php echo $prestations->souscripteur2; ?><br>
												<small>Date Naissance : <?= $prestations->datenaissance ?></small><br>
												<small>contact : <?= $prestations->cel ?></small>
											</td>

											<td class="text-wrap"><?= $prestations->typeprestation ?><br>
												<small>Montant : <?= $prestations->montantSouhaite ?> FCFA</small><br>
												<small>mode de paiement : <?= $prestations->lib_Operateur ?></small>
											</td>
											<td>
												<span class="<?php echo $prestations->color_statut; ?>"><?php echo $prestations->lib_statut; ?></span>
											</td>
											<td class="table-plus">
												<button class="btn btn-warning btn-sm view" id="view-<?= $i ?>" style="background-color:#F9B233;color:white"><i class="fa fa-eye"></i> Détail demande</button>
												<?php if ($prestations->etape == "1"): ?>
													<button class="btn btn-success btn-sm traiter" id="traiter-<?= $i ?> " style="background-color:#033f1f; color:white"><i class="fa fa-mouse-pointer"></i> Traiter la demande</button>
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


	<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-body text-center font-18">
					<h4 class="padding-top-30 mb-30 weight-500">Voulez vous supprimer la demande n° : <span id="a_afficher3" style="color: #F9B233;"> </span>?</h4>
					<input type="text" hidden class="form-control" name="idobjet" id="idobjet">

					<div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
						<div class="col-6">
							<button type="button" id="validerSuprime" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
							OUI
						</div>
						<div class="col-6">
							<button type="button" id="annulerSuprime" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
							NON
						</div>

					</div>
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
		var filtre = document.getElementById("myDIV");
		filtre.style.display = "none";


		$(document).ready(function() {


			// $(".fa-mouse-pointer").click(function(evt) {
			// 	var data = evt.target.id

			// 	var result = data.split('-');
			// 	var ind = result[1]
			// 	if (ind != undefined) {
			// 		var idprestation = $("#id-" + ind).html()
			// 		var code = $("#code-" + ind).html()
			// 		var idcontrat = $("#idcontrat-" + ind).html()

			// 		document.cookie = "id=" + idprestation;
			// 		document.cookie = "code=" + code;
			// 		document.cookie = "idcontrat=" + idcontrat;
			// 		document.cookie = "action=traiter";
			// 		location.href = "fiche-prestation";
			// 	}
			// })


			// $(".fa-eye").click(function(evt) {
			// 	var data = evt.target.id

			// 	var result = data.split('-');
			// 	var ind = result[1]
			// 	if (ind != undefined) {
			// 		var idprestation = $("#id-" + ind).html()
			// 		var code = $("#code-" + ind).html()
			// 		var idcontrat = $("#idcontrat-" + ind).html()

			// 		document.cookie = "id=" + idprestation;
			// 		document.cookie = "code=" + code;
			// 		document.cookie = "idcontrat=" + idcontrat;
			// 		document.cookie = "action=traiter";
			// 		location.href = "detail-prestation";
			// 	}

			// })

			// Voir detail
			$(document).on('click', '.view', function() {
				const ind = this.id.split('-')[1];

				var idprestation = $("#id-" + ind).html()
				var code = $("#code-" + ind).html()
				var idcontrat = $("#idcontrat-" + ind).html()

				document.cookie = "id=" + idprestation;
				document.cookie = "code=" + code;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = "detail-prestation";
			});

			// Traiter
			$(document).on('click', '.traiter', function() {
				const ind = this.id.split('-')[1];

				var idprestation = $("#id-" + ind).html()
				var code = $("#code-" + ind).html()
				var idcontrat = $("#idcontrat-" + ind).html()

				document.cookie = "id=" + idprestation;
				document.cookie = "code=" + code;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";

				//alert(idprestation)
				location.href = "fiche-prestation";
			});

		})


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