<?php

session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("autoload.php");


$idusers = GetParameter::FromArray($_COOKIE, 'idusers');
if ($idusers == null) header("Location:deconnexion.php");
else {

	echo $sqlSelect = "
   SELECT 
    users.*, 
    COUNT(tblrdv.idrdv) AS nb_rdv,
    TRIM(tblvillebureau.libelleVilleBureau) AS ville_nom , 
	TRIM(tblvillebureau.localisation) AS localisation
FROM 
    users
LEFT JOIN 
    tblrdv ON tblrdv.gestionnaire = users.id
LEFT JOIN 
    tblvillebureau ON users.ville = tblvillebureau.idVilleBureau
WHERE 
    users.id = '" . trim($idusers) . "' 
GROUP BY 
    users.id, tblvillebureau.libelleVilleBureau
ORDER BY 
    nb_rdv DESC;
";

	$getUser = $fonction->_getSelectDatabases($sqlSelect);
	if ($getUser == null) {
		echo "<script>
		alert('Desole aucun resultat n\'a ete trouve, veuillez ressayer');
		window.history.back();
		</script>";
	} else {
		$user = $getUser[0];
	}

	$option_rdv = $fonction->getRetourneOptionRDV($user);
	if ($option_rdv != null) $effectueoptordv = count($option_rdv);
	else $effectueoptordv = 0;
}


?>

<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>DeskApp - Bootstrap Admin Dashboard HTML Template</title>

	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="vendors/images/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon-16x16.png">

	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
		rel="stylesheet">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/cropperjs/dist/cropper.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/style.css">

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-119386393-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());

		gtag('config', 'UA-119386393-1');
	</script>
</head>

<body>
	<div class="pre-loader">
		<div class="pre-loader-box">
			<div class="loader-logo"><img src="vendors/images/deskapp-logo.svg" alt=""></div>
			<div class='loader-progress' id="progress_div">
				<div class='bar' id='bar1'></div>
			</div>
			<div class='percent' id='percent1'>0%</div>
			<div class="loading-text">
				Loading...
			</div>
		</div>
	</div>

	<head>
		<?php include "include/entete.php"; ?>
	</head>


	<?php include "include/header.php"; ?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Fiche Utilisateur</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.html">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">Fiche Utilisateur</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">
							<div class="profile-photo">
								<img src="vendors/images/avatar-2.png" alt="" class="avatar-photo">
							</div>
							<h5 class="text-center h5 mb-0"><?= strtoupper($user->nom . " " . $user->prenom) ?></h5>
							<p class="text-center text-muted font-14">
								<?= $user->typeCompte == "gestionnaire" ? "Gestionnaire" : "admin RDV"; ?></p>
							<div class="profile-info">
								<h5 class="text-center h5 mb-0">Contact</h5>
								<p class="text-center text-muted font-14"> email : <span style="font-bold"><?= $user->email ? $user->email : $user->login; ?></span></p>
								<p class="text-center text-muted font-14"> telephone : <span style="font-bold"><?= $user->telephone ? $user->telephone : "--"; ?></span></p>
								<p class="text-center text-muted font-14"> adresse : <span style="font-bold"><?= $user->adresse ? $user->adresse : "--"; ?></span></p>
								
							</div>
							<div class="profile-social">
								<h5 class="mb-20 h5 text-blue">Information Reception Rendez-vous</h5>
								<?php

								$jourReception = "";
								$nbreParReception = 0;
								if ($_SESSION["typeCompte"] == "rdv") {

									if ($option_rdv != null) {
										foreach ($option_rdv as $optionJour) {

											$jourReception .= $optionJour->jour . " , ";
											$nbreParReception = $optionJour->nbmax;
								?>

										<?php
										}
										$jourReception = substr($jourReception, 0, -2);
										?>
										<div class="date">Villes : <span
										class="badge badge-info"><?= $user->ville_nom ? $user->ville_nom : "--"; ?></span> </div>
										<div class="date">Jour reception : <span
												class="badge badge-success"><?= $jourReception; ?></span> </div>
										<div class="date">Heure reception : <span style="color:black ; font-weight:bold">08:00 - 16:00</span> </div>
										<div class="date">Max par jour : <span
												style="color:black ; font-weight:bold"><?= $nbreParReception; ?></span>
										</div>
										<div class="date">Lieu reception : <span
												style="color:black ; font-weight:bold"><?= $user->localisation ? $user->localisation : "--"; ?></span>
										</div>
								<?php
									}
								}
								?>
							</div>
						</div>
					</div>
					<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="pd-20 card-box height-100-p">
							<h4 class="text-center p-2" style="color:#033f1f !important; font-weight:bold;"> Liste des rendez-vous transmis </h4>
							<div style="border-top: 4px solid #033f1f;width : 100%;text-align: center;"></div>
							<hr>


							<?php
							$retourStatut = $fonction->pourcentageRDV(" and gestionnaire = '" . $user->id . "'");
							if (isset($retourStatut) && $retourStatut != null) {

							?>

								<div class="row">
									<?php
									foreach ($retourStatut as $etat => $statut) {
										if ($statut["nb_ligne_element"] == 0) {
											continue;
										}

									?>
										<div class="col-xl-3 mb-30">
											<div class="card-box height-100-p widget-style1 text-white"
												style="background-color:<?= trim($statut["color"]) ?>; font-weight:bold; ">
												<div class="d-flex flex-wrap align-items-center">
													<div class="progress-data">

													</div>
													<div class="widget-data">
														<div class="h4 mb-0 text-white"><?= trim($statut["nb_ligne_element"]) ?></div>
														<div class="weight-600 font-14">RDV <?= trim(strtoupper($statut["libelle"])) ?>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php
									}
									?>
								</div>
							<?php
							}
							?>
							<hr>
							<div class="card-body" style="background-color: whitesmoke;">
								<?php
								$sqlSelect = "SELECT  tblrdv.*,   CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire, users.telephone as telgest , users.adresse as localisation , users.email as mailgest,     TRIM(tblvillebureau.libelleVilleBureau) AS villes   FROM     tblrdv LEFT JOIN      users ON tblrdv.gestionnaire = users.id LEFT JOIN   tblvillebureau ON tblrdv.`idTblBureau` = tblvillebureau.idVilleBureau WHERE  tblrdv.gestionnaire = '" . $user->id . "'  ORDER BY  tblrdv.idrdv DESC ";

								$liste_rdvs = $fonction->_getSelectDatabases($sqlSelect);
								if ($liste_rdvs != null) $effectue = count($liste_rdvs);
								else $effectue = 0;

								?>
								<table class="table hover  data-table-export nowrap"
									style="font-size:8pt;">
									<thead>
										<tr>
											<th class="table-plus datatable-nosort">#Ref</th>
											<th hidden>Id</th>
											<th>Date</th>
											<th>Nom & prénom(s)</th>
											<th hidden>Id contrat</th>
											<th>Motif</th>
											<th>Detail RDV</th>
											<th>Etat</th>
											<th class="table-plus datatable-nosort">Action</th>
										</tr>
									</thead>
									<tbody>
										<?php
										for ($i = 0; $i <= ($effectue - 1); $i++) {
											$rdv = $liste_rdvs[$i];
											if (isset($rdv->etat) && $rdv->etat !== null && in_array($rdv->etat, array_keys(Config::tablo_statut_rdv)))  $etat = $rdv->etat;
											else $etat = 1;
											$retourEtat = Config::tablo_statut_rdv[$etat];
										?>
											<tr>
												<td id="id-<?= $i ?>" hidden><?= $rdv->idrdv; ?></td>
												<td id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
												<td><?= $rdv->dateajou; ?></td>
												<td class="text-wrap"><?= $rdv->nomclient; ?></td>
												<td class="text-wrap" id="idcontrat-<?= $i ?>" hidden>
													<?php echo $rdv->police; ?></td>
												<td class="text-wrap"><?= $rdv->motifrdv; ?>
													<br>
													<small>ref contrat :
														<?php echo $rdv->police; ?></small>
												</td>
												<td class="text-wrap" id="daterdv-<?= $i ?>">
													<?php echo $rdv->daterdv; ?>
													<br>
													<small
														style="font-weight:bold; color:#F9B233!important;">Ville
														rdv : <?php echo $rdv->villes; ?></small>
												</td>

												<td>
													<span
														class="<?php echo $retourEtat["color_statut"]; ?>"><?php echo $retourEtat["libelle"] ?></span>
												</td>
												<td class="table-plus text-wrap">
													<label class="btn btn-secondary"
														style="background-color:#F9B233 ;"
														for="click-<?= $i ?>"><i class="fa fa-eye"
															id="click-<?= $i ?>"> Voir </i></label>
												</td>

											</tr>
										<?php
										}
										?>
									</tbody>
								</table>
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
			<script src="src/plugins/cropperjs/dist/cropper.js"></script>

			<!-- js -->
			<script src="vendors/scripts/core.js"></script>
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
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


			<script>
				window.addEventListener('DOMContentLoaded', function() {
					var image = document.getElementById('image');
					var cropBoxData;
					var canvasData;
					var cropper;

					$('#modal').on('shown.bs.modal', function() {
						cropper = new Cropper(image, {
							autoCropArea: 0.5,
							dragMode: 'move',
							aspectRatio: 3 / 3,
							restore: false,
							guides: false,
							center: false,
							highlight: false,
							cropBoxMovable: false,
							cropBoxResizable: false,
							toggleDragModeOnDblclick: false,
							ready: function() {
								cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
							}
						});
					}).on('hidden.bs.modal', function() {
						cropBoxData = cropper.getCropBoxData();
						canvasData = cropper.getCanvasData();
						cropper.destroy();
					});
				});

				$(".fa-eye").click(function(evt) {
					var data = evt.target.id

					var result = data.split('-');
					var ind = result[1]
					if (ind != undefined) {
						var idrdv = $("#id-" + ind).html()
						var idcontrat = $("#idcontrat-" + ind).html()

						alert(idrdv + " " + idcontrat);

						document.cookie = "idrdv=" + idrdv;
						document.cookie = "idcontrat=" + idcontrat;
						document.cookie = "action=traiter";
						location.href = "detail-rdv";
					}

				})
			</script>
</body>

</html>