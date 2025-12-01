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


// $plus = " AND YEAR(STR_TO_DATE(tblrdv.daterdv, '%d/%m/%Y')) = YEAR(CURDATE())";
// $sqlSelect = "SELECT  tblrdv.*,   CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.telephone as telgest , users.adresse as localisation , users.email as mailgest,     TRIM(tblvillebureau.libelleVilleBureau) AS villes   FROM     tblrdv LEFT JOIN      users ON tblrdv.gestionnaire = users.id LEFT JOIN   tblvillebureau ON tblrdv.`idTblBureau` = tblvillebureau.idVilleBureau WHERE tblrdv.etat IN ('2', '3') $plus ORDER BY  RAND() DESC ";

$liste_rdvs = $fonction->getSelectRDVAfficher("2");
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
								<h4>Liste des Rendez-vous Transmis</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page"> Liste des Rendez-vous Transmis </li>
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
						<h4 class="text-center" style="color:#033f1f; "> Liste des Rendez-vous Transmis (<span style="color:#F9B233;"><?= $effectue ?></span>) </h4>
					</div>

					<div class="pb-20">
						<table class="table hover  data-table-export nowrap" id="liste-rdv-transmis" style="width:100%; font-size:10px;">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th>Id</th>
									<th>Date</th>
									<th>Nom & prénom(s)</th>
									<th>Id contrat</th>
									<th>Motif</th>
									<th>Date RDV Effective</th>
									<th>lieu RDV Effective</th>
									<th>Agent Transformation</th>
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
										if ($rdv->villes != null) $villes = $rdv->villes;
										else $villes = "Non mentionné";
										if (isset($rdv->etat) && $rdv->etat !== null && in_array($rdv->etat, array_keys(Config::tablo_statut_rdv)))  $etat = $rdv->etat;
										else $etat = 1;
										$retourEtat = Config::tablo_statut_rdv[$etat];

								?>
										<tr>
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>"><?php echo $rdv->idrdv; ?></td>
											<td><?php echo $rdv->updatedAt; ?></td>
											<td class="text-wrap"><?php echo $rdv->nomclient; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Téléphone :<span
														style="font-weight:bold;"><?php echo $rdv->tel; ?></span>
												</p>
											</td>

											<td class="text-wrap" id="idcontrat-<?= $i ?>"><?php echo $rdv->police; ?></td>

											<td class="text-wrap"><?php echo $rdv->motifrdv; ?> </td>
											<td><?php echo date("d/m/Y", strtotime($rdv->daterdveff)) ?></td>
											<td class="text-wrap" style="font-weight:bold; color:#F9B233!important;"><?php echo $villes; ?></td>
											<td class="text-wrap" style="font-weight:bold; color:#033f1f!important;">
												<?php echo $rdv->nomgestionnaire ?? "Non mentionné"; ?>
											</td>
											<td>
												<span class="<?php echo $retourEtat["color_statut"]; ?>"><?php echo $retourEtat["libelle"] ?></span>
											</td>

											<td class="table-plus text-wrap">
												<button class="btn btn-warning btn-sm view" id="view-<?= $i ?>" style="background-color:#F9B233;color:white"><i class="fa fa-eye"></i> Détail</button>
												<button class="btn btn-success btn-sm traiter" id="traiter-<?= $i ?> " style="background-color:#033f1f; color:white"><i class="fa fa-mouse-pointer"></i> Traiter</button>

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

				alert("Traitement en cours");
				const index = this.id.split('-')[1];
				const idrdv = $("#id-" + index).html();
				const idcontrat = $("#idcontrat-" + index).html();
				document.cookie = "idrdv=" + idrdv;
				document.cookie = "idcontrat=" + idcontrat;
				document.cookie = "action=traiter";
				location.href = "fiche-rdv";
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