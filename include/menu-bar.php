<?php
if (!isset($_SESSION['typeCompte'])) {
    header('Location: deconnexion');
    exit;
}

?>

<div class="brand-logo">
    <a href="<?= Config::URL_YAKO ?>">
        <img src="vendors/images/logo.png" alt="" class="dark-logo">
        <img src="vendors/images/logo.png" alt="" class="light-logo">
    </a>
    <div class="close-sidebar" data-toggle="left-sidebar-close">
        <i class="ion-close-round"></i>
    </div>
</div>

<div class="menu-block customscroll p-4">
    <div class="sidebar-menu">
        <ul id="accordion-menu">
            <li class="dropdown">
                <a href="intro" class="dropdown-toggle no-arrow">
                    <span class="micon dw dw-house-1"></span><span class="mtext">Accueil</span>
                </a>
            </li>
            <li>
                <div class="dropdown-divider"></div>
            </li>



            <?php

            // print_r($_SESSION); exit;
            switch ($_SESSION['typeCompte']) {

                case "admin":
            ?>
                    <li>
                        <div class="sidebar-small-cap"><?= strtoupper($_SESSION['profil'] . " " . $_SESSION['typeCompte'] . "s") ?></div>
                    </li>
                    <li class="dropdown">
                        <a href="liste-gestionnaires" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-users"></span><span class="mtext">Listes<br>utilisateurs</span>

                        </a>
                    </li>
                    <?php

                    break;
                case "prestation":
                    if ($_SESSION['profil'] == "supervisseur") {
                    ?>
                        <li>
                            <div class="sidebar-small-cap"><?= strtoupper($_SESSION['profil'] . " " . $_SESSION['typeCompte'] . "s") ?></div>
                        </li>
                        <li class="dropdown">
                            <a href="tableau-suivi-prestations" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-area-chart"></span><span class="mtext">Tableau de suivi</span>
                            </a>
                        </li>

                        <li class="dropdown">
                            <a href="liste-prestations" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-folder"></span><span class="mtext">Toutes les Demandes</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>

                        <li class="dropdown">
                            <a href="liste-gestionnaires" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-users"></span><span class="mtext">Listes<br>utilisateurs</span>

                            </a>
                        </li>

                    <?php
                    } else {
                        //admin
                    ?>
                        <li>
                            <div class="sidebar-small-cap"><?= strtoupper($_SESSION['typeCompte'] . "s") ?></div>
                        </li>

                        <li class="dropdown">
                            <a href="liste-prestation-attente" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon dw dw-edit"></span><span class="mtext">Demande <br>En attente</span>
                            </a>

                        </li>
                        <li class="dropdown">
                            <a href="liste-prestation-traite" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-check"></span><span class="mtext">Demande<br>Acceptée</span>

                            </a>
                        </li>

                        <li class="dropdown">
                            <a href="liste-prestation-rejet" class="dropdown-toggle no-arrow" style="font-size:14px">
                                <span class="micon fa fa-close"></span><span class="mtext">Demande<br>Rejetée</span>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                    <?php
                    }

                    ?>
                    <li class="dropdown">
                        <a href="bordereau-prestation" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon dw dw-folder"></span><span class="mtext">Bordereau<br>prestation</span>
                        </a>
                    </li>
                    <li>
                        <div class="sidebar-small-cap">Options</div>
                    </li>
                    <li class="dropdown">
                        <a href="recherche-prestation" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon dw dw-search"></span><span class="mtext">Rechercher<br>une demande<br>de prestation</span>
                        </a>
                    </li>

                <?php
                    break;
                case "rdv":
                ?>
                    <li>
                        <div class="sidebar-small-cap"><?= strtoupper($_SESSION['typeCompte'] . "s") ?></div>
                    </li>
                    <li class="dropdown">
                        <a href="liste-rdv?i=1" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon dw dw-edit"></span><span class="mtext">rdv<br>En attente</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="liste-rdv?i=2" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-forward "></span><span class="mtext">rdv<br>transmis</span>

                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="liste-rdv?i=3" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-check"></span><span class="mtext">rdv<br>traite(s)</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="liste-rdv?i=0" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-trash"></span><span class="mtext">rdv<br>Rejeté</span>
                        </a>
                    </li>

                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li class="dropdown">
                        <a href="tableau-suivi-rdv" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-area-chart"></span><span class="mtext">Tableau de suivi</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="recherche-rdv" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon dw dw-search"></span><span class="mtext">rechercher<br>un rdv</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="liste-bordereau-rdv" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-file"></span><span class="mtext">BORDEREAU RDV</span>

                        </a>
                    </li>
                <?php
                    break;
                case "gestionnaire":
                ?>
                    <li>
                        <div class="sidebar-small-cap"><?= strtoupper($_SESSION['typeCompte'] . "s") ?></div>
                    </li>

                    <li class="dropdown">
                        <a href="rdv-gestionnaire?i=2" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon dw dw-edit"></span><span class="mtext">rdv<br>transmis</span>
                        </a>
                    </li>
                    <li class="dropdown">
                        <a href="rdv-gestionnaire?i=3" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-check"></span><span class="mtext">rdv<br>traite(s)</span>

                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li class="dropdown">
                        <a href="liste-calendrierJourReceptionVilles" class="dropdown-toggle no-arrow" style="font-size:14px">
                            <span class="micon fa fa-calendar"></span><span class="mtext">Jour/Villes<br>Reception</span>

                        </a>
                    </li>
            <?php
                    break;
            }
            ?>



            <li>
                <div class="dropdown-divider"></div>
            </li>


            <li class="dropdown">
                <a href="deconnexion.php" class="dropdown-toggle no-arrow">
                    <span class="micon dw dw-logout"></span><span class="mtext">Deconnexion</span>
                </a>
            </li>
            <li>
                <div class="dropdown-divider"></div>
            </li>


        </ul>
    </div>
</div>