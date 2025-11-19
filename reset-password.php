<?php

session_start();

if (!isset($_SESSION['id'])) {
	$_SESSION['id'] = "";
}

include("autoload.php");

?>
<!DOCTYPE html>
<html>

<head>
	<!-- Basic Page Info -->
	<meta charset="utf-8">
	<title>E-DEMANDES COMPTE</title>
	<!-- Site favicon -->
	<link rel="apple-touch-icon" sizes="180x180" href="vendors/images/favicon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon.png">
	<link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon.png">
	<!-- Mobile Specific Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<!-- Google Font -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
	<link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
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
	<div class="login-header box-shadow">
		<div class="login-header box-shadow">
			<div class="container-fluid d-flex justify-content-between align-items-center">
				<div class="brand-logo">
					<a href="">
						<img src="vendors/images/logo-icon.png" width="180" alt="">
					</a>
				</div>
				<div class="login-menu">
					<h5>
						<span id="dateheure"></span>
					</h5>
				</div>
			</div>
		</div>
	</div>
	<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
		<div class=" container">
			<div class="row align-items-center">
				<div class="col-md-6">
				</div>
				<div class="col-md-6">
					<div class="login-box bg-white box-shadow border-radius-10">


						<?php
						$id = GetParameter::FromArray($_SESSION, 'id');
						if ($id != NULL) {
							$plus = " AND id='" . trim($id) . "'";
							$retourUsers = $fonction->_GetUsers($plus);
							if ($retourUsers != NULL) {

								if (strtoupper($retourUsers->genre) == "F") $images = "images-users-2.jpg";
								else $images = "images-users-3.jpg";
							}
						?>
							<div class="login-title">
								<h2 class="text-center text-info">MODIFICATION DE MOT DE PASSE</h2>
							</div>
							<div id="modifier-mot-de-passe">
								<h6 class="mb-20"> <span style="color:#033f1f"><?php echo $retourUsers->userConnect;  ?> !!<br></span> Veuillez renseigner votre nouveau mot de passe , le confirmer et le soumettre</h6>

								<div class="input-group custom">
									<input type="password" class="form-control form-control-lg" id="n_mdp" name="n_mdp" placeholder="nouveau mot de passe">

									<span class="text-danger" id="notif_n_mdp"></span>
									<div class="input-group-append custom">
										<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
									</div>
								</div>
								<div class="input-group custom">
									<input type="password" class="form-control form-control-lg" id="n_mdp_2" name="n_mdp_2" placeholder="Confirmer le nouveau mot de passe">
									<div class="input-group-append custom">
										<span class="input-group-text"><i class="dw dw-padlock1"></i></span>
									</div>
								</div>
								<span class="text-info" id="notif_mdp_valide"></span>

								<div class="input-group custom">
									<button type="button" class="btn btn-info  bx bx-cloud-upload px-5" id="modifierPasse" name="modifierPasse">Modifier mon mot de passe</button>

								</div>
							</div>
						<?php
						} else {
						?>

							<div class="login-title">
								<h2 class="text-center text-info">MOT DE PASSE OUBLIE</h2>
							</div>
							<div id="mot-de-passe-oublie">
								<div class="input-group custom">
									<label style="color:red">Veuillez renseigner votre adresse email professionnel SVP !!</label>
									<input type="text" class="form-control form-control-lg" id="emailPro" name="emailPro" placeholder="votre adresse email professionnel SVP !! ">

									<input type="password" class="form-control form-control-lg" id="n_mdp" name="n_mdp" hidden>
									<input type="password" class="form-control form-control-lg" id="n_mdp_2" name="n_mdp_2" hidden>

								</div>
								<span class="text-info" id="notif_pass_oublie"></span>
								<div class="row align-items-center">
									<div class="col-5">
										<div class="input-group mb-0">
											<button type="button" class="btn btn-info  bx bx-cloud-upload px-5" id="passeOublie" name="passeOublie">Soumettre</button>
										</div>
									</div>
								</div>
							</div>

						<?php
						}
						?>

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
					<div class="card-body">
						<p id="msgEchec" style="font-weight:bold; font-size:20px; color:red"></p>
					</div>
				</div>
				<div class="modal-footer">
					<!--button type="submit" name="traitGen" id="traitGen" class="btn btn-success" style="background: #033f1f !important;">OK</button-->
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

	<script>
		let notification = ""
		let notification2 = ""
		let pass1 = ""
		let pass2 = ""

		var n_mdp = document.getElementById("n_mdp");
		var n_mdp_2 = document.getElementById("n_mdp_2");
		// When the user clicks on the password field, show the message box
		n_mdp.onfocus = function() {
			//console.log("viens d'entree")
		}

		// When the user clicks outside of the password field, hide the message box
		n_mdp.onblur = function() {
			console.log("viens de sortir")
			pass1 = document.getElementById("n_mdp").value;

			if (pass1 == "" || pass1.length < '5') {

				notification = "veuillez renseigner le mot de passe SVP !!"
				alert(notification);
				$("#notif_n_mdp").text(notification)
				document.getElementById("n_mdp").focus();

			} else {
				$("#notif_n_mdp").text("")

			}
		}

		n_mdp_2.onblur = function() {
			console.log("viens de sortir")
			pass2 = document.getElementById("n_mdp_2").value;

			if (pass2 == "" || pass2.length < '5') {

				notification = "veuillez renseigner le mot de passe SVP !!"
				alert(notification);
				$("#notif_n_mdp").text(notification)
				document.getElementById("n_mdp_2").focus();

			} else {
				if (pass1 != pass2) {
					alert("Desole les mots de passes renseignés ne sont pas conforment ! veuillez essayer a nouveau SVP !!");
					document.getElementById("n_mdp").focus();
				} else {
					alert("Les mots de passes sont conforment !")
					validate()
				}
			}
		}



		$("#modifierPasse").click(function(evt) {

			let idusers = "<?php echo $_SESSION['id']; ?>";
			var n_mdp = document.getElementById("n_mdp").value;
			var n_mdp_2 = document.getElementById("n_mdp_2").value;

			// $.ajax({
			// 	url: "config/routes.php",
			// 	data: {
			// 		idusers: idusers,
			// 		pass2: n_mdp_2,
			// 		pass1: n_mdp,
			// 		etat: "modifierPasse"
			// 	},
			// 	dataType: "json",
			// 	method: "post",
			// 	//async: false,
			// 	success: function(response, status) {
			// 		//console.log(response)

			// 		let a_afficher = ""
			// 		if (response != '-1') {

			// 			a_afficher = `
			// 			<div class="alert alert-success" role="alert">
			// 					<h2>Votre mot de passe  a bien été modifiée  !</h2> 
			// 					<div style="float:center">
			// 					 <a class="btn" style="color:#F9B233 !important;" href="index"> <i class='fa fa-arrow-left'> Retour</i> </a>
			// 						</div>
			// 			</div>`
			// 			$("#modifier-mot-de-passe").html(a_afficher)
			// 		} else {
			// 			a_afficher = `
			// 			<div class="alert alert-danger" role="alert">
			// 					<h2>"Désolé , une erreur est survenue sur lors de la modification de votre mot de passe !</h2><br> Veuillez reessayer plus tard 
			// 			</div>`
			// 			$("#msgEchec").html(a_afficher)
			// 			$('#notificationValidation').modal("show")
			// 		}



			// 		//

			// 	},
			// 	error: function(response, status, etat) {
			// 		console.log(etat, response)
			// 	}
			// })

		})



		$("#passeOublie").click(function(evt) {

			var emailPro = document.getElementById("emailPro").value;
			if (emailPro == "" || emailPro.length <= '0') {
				alert("Veuillez renseigner votre adresse email professionnel SVP !!");
				document.getElementById("emailPro").focus();
			} else {

				if (checkEmail(emailPro)) {

					let tabloEmail = emailPro.split('@');

					if (tabloEmail[1] == "yakoafricassur.com" || tabloEmail[1] == "laloyalevie.com") {
						//console.log(tabloEmail[0])

						$.ajax({
							url: "config/routes.php",
							data: {
								emailPro: emailPro,
								etat: "passeOublie"
							},
							dataType: "json",
							method: "post",
							//async: false,
							success: function(response, status) {
								//console.log(response)

								let a_afficher = ""
								if (response != '-1') {

									a_afficher = `
						<div class="alert alert-success" role="alert">
								<h2> ` + response + ` ! vos accès ont bien été reinitialisé <br>Veuillez verifier votre messagerie  !</h2> 
								<div style="float:center">
									<button class="btn btn-warning text-white" style="background:#F9B233;" onclick="retour()"><i class='fa fa-arrow-left'>Retour</i></button>
								</div>
						</div>`
									$("#mot-de-passe-oublie").html(a_afficher)
								} else {
									a_afficher = `
						<div class="alert alert-danger" role="alert">
								<h2>Désolé, cet e-mail ` + emailPro + ` n'est pas associé à un compte autorisé pour cette activité !</h2><br> Veuillez contacter le service support pour plus d'assistance. 
						</div>`
									$("#msgEchec").html(a_afficher)
									$('#notificationValidation').modal("show")
								}

							},
							error: function(response, status, etat) {
								console.log(etat, response)
							}
						})
					} else {
						alert("Veuillez renseigner votre adresse email professionnel SVP !!");
						document.getElementById("emailPro").focus();
						return false;
					}

				} else {
					alert("Veuillez renseigner votre adresse email professionnel SVP !!");
					document.getElementById("emailPro").focus();
					return false;
				}
			}
		})


		function checkEmail(email) {
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		}


		function validate() {
			var msg = "";
			let retour = false;
			var str = document.getElementById("n_mdp").value;
			if (str.match(/[0-9]/g) &&
				str.match(/[A-Z]/g) &&
				str.match(/[a-z]/g) &&
				str.match(/[^a-zA-Z\d]/g) &&
				str.length >= 10) {

				retour = true;
				msg = "<p style='color:green'>Mot de passe fort.</p>";

			} else {
				msg = "<p style='color:red'>Mot de passe faible.</p>";
			}

			document.getElementById("notif_mdp_valide").innerHTML = msg;
			return retour;
		}


		function pause(ms) {
			return new Promise(resolve => setTimeout(resolve, ms));
		}

		async function afficherDate() {
			while (true) {
				await pause(1000);
				var cejour = new Date();
				var options = {
					weekday: "long",
					year: "numeric",
					month: "long",
					day: "2-digit"
				};
				var date = cejour.toLocaleDateString("fr-FR", options);
				var heure = ("0" + cejour.getHours()).slice(-2) + ":" + ("0" + cejour.getMinutes()).slice(-2) + ":" + ("0" + cejour.getSeconds()).slice(-2);
				var dateheure = date + " " + heure;
				var dateheure = dateheure.replace(/(^\w{1})|(\s+\w{1})/g, lettre => lettre.toUpperCase());
				document.getElementById('dateheure').innerHTML = dateheure;
			}
		}
		afficherDate();
	</script>
</body>

</html>