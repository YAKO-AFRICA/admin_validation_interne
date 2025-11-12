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

            <div class="row align-items-center">

                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center" style="color:#033f1f;  font-weight:bold; ">Connexion</h2>
                        </div>
                        <div class="input-group custom">
                            <input type="text" class="form-control form-control-lg" id="login"
                                placeholder="Entrez votre login">

                        </div>
                        <div class="input-group custom">
                            <input type="password" class="form-control form-control-lg" id="passW"
                                placeholder="Entrez votre mot de passe">

                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12 col-md-12 text-left">
                                <bold style="color: #F9B233; font-weight: bold;font-size: 9px;"> *</bold>
                                <span style="color:#033f1f; font-weight: bold;font-size: 12px;"> vous n'avez oublie
                                    votre mot de passe </span>
                                <a class="btn" style="color:#F9B233 !important;font-weight: bold; font-size: 11px;"
                                    type="submit" name="passOublie" id="passOublie">cliquez ici</a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="input-group mb-0">
                                    <button id="connexion" name="connexion" class="btn btn-success btn-lg btn-block "
                                        style="background: #033f1f !important;">connexion</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-7">
                    <img src="vendors/images/images-2.jpg" alt="">
                </div>

            </div>
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