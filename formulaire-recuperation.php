<?php

include("autoload.php");
$id = GetParameter::FromArray($_REQUEST, 'i');
$action = GetParameter::FromArray($_REQUEST, 'p');


$retour_recup_users = null;
if ($id != null && $action != null) {


    if ($action == "mp") {
        $created_at = date('Y-m-d H:i:s');
        $expire = strtotime($created_at . ' +1 day');
        $expireDate = date('Y-m-d H:i:s', $expire);

        $users = $fonction->_GetUsers(" AND id = '$id'  ");
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8">
    <title>E-PRESTATION ADMIN</title>
    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="vendors/images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="vendors/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="vendors/images/favicon.png">
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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

<body class="login-page">
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
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container" style="background-color: #033f1f;">

            <?php if (!empty($users)) : ?>
                <div class="row align-items-center">

                    <div class="col-md-5 col-lg-7 text-center">
                        <img src="vendors/images/images-3.jpg" class="img-fluid" alt="Illustration">
                    </div>

                    <div class="col-md-7 col-lg-5">
                        <div class="login-box bg-white box-shadow border-radius-10 p-4">
                            <div class="login-title mb-4">
                                <h2 class="text-center text-success fw-bold">Réinitialisation de votre mot de passe</h2>
                            </div>

                            <p class="text-center">
                                Agent : <strong><?= htmlspecialchars($users->userConnect); ?></strong>
                            </p>

                            <input type="hidden" id="idusers" name="idusers" value="<?= $users->id ?>" hidden>
                            <div class="input-group mb-3">
                                <input type="password" class="form-control form-control-lg" id="new_passe" name="new_passe"
                                    placeholder="Nouveau mot de passe">
                                <div class="validation" id="validNom" style="color:#F9B233"></div>
                            </div>

                            <div class="input-group mb-4">
                                <input type="password" class="form-control form-control-lg" id="confirmer_new_passe" name="confirmer_new_passe"
                                    placeholder="Confirmer le mot de passe">
                                <div class="validation" id="validNom" style="color:#F9B233"></div>
                            </div>

                            <button id="btnReset" class="btn btn-success btn-lg btn-block" style="background-color:#033f1f;" onclick="getModifierMonMDP()">
                                Réinitialiser le mot de passe
                                <span id="spinnerMDP" class="spinner-border spinner-border-sm"></span>
                            </button>
                        </div>
                    </div>

                </div>
            <?php else : ?>
                <div class="alert alert-danger text-center my-5">
                    <strong>Lien invalide ou expiré.</strong><br>
                    Veuillez refaire une demande de réinitialisation.
                </div>
            <?php endif; ?>

        </div>
    </div>
    <!------POP UP NOTIFICATION -->
    <?php include "include/modals.php"; ?>
    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>

    <script>
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
                var heure = ("0" + cejour.getHours()).slice(-2) + ":" + ("0" + cejour.getMinutes()).slice(-2) +
                    ":" + ("0" + cejour.getSeconds()).slice(-2);
                var dateheure = date + " " + heure;
                var dateheure = dateheure.replace(/(^\w{1})|(\s+\w{1})/g, lettre => lettre.toUpperCase());
                document.getElementById('dateheure').innerHTML = dateheure;
            }
        }
        afficherDate();

        // Réinitialisation du mot de passe
        $("#btnReset").click(function() {
            const passW = $("#passW").val().trim();
            const passW2 = $("#passW2").val().trim();

            if (passW === "" || passW2 === "") {
                alert("Veuillez renseigner les deux champs de mot de passe.");
                return;
            }
            if (passW !== passW2) {
                alert("Les mots de passe ne correspondent pas.");
                return;
            }

            alert("Votre mot de passe a été réinitialisé avec succès.");
            // $.ajax({
            //     url: "config/routes.php",
            //     method: "POST",
            //     dataType: "json",
            //     data: {
            //         etat: "reinitialiser_motdepasse",
            //         id_user: "<?= $retour_recup_users[0]->id_users ?? ''; ?>",
            //         nouveau_pass: passW
            //     },
            //     success: function (response) {
            //         if (response.status === "success") {
            //             alert("Votre mot de passe a été réinitialisé avec succès.");
            //             window.location.href = "index.php";
            //         } else {
            //             alert("Une erreur est survenue, merci de réessayer.");
            //         }
            //     },
            //     error: function () {
            //         alert("Erreur système, merci de réessayer plus tard.");
            //     }
            // });
        });


        $("#connexion").click(function(evt) {
            var login = document.getElementById("login").value;
            var passW = document.getElementById("passW").value;



            if (login == "") {
                alert("Veuillez renseigner votre login svp !!");
                document.getElementById("login").focus();
                return false;
            } else if (passW == "") {
                alert("Veuillez renseigner votre mot de passe svp !!");
                document.getElementById("passW").focus();
                return false;
            } else {

                $.ajax({
                    url: "config/routes.php",
                    data: {
                        login: login,
                        passW: passW,
                        etat: "connexion"
                    },
                    dataType: "json",
                    method: "post",
                    //async: false,

                    success: function(response, status) {

                        console.log(response)
                        etat = response
                        if (etat !== '-1') {
                            location.href = "intro";
                        } else {
                            var a_afficher =
                                "DESOLE LOGIN / MOT DE PASS INCORRECT , Merci de ressayer"
                            $("#a_afficher2").text(a_afficher)
                            $('#error').modal("show")
                        }
                    },
                    error: function(response, status, etat) {
                        console.log(response)
                        etat = '-1';
                        alert("Erreur Systeme , Merci de ressayer plus tard ")
                    }
                })
            }
        })


        $("#passOublie").click(function() {

            //alert("Veuillez contacter votre administrateur")
            $('#PassOublieModale').modal("show")

        })


        $("#close").click(function() {
            $('#error').modal('hide')
            location.reload(true)
        })

        function getModifierMonMDP() {

            //let passe_actuel = document.getElementById("passe_actuel");
            let new_passe = document.getElementById("new_passe");
            let confirmer_new_passe = document.getElementById("confirmer_new_passe");
            let idusers = document.getElementById("idusers").value
            // Réinitialise les messages
            document.querySelectorAll(".validation").forEach(e => e.innerHTML = "");

            // // Vérification du mot de passe actuel
            // if (passe_actuel.value.trim() === "") {
            //     passe_actuel.focus();
            //     passe_actuel.nextElementSibling.innerHTML = "Veuillez entrer votre mot de passe actuel.";
            //     document.getElementById("spinnerMDP").style.display = "none";
            //     return false;
            // }

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

            // // Nouveau mot de passe ne doit pas être le même que l'ancien
            // if (new_passe.value === passe_actuel.value) {
            //     new_passe.focus();
            //     new_passe.nextElementSibling.innerHTML = "Le nouveau mot de passe ne peut pas être identique à l’actuel.";
            //     document.getElementById("spinnerMDP").style.display = "none";
            //     return false;
            // }

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


        function checkEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    </script>
</body>

</html>

</html>