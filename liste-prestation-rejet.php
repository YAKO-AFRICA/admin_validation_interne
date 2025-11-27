<?php
session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}


include("autoload.php");

$plus = "";
if (isset($_REQUEST['filtreliste'])) {
	$retourPlus = $fonction->getFiltreuse();
	$filtre = $retourPlus["filtre"];
	$libelle = $retourPlus["libelle"];

	if ($filtre != null) {
		list($ii, $pars1) = explode('AND', $filtre, 2);
		$plus = " WHERE $pars1 ";
	}
} else {
	$filtre = '';
}

$plus = " WHERE tbl_prestations.etape ='3' $filtre ";
//echo $sqlSelect =  "SELECT DISTINCT tbl_prestations.*, tbl_motifrejetprestations.libelle as libellemotif ,tbl_motifrejetprestations.code as codemotif   FROM tbl_prestations INNER JOIN tbl_motifrejetprestations ON tbl_prestations.codemotifrejet = tbl_motifrejetprestations.code $plus ORDER BY `tbl_prestations`.`created_at` DESC ";
//$sqlSelect =  "SELECT DISTINCT *  FROM tbl_prestations  $plus ORDER BY `tbl_prestations`.`created_at` DESC ";

//$liste_prestations = $fonction->_getSelectDatabases($sqlSelect);
$liste_prestations = $fonction->_getRetourneListePrestation("3", $filtre);
if ($liste_prestations != null) $effectue = count($liste_prestations);
else $effectue = 0;

//exit;
//$ttt = $fonction->pourcentageAllMotifRejetPrestation();
//print($ttt);

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
								<h4>Recapitulatif des prestations rejetées</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a
											href="intro"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page"> Recapitulatif des
										prestations rejetées </li>
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
						<h4 class="text-left" style="color:#033f1f; "> Liste des demandes des prestations rejetées (
							<span style="color:#F9B233;"><?= $effectue ?></span> )
						</h4>
					</div>

					<div class="col" style="text-align: right;">
						<input type="text" name="search" id="search" value="<?= $plus ?>" hidden />
						<button type="button" class="btn btn-info  bx bx-cloud-upload px-5" id="exportButton"
							name="exportButton">Exporter vers excel</button>
					</div>

					<div class="pb-20">
						<table class="table hover  data-table-export nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th hidden>Idprestation</th>
									<th>Code</th>
									<th>Date<br>demande</th>
									<th>Id contrat</th>
									<th>Nom & prénom(s)</th>
									<!-- <th>Téléphone</th> -->
									<th>Type prestation</th>
									<th>Montant souhaite</th>
									<th>Etat </th>
									<th>Détails<br>traitement</th>
									<th>Motif </th>
									<th class="datatable-nosort"></th>

								</tr>
							</thead>
							<tbody>
								<?php
								if ($liste_prestations != null) {

									$effectue = count($liste_prestations);
									for ($i = 0; $i <= ($effectue - 1); $i++) {

										$prestations = new tbl_prestations($liste_prestations[$i]);

										$ListeMotifRejet = $fonction->_GetListeMotifRejetPrestation($prestations->code, null, true);

								?>
										<tr>
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $prestations->id; ?></td>
											<td id="code-<?= $i ?>"><?php echo $prestations->code; ?></td>
											<td class="text-wrap">
												<?php echo $prestations->created_at; ?>
											</td>

											<td id="idcontrat-<?= $i ?>"><?php echo $prestations->idcontrat; ?></td>
											<td>
												<?php echo $prestations->souscripteur2; ?><br>
												<small>téléphone : <?php echo $prestations->cel; ?></small>
											</td>
											<!-- <td><?php echo $prestations->cel; ?></td> -->
											<td><?php echo $prestations->typeprestation; ?></td>
											<td><?php echo $prestations->montantSouhaite; ?> FCFA</td>
											<td class="text-wrap">
												<span
													class="<?php echo $prestations->color_statut; ?>"><?php echo $prestations->lib_statut; ?></span>
											</td>
											<td class="text-wrap">
												<p class="mb-0 text-dark" style="font-size: 0.9em;">
													<span style="font-weight:bold;">traité le : <?php echo $prestations->updated_at; ?></span><br>
													<span style="font-weight:bold;">traité par : </span><span class="badge badge-info" style="background:info; color:white ! important;"><?php echo $prestations->traiterpar; ?> </span>
												</p>
											</td>
											<td class="text-wrap">
												<details style="font: size 12px;"><?php echo $ListeMotifRejet; ?></details>
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

					<div class="card radius-12 w-100 " hidden id="infosRecherche">
						<div class="card-body text-left " style="background:gray" id="content-infosSS"></div>
						<table class="table table-stripped table-hover" id="resultatRecherche">
							<thead style="background-color: #033f1f !important;color:white">
								<tr>
									<th>#</th>
									<th>code</th>
									<th>date de demande</th>
									<th>nom</th>
									<th>prenom</th>
									<th>date naissance</th>
									<th>lieu naissance</th>
									<th>telephone</th>
									<th>email</th>
									<th>id contrat</th>
									<th>type prestation</th>
									<th>montant Souhaite</th>
									<th>moyen Paiement</th>
									<th>traiter le</th>
									<th>traiter par</th>
									<th>etape prestation</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="content-detailsRecher">
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
	</div>




	<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>


	<script>
		var filtre = document.getElementById("myDIV");
		filtre.style.display = "none";


		$(document).ready(function() {

			//alert(document.title);
			//console.log(document.title);

			//introPretations()
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


			var search = document.getElementById("search").value;


			if (search != "") {


				$.ajax({
					url: "config/routes.php",
					data: {
						search: search,
						etat: "exporterExcel"
					},
					dataType: "json",
					method: "post",

					success: function(response, status) {

						let plus = ""
						let lib_statut = ""
						//console.log(response)

						if (response !== "-1") {
							$.each(response, function(indx, element) {
								//console.log(element)

								if (element.etape == "1") lib_statut =
									"<?php echo Config::EN_ATTENTE ?>";
								else if (element.etape == "2") lib_statut =
									"<?php echo Config::VALIDER ?>";
								else lib_statut = "<?php echo Config::REJETE ?>";

								plus += `<tr>
								<td>${indx+1}</td>
								<td>${element.code}</td>
								<td>${element.created_at}</td>
								<td>${element.nom}</td>
								<td>${element.prenom}</td>
								<td>${element.datenaissance}</td>
								<td>${element.lieunaissance}</td>
								<td>${element.cel}</td>
								<td>${element.email}</td>
								<td>${element.idcontrat}</td>
								<td>${element.typeprestation}</td>
								<td>${element.montantSouhaite}</td>
								<td>${element.moyenPaiement}</td>
								<td>${element.traiterle}</td>
								<td>${element.traiterpar}</td>
								<td>${lib_statut}</td>
								</tr>`
							})

							$("#content-detailsRecher").html(plus);

						}

					},
					error: function(response, status, etat) {
						console.log(response, status, etat)
					}
				})


			}


		})

		$("#exportButton").click(function(evt) {

			var table = document.getElementById("resultatRecherche");
			// Convertir le tableau HTML en un "workbook" Excel
			var wb = XLSX.utils.table_to_book(table, {
				sheet: "Feuille1"
			});

			// Générer le fichier Excel et télécharger
			XLSX.writeFile(wb, "liste-prestation-traite.xlsx");
		})



		function myFunction() {
			var x = document.getElementById("myDIV");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
		}

		function introPretations() {
			let tablo = [];
			let tabloStat = [];
			let tabloVal = [];
			let barColors = [];

			let aTraiter = 0;


			$.ajax({
				url: "config/routes.php",
				data: {
					etat: "introRejet"
				},
				dataType: "json",
				method: "post",

				success: function(response, status) {

					let retourStatut = response['MotifRejet']

					console.log(retourStatut);

					if (retourStatut != null) {
						$.each(retourStatut, function(indx, element) {

							if (element.etat == "1") aTraiter = element.nb_ligne_element;
							tabloStat.push(element.keyword + " - (" + (element.nb_ligne_element) + " - " +
								element.pourcentage + " % )");
							tabloVal.push(element.nb_ligne_element);
							barColors.push(element.color);

							tablo += `<tr>
								<td style="font-size:14px"><i class="bx bxs-circle me-2"  ></i>${element.keyword}</td>
								<td style="font-size:14px"><span class="badge ${element.bagde} badge-pill">${element.nb_ligne_element}</span></td>
								<td style="font-size:14px"><span class="badge ${element.bagde} badge-pill">${element.pourcentage} %</span></td>
								
							</tr>`
						})

						templateDiagrammeBar(tabloStat, tabloVal, barColors,
							"Production par statut de traitement")
						$("#afficheuseStat").html(tablo);

						$("#a_traiter").text(aTraiter + ' demandes non traitées');
					}

				},
				error: function(response, status, etat) {
					//var a_afficher = "traitement enregistrer avec succes !!"
					// $("#a_afficher2").text(a_afficher)
					// $('#notification').modal("show")
				}
			})
		}

		function templateDiagrammeBar(arg1, arg2, arg3 = null, textlegend = "Ma production recouvrement") {
			var xValues = arg1;
			var yValues = arg2;
			var barColors = "";



			if (arg3 == null) barColors = ["red", "green", "blue", "orange", "brown", "gold"];
			barColors = arg3;
			//var barColors = arg3;

			//console.log(barColors)

			new Chart("myChart3", {
				type: "bar",
				data: {
					labels: xValues,
					datasets: [{
						backgroundColor: barColors,
						data: yValues
					}]
				},
				options: {
					legend: {
						display: false
					},
					title: {
						display: true,

						text: textlegend
					}
				}
			});
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