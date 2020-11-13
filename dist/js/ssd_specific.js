/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
/* global $ */
/*global toastr */
$(document).ready(function() {
    // on va d'abord mettre les bons textes sur les bons boutons...

    /*
    Notes Merrick, je cache tout ça, çar c'est géré par le php au chargement de la page
    mais on peut en avoir besoin plus loin
    */

    /*$(".bouton-install").each(function() {
        let appli = $(this).attr("data-appli");
        $.ajax({
            url: "ajax/check_service.php?service=" + appli
            // appel simple, en GET
            // on peut rajouter des options si besoin pour du POST
        }).done(function(data) {
            // On est dans le done, tout est ok
            // la requête est passée
            // le résultat de la requête est maintenant dans la variable "data"
            if (data === "ok") {
                // le service tourne
                $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                $(".start-stop-button-" + appli).show();
                $("#div-" + appli).attr("data-installed", 1);
            }
            else {
                // le service ne tourne pas
                $("#status-" + appli).html("Installer");
                $(".start-stop-button-" + appli).hide();
                $("#div-" + appli).attr("data-installed", 0).css('opacity', '0.5');
                
            }
        }).fail(function() {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });
    });*/


    // on va intercepter le click sur le bouton status
    $(".bouton-install").click(function() {
        let appli = $(this).attr("data-appli");
        console.log("Appli appelée " + appli)
        // on va considérer que le texte du bouton est ok
        // a voir si on refait un appel ajax pour vérifier ?
        if ($("#status-" + appli).html() === "Installer") {
            // on change le texte du bouton 
            $("#status-" + appli).html("Installation...");
            // on lance un ajax qui va installer tout ça
            $.ajax({
                url: "ajax/install_service.php?service=" + appli
            }).done(function(data) {
                // On est dans le done, tout est ok
                // la requête est passée
                console.log("result " + data);
                // on change le texte du bouton 
                $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                // on afficher les boutons start/stop
                $(".start-stop-button-" + appli).show();
                // on affiche les logs
                // il suffit d'afficher la dic modalYT1 qui contient déjà un iframe de défilement des logs
                $('#modalYT1').modal('show');
                // on met à jour les infos de la div
                $("#div-" + appli).attr("data-installed", 1).removeClass('div-uninstalled');
            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        }
        else if ($("#status-" + appli).html() === "Désinstaller") {
            $("#status-" + appli).html("Désinstallation...");
            $.ajax({
                url: "ajax/uninstall_service.php?service=" + appli
            }).done(function() {
                // On est dans le done, tout est ok
                // la requête est passée
                $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                // on cache les boutons start/stop
                $(".start-stop-button-" + appli).hide();
                // on affiche le toaster
                toastr.success("Désinstallation de " + appli + " en cours");
                // on ajoute la transparence
                $("#div-" + appli).addClass('div-uninstalled');
            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        }
        else {
            console.log('Erreur sur le texte du bouton, impossible de continuer');
        }

    });

    //le bouton restart
    $(".bouton-start").click(function() {
        console.log("Bouton start clické");
        let appli = $(this).attr("data-appli");
        console.log("Restart de " + appli);
        let texte_alerte = 'Redémarrage';
        // on checke si le service tourne
        $.ajax({
            url: "ajax/check_service.php?service=" + appli
            // appel simple, en GET
            // on peut rajouter des options si besoin pour du POST
        }).done(function(data) {
            // On est dans le done, tout est ok
            // la requête est passée
            // le résultat de la requête est maintenant dans la variable "data"
            if (data !== "ok") {
                // le service ne tourne pas
                $("#reset-" + appli).html("Redémarrer");
                texte_alerte = 'Démarrage';
            }
        }).fail(function() {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });
        //
        $.ajax({
            url: "ajax/restart_service.php?service=" + appli
        }).done(function() {
            toastr.success(texte_alerte + " de " + appli + " en cours");
        }).fail(function() {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });

    });

    //le bouton stop
    $(".bouton-stop").click(function() {
        console.log("Bouton stop clické");
        let appli = $(this).attr("data-appli");
        console.log("Arrêt de " + appli);

        $.ajax({
            url: "ajax/stop_service.php?service=" + appli
        }).done(function() {
            toastr.success("Arrêt de " + appli + " en cours");
            $(".start-stop-button-" + appli).show();
        }).fail(function() {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $(".start-stop-button-" + appli).show();
            $("#status-" + appli).html("Erreur ajax");
        });
    });


    // gestion de la zone de recherche
    $("#searchappli").on('input', function() {
        let searchcontent = $("#searchappli").val();
        searchcontent = searchcontent.toLowerCase();
        $(".divappli").each(function() {

            let iddiv = $(this).attr('id');
            let nomdiv = $(this).attr('data-appli');
            if (nomdiv.includes(searchcontent)) {
                $("#" + iddiv).show();

            }
            else {
                $("#" + iddiv).hide();
            }
        });
    });


    // gestion de la case à cocher pour afficher les applis installées
    $("#installed_appli").change(function() {
        if ($(this).is(":checked")) {
            $(".divappli").each(function() {

                let isinstalled = $(this).attr('data-installed');

                if (isinstalled == 1) {
                    $(this).show();
                }
                else {
                    $(this).hide();
                }
            });
        }
        else {
            $(".divappli").show();
        }
    });

    /* fonction de refresh automatique  */
    window.setInterval(function() {
        console.log('test régulier');
        $(".divappli").each(function() {
            let appli = $(this).attr('data-appli');
            let divid = $(this).attr('id');
            $.ajax({
                url: "ajax/etat_service.php?service=" + appli,
                dataType: "json"
            }).done(function(data) {
                let running = data.running;
                let installed = data.installed;
                //console.log('Etat service '+ appli + ', installed :' + installed + ', running ' + running);
                // on va modifier le bouton en fonction de l'install
                if (installed) {
                    $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                    $("#" + divid).removeClass('div-uninstalled');
                    $(".start-stop-button-" + appli).show();
                    // l'appli tourne, on va modifier les boutons si besoin
                    if (running) {
                        $("#texte-bouton-restart-" + appli).html("Redémarrer");
                    }
                    else {
                        $("#texte-bouton-restart-" + appli).html("Démarrer");
                    }
                }
                else {
                    $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                    $("#" + divid).addClass('div-uninstalled');
                    $(".start-stop-button-" + appli).hide();
                }

            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });



        });
    }, 15000);
});
