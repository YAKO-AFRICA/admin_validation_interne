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



switch ($_SESSION['typeCompte']) {
	case 'rdv':
		$plus = " AND typeCompte IN ('gestionnaire', 'rdv')";
		break;
	case 'prestation':
		$plus = " AND typeCompte IN ('prestation')";
		break;
	default:
		$plus = '';
		break;
}

$sqlSelect = "
   SELECT 
    users.*, 
    COUNT(tblrdv.idrdv) AS nb_rdv,
    TRIM(tblvillebureau.libelleVilleBureau) AS ville_nom
FROM 
    users
LEFT JOIN 
    tblrdv ON tblrdv.gestionnaire = users.id
LEFT JOIN 
    tblvillebureau ON users.ville = tblvillebureau.idVilleBureau
WHERE 
    users.etat = '1' $plus
GROUP BY 
    users.id, tblvillebureau.libelleVilleBureau
ORDER BY 
    nb_rdv DESC;
";

$liste_rdvs = $fonction->_getSelectDatabases($sqlSelect);
if ($liste_rdvs != null) $effectue = count($liste_rdvs);
else $effectue = 0;

//print_r($liste_rdvs);exit;

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
								<h4>Liste des Utilisateurs</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="accueil-operateur.php"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page"> Liste des Utilisateurs</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<div class="card-box mb-30">
					<div class="pd-20">
						<button class="btn btn-secondary p-2 m-2" name="addRDV" id="addRDV" style="background: #033f1f!important;float:right"><i class="icon-copy fa fa-user-plus" aria-hidden="true"> AJOUTER UTILISATEUR</i></button>
					</div>
					<div class="pd-20">
						<h4 class="text-center" style="color:#033f1f; "> Liste des Utilisateurs (<span style="color:#F9B233;"><?= $effectue ?></span>) </h4>
					</div>

					<div class="pb-20">
						<table class="table hover  data-table-export nowrap">
							<thead>
								<tr>
									<th class="table-plus datatable-nosort">#Ref</th>
									<th hidden>Id</th>
									<th>Nom & prénom(s)</th>
									<th>Contact</th>
									<th>Code agent</th>
									<th>lieu Reception</th>
									<th>type compte</th>
									<th>profil</th>

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

										if (isset($rdv->etat) && $rdv->etat !== null && in_array($rdv->etat, array_keys(Config::tablo_statut_rdv)))  $etat = $rdv->etat;
										else $etat = 1;
										$retourEtat = Config::tablo_statut_rdv[$etat];

										//Array ( [0] => stdClass Object ( [id] => 38 [nom] => AKPOUE [prenom] => GERMAIN [login] => germain.akoue@laloyalevie.com [password] => fcea920f7412b5da7be0cf42b8c93759 
										//[genre] => M [date] => [telephone] => 0546264979 [adresse] => [ville] => 13 [pays] => COTE D'IVOIRE [modifiele] => 
										//[image] => [typeCompte] => gestionnaire [email] => [codeagent] => [etat] => 1 [nb_rdv] => 2038 [ville_nom] => DALOA ) 

								?>
										<tr>
											<td class="table-plus" id="ref-<?= $i ?>"><?php echo $i + 1; ?></td>
											<td id="id-<?= $i ?>" hidden><?php echo $rdv->id; ?></td>
											<td><?php echo $rdv->nom . " " . $rdv->prenom; ?></td>
											<td class="text-wrap"><?= $rdv->email ? $rdv->email : $rdv->login; ?>
												<p class="mb-0 text-dark" style="font-size: 0.7em;">
													Téléphone :<span
														style="font-weight:bold;"><?php echo $rdv->telephone; ?></span>
												</p>

											</td>

											<td class="text-wrap" id="codeagent-<?= $i ?>"><?php echo $rdv->codeagent; ?></td>
											<td class="text-wrap" style="font-weight:bold; color:#F9B233!important;"><?php echo $rdv->ville_nom; ?></td>
											<td class="text-wrap"><?= $rdv->typeCompte == "gestionnaire" ? "Gestionnaire" : "admin RDV"; ?>
												<?php if ($rdv->typeCompte == "gestionnaire") {
													echo "<p class=\"mb-0 text-dark\" style=\"font-size: 0.7em; color:#F9B233;\">Compteur rdv : <span style=\"font-weight:bold;\">" . $rdv->nb_rdv . "</span></p>";
												} ?>
											</td>
											<td><?php echo $rdv->profil; ?></td>
											<td>
												<?php if (isset($rdv->etat) && $rdv->etat !== null && $rdv->etat == "1") {
													echo "<span class=\"badge badge-success\">Actif</span>";
												} else {
													echo "<span class=\"badge badge-danger\">Inactif</span>";
												}
												?>
											</td>
											<td class="table-plus text-wrap">
												<label class="btn btn-secondary" style="background-color:#F9B233 ;" for="click-<?= $i ?>"><i class="fa  fa-eye" id="click-<?= $i ?>"> Détail </i></label>
												<!-- <?php if ($rdv->etat == "1") { ?>
													<label class="btn btn-secondary" style="background-color:blue ;" for="click-<?= $i ?>"><i class="fa  fa-edit" id="click-<?= $i ?>"> modifier</i></label>
													<label class="btn btn-secondary" style="background-color:red ;" for="click-<?= $i ?>"><i class="fa  fa-trash" id="click-<?= $i ?>"> supprimer</i></label>

												<?php  } ?> -->
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

	<div class="modal fade" id="AjouterRDV" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 style="color: #033f1f !important;"><span id="titreModale" style="font-size:24px; font-weight:bold; color:#F9B233;"></span> </h5>
				</div>
				<div class="modal-body">
					<div class="col-12 col-lg-12 col-xl-12 d-flex">
						<div class="card">
							<div class="card-body radius-12 w-100">
								<div class="row" id="formOperateur">

									<div class="form-group col-sm-12 col-md-12">
										<h4 style="color:#033f1f; font-size:16px; font-weight:bold;"> Veuillez renseigner les informations de l'utilisateur a ajouter svp !!</h4>
									</div>
									<input type="text" class="form-control" name="agent_id" id="agent_id" hidden>
									<input type="text" class="form-control" name="action" id="action" hidden>
									<hr>
									<div class="form-group col-sm-12 col-md-4">
										<label for="nomRdv" style="color: #000000;">Nom <bold style="color: #F9B233;"> *</bold></label>
										<input type="text" id="nom" name="nom" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez le nom" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-8">
										<label for="nomRdv" style="color: #000000;">Prenom <bold style="color: #F9B233;"> *</bold></label>
										<input type="text" id="prenom" name="prenom" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez le prenom" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">Telephone <bold style="color: #F9B233;"> *</bold></label>
										<input type="number" id="telephone" name="telephone" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez le telephone" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">email <bold style="color: #F9B233;"> *</bold></label>
										<input type="email" id="email" name="email" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez le email" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">Type de compte <bold style="color: #F9B233;"> *</bold></label>
										<select id="typeCompte" name="typeCompte" class="form-control" required>
											<option value="" selected disabled>Veuillez selectionner</option>
											<option value="rdv">RDV</option>
											<option value="prestation">gestionnaire Prestation</option>
											<option value="sinistre">gestionnaire Sinistre</option>
											<option value="gestionnaire">gestionnaire RDV</option>
											<option value="compte-ynov">compte-ynov</option>
										</select>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="nomRdv" style="color: #000000;">profil <bold style="color: #F9B233;"> *</bold></label>
										<select id="typeCompte" name="typeCompte" class="form-control" required>
											<option value="" selected disabled>Veuillez selectionner</option>
											<option value="agent">agent</option>
											<option value="supervisseur">supervisseur</option>
											<option value="admin">administrateur</option>

										</select>
									</div>
									<div class="form-group col-sm-12 col-md-12" id="divCible"></div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="mobile" style="color: #000000;"> Statut traitement <bold style="color:   #F9B233;"> * </label>
										<?php
										//echo $fonction->getSelectStatutTicket();
										?>
									</div>

								</div>
								<div class="modal-footer" id="footer">
									<button type="submit" name="traitAll" id="traitAll" class="btn btn-warning" onclick="getTraitementAjoutOperateur()"><span id="titreAction" style="color:white"></span></button>
									<button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
								</div>
							</div>
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
		$(document).ready(function() {



			$("#AjouterRDV").on("change", "#typeCompte", function(evt) {

				if (evt.target.value == "prestation") {

					let notif = `
                		<label for="nomRdv" style="color: #000000;">cible <bold style="color: #F9B233;"> *</bold></label>
										<select id="ciblePrestation" name="ciblePrestation" class="form-control" required>
										<option value="" selected disabled>Veuillez selectionner</option>
											<option value="administratif">administratif</option>
											<option value="technique">technique</option>
											
										</select>`;

					$("#divCible").html(notif);

				} else {
					$("#divCible").html("");
				}
			})



			$(".fa-mouse-pointer").click(function(evt) {


				var data = evt.target.id

				var result = data.split('-');
				var ind = result[1]

				if (ind != undefined) {
					var idrdv = $("#id-" + ind).html()
					var codeagent = $("#codeagent-" + ind).html()

					//alert(idrdv + " " + codeagent);
					document.cookie = "idusers=" + idrdv;
					document.cookie = "codeagent=" + codeagent;
					document.cookie = "action=traiter";
					location.href = "fiche-users";
				}
			})



			$(".fa-edit").click(function(evt) {


				var data = evt.target.id

				var result = data.split('-');
				var ind = result[1]

				if (ind != undefined) {
					var idrdv = $("#id-" + ind).html()
					var codeagent = $("#codeagent-" + ind).html()

					//alert(idrdv + " " + codeagent);
				}
			})

			$(".fa-eye").click(function(evt) {
				var data = evt.target.id

				var result = data.split('-');
				var ind = result[1]
				if (ind != undefined) {

					var idrdv = $("#id-" + ind).html()
					var codeagent = $("#codeagent-" + ind).html()

					//alert(idrdv + " " + codeagent);
					document.cookie = "idusers=" + idrdv;
					document.cookie = "codeagent=" + codeagent;
					document.cookie = "action=traiter";
					location.href = "fiche-users";
				}

			})


			$("#addRDV").click(function(evt) {

				var action = "ajouterMotif"
				var agent_id = null


				$("#titreModale").text("Ajouter un utilisateur")
				$("#titreAction").text("Ajouter")

				$("#action").val(action)
				$("#agent_id").val(agent_id)
				$('#AjouterRDV').modal("show")

			})

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