<?php

session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("autoload.php");


$id = GetParameter::FromArray($_SESSION, 'id');
$plus = " AND id='" . trim($id) . "'";
$retourUsers = $fonction->_GetUsers($plus);
if ($retourUsers != NULL) {

	if (strtoupper($retourUsers->genre) == "F") $images = "images-users-2.jpg";
	else $images = "images-users-3.jpg";
} else header("Location:deconnexion.php");


$sqlQuery = "SELECT  u.*,  TRIM(CONCAT(u.nom, ' ', u.prenom)) AS gestionnairenom, u.adresse as localisation ,   v.libelleVilleBureau AS villes_reception
                FROM  users u  LEFT JOIN  tblvillebureau v ON v.idVilleBureau = u.ville   WHERE     u.etat = '1' AND u.id = '$id'  ORDER BY   id DESC ";
$resultat = $fonction->_getSelectDatabases($sqlQuery);
if ($resultat != NULL) {
	$retourUsers =  new users($resultat[0]);
}
?>

<!DOCTYPE html>
<html>

<head>
	<?php include "include/entete.php"; ?>
</head>

<body>

	<?php include "include/header.php"; ?>

	<div class="mobile-menu-overlay"></div>

	<div class="main-container">
		<div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>MON PROFIL</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item active" aria-current="page">Mon Profil</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
			</div>

			<div class="row clearfix">


				<div class="col-lg-6 col-md-8 col-sm-12 mb-30">
					<div class="card user-profile shadow-sm border-0 mb-4">
						<div class="card-body text-center py-4">
							<div class="profile-photo mb-3">
								<img src="vendors/images/avatar-2.png" alt="" class="avatar-photo">
							</div>

							<h5 class="card-title mb-1 text-uppercase">
								<?= strtoupper($retourUsers->nom . " " . $retourUsers->prenom) ?>
							</h5>

							<p class="text-muted mb-2">
								<i class="bi bi-person-badge"></i>
								<strong>Profil :</strong> <?= ucfirst($retourUsers->profil) ?>
							</p>

							<p class="text-muted mb-4">
								<i class="bi bi-gear"></i>
								<strong>Type de compte :</strong> <?= ucfirst($retourUsers->typeCompte) ?>
								<?= $retourUsers->cible ? " - " . ucfirst($retourUsers->cible) : "" ?>
							</p>

							<!-- ✅ Statut du compte -->
							<?php
							$actif = isset($retourUsers->etat) && $retourUsers->etat == 1;
							?>
							<p class="mb-4">
								<span class="badge <?= $actif ? 'bg-success' : 'bg-danger' ?> px-3 py-2 text-white">
									<i class="bi <?= $actif ? 'bi-check-circle' : 'bi-x-circle' ?> me-1"></i>
									<?= $actif ? '✅ Compte actif' : ' ❌ Compte désactivé' ?>
								</span>
							</p>
							<hr class="my-3">

							<h6 class="text-uppercase text-secondary mb-3">Contact</h6>

							<ul class="list-unstyled mb-0">
								<li class="mb-2">
									<i class="bi bi-envelope text-primary me-2"></i>
									<strong>Email :</strong>
									<?= $retourUsers->email ? htmlspecialchars($retourUsers->email) : htmlspecialchars($retourUsers->login) ?>
								</li>
								<li class="mb-2">
									<i class="bi bi-telephone text-success me-2"></i>
									<strong>Téléphone :</strong>
									<?= $retourUsers->telephone ? htmlspecialchars($retourUsers->telephone) : "--" ?>
								</li>
								<li>
									<i class="bi bi-geo-alt text-danger me-2"></i>
									<strong>Adresse :</strong>
									<?= $retourUsers->adresse ? htmlspecialchars($retourUsers->adresse) : "--" ?>
								</li>
							</ul>

							<!-- ======================= -->
							<!-- SECTION RECEPTION RDV -->
							<!-- ======================= -->
							<?php if ($_SESSION["typeCompte"] == "gestionnaire") : ?>
								<hr class="my-4">
								<h5 class="mb-3 h5 text-blue">
									<i class="bi bi-calendar-week me-1"></i> Informations Réception Rendez-vous
								</h5>

								<?php
								$jourReception = "";
								$nbreParReception = 0;

								$option_rdv = $fonction->getRetourJourReception($retourUsers->ville);
								if ($option_rdv != null) $effectueoptordv = count($option_rdv);
								else $effectueoptordv = 0;

								foreach ($option_rdv as $optionJour) {
									$jourReception .= $optionJour->jour . ", ";
									$nbreParReception = $optionJour->nbmax;
								}
								$jourReception = rtrim($jourReception, ", ");
								?>

								<div class="text-start d-inline-block">
									<p><strong>Villes :</strong>
										<span class="badge bg-info text-white"><?= $retourUsers->villes_reception ?: "--"; ?></span>
									</p>
									<p><strong>Jour(s) de réception :</strong>
										<span class="badge bg-success text-white"><?= $jourReception ?: "--"; ?></span>
									</p>
									<p><strong>Heure de réception :</strong>
										<span class="text-dark fw-bold">08:00 - 14:00</span>
									</p>
									<p><strong>Nombre max. par jour :</strong>
										<span class="text-dark fw-bold"><?= $nbreParReception ?: "--"; ?></span>
									</p>
									<p><strong>Lieu de réception :</strong>
										<span class="text-dark fw-bold"><?= $retourUsers->localisation ?: "--"; ?></span>
									</p>
								</div>
							<?php endif; ?>
						</div>
					</div>

				</div>
				<div class="col-lg-6 col-md-9 col-sm-12 mb-30">

					<div class="card-box height-100-p overflow-hidden">
						<div class="card-body p-2">
							<h2 class="text-center" style="color:#F9B233; font-size:26px; font-weight:bold;"> Veuillez renseigner vos informations svp !!</h2>
						</div>
						<hr>
						<div class="card-body p-2">
							<button type="button" id="modifierPasse" name="modifierPasse" class="btn btn-secondary btn-lg btn-block">Je modifie mon mot de passe</button>
							<button type="button" id="modifierInfos" name="modifierInfos" class="btn btn-success btn-lg btn-block">Je modifie mes informations personnelles</button>

						</div>
					</div>
				</div>

			</div>



			<div class="footer-wrap pd-20 mb-20">
				<?php include "include/footer.php";    ?>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modifierMesInformations" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-header" style="background-color: #033f1f !important;">
					<h5 style="color:white !important;">Modifier mes informations </h5>
				</div>
				<div class="modal-body">
					<div class="col-12 col-lg-12 col-xl-12 d-flex">
						<div class="card">
							<div class="card-body radius-12 w-100">
								<div class="row">
									<div class="card-body p-2">
										<h4 class="text-center" style="color:#F9B233; font-size:16px; font-weight:bold;"> Veuillez renseigner vos informations svp !!</h4>
									</div>
									<hr>
									<div class="form-group col-sm-12 col-md-12">
										<label for="nomRdv" style="color: #000000;">Nom <bold style="color: #F9B233;"> *</bold></label>
										<input type="text" id="nom" name="nom" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez votre nom" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-12">
										<label for="prenom" style="color: #000000;"> Prenom(s) <bold style="color: #F9B233;">*</bold></label>
										<input type="text" id="prenoms" name="prenoms" onkeyup="this.value=this.value.toUpperCase()" data-rule="required" required placeholder="Entrez vos Prénom(s)" value="" class="form-control">
										<div class="validation" id="validPrenom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-12">
										<label for="email" style="color: #000000;"> Email <bold style="color: #F9B233;"> * </label>
										<input type="text" id="email" name="email" placeholder="Entrez votre Email" value="" class="form-control">
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="mobile" style="color: #000000;"> Mobile 1 <bold style="color:   #F9B233;"> * </label>
										<input type="text" id="telephone" name="telephone" data-rule="required" required placeholder="Entrez votre numéro de telephone mobile" value="" class="form-control">
										<div class="validation" id="validmobile" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-6">
										<label for="mobile2" style="color: #000000;"> Mobile 2</label>
										<input type="text" id="mobile2" name="mobile2" data-rule="required" placeholder="Entrez votre numéro de telephone portable 2" class="form-control">
										<div class="validation" id="validtelMobile2" style="color:red"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="traitAll" id="traitAll" class="btn btn-warning" onclick="getModifierMesInfos()">Modifier mes informations</button>
					<button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modifierMonMotDePasse" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-header" style="background-color: #033f1f !important;">
					<h5 style="color:white !important;">Modifier mon mot de passe </h5>
				</div>
				<div class="modal-body">
					<div class="col-12 col-lg-12 col-xl-12 d-flex">
						<div class="card">
							<div class="card-body radius-12 w-100" id="ModuleMonMotDePasse">
								<div class="row">
									<div class="card-body p-2">
										<h4 class="text-center" style="color:#F9B233; font-size:16px; font-weight:bold;"> Veuillez renseigner vos informations svp !!</h4>
									</div>
									<hr>
									<input type="hidden" id="idusers" name="idusers" class="form-control" hidden>
									<div class="form-group col-sm-12 col-md-12">
										<label for="nomRdv" style="color: #000000;">Mot de passe actuel <bold style="color: #F9B233;"> *</bold></label>
										<input type="password" id="passe_actuel" name="passe_actuel" data-rule="required" required placeholder="Entrez votre mot de passe actuel" value="" class="form-control">
										<div class="validation" id="validNom" style="color:#F9B233"></div>
									</div>

									<div class="form-group col-sm-12 col-md-12">
										<label for="prenom" style="color: #000000;"> Nouveau mot de passe <bold style="color: #F9B233;">*</bold></label>
										<input type="password" id="new_passe" name="new_passe" data-rule="required" required placeholder="Entrez votre nouveau mot de passe" value="" class="form-control">
										<div class="validation" id="validPrenom" style="color:#F9B233"></div>
									</div>
									<div class="form-group col-sm-12 col-md-12">
										<label for="prenom" style="color: #000000;"> Confirmer votre nouveau mot de passe <bold style="color: #F9B233;">*</bold></label>
										<input type="password" id="confirmer_new_passe" name="confirmer_new_passe" data-rule="required" required placeholder="confirmer votre nouveau mot de passe" value="" class="form-control">
										<div class="validation" id="validPrenom" style="color:#F9B233"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="modifierMonMDP" id="modifierMonMDP" class="btn btn-warning" onclick="getModifierMonMDP()">Modifier mon mot de passe
						<span id="spinnerMDP" class="spinner-border spinner-border-sm"></span>
					</button>
					<button type="button" id="closeMonMDP" name="closeMonMDP" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade in" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-body text-center">
					<div class="card-body">
						<p id="msgEchec" style="font-weight:bold; font-size:20px; color:red"></p>
					</div>
				</div>
				<div class="modal-footer" id="closeEchec">
					<button type="submit" name="traitGen" id="traitGen" class="btn btn-success" style="background: #033f1f !important;">OK</button>
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
	<script src="src/plugins/cropperjs/dist/cropper.js"></script>
	<script>
		$("#closeEchec").click(function(evt) {
			$('#notificationValidation').modal('hide')
			location.reload();
		})

		$("#modifierPasse").click(function(evt) {

			let idusers = "<?php echo $_SESSION['id']; ?>";

			$.ajax({
				url: "config/routes.php",
				data: {
					idusers: idusers,
					etat: "checkUsers"
				},
				dataType: "json",
				method: "post",
				//async: false,
				success: function(response, status) {

					let a_afficher = ""
					if (response != '-1') {

						$("#passe_actuel").val(response.password)
						$("#idusers").val(response.id)
						$('#modifierMonMotDePasse').modal("show")

					} else {
						a_afficher = `
						<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors de la modification de vos informations !</h2><br> Veuillez reessayer plus tard 
						</div>`
						$("#msgEchec").html(a_afficher)
						$('#notificationValidation').modal("show")
					}

				},
				error: function(response, status, etat) {
					console.log(etat, response)
				}
			})

			//$('#modifierMonMotDePasse').modal("show")
		})

		$("#closeMonMDP").click(function(evt) {
			$('#modifierMonMotDePasse').modal("hide")
		})





		$("#modifierInfos").click(function(evt) {

			let idusers = "<?php echo $_SESSION['id']; ?>";

			$.ajax({
				url: "config/routes.php",
				data: {
					idusers: idusers,
					etat: "checkUsers"
				},
				dataType: "json",
				method: "post",
				//async: false,
				success: function(response, status) {

					let a_afficher = ""
					if (response != '-1') {

						$("#nom").val(response.nom)
						$("#prenoms").val(response.prenom)
						$("#telephone").val(response.telephone)
						$("#email").val(response.email)

						$('#modifierMesInformations').modal("show")

					} else {
						a_afficher = `
						<div class="alert alert-danger" role="alert">
								<h2>"Désolé , une erreur est survenue sur lors de la modification de vos informations !</h2><br> Veuillez reessayer plus tard 
						</div>`
						$("#msgEchec").html(a_afficher)
						$('#notificationValidation').modal("show")
					}

				},
				error: function(response, status, etat) {
					console.log(etat, response)
				}
			})

		})


		function getModifierMonMDP() {

			let passe_actuel = document.getElementById("passe_actuel");
			let new_passe = document.getElementById("new_passe");
			let confirmer_new_passe = document.getElementById("confirmer_new_passe");
			let idusers = document.getElementById("idusers").value
			// Réinitialise les messages
			document.querySelectorAll(".validation").forEach(e => e.innerHTML = "");

			// Vérification du mot de passe actuel
			if (passe_actuel.value.trim() === "") {
				passe_actuel.focus();
				passe_actuel.nextElementSibling.innerHTML = "Veuillez entrer votre mot de passe actuel.";
				document.getElementById("spinnerMDP").style.display = "none";
				return false;
			}

			// Vérification du nouveau mot de passe
			if (new_passe.value.trim() === "") {
				new_passe.focus();
				new_passe.nextElementSibling.innerHTML = "Veuillez entrer un nouveau mot de passe.";
				document.getElementById("spinnerMDP").style.display = "none";
				return false;
			}

			// Longueur minimale du nouveau mot de passe
			if (new_passe.value.length <= 5) {
				new_passe.focus();
				new_passe.nextElementSibling.innerHTML = "Le mot de passe doit contenir au moins 6 caractères.";
				document.getElementById("spinnerMDP").style.display = "none";
				return false;
			}

			// Nouveau mot de passe ne doit pas être le même que l'ancien
			if (new_passe.value === passe_actuel.value) {
				new_passe.focus();
				new_passe.nextElementSibling.innerHTML = "Le nouveau mot de passe ne peut pas être identique à l’actuel.";
				document.getElementById("spinnerMDP").style.display = "none";
				return false;
			}

			// Vérification de la confirmation
			if (confirmer_new_passe.value.trim() === "") {
				confirmer_new_passe.focus();
				confirmer_new_passe.nextElementSibling.innerHTML = "Veuillez confirmer le nouveau mot de passe.";
				document.getElementById("spinnerMDP").style.display = "none";
				return false;
			}

			// Vérifier si confirmation correspond
			if (new_passe.value !== confirmer_new_passe.value) {
				confirmer_new_passe.focus();
				confirmer_new_passe.nextElementSibling.innerHTML = "Les deux mots de passe ne correspondent pas.";
				document.getElementById("spinnerMDP").style.display = "none";
				return false;
			}

			// Si tout est bon, on peut envoyer au backend
			// Tu peux ajouter ton AJAX ici si besoin


			//alert("Mot de passe validé — vous pouvez maintenant procéder à la mise à jour. " + new_passe.value);

			$('#modifierMonMotDePasse').modal("hide")
			let monMDP = new_passe.value;

			$.ajax({
				url: "config/routes.php",
				data: {
					idusers: idusers,
					pass1: monMDP,
					pass2: monMDP,
					etat: "modifierPasse"
				},
				dataType: "json",
				method: "post",
				//async: false,
				success: function(response, status) {

					console.log(response)
					let a_afficher = ""
					if (response != '-1') {

						a_afficher = `<div class="alert alert-success" role="alert">
										<h2>Cher(e) utilisateur votre mot de passe ont bien a bien été modifiée  !</h2></div>`
					} else {
						a_afficher = `<div class="alert alert-danger" role="alert">
										<h2>"Désolé , une erreur est survenue sur lors de la modification de vos informations !</h2><br> Veuillez reessayer plus tard 
									</div>`
					}
					//$("#msgEchec").html(a_afficher)
					//$('#notificationValidation').modal("show")
					alert("Cher(e) utilisateur votre mot de passe ont bien a bien été modifiée  !")
					location.href = "index.php";

				},
				error: function(response, status, etat) {
					console.log(etat, response)
				}
			})


			return true;
		}


		function getModifierMesInfos() {

			let idusers = "<?php echo $_SESSION['id']; ?>";

			var nom = document.getElementById("nom").value;
			var prenoms = document.getElementById("prenoms").value;
			var telephone = document.getElementById("telephone").value;

			var email = document.getElementById("email").value;
			var mobile2 = document.getElementById("mobile2").value;

			if (nom == "") {

				notification = "veuillez renseigner votre nom SVP !!"
				alert("veuillez renseigner votre nom SVP !!");
				document.getElementById("nom").focus();

			} else {
				if (prenoms == "") {
					alert("veuillez renseigner vos prenoms SVP !!");
					document.getElementById("prenoms").focus();
					return false;
				} else {
					if (email == "") {
						alert("veuillez renseigner votre email SVP !!");
						document.getElementById("email").focus();
						return false;
					} else {
						if (checkEmail(email)) {
							alert("Veuillez renseigner votre adresse email  SVP !!");
							document.getElementById("email").focus();
							return false;
						} else {
							if (telephone == "" || telephone.length < '10') {
								alert("Veuillez renseigner votre numero de  telephone  SVP !!");
								document.getElementById("telephone").focus();
								return false;
							} else {

								//console.log(nom, prenoms, telephone, email, mobile2)

								$('#modifierMesInformations').modal("hide")

								$.ajax({
									url: "config/routes.php",
									data: {
										idusers: idusers,
										nom: nom,
										prenoms: prenoms,
										telephone: telephone,
										email: email,
										mobile2: mobile2,
										etat: "ModifierMesInfos"
									},
									dataType: "json",
									method: "post",
									//async: false,
									success: function(response, status) {


										let a_afficher = ""
										if (response != '-1') {

											a_afficher = `<div class="alert alert-success" role="alert">
														<h2>Cher(e) <span class="text-success">` + nom + " " + prenoms + `</span> vos informations ont bien a bien été modifiée  !</h2></div>`


										} else {
											a_afficher = `
												<div class="alert alert-danger" role="alert">
														<h2>"Désolé , une erreur est survenue sur lors de la modification de vos informations !</h2><br> Veuillez reessayer plus tard 
												</div>`

										}
										$("#msgEchec").html(a_afficher)
										$('#notificationValidation').modal("show")

									},
									error: function(response, status, etat) {
										console.log(etat, response)
									}
								})
							}
						}
					}

				}

			}


		}


		function checkEmail(email) {
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		}
	</script>
</body>

</html>