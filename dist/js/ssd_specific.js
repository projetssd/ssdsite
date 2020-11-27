/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
/* global $ */

/*global toastr */

function test_etat() {
    $(".divappli").each(function () {

        let appli = $(this).attr('data-appli');
        $.ajax({
            url: "ajax/etat_service.php?service=" + appli,
            dataType: "json"
        }).done(function (data) {
            // le "data" est le retour de la page etat_service
            // c'est un json prêt à être exploité, de la forme
            // {"running":true,"installed":true}

            let running = data.running;
            let installed = data.installed;
            let public_url = data.public_url;
            // on va modifier le bouton en fonction de l'install
            if (installed) {
                $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                $("#div-" + appli).removeClass('div-uninstalled');
                $(".start-stop-button-" + appli).show();
                // l'appli tourne, on va modifier les boutons si besoin
                if (running) {
                    $("#texte-bouton-restart-" + appli).html("Redémarrer");
                } else {
                    $("#texte-bouton-restart-" + appli).html("Démarrer");
                }
                $("#nomAppli").wrap('<a href="' + public_url + '" target="_blank">');
            } else {
                $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                $("#div-" + appli).addClass('div-uninstalled');
                $(".start-stop-button-" + appli).hide();
                $("#nomAppli").unwrap();
            }
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });
    });

}

$(document).ready(function () {

    $(".check_install").click(function () {
        if ($('#myCheck').is(':checked')) {
            $("#text").show();
            $("#subdomain").show();
        } else {
            $("#text").hide();
            $("#subdomain").hide();
        }
    });

    $(".affichage-modal").click(function () {
        let appli = $(this).attr("data-appli");
        if ($("#status-" + appli).html() === "Installer") {
            $("#nomappliencours").html(appli);
            $("#validation_install_appli").attr('data-appli', appli);
            $('#modalPoll').modal('show');
        } else if ($("#status-" + appli).html() === "Désinstaller") {
            $("#status-" + appli).html("Désinstallation...");
            $.ajax({
                url: "ajax/uninstall_service.php?service=" + appli
            }).done(function () {
                // On est dans le done, tout est ok
                // la requête est passée
                $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                // on cache les boutons start/stop
                $(".start-stop-button-" + appli).hide();
                // on affiche le toaster
                toastr.success("Désinstallation de " + appli + " en cours");
                // on ajoute la transparence
                $("#div-" + appli).addClass('div-uninstalled');
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        } else {
            console.log('Erreur sur le texte du bouton, impossible de continuer');
        }
    });

    // on va intercepter le click sur le bouton status
    $(".bouton-install").click(function () {
        let appli = $(this).attr("data-appli");
        console.log("Appli appelée " + appli)
        // on va considérer que le texte du bouton est ok
        // a voir si on refait un appel ajax pour vérifier ?
        if ($("#validation_install_appli").html() === "Installer") {

            if ($("#subdomain").val() !== "") {
                console.log('Subdomain n est pas vide');
                var subdomain = $("#subdomain").val();
                console.log('Subdomain a la valeur ' + subdomain);
                $("#validation_install_appli").attr('data-subdomain', subdomain);
            }
            else
            {
                console.log('Subdomain est VIDE !');
            }

            // on change le texte du bouton
            $("#status-" + appli).html("Installation...");
            // on lance un ajax qui va installer tout ça
            // là je ferme le modal, jusque là ca va et le modal "modalYT1" se lance
            $('#modalPoll').modal('hide');
            $('#modalYT1').modal('show');
            console.log('Subdomain a ENCORE la valeur ' + subdomain);
            $.ajax({
                url: "ajax/install_service.php?service=" + appli + "&subdomain=" + subdomain
            }).done(function (data) {
                // On est dans le done, tout est ok
                // la requête est passée
                console.log("result " + data);
                // on change le texte du bouton 
                $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                // on afficher les boutons start/stop
                $(".start-stop-button-" + appli).show();
                // on affiche les logs
                // il suffit d'afficher la dic modalYT1 qui contient déjà un iframe de défilement des logs

                // on met à jour les infos de la div
                $("#div-" + appli).attr("data-installed", 1).removeClass('div-uninstalled');
                // on rafraichit les applis
                test_etat();
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        } else {
            console.log('Erreur sur le texte du bouton, impossible de continuer');
        }

    });

    //le bouton restart
    $(".bouton-start").click(function () {
        console.log("Bouton start clické");
        let appli = $(this).attr("data-appli");
        console.log("Restart de " + appli);
        let texte_alerte = 'Redémarrage';
        // on checke si le service tourne
        $.ajax({
            url: "ajax/check_service.php?service=" + appli
            // appel simple, en GET
            // on peut rajouter des options si besoin pour du POST
        }).done(function (data) {
            // On est dans le done, tout est ok
            // la requête est passée
            // le résultat de la requête est maintenant dans la variable "data"
            if (data !== "ok") {
                // le service ne tourne pas
                $("#reset-" + appli).html("Redémarrer");
                texte_alerte = 'Démarrage';
            }
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });
        //
        $.ajax({
            url: "ajax/restart_service.php?service=" + appli
        }).done(function () {
            toastr.success(texte_alerte + " de " + appli + " en cours");
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });

    });

    //le bouton stop
    $(".bouton-stop").click(function () {
        console.log("Bouton stop clické");
        let appli = $(this).attr("data-appli");
        console.log("Arrêt de " + appli);

        $.ajax({
            url: "ajax/stop_service.php?service=" + appli
        }).done(function () {
            toastr.success("Arrêt de " + appli + " en cours");
            $(".start-stop-button-" + appli).show();
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $(".start-stop-button-" + appli).show();
            $("#status-" + appli).html("Erreur ajax");
        });
    });


    // gestion de la zone de recherche
    $("#searchappli").on('input', function () {
        let searchcontent = $("#searchappli").val();
        searchcontent = searchcontent.toLowerCase();
        $(".divappli").each(function () {

            let iddiv = $(this).attr('id');
            let nomdiv = $(this).attr('data-appli');
            if (nomdiv.includes(searchcontent)) {
                $("#" + iddiv).show();
            } else {
                $("#" + iddiv).hide();
            }
        });
    });


    // gestion de la case à cocher pour afficher les applis installées
    $("#installed_appli").change(function () {
        if ($(this).is(":checked")) {
            $(".divappli").each(function () {
                let isinstalled = $(this).attr('data-installed');
                if (isinstalled == 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        } else {
            $(".divappli").show();
        }
    });

    // récupération des version
    $(".divappli").each(function () {

        let appli = $(this).attr('data-appli');
        // on met la bonne image
        $("#logo-" + appli).attr("src", "https://www.scriptseedboxdocker.com/wp-content/uploads/icones/" + appli + ".png");
        $.ajax({
            url: "ajax/check_version.php?service=" + appli,
        }).done(function (data) {
            $("#version-" + appli).html(data);
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#version-".appli).html("erreur ajax");
        });
        // est-ce que le service tourne ?

    });

    /* fonction de refresh automatique  */
    window.setInterval(function () {
        test_etat();
    }, 15000); // timer en ms

    // statut du serveur
    $.ajax({
        url: "ajax/system_release.php",
    }).done(function (data) {
        $("#server-version").html(data);

    }).fail(function () {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#server-version").html("Erreur ajax");
    });
    // uptime
    $.ajax({
        url: "ajax/uptime.php",
    }).done(function (data) {
        $("#uptime").html(data);

    }).fail(function () {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#uptime").html("Erreur ajax");
    });
    // disque libre
    $.ajax({
        url: "ajax/disque.php",
    }).done(function (data) {
        $("#free-disk").html(data);

    }).fail(function () {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#free-disk").html("Erreur ajax");
    });
    // etat des vignettes d'appli

    test_etat();
    // on met à blanc les valeurs
    $("#subdomain").val('');
    $("#myCheck").prop( "checked", false );
});
