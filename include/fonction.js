function retour() {
    window.history.back();
}


        function getStatsGenerales(rows, colonnes) {
            const stats = {};
            stats.total = rows.length;

            // Initialiser les clés pour chaque colonne
            colonnes.forEach(col => {
                stats[col] = {};
            });

            // Parcourir les lignes
            rows.forEach(row => {
                colonnes.forEach(col => {
                    let val = row[col] ?? null;
                    if (val === null) val = "NON RENSEIGNÉ";

                    if (!stats[col][val]) {
                        stats[col][val] = 0;
                    }
                    stats[col][val]++;
                });
            });
            return stats;
        }

        function formGraphEtat(valueEtat) {
            $(".dial2").knob();
            $({
                animatedVal: 0
            }).animate({
                animatedVal: valueEtat
            }, {
                duration: 3000,
                easing: "swing",
                step: function() {
                    $(".dial2").val(Math.ceil(this.animatedVal)).trigger("change");
                }
            });
        }

        function afficheuseVilles(tabloVilles) {
            //console.log(tabloVilles);
            let optionVilles = ``;
            let tablo_graph = [];

            $.each(tabloVilles, function(indx, data) {

                tablo_graph.push([indx, data, false], );
                optionVilles += `<tr>
                                    <td>${indx}</td>
                                    <td>${data}</td>
                                </tr>`;
            });

            //console.log(tablo_graph);
            let htmlVilles = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par ville :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionVilles + `
                                                    </thead>
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseVilles").html(htmlVilles);
            // chart 5
            Highcharts.chart('chart5', {
                title: {
                    text: 'Statistiques par ville'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                series: [{
                    type: 'pie',
                    allowPointSelect: true,
                    keys: ['name', 'y', 'selected', 'sliced'],
                    data: tablo_graph,
                    showInLegend: true
                }]
            });

        }

        function afficheuseMotif(tabloMotif) {
            console.log(tabloMotif);

            let optionMotif = ``;
            let tablo_graph = [];

            $.each(tabloMotif, function(indx, data) {

                tablo_graph.push([indx, data, false], );
                optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td>${data}</td>
                                </tr>`;
            });

            let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Motif :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionMotif + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseMotif").html(htmlMotif);

            Highcharts.chart('chart1', {
                title: {
                    text: 'Statistiques par Motif'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                series: [{
                    type: 'pie',
                    allowPointSelect: true,
                    keys: ['name', 'y', 'selected', 'sliced'],
                    data: tablo_graph,
                    showInLegend: true
                }]
            });
        }

        function afficheuseEtat(tabloEtat) {

            const tablo_statut_rdv = {
                "1": {
                    lib_statut: "En attente",
                    libelle: "En attente",
                    statut_traitement: "1",
                    color_statut: "badge badge-secondary",
                    color: "gray",
                    url: "liste-rdv-attente",
                    icone: "micon dw dw-edit"
                },

                "2": {
                    lib_statut: "Transmis",
                    libelle: "TRANSMIS",
                    statut_traitement: "2",
                    color_statut: "badge badge-secondary",
                    color: "blue",
                    url: "liste-rdv-transmis",
                    icone: "micon fa fa-forward fa-2x"
                },

                "0": {
                    lib_statut: "Rejete",
                    libelle: "REJETE",
                    statut_traitement: "0",
                    color_statut: "badge badge-danger",
                    color: "red",
                    url: "",
                    icone: "micon fa fa-close"
                },

                "3": {
                    lib_statut: "Traiter",
                    libelle: "TRAITER",
                    statut_traitement: "3",
                    color_statut: "badge badge-success",
                    color: "#033f1f",
                    url: "liste-rdv-traite",
                    icone: "micon fa fa-check"
                },

                "-1": {
                    lib_statut: "Saisie inachevée",
                    libelle: "SAISIE INACHEVEE",
                    statut_traitement: "-1",
                    color_statut: "badge badge-dark",
                    color: "black",
                    url: "liste-rdv-rejet",
                    icone: "micon fa fa-close"
                }
            };

            let optionEtat = ``;

            // ---- CALCUL TOTAL ----
            let total = 0;
            $.each(tabloEtat, function(indx, data) {
                total += parseInt(data);
            });

            // ---- CARTES INDIVIDUELLES ----
            $.each(tabloEtat, function(indx, data) {

                let valeurDDD = tablo_statut_rdv[indx] ?? 0;
                console.log(valeurDDD);

                optionEtat += `
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                        <div class="card-box pd-20 text-center shadow-sm border rounded">
                            <input type="text" 
                                class="knob dial2"      value="0" 
                                data-valFinal="${data}" data-max="${total}"   data-width="120"  data-height="120" 
                                data-linecap="round" data-thickness="0.12"   data-bgColor="#f1f1f1"  data-fgColor="${valeurDDD.color}"    data-angleOffset="180" 
                                readonly>

                            <h5 class="mt-2" style="color: ${valeurDDD.color};">
                                ${data} RDV ${valeurDDD.libelle}
                            </h5>
                        </div>
                    </div>`;
            });

            // ---- CARTE TOTAL ----
            // optionEtat += `
            //     <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
            //         <div class="card-box pd-20 text-center shadow-sm border rounded">
            //             <input type="text" class="knob dial2 total-knob" value="0"  data-valFinal="${total}"  data-max="${total}" data-width="120" 
            //                 data-height="120"  data-linecap="round"  data-thickness="0.12"  data-bgColor="#f1f1f1" 
            //                 data-fgColor="green"  data-angleOffset="180"  readonly>

            //             <h5 class="mt-2" style="color: green; font-weight:bold;">
            //                 TOTAL : ${total}
            //             </h5>
            //         </div>
            //     </div>`;

            // ---- INJECTION HTML ----
            $("#afficheuseEtat").html(`<div class="row mb-4"> 
                            ${optionEtat}
                            
                        </div>`);

            // ---- INIT KNOBS ----
            $(".dial2").knob();

            // ---- ANIMATION ----
            $(".dial2").each(function() {
                let $this = $(this);
                let finalVal = parseInt($this.data("valfinal"));

                $({
                    val: 0
                }).animate({
                    val: finalVal
                }, {
                    duration: 2000,
                    easing: "swing",
                    step: function() {
                        $this.val(Math.ceil(this.val)).trigger("change");
                    }
                });
            });
        }

        function getStatsDelaiRDV2(rows, colonneDate) {
            const stats = {};

            rows.forEach(row => {
                const delai = getDelaiRDV(row[colonneDate]);
                const etat = delai.etat;

                if (!stats[etat]) {
                    stats[etat] = 0;
                }
                stats[etat]++;
            });

            return stats;
        }

        function getStatsDelaiRDV(rows, colonneDate) {
            const stats = {};

            rows.forEach(row => {
                const delai = getDelaiRDV(row[colonneDate]);
                const etat = delai.etat;

                // Si l'état n'existe pas encore, on le crée
                if (!stats[etat]) {
                    stats[etat] = {
                        total: 0,
                        couleur: delai.couleur,
                        badge: delai.badge,
                        libelle: delai.libelle,
                        jours: [],       // liste des jours pour cet état
                        lignes: []       // si tu veux lister les lignes associées
                    };
                }

                stats[etat].total++;
                stats[etat].jours.push(delai.jours);
                stats[etat].lignes.push({
                    ...row,
                    delai: delai        // on ajoute toutes les infos du délai
                });
            });

            return stats;
        }



        function getDelaiRDV(dateRDV) {
            // Convertir d/m/Y → Y-m-d
            if (dateRDV && dateRDV.includes("/")) {
                const [jour, mois, annee] = dateRDV.split("/");
                dateRDV = `${annee}-${mois}-${jour}`;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const rdv = new Date(dateRDV);
            if (isNaN(rdv.getTime())) {
                return {
                    etat: "indisponible",
                    couleur: "gray",
                    badge: "",
                    libelle: "Date non disponible",
                    jours: null
                };
            }

            rdv.setHours(0, 0, 0, 0);

            const diffTime = rdv - today;
            const jours = Math.round(diffTime / (1000 * 60 * 60 * 24));

            // RDV EXPIRÉ
            if (jours < 0) {
                return {
                    etat: "expire",
                    couleur: "red",
                    badge: "badge badge-danger",
                    libelle: `Délai expiré depuis ${Math.abs(jours)} jour(s)`,
                    jours: Math.abs(jours)
                };
            }

            // RDV AUJOURD'HUI
            if (jours === 0) {
                return {
                    etat: "ok",
                    couleur: "#f39c12",
                    badge: "badge badge-warning",
                    libelle: "Aujourd’hui",
                    jours: 0
                };
            }

            // RDV À VENIR
            return {
                etat: "prochain",
                couleur: "#033f1f",
                badge: "badge badge-success",
                libelle: `${jours} jour(s) restant(s)`,
                jours: jours
            };
        }

       function afficheuseDelaiRDV(tabloMotif){

            console.log(tabloMotif);

            let optionMotif = ``;
            let tablo_graph = [];

            $.each(tabloMotif, function(indx, data) {

                tablo_graph.push([indx, data, false], );
                optionMotif += `<tr>
                                    <td>${indx}</td>
                                    <td>${data}</td>
                                </tr>`;
            });

            let htmlMotif = `<div class="card-box pd-20 shadow-sm border rounded">
                                            <h5 class="mb-3">Statistiques par Motif :</h5>
                                            
                                            <div class="table-responsive" style="height:400px;">
                                                <table class="table table-striped table-bordered mb-0">
                                                    <thead>
                                                        ` + optionMotif + `
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>`;
            $("#afficheuseMotif").html(htmlMotif);

            Highcharts.chart('chart7', {
                title: {
                    text: 'Statistiques par Motif'
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                series: [{
                    type: 'pie',
                    allowPointSelect: true,
                    keys: ['name', 'y', 'selected', 'sliced'],
                    data: tablo_graph,
                    showInLegend: true
                }]
            });
        }