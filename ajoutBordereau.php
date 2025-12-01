<?php
session_start();

if (!isset($_SESSION['id'])) {
	header('Location: index.php');
	exit;
}

include("autoload.php");

$plus = "";
$libelle = "";
$afficheuse = false;
$error = "";
$message = "";
$preview = "";



$search = GetParameter::FromArray($_COOKIE, 'search');
$libelle = GetParameter::FromArray($_COOKIE, 'libelle');

if ($search) {
	$plus = $search;
}


$sqlSelect = "
    SELECT 
        tblrdv.*,
        CONCAT(users.nom, ' ', users.prenom) AS nomgestionnaire,
        users.telephone AS telgest,
        users.adresse AS localisation,
        users.email AS mailgest,
        TRIM(tblvillebureau.libelleVilleBureau) AS villes
    FROM tblrdv
    LEFT JOIN users ON tblrdv.gestionnaire = users.id
    LEFT JOIN tblvillebureau ON tblrdv.idTblBureau = tblvillebureau.idVilleBureau
    $plus
    ORDER BY tblrdv.idrdv DESC
";

//echo $sqlSelect; exit;
$liste_prestations = $fonction->_getSelectDatabases($sqlSelect);
$effectue = is_array($liste_prestations) ? count($liste_prestations) : 0;

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
								<h4>BORDEREAU RDV</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="intro">Accueil</a></li>
									<li class="breadcrumb-item" aria-current="page">BORDEREAU RDV</li>
									<li class="breadcrumb-item active" aria-current="page">AJOUTER BORDEREAU RDV</li>
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
				if ($effectue > 0) {



				?>


					<div class="card-box mb-30">
						<div class="pd-20 text-center">
							<h4 style="color:#033f1f;">Liste des prestations (<span style="color:#F9B233;"><?= $effectue ?></span>)</h4>
							<h6 style="color:#033f1f;"><?= $libelle ?></h6>
						</div>

						<div class="pd-20">
							<div class="card-body radius-12 w-100 pb-20">
								<h4 class="text-center p-2 font-weight-bold" style="color:#033f1f;">Détails du fichier à charger</h4>
								<hr>

								<!-- Informations sur le format attendu -->
								<div class="row pd-20">
									<div class="card-body radius-12 w-100 p-4" style="color:#033f1f;">
										<div class="row">
											<div class="col-md-6">
												<p>
													<span class="font-weight-bold">Format conventionnel :</span>
													<span style="font-size:16px; font-weight:bold; color:#F9B233">.csv</span>
													avec séparateur
													<span style="font-size:16px; font-weight:bold; color:#F9B233">;</span>
												</p>
												<p>
													<span class="font-weight-bold">Modèle fichier :</span>
													<button type="button" class="btn btn-warning" style="background-color:#F9B233;">
														<i class="dw dw-eye"></i> Voir fichier exemple
													</button>
												</p>
											</div>

											<div class="col-md-6">
												<p>
													<span class="font-weight-bold">Taille maximale du fichier :</span>
													<span style="font-size:16px; font-weight:bold; color:#F9B233">5MB</span>
												</p>
												<p>
													<span class="font-weight-bold">Nombre de colonnes attendu :</span>
													<span style="font-size:16px; font-weight:bold; color:#F9B233">9</span>
												</p>
											</div>
										</div>
									</div>
								</div>

								<!-- Formulaire de chargement -->
								<div class="card-body mb-4 p-3" style="border:2px dashed #F9B233; background-color:#fefbf4;">
									<label for="upload" class="font-weight-bold mb-2">Sélectionnez un fichier :</label>
									<input type="file" name="upload" id="upload" accept=".xlsx, .csv" class="form-control-file form-control height-auto" required>
								</div>

								<!-- Aperçu et bouton d'import -->
								<div class="card-body w-100" style="font-size:12px">
									<!-- Bouton d'import -->
									<button id="importBtn" style="display: none;" class="btn btn-success mb-3">
										<i class="fa fa-cloud-upload" aria-hidden="true"></i> Charger le fichier
									</button>

									<!-- Barre de progression -->
									<div id="progressContainer" style="display: none; margin-bottom: 10px;">
										<progress id="progressBar" max="100" value="0" style="width: 100%;"></progress>
										<span id="progressText" class="d-block mt-2">Chargement...</span>
									</div>
									<div id="spinner" style="display: none; margin-top: 10px;">
										<div class="spinner-border" style="color: #076633;" role="status">
											<span class="visually-hidden"></span>
										</div>
									</div>

									<!-- Tableau de prévisualisation -->
									<div class="table-responsive">
										<table class="table table-bordered table-striped nowrap" id="previewTable" style="width:100%; font-size:10px;">
											<!-- Le contenu sera injecté dynamiquement -->
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>


				<?php

				} else {
				?>
					<div class="card-box pd-10 height-100-p mb-15" id="myDIV">
						<div class="card-body mb-30">
							<div class="card-body " style="border:2px solid #F9B233;">

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


	<div class="modal fade" id="modaleAfficheDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
		aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
			<div class="modal-content ">
				<div class="modal-body text-center">
					<div class="card-body" id="iframeAfficheDocument">

					</div>

				</div>
				<div class="modal-footer">
					<div>

						<button class="btn btn-success" onclick="window.open('documents/template-document.csv', '_blank')">
							TÉLÉCHARGER DOCUMENT
						</button>
						<button type="button" id="closeEchec" class="btn btn-secondary"
							data-dismiss="modal">FERMER</button>
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>


	<script>
		function retour() {
			window.history.back();
		}

		let excelData = []; // Pour stocker les données Excel

		// document.getElementById('upload').addEventListener('change', function(e) {
		// 	const file = e.target.files[0];

		// 	const reader = new FileReader();
		// 	reader.onload = function(e) {
		// 		const data = new Uint8Array(e.target.result);
		// 		const workbook = XLSX.read(data, {
		// 			type: 'array'
		// 		});

		// 		const firstSheetName = workbook.SheetNames[0];
		// 		const worksheet = workbook.Sheets[firstSheetName];

		// 		const json = XLSX.utils.sheet_to_json(worksheet, {
		// 			header: 1
		// 		});

		// 		// ✅ Fonction pour convertir les dates Excel en format AAAA-MM-JJ
		// 		function excelDateToJSDate(serial) {
		// 			if (!serial || isNaN(serial)) return '';
		// 			const utc_days = Math.floor(serial - 25569);
		// 			const utc_value = utc_days * 86400;
		// 			const date_info = new Date(utc_value * 1000);
		// 			return date_info.toISOString().split('T')[0];
		// 		}

		// 		// ✅ Colonnes qui contiennent des dates (exactement comme les noms de colonnes dans le fichier)
		// 		const dateColumns = ['Date d\'effet', 'Date d\'échéance'];

		// 		const headers = json[0];
		// 		for (let i = 1; i < json.length; i++) {
		// 			for (let j = 0; j < json[i].length; j++) {
		// 				const headerName = headers[j]?.trim();
		// 				if (dateColumns.includes(headerName)) {
		// 					json[i][j] = excelDateToJSDate(json[i][j]);
		// 				}
		// 			}
		// 		}

		// 		excelData = json;
		// 		console.log(excelData);

		// 		if (json.length === 0) return;

		// 		// Construction de THEAD
		// 		let thead = '<thead><tr>';
		// 		json[0].forEach(cell => {
		// 			thead += `<th class="text-wrap">${cell ?? ''}</th>`;
		// 		});
		// 		thead += '</tr></thead>';

		// 		// Construction de TBODY
		// 		let tbody = '<tbody>';
		// 		for (let i = 1; i < json.length; i++) {
		// 			tbody += '<tr>';
		// 			json[i].forEach(cell => {
		// 				tbody += `<td class="text-wrap">${cell ?? ''}</td>`;
		// 			});
		// 			tbody += '</tr>';
		// 		}
		// 		tbody += '</tbody>';

		// 		// Injecter dans le tableau HTML
		// 		document.getElementById('previewTable').innerHTML = thead + tbody;
		// 		document.getElementById('importBtn').style.display = 'inline-block';
		// 	};

		// 	reader.readAsArrayBuffer(file);
		// });

		document.getElementById('upload').addEventListener('change', function(e) {
			const file = e.target.files[0];

			const reader = new FileReader();
			reader.onload = function(e) {
				const data = new Uint8Array(e.target.result);
				const workbook = XLSX.read(data, {
					type: 'array'
				});

				const firstSheetName = workbook.SheetNames[0];
				const worksheet = workbook.Sheets[firstSheetName];

				const json = XLSX.utils.sheet_to_json(worksheet, {
					header: 1
				});

				// ✅ Fonction pour convertir une date Excel en 'YYYY-MM-DD'
				function excelDateToJSDate(serial) {
					if (!serial || isNaN(serial)) return null;
					const utc_days = Math.floor(serial - 25569);
					const utc_value = utc_days * 86400;
					const date_info = new Date(utc_value * 1000);
					return date_info.toISOString().split('T')[0];
				}

				// ✅ Colonnes de type date (les entêtes doivent correspondre exactement)
				const dateColumns = ['Date d\'effet', 'Date d\'échéance'];

				const headers = json[0];
				for (let i = 1; i < json.length; i++) {
					for (let j = 0; j < json[i].length; j++) {
						const headerName = headers[j]?.trim();
						const cellValue = json[i][j];

						// 🔁 Conversion selon le type de colonne
						if (dateColumns.includes(headerName)) {
							json[i][j] = excelDateToJSDate(cellValue);
						} else if (cellValue === '' || typeof cellValue === 'undefined') {
							json[i][j] = null;
						}
					}
				}

				excelData = json;
				console.log(excelData);

				if (json.length === 0) return;

				// Construction de THEAD
				let thead = '<thead><tr>';
				headers.forEach(cell => {
					thead += `<th class="text-wrap">${cell ?? ''}</th>`;
				});
				thead += '</tr></thead>';

				// Construction de TBODY
				let tbody = '<tbody>';
				for (let i = 1; i < json.length; i++) {
					tbody += '<tr>';
					json[i].forEach(cell => {
						const displayValue = (cell === null) ? '' : cell;
						tbody += `<td class="text-wrap">${displayValue}</td>`;
					});
					tbody += '</tr>';
				}
				tbody += '</tbody>';

				// Injecter dans le tableau HTML
				document.getElementById('previewTable').innerHTML = thead + tbody;
				document.getElementById('importBtn').style.display = 'inline-block';
			};

			reader.readAsArrayBuffer(file);
		});


		// document.getElementById('upload').addEventListener('change', function(e) {
		// 	const file = e.target.files[0];

		// 	const reader = new FileReader();
		// 	reader.onload = function(e) {
		// 		const data = new Uint8Array(e.target.result);
		// 		const workbook = XLSX.read(data, {
		// 			type: 'array'
		// 		});

		// 		const firstSheetName = workbook.SheetNames[0];
		// 		const worksheet = workbook.Sheets[firstSheetName];

		// 		const json = XLSX.utils.sheet_to_json(worksheet, {
		// 			header: 1
		// 		});



		// 		excelData = json;
		// 		console.log(excelData);

		// 		if (json.length === 0) return;

		// 		let thead = '<thead><tr>';
		// 		json[0].forEach(cell => {
		// 			thead += `<th class="text-wrap">${cell ?? ''}</th>`;
		// 		});
		// 		thead += '</tr></thead>';

		// 		let tbody = '<tbody>';
		// 		for (let i = 1; i < json.length; i++) {
		// 			tbody += '<tr>';
		// 			json[i].forEach(cell => {
		// 				tbody += `<td class="text-wrap">${cell ?? ''}</td>`;
		// 			});
		// 			tbody += '</tr>';
		// 		}
		// 		tbody += '</tbody>';

		// 		// Injecter dans le tableau
		// 		document.getElementById('previewTable').innerHTML = thead + tbody;
		// 		// Afficher le bouton d'import
		// 		document.getElementById('importBtn').style.display = 'inline-block';
		// 	};

		// 	reader.readAsArrayBuffer(file);
		// });


		document.getElementById('importBtn').addEventListener('click', function() {
			if (excelData.length < 2) {
				alert("Aucune donnée à importer !");
				return;
			}

			// Transformer en objets (clé: en-tête)
			const headers = excelData[0];
			const rows = [];

			for (let i = 1; i < excelData.length; i++) {
				const row = {};
				// headers.forEach((header, index) => {
				// 	row[i] = excelData[i][index] ?? '';
				// });
				rows.push(excelData[i]);
				//console.log(excelData[i]);
			}

			console.log(rows);

			// Afficher la barre de progression
			document.getElementById('progressContainer').style.display = 'block';
			document.getElementById('progressBar').value = 0;
			document.getElementById('progressText').textContent = 'Envoi en cours...';
			const spinner = document.getElementById("spinner");
			spinner.style.display = "block"; // Afficher le spinner

			// Envoi AJAX à PHP
			//console.log(rows);
			const dataATraiter = JSON.stringify(rows);
			console.log(dataATraiter);

			$.ajax({
				url: "config/routes.php",
				data: {
					params: dataATraiter,
					etat: "importBordereau"
				},
				dataType: "json",
				method: "post",
				success: function(response, status) {
					console.log(response);
					if (response.error != false) {
						console.log(response);

						spinner.style.display = "none"; // Masquer le spinner
						document.getElementById('progressContainer').style.display = 'none';

						let message = " le document " + response.reference + " a bien ete importe ! ";
						alert(message);
						return
					} else {
						spinner.style.display = "none"; // Masquer le spinner
						document.getElementById('progressContainer').style.display = 'none';
						let message = " desole le document " + response.reference + " n'a pas ete importe ! ";
						alert(message);
					}

				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});

		});

		// document.getElementById('upload').addEventListener('change', function(e) {
		// 	const file = e.target.files[0];
		// 	const reader = new FileReader();

		// 	reader.onload = function(e) {
		// 		const data = new Uint8Array(e.target.result);
		// 		const workbook = XLSX.read(data, {
		// 			type: 'array'
		// 		});

		// 		const sheetName = workbook.SheetNames[0];
		// 		const worksheet = workbook.Sheets[sheetName];
		// 		const jsonData = XLSX.utils.sheet_to_json(worksheet); // [{col1: val1, col2: val2}, ...]

		// 		//console.log(jsonData);



		// 		


		// 	};

		// 	reader.readAsArrayBuffer(file);
		// });




		$(".bx-show").click(function(evt) {
			var path_document = "exemple_RDV.xlsx";

			if (path_document !== "" && path_document.length > 0) {
				// Charger le contenu du fichier CSV
				fetch(path_document)
					.then(response => response.text())
					.then(csvText => {
						// Afficher le texte brut dans le modal
						let html = `<pre style="text-align:left;">${csvText}</pre>`;
						$("#lienAfficheDocument").val(path_document);
						$("#iframeAfficheDocument").html(html);
						$('#modaleAfficheDocument').modal("show");
					})
					.catch(error => {
						alert("Erreur lors du chargement du fichier : " + error);
					});
			} else {
				alert("Aucun document trouvé");
			}
		});

		// Quand la ville change
		$('#villesRDV').change(function() {

			if ($(this).val() === "null") return;

			const [idvillesRDV, villesRDV] = $(this).val().split(";");

			console.log("Nouvelle ville RDV Effective sélectionnée :", villesRDV + " (" + idvillesRDV + ")  ");
			getListeSelectAgentTransformations(idvillesRDV, villesRDV);

		});




		$(".fa-eye-slash").click(function(evt) {
			const [_, index] = evt.target.id.split('-');
			if (index !== undefined) {
				const idrdv = $("#id-" + index).text();
				const idcontrat = $("#idcontrat-" + index).text();

				document.cookie = `idrdv=${idrdv}`;
				document.cookie = `idcontrat=${idcontrat}`;
				document.cookie = "action=traiter";
				location.href = "detail-rdv";
			}
		});


		function excelDateToJSDate22(serial) {
			if (!serial || isNaN(serial)) return '';
			const utc_days = Math.floor(serial - 25569);
			const utc_value = utc_days * 86400; // seconds
			const date_info = new Date(utc_value * 1000);
			return date_info.toISOString().split('T')[0]; // format: YYYY-MM-DD
		}



		function getListeSelectAgentTransformations(idVilleEff, villesRDV) {

			console.log("selction de gestionnaire de transformation", idVilleEff, villesRDV)
			$.ajax({
				url: "config/routes.php",
				data: {
					idVilleEff: idVilleEff,
					etat: "afficherGestionnaire"
				},
				dataType: "json",
				method: "post",
				success: function(response, status) {
					let html = `<option value="">[Les agents de Transformations de ${villesRDV}]</option>`;

					$.each(response, function(indx, data) {
						let agent = data.gestionnairenom;
						html += `<option value="${data.id}|${agent}|${idVilleEff}|${villesRDV}" id="ob-${indx}">${agent}</option>`;
					});

					$("#ListeGest").html(html);
					//verifierActivationBouton(); // Vérifie après chargement
				},
				error: function(response, status, etat) {
					console.log(response, status, etat);
				}
			});
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