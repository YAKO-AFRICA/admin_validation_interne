<?php
// Initialisation de la date actuelle
$currentDate = date('Y-m-d'); // Date au format YYYY-MM-DD
$currentMonth = date('m'); // Mois en format numérique
$currentYear = date('Y'); // Année actuelle
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier avec annotations dynamiques</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .calendar-container {
            max-width: 900px;
            margin: 0 auto;
            padding-top: 20px;
        }

        .calendar-header {
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            padding: 10px 50px;
        }

        .nav-buttons button {
            padding: 10px;
            background-color: #F9B233;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .form-container {
            margin-top: 20px;
            padding: 10px;
            background-color: #f4f4f4;
            border-radius: 8px;
        }

        .form-container input,
        .form-container textarea {
            margin-bottom: 10px;
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
    
</head>

<body>

    <div class="calendar-container">
        <div class="calendar-header">
            <h2>Calendrier de <?php echo date('F Y', strtotime($currentYear . '-' . $currentMonth . '-01')); ?></h2>
        </div>

        <div class="nav-buttons">
            <button onclick="changeMonth('prev')">Mois précédent</button>
            <button onclick="changeMonth('next')">Mois suivant</button>
        </div>

        <div id="calendar"></div>

        <!-- Formulaire pour ajouter des événements -->
        <!-- <div class="form-container">
            <h3>Ajouter une annotation :</h3>
            <form id="eventForm">
                <input type="text" id="eventTitle" placeholder="Titre de l'événement" required>
                <textarea id="eventDescription" rows="3" placeholder="Description de l'événement"></textarea>
                <input type="date" id="eventDate" value="<?= $currentDate ?>" required>
                <button type="submit">Ajouter</button>
            </form>
        </div> -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>

    <script>
        // Initialiser le calendrier avec des événements dynamiques
        $(document).ready(function() {
            var calendar = $('#calendar').fullCalendar({
                locale: 'fr', // Définir la langue sur 'fr' pour le français
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: function(start, end, timezone, callback) {
                    $.ajax({
                        url: "config/routes.php",
                        data: {
                            idvilles: "",
                            etat: "getJourReception"
                        },
                        dataType: "json",
                        method: "post",
                        success: function(response) {
                            if (response.success) {
                                // Mappage des jours de la semaine pour FullCalendar
                                var jourMappings = {
                                    1: 'Lundi', // Lundi
                                    2: 'Mardi', // Mardi
                                    3: 'Mercredi', // Mercredi
                                    4: 'Jeudi', // Jeudi
                                    5: 'Vendredi', // Vendredi
                                    6: 'Samedi', // Samedi
                                    7: 'Dimanche' // Dimanche
                                };

                                // Mappage des événements en utilisant les données reçues
                                var events = response.events.map(function(event) {
                                    // Calculer la date à partir du jour de la semaine
                                    var startDate = moment().day(event.jour); // Le jour de la semaine
                                    return {
                                        title: event.location, // Utilisez la localisation comme titre
                                        start: startDate.format('YYYY-MM-DD'), // Date calculée
                                        description: event.location, // Utiliser la même info pour la description
                                        location: event.location,
                                        jour: jourMappings[event.jour], // Le nom du jour (ex: Lundi)
                                        allDay: true
                                    };
                                });

                                // Utiliser la fonction callback pour afficher les événements sur le calendrier
                                callback(events);
                            }
                        },
                        error: function(response, status, etat) {
                            console.log(etat, response);
                        }
                    });
                },
                eventClick: function(event) {
                    alert('Vous avez cliqué sur : ' + event.title);
                }
            });

            // Fonction pour changer de mois
            function changeMonth(direction) {
                var currentMonth = $('#calendar').fullCalendar('getDate').month();
                var newMonth;

                if (direction === 'prev') {
                    newMonth = currentMonth - 1;
                } else if (direction === 'next') {
                    newMonth = currentMonth + 1;
                }

                $('#calendar').fullCalendar('gotoDate', moment().month(newMonth).startOf('month'));
            }

            // Ajouter un événement depuis le formulaire
            $('#eventForm').submit(function(e) {
                e.preventDefault();

                var title = $('#eventTitle').val();
                var description = $('#eventDescription').val();
                var date = $('#eventDate').val();

                if (title && date) {
                    // Ajouter l'événement au calendrier
                    $('#calendar').fullCalendar('renderEvent', {
                        title: title,
                        start: date,
                        description: description
                    }, true); // true pour que l'événement soit ajouté immédiatement

                    // Réinitialiser le formulaire
                    $('#eventForm')[0].reset();
                } else {
                    alert('Veuillez remplir tous les champs obligatoires.');
                }
            });
        });
    </script>

</body>

</html>