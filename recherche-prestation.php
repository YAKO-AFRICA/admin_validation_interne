<?php

session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}
include("autoload.php");


$plus = "";
$resultat = "";
$afficheuse = FALSE;

if (isset($_REQUEST['filtreliste'])) {

	$afficheuse = TRUE;
	$retourPlus = $fonction->getFiltreuse();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];

	if ($filtre != null) {
		list($ii, $pars1) = explode('AND', $filtre, 2);
		$plus = " WHERE $pars1 ";
	}
} else {
	$filtre = '';
	$libelle = '';
	$plus = " WHERE etape !='1'  ";
}


$liste_prestations = $fonction->_getRetournePrestation($plus);
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
								<h4>TRAITEMENT DES DEMANDES</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item " aria-current="page">Liste des demandes</li>
									<li class="breadcrumb-item active" aria-current="page">Traitement demande</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<div class="pd-20">
					<div style="float:left">
						<button class="btn btn-warning text-white" style="background:#F9B233;" onclick="retour()"><i class='fa fa-arrow-left'> Retour</i></button>
					</div>
				</div>
				<br>


				<?php
				if ($afficheuse) {



				?>
					<div class="card-box mb-30">
						<div class="pd-20">
							<h4 class="text-center" style="color:#033f1f; "> Recapitulatif des traitements des prestations (<span style="color:#F9B233;"><?= $effectue ?></span>) </h4>
							<hr>
							<h6 class="text-center" style="color:#033f1f; "> <?= $libelle ?> </h6>
						</div>

						<div class="pb-20">
							<table class="data-table table stripe hover nowrap">
								<thead>
									<tr>
										<th class="table-plus datatable-nosort">#Ref</th>
										<th hidden>Idprestation</th>
										<th>Code</th>
										<th>Date<br>demande</th>
										<th>Date<br>traitement</th>
										<th>Id contrat</th>
										<th>Nom & prénom(s)</th>
										<th>Téléphone</th>
										<th>Type prestation</th>
										<th>Montant souhaite</th>
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
											<tr>
												<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
												<td id="id-<?= $i ?>" hidden><?php echo $prestations->id; ?></td>
												<td id="code-<?= $i ?>"><?php echo $prestations->code; ?></td>
												<td>
													<?php echo $prestations->created_at; ?>
												</td>
												<td>
													<?php echo $prestations->updated_at; ?>
												</td>
												<td id="idcontrat-<?= $i ?>"><?php echo $prestations->idcontrat; ?></td>
												<td><?php echo $prestations->souscripteur; ?></td>
												<td><?php echo $prestations->cel; ?></td>
												<td><?php echo $prestations->typeprestation; ?></td>
												<td><?php echo $prestations->montantSouhaite; ?></td>
												<td>
													<span class="<?php echo $prestations->color_statut; ?>"><?php echo $prestations->lib_statut; ?></span>
												</td>


												<td class="table-plus">
													<label class="btn btn-secondary" style="background-color:#F9B233 ;" for="click-<?= $i ?>"><i class="fa  fa-eye" id="click-<?= $i ?>"> Détail prestation </i></label>
													<?php if ($prestations->etape == "1") { ?>
														<label class="btn btn-secondary" style="background-color: #033f1f ;" for="click-<?= $i ?>"><i class="fa  fa-mouse-pointer" id="click-<?= $i ?>"> Traiter la prestation </i></label>
													<?php  } ?>
												</td>

											</tr>
									<?php
										}
									} else {
										
										//echo '<script>alert("Desole aucun resultat n\'a ete trouve, veuillez ressayer");</script>';
									}

									?>
								</tbody>
							</table>
						</div>
					</div>

				<?php

				} else {
				?>
					<div class="card-box pd-10 height-100-p mb-15" id="myDIV">
						<div class="card-body mb-30">
							<div class="card-body " style="border:2px solid #F9B233;">
								<form method="POST">
									<div class="row">
										<div class="col-md-6">
											<div class="row">
												<div class="col-md-4">
													<h6 style="color: #033f1f !important;">Nom</h6>
													<p> <input type="text" name="nom" id="nom" class="form-control" /></p>
												</div>
												<div class="col-md-8">
													<h6 style="color: #033f1f !important;">Prenom(s)</h6>
													<p> <input type="text" name="prenoms" id="prenoms" class="form-control" /></p>
												</div>

												<div class="col-md-6">
													<h6 style="color: #033f1f !important;"> Id proposition / Id contrat </h6>
													<p><input type="number" name="IdProposition" id="IdProposition" class="form-control" /></p>
												</div>
												<div class="col-md-6 ">
													<h6 style="color: #033f1f !important;">Code demande prestation </h6>
													<input type="text" class="form-control" name="codePrestation" id="codePrestation" />
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-md-6 form-group">
													<h6 style="color: #033f1f !important;">Type demande prestation</h6>
													<?php echo $fonction->getSelectTypePrestationFiltre(); ?>
												</div>
												<div class="col-md-6 form-group">
													<h6 style="color: #033f1f !important;">Etape demande prestation</h6>
													<?php echo $fonction->getSelectTypeEtapePrestation(); ?>
												</div>
												<div class="col-md-4 form-group">
													<h6 style="color: #033f1f !important;">Migration NSIL</h6>
													<select name="migration" id="migration" class="form-control" data-msg="Objet" data-rule="required">
														<option value="">...</option>
														<option value="1">Oui</option>
														<option value="0">En attente</option>
													</select>
												</div>
												<div class="col-md-8 form-group">
													<h6 style="color: #033f1f !important;"> Date declaration NSIL </h6>
													<input type="date" class="form-control" name="DateNIL" id="DateNIL" />
												</div>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-6 form-group ">
											<h6 style="color: #033f1f !important;">Filtre date demande prestation </h6>
											<input type="date" class="form-control" name="DateDebutPrest" id="DateDebutPrest" placeholder="Date Debut" /></br>
											<input type="date" class="form-control" name="DateFinPrest" id="DateFinPrest" placeholder="Date Fin" />
										</div>
										<div class="col-md-6 form-group ">
											<h6 style="color: #033f1f !important;">Filtre date traitement prestation </h6>
											<input type="date" class="form-control" name="DateDebutTrait" id="DateDebutTrait" placeholder="Date Debut" /></br>
											<input type="date" class="form-control" name="DateFinTrait" id="DateFinTrait" placeholder="Date Fin" />
										</div>
									</div>

									<div class="modal-footer" id="footer">
										<button type="submit" name="filtreliste" id="filtreliste" class="btn btn-danger" style="background: #F9B233 !important;border-color: #F9B233 !important;">RECHERCHER</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php
				}
				?>
			</div>
		</div>

		<div class="footer-wrap pd-20 mb-20">
			<?php include "include/footer.php";    ?>
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
		function retour() {
			window.history.back();
		}

		$(".fa-eye").click(function(evt) {
			var data = evt.target.id

			var result = data.split('-');
			var ind = result[1]
			if (ind != undefined) {
				var idprestation = $("#id-" + ind).html()
				var code = $("#code-" + ind).html()
				var idcontrat = $("#idcontrat-" + ind).html()

				document.cookie = "id=" + idprestation;
				document.cookie = "code=" + code;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = "detail-prestation";
			}

		})


		$("#closeNotif").click(function() {
			$('#notification').modal('hide')
			window.history.back();
		})

		$("#closeEchec").click(function() {
			$('#echecNotification').modal('hide')
			location.reload();
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

<!--script>
	var oTable = $('#listeDemande').DataTable({
		order: [
			[0, 'desc']
		],
		buttons: [
			'copy', 'excel', 'pdf'
		]
	});
</script-->

</html>