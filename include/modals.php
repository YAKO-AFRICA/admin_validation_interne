<div class="modal fade" id="modaleAfficheDocument" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content ">
            <div class="modal-body text-center">
                <div class="card-body" id="iframeAfficheDocument">

                </div>
                <input type="text" class="form-control" id="val_doc2" name="val_doc3" hidden>
                <input type="text" class="form-control" id="document" name="document" hidden>
            </div>
            <div class="modal-footer">
                <button type="button" name="valid_download" id="valid_download" class="btn btn-success" style="background: #033f1f !important;">VALIDER DOCUMENT</button>
                <button type="button" name="annuler_download" id="annuler_download" class="btn btn-danger" style="background:red !important;">REJETER DOCUMENT</button>
                <button type="button" id="closeAfficheDocument" name="closeAfficheDocument" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center font-18">
                <h4 class="padding-top-30 mb-30 weight-500">
                    Voulez vous rejeter la demande de prestation <span id="a_afficher_1" style="color:#033f1f!important; font-weight:bold;"> </span> ? <!--br> Motif de rejet: <span id="a_afficher_2" style="color: #F9B233 !important; font-weight:bold;"> </span-->

                </h4>
                <span style='color:red;'>Attention cette action est irreversible !!</span><br>
                <span style='color:seagreen'>le client sera notifier du rejet de la prestation</span>
                </hr>
                <input type="text" id="idprestation" name="idprestation" hidden>
                <input type="text" id="motif" name="motif" hidden>
                <input type="text" id="traiterpar" name="traiterpar" hidden>
                <input type="text" id="observation" name="observation" hidden>

                <div class="padding-bottom-30 row" style="max-width: 170px; margin: 0 auto;">
                    <div class="col-6">
                        <button type="button" id="annulerRejet" name="annulerRejet" class="btn btn-secondary border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-times"></i></button>
                        NON
                    </div>
                    <div class="col-6">
                        <button type="button" id="validerRejet" name="validerRejet" class="btn btn-danger border-radius-100 btn-block confirmation-btn" data-dismiss="modal"><i class="fa fa-check"></i></button>
                        OUI
                    </div>
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
                <button type="submit" id="retourNotification" name="retourNotification" class="btn btn-success" style="background: #033f1f !important;">OK</button>
                <button type="button" id="closeEchec" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
            </div>
        </div>
    </div>
</div>

<div class="modal  hide fade in" data-backdrop="static" id="PassOublieModale" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content ">
            <div class="modal-header" style="background:#033f1f; color: #fff; font-weight:bold;"> Identification Agent pour mot de passe oublie - PLATEFORME VALIDATION YAKO AFRICA ASSURANCES VIE</div>
            <div class="modal-body ">
                <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;"> Identification agent </h4>
                <hr>
                <h6 class="text-center p-2" style="color:red ; font-weight:bold;"> Veuillez renseigner le formulaire ci-dessous : </h6>
                <div class="row">

                    <div class="form-group col-sm-12 col-md-7">
                        <label for="nomRdv">Veuillez renseigner votre Login / nom d'utilisateur <bold style="color: #F9B233;"> *</label>
                        <input type="text" id="loginPO" name="loginPO" data-rule="required" required placeholder="Entrez votre Login / nom d'utilisateur" class="form-control">
                    </div>

                    <div class="form-group col-sm-12 col-md-5">
                        <label for="nomRdv">Veuillez renseigner votre email <bold style="color: #F9B233;"> *</label>
                        <input type="text" id="email" name="email" data-rule="required" required placeholder="Entrez votre email" class="form-control">
                    </div>

                    <small class="text-danger" id="notif_n_mdp"></small>
                </div>

                <div class="modal-footer">
                    <div id="closeNotif">
                        <button type="button" id="closeNotif" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                        <button type="submit" class="btn btn-warning text-white" name="verifierPass" id="verifierPass" style="background: #F9B233">SOUMETTRE LA DEMANDE </button>
                        <span id="lib2"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="error" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="form-group">
                    <h2>
                        <span id="a_afficher2"></span>
                    </h2>
                </div>
            </div>
            <div class="modal-footer">
                <div id="annuler">
                    <button type="button" id="close" class="btn btn-secondary" data-dismiss="modal">FERMER</button>
                </div>
            </div>
        </div>
    </div>
</div>