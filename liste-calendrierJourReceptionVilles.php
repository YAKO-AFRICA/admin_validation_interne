<?php
session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("autoload.php");

setlocale(LC_TIME, 'fr_FR.UTF-8'); // Active la langue française
// Initialisation de la date actuelle
$currentDate = date('Y-m-d');
$currentMonth = date('m');
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<title>Liste des Gestionnaires</title>
	<?php include "include/entete.php"; ?>

	<!-- ✅ Bootstrap CSS (nécessaire pour .modal) -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
								<h4>Jour de reception</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="accueil-operateur.php"><?= Config::lib_pageAccueil ?></a></li>
									<li class="breadcrumb-item active" aria-current="page"> Jour de reception</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<hr>

				<div class="card-box mb-30">
					<div class="card-body">
						<div class="calendar-container">
							<div class="calendar-header">
								<h2>Calendrier de réception des RDV	</h2>
							</div>
							<hr>
							<div class="nav-buttons">
								<button onclick="changeMonth('prev')">Mois précédent</button>
								<button onclick="changeMonth('next')">Mois suivant</button>
							</div>
							<div id="calendar"></div>
						</div>
					</div>
				</div>

				<hr>
			</div>

			<div class="footer-wrap pd-20 mb-20">
				<?php include "include/footer.php"; ?>
			</div>
		</div>
	</div>

	<!-- ✅ Modal de confirmation -->
	<div class="modal fade" id="notificationValidation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-body text-center" style="background-color: whitesmoke;">
					<div class="card-body" id="msgEchec"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" style="background: #033f1f;" data-dismiss="modal">OK</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
				</div>
			</div>
		</div>
	</div>

	<!-- ✅ JS includes -->

	<!-- jQuery (déjà dans ton code) -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<!-- ✅ Bootstrap JS avec Popper inclus -->
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

	<!-- FullCalendar + Moment -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
	<!-- Localisation française -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/locale/fr.js"></script>
	<!-- Ton script calendrier -->
	<script>
		$(document).ready(function() {
			$('#calendar').fullCalendar({
				locale: 'fr',
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				events: function(start, end, timezone, callback) {
					$.ajax({
						url: "config/routes.php",
						method: "post",
						data: {
							idvilles: "",
							etat: "getJourReception"
						},
						dataType: "json",
						success: function(response) {
							if (response.success) {
								const jourMappings = {
									1: 'Lundi',
									2: 'Mardi',
									3: 'Mercredi',
									4: 'Jeudi',
									5: 'Vendredi',
									6: 'Samedi',
									7: 'Dimanche'
								};

								const events = response.events.map(function(event) {
									const startDate = moment().day(event.jour);
									return {
										title: event.location,
										start: startDate.format('YYYY-MM-DD'),
										description: event.location,
										idvilles: event.idvilles,
										jour: jourMappings[event.jour],
										allDay: true
									};
								});
								callback(events);
							}
						},
						error: function(xhr, status, error) {
							console.error("Erreur lors du chargement des événements :", error);
						}
					});
				},
				eventClick: function(event) {
					checkDate(event.idvilles, event.start.format('YYYY-MM-DD'), event.title);
					//alert(`Vous avez cliqué sur : ${event.title} , réception ${event.jour} le ${event.start.format('YYYY-MM-DD')} de 08:00 à 16:00`);
				}
			});
		});

		function changeMonth(direction) {
			let currentMonth = $('#calendar').fullCalendar('getDate').month();
			let newMonth = (direction === 'prev') ? currentMonth - 1 : currentMonth + 1;
			$('#calendar').fullCalendar('gotoDate', moment().month(newMonth).startOf('month'));
		}

		function checkDate(idVille, dateRDV, location) {
			console.log("checkDate : " + dateRDV + " : " + idVille + " - " + location);


			$.ajax({
				url: "config/routes.php",
				method: "post",
				dataType: "json",
				data: {
					parms: 1,
					idVilleEff: idVille,
					daterdveff: dateRDV,
					daterdv: dateRDV,
					etat: "compteurRdv"
				},
				success: function(response) {
					let totalRDV = parseInt(response?.data?.totalrdv || 0);
					let messageHTML = "";

					if (totalRDV > 0) {
						messageHTML = `Il y a <span style='color: red; font-weight: bold;'>${totalRDV}</span> RDV(s) programmé(s) à cette date <span style='color:#033f1f; font-weight: bold;'>${response.daterdv}</span> pour la ville <span style='color:#033f1f; font-weight: bold;'>${location}</span>`;
					} else {
						messageHTML = `Pas de RDV programmé à cette date <span style='color:#033f1f; font-weight: bold;'>${response.daterdv}</span> pour la ville <span style='color:#033f1f; font-weight: bold;'>${location}</span>`;
					}

					$("#msgEchec").html(messageHTML);
					$('#notificationValidation').modal("show");
				},
				error: function(xhr, status, error) {
					console.error("Erreur AJAX :", status, error, xhr.responseText);
				}
			});
		}
	</script>
</body>

</html>