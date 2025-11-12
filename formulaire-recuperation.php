<?php

include("autoload.php");
$id = GetParameter::FromArray($_REQUEST, 'i');
$action = GetParameter::FromArray($_REQUEST, 'p');


$retour_recup_users = null;
if ($id != null && $action != null) {


    if ($action == "mp") {

        $req = "SELECT tbl_recup_users.* , users.nom , users.prenom , users.profil as profil_agent , CONCAT(users.nom, ' ', users.prenom) AS nom_prenom FROM tbl_recup_users INNER JOIN users ON tbl_recup_users.id_users = users.id WHERE tbl_recup_users.id = '" . $id . "' ";
        $retour_recup_users = $fonction->_getSelectDatabases($req);

        $created_at = $retour_recup_users[0]->created_at;
        $expire = strtotime($created_at . ' +1 day');
        $expireDate = date('Y-m-d H:i:s', $expire);
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

            <?php if (!empty($retour_recup_users)) : ?>
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
                                Agent : <strong><?= htmlspecialchars($retour_recup_users[0]->nom_prenom); ?></strong>
                            </p>

                            <div class="input-group mb-3">
                                <input type="password" class="form-control form-control-lg" id="passW"
                                    placeholder="Nouveau mot de passe">
                            </div>

                            <div class="input-group mb-4">
                                <input type="password" class="form-control form-control-lg" id="passW2"
                                    placeholder="Confirmer le mot de passe">
                            </div>

                            <button id="btnReset" class="btn btn-success btn-lg btn-block" style="background-color:#033f1f;">
                                Réinitialiser le mot de passe
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

        $("#verifierPass").click(function() {

            var login = document.getElementById("loginPO").value;
            var email = document.getElementById("email").value;


            if (login == "") {
                alert("veuillez renseigner votre login SVP !!");
                document.getElementById("loginPO").focus();
                return false;
            } else {

                if (email == "") {
                    alert("veuillez renseigner votre email SVP !!");
                    document.getElementById("email").focus();
                    return false;
                } else {

                    if (checkEmail(email)) {
                        //alert("email valide");

                        $.ajax({
                            url: "config/routes.php",
                            data: {
                                login: login,
                                email: email,
                                etat: "motdepasseOublie"
                            },
                            dataType: "json",
                            method: "post",
                            //async: false,
                            success: function(response, status) {

                                console.log(response)
                                // let a_afficher = ""
                                // if (response != '-1') {
                                //     a_afficher = `<div class="alert alert-success" role="alert">
                                //                         <h2>Cher(e) <span class="text-success">` + login + `</span> votre mot de passe a bien été envoyé par email  !</h2></div>`
                                //     $("#a_afficher").text(a_afficher)
                                //     $('#passOublieModale').modal("hide")
                                // } else {
                                //     var a_afficher =
                                //         "DESOLE LOGIN / EMAIL INCORRECT , Merci de ressayer"
                                //     $("#a_afficher").text(a_afficher)
                                //     $('#passOublieModale').modal("show")
                                // }
                            },
                            error: function(err) {
                                console.error("Erreur AJAX rejet RDV", err);
                            }

                        })



                    } else {

                        alert("Veuillez renseigner votre adresse email  SVP !!");
                        document.getElementById("email").focus();
                        return false;
                    }

                }
            }

        })

        $("#close").click(function() {
            $('#error').modal('hide')
            location.reload(true)
        })


        function checkEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }
    </script>
</body>

</html>

</html>