<div class="row">
    <div class="col-md-4">
        <div class="card-body height-100-p pd-20">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-box mb-30 p-2" style="background-color:bisque ;color:white !important; font-weight:bold;">
                            <h4 class="text-center p-2" style="color:#033f1f ; font-weight:bold;">Liste des documents joints</h4>

                            <div class="text-center mb-2">
                                <span id="compteur_valides" class="badge bg-success">Validés : 0</span>
                                <span id="compteur_restants" class="badge bg-danger">Restants : <?= count($retour_documents); ?></span>
                            </div>

                            <div class="text-center mt-2">
                                <span id="checking" class="text-success fw-bold"></span>
                            </div>
                            <hr>
                            <?php
                            $i = 0;
                            if ($retour_documents != null) {
                                for ($i = 0; $i <= count($retour_documents) - 1; $i++) {
                                    $tablo = $retour_documents[$i];

                                    $id_prestation = $tablo["idPrestation"];
                                    $path_doc = trim($tablo["path"]);
                                    $type_doc = trim($tablo["type"]);
                                    $doc_name = trim($tablo["libelle"]);
                                    $ref_doc = trim($tablo["id"]);
                                    $datecreation_doc = trim($tablo["created_at"]);
                                    $documents = Config::URL_PRESTATION_RACINE . $path_doc;

                                    array_push($tablo_doc_attendu,  $ref_doc);

                                    switch ($type_doc) {
                                        case 'RIB':
                                            $nom_document = "RIB";
                                            break;
                                        case 'Police':
                                            $nom_document = "Police du contrat d'assurance";
                                            break;
                                        case 'bulletin':
                                            $nom_document = "Bulletin de souscription";
                                            break;
                                        case 'AttestationPerteContrat':
                                            $nom_document = "Attestation de déclaration de perte";
                                            break;
                                        case 'CNI':
                                            $nom_document = "CNI";
                                            break;
                                        case 'etatPrestation':
                                            $nom_document = "Fiche de demande de prestation";
                                            break;
                                        default:
                                            $nom_document = "Fiche d'identification du numéro de paiement";
                                            break;
                                    }

                                    $values = $id_prestation . "-" . $ref_doc . "-" . $nom_document . "-" . $doc_name;
                            ?>
                                    <div class="d-flex align-items-center mt-3 document-ligne">
                                        <input type="text" class="val_doc" name="val_doc" value="<?php echo $values; ?>" hidden>
                                        <input type="text" class="path_doc" name="path_doc" value="<?php echo $documents; ?>" hidden>

                                        <div class="fm-file-box text-success p-2">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h6 class="mb-0" style="font-size: 12px;">
                                                <a href="<?= $documents ?>" target="_blank"> <?= $nom_document ?> </a>
                                            </h6>
                                            <p class="mb-0 text-secondary" style="font-size: 0.8em;"> <?= $datecreation_doc ?> </p>
                                        </div>
                                        <button type="button" class="btn btn-warning bx bx-show" data-doc-id="<?= $documents; ?>"
                                            data-path-doc="<?= $documents; ?>" style="background-color:#F9B233 !important;">
                                            <i class="dw dw-eye"> voir</i>
                                        </button>
                                    </div>
                            <?php
                                }
                            } else {
                                echo '<div class="alert alert-danger" role="alert">  Attention ! <strong>Aucun document joint</strong>. </div>';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let document_valider = [];
let total_documents = <?= count($retour_documents); ?>;

function updateCompteurs() {
    let nb_valides = document_valider.length;
    let nb_restants = total_documents - nb_valides;
    $("#compteur_valides").text("Validés : " + nb_valides);
    $("#compteur_restants").text("Restants : " + nb_restants);
}

$(".bx-show").click(function () {
    let path_document = $(this).data("path-doc");
    let val_doc = $(this).closest('.d-flex').find('.val_doc').val();

    let html = `<iframe src="${path_document}" width="100%" height="500"></iframe>`;
    $("#document").val(path_document);
    $("#val_doc2").val(val_doc);
    $("#iframeAfficheDocument").html(html);
    $('#modaleAfficheDocument').modal("show");
});

$("#modaleAfficheDocument").on("click", "#valid_download", function () {
    let doc = $("#document").val();
    let val_doc = $("#val_doc2").val();

    let tab = val_doc.split('-');
    let id_prestation = tab[0];
    let ref_doc = tab[1];
    let doc_name = tab[2];
    let type_doc = tab[3];

    if (document_valider.includes(ref_doc)) {
        alert(doc_name + " a déjà été validé");
        $("#checking").html(`<i class="fa fa-check text-warning"> ${doc_name} a déjà été validé</i>`);
    } else {
        document_valider.push(ref_doc);
        alert(doc_name + " validé avec succès");

        $("#checking").html(`<i class="fa fa-check text-success"> ${doc_name} a bien été validé</i>`);

        $(".val_doc").each(function () {
            if ($(this).val().includes(ref_doc)) {
                $(this).closest(".d-flex")
                    .addClass("border border-success bg-light")
                    .fadeOut(2000);
            }
        });

        updateCompteurs();
    }
    $('#modaleAfficheDocument').modal("hide");
});
</script>

<!-- Placez ici la modale modaleAfficheDocument + inputs val_doc2 et document comme dans votre code d'origine -->
