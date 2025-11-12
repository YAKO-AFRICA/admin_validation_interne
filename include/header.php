<?php

session_start();

?>


<div class="header">


    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
        <div class="header-search">
        </div>
    </div>

    <div class="header-right">
        <div class="dashboard-setting user-notification">
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="" data-toggle="right-sidebar">
                    <span id="dateheure"></span>
                </a>
            </div>
        </div>
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <i class="dw dw-user2"></i>
                    </span>
                    <span class="user-name"><?= $_SESSION['utilisateur']; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="profile.php"><i class="dw dw-user1"></i> Profile</a>
                    <a class="dropdown-item" href="deconnexion.php"><i class="dw dw-logout"></i> Deconnexion</a>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="left-side-bar">

    <?php
    include "include/menu-bar.php";
    ?>
</div>


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
            var heure = ("0" + cejour.getHours()).slice(-2) + ":" + ("0" + cejour.getMinutes()).slice(-2) + ":" + ("0" + cejour.getSeconds()).slice(-2);
            var dateheure = date + " " + heure;
            var dateheure = dateheure.replace(/(^\w{1})|(\s+\w{1})/g, lettre => lettre.toUpperCase());
            document.getElementById('dateheure').innerHTML = dateheure;
        }
    }
    afficherDate();
</script>