/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
/* global $ */

/*global toastr */

function test_etat() {
    $(".divappli").each(function () {
        let appli = $(this).attr('data-appli');
        if ($("#status-" + appli).html() === "Installation...") {
            console.log('Appli en cours d install');
        } else {
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
                let version = data.version;
                // on va modifier le bouton en fonction de l'install
                if (installed) {
                    $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                    $("#div-" + appli).removeClass('div-uninstalled').attr('data-installed', '1');
                    $(".start-stop-button-" + appli).show();
                    // l'appli tourne, on va modifier les boutons si besoin
                    if (running) {
                        $("#texte-bouton-restart-" + appli).html("Redémarrer");
                        $("#version-" + appli).html(version);
                        $("#i_bouton_status_" + appli).removeClass("fa-play-circle").addClass("fa-redo-alt");
                        $("#stop-" + appli).show();
                    } else {
                        $("#texte-bouton-restart-" + appli).html("Démarrer");
                        $("#version-" + appli).html("Service non démarré");
                        $("#i_bouton_status_" + appli).addClass("fa-play-circle").removeClass("fa-redo-alt");
                        $("#stop-" + appli).hide();
                    }
                    if (public_url !== false) {
                        $("#nomAppli-" + appli).unwrap().wrap('<a href="https://' + public_url + '" target="_blank">');
                    } else {
                        $("#nomAppli-" + appli).unwrap().wrap('<a>');
                    }

                } else {
                    $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                    $("#div-" + appli).addClass('div-uninstalled').attr('data-installed', '0');
                    $(".start-stop-button-" + appli).hide();
                    $("#nomAppli-" + appli).unwrap().wrap('<a>');
                    $("#version-" + appli).html("Application non installée");
                }
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        }

    });

}

$(document).ready(function () {

    $(".install_outils").click(function () {
        var appli = $(this).attr('data-appli');
        var desc = $("#desc-" + appli).html();
        console.log('affiche' + desc);
        $('#modalOutils').modal('show');
        $("#description").css({"margin-left": "15px", "font-family": "Verdana", "margin-right": "15px"});
        $("#description").html(desc);
        $("#outils").html(appli);
        $("#outils_install").attr('data-outils', appli);
    });

    $("#form_install_oauth").validate({
        rules: {
            clientoauth: {
                required: true
            },
            secretoauth: {
                required: true
            },
            mailoauth: {
                required: true
            },
        }
    });

    $("#form_install_cloud").validate({
        rules: {
            emailcloud: {
                required: true
            },
            apicloud: {
                required: true
            },
        }
    });

    $(".option_install").click(function () {
        var outils = $(this).attr('data-outils');
        console.log('outils a la valeur ' + outils);

        if (outils == "oauth") {
            console.log('outils n est pas vide');
            $('#modalOutils').modal('hide');
            $('#modalOauth').modal('show');

            $(".oauth_install").click(function () {
                if ($("#form_install_oauth").valid()) {
                    var clientoauth = $("#clientoauth").val();
                    console.log('clientoauth a la valeur ' + clientoauth);
                    var secretoauth = $("#secretoauth").val();
                    console.log('secretoauth a la valeur ' + secretoauth);
                    var mailoauth = $("#mailoauth").val();
                    console.log('mailoauth a la valeur ' + mailoauth);
                    $('#modalOauth').modal('hide');
                    toastr.success("Installation de " + outils + " en cours");
                    toastr.warning("Déconnection du site imminente, nettoyer l'historique une fois l'installation terminée");
                    $.ajax({
                        url: "ajax/install_oauth.php",
                        method: "POST",
                        data: {clientoauth: clientoauth, secretoauth: secretoauth, mailoauth: mailoauth}
                    }).done(function (data) {
                        console.log("result " + data);
                        toastr.success("Installation de " + outils + " terminée");
                    }).fail(function () {
                        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                    });
                } else {
                    toastr.warning('Merci de VERIFIER la saisie des champs');
                    console.log('Au moins un des champs est VIDE !')
                }
            });
        } else if (outils == "cloudflare") {
            console.log('outils n est pas vide');
            $('#modalOutils').modal('hide');
            $('#modalCloudflare').modal('show');

            $(".cloud_install").click(function () {
                if ($("#form_install_cloud").valid()) {
                    var emailcloud = $("#emailcloud").val();
                    console.log('emailcloud a la valeur ' + emailcloud);
                    var apicloud = $("#apicloud").val();
                    console.log('apicloud a la valeur ' + apicloud);
                    $('#modalCloudflare').modal('hide');
                    toastr.success("Installation de " + outils + " en cours");
                    toastr.warning("Déconnection temporaire du site imminente");
                    $.ajax({
                        url: "ajax/install_cloudflare.php",
                        method: "POST",
                        data: {emailcloud: emailcloud, apicloud: apicloud}
                    }).done(function (data) {
                        console.log("result " + data);
                        toastr.success("Installation de " + outils + " terminée");
                    }).fail(function () {
                        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                    });
                } else {
                    toastr.warning('Merci de VERIFIER la saisie des champs');
                    console.log('Au moins un des champs est VIDE !')
                }
            });
        } else {
            $('#modalOutils').modal('hide');
            toastr.success("Installation de " + outils + " en cours...")

            $.ajax({
                url: "ajax/install_options.php?outils=" + outils
            }).done(function (data) {
                console.log("result " + data);
                toastr.success("Installation de " + outils + " terminée");
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            });
        }
    });

    $(".check_install").click(function () {
        if ($('#myCheck').is(':checked')) {
            $("#text").show();
            $("#subdomain").show();
        } else {
            $("#text").hide();
            $("#subdomain").hide();
        }
    });

    $("#affiche-install-appli").click(function () {
        console.log('affiche install');
        $('#modal_install_applis').modal('show');
    });

    $(".install-modal").click(function () {
        $('#seedbox').modal('show');
    });

    $(".rclone-modal").click(function () {
        $('#rclone_token').modal('show');
    });

    // affichage du pop up de confirmation de désinstall
    $(".uninstall").click(function () {
        var appli = $(this).attr('data-appli');
        $("#confirm-uninstall").attr('data-appli', appli);
        $("#modal-confirm-uninstall").modal('show');
        $(".appli-uninstall").html(appli);
    });

    // confirmation de la désinstall
    $("#confirm-uninstall").click(function () {
        $("#modal-confirm-uninstall").modal('hide');
        var appli = $(this).attr('data-appli');
        toastr.warning("Désinstallation de " + appli + " en cours...")

        $.ajax({
            url: "ajax/uninstall_service.php?service=" + appli
        }).done(function () {
            // On est dans le done, tout est ok
            // la requête est passée
            $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
            // on cache les boutons start/stop
            $(".start-stop-button-" + appli).hide();
            // on affiche le toaster
            toastr.success("Désinstallation de " + appli + " terminée");
            // on ajoute la transparence
            $("#div-" + appli).addClass('div-uninstalled').attr('data-installed', '0');
            window.location.reload();
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });
    });

    $(".install_appli_etape_1").click(function (event) {
        event.preventDefault();
        let appli = $(this).attr("data-appli");
        var desc = $("#desc-" + appli).html();
        console.log('affiche' + desc);
        $("#description-appli").css({"margin-left": "15px", "font-family": "Verdana", "margin-right": "15px"});
        $("#description-appli").html(desc);
        $("#nomappliencours").html(appli);
        $("#validation_install_appli").attr('data-appli', appli);
        $("#subdomain").val(appli);
        $('#modal_install_applis').modal('hide');
        $('#modalPoll').modal('show');
    });

    // on va intercepter le click sur le bouton sur le modal rclone
    $("#rclone_token_valid").click(function () {
        if ($("#token").val() !== "") {
            console.log('token n est pas vide');
            var token = $("#token").val();
            console.log('token a la valeur ' + token);
            var drive = $('input[type=radio][name=drive]:checked').attr('value');
            console.log('drive a la valeur ' + drive);
            var drivename = $("#drivename").val();
            console.log('drivename a la valeur ' + drivename);
        } else {
            console.log('TOKEN est VIDE !');
        }
        $.ajax({
            url: "ajax/install_token.php?token=" + token + "&drive=" + drive + "&drivename=" + drivename
        }).done(function (data) {
            // On est dans le done, tout est ok
            // la requête est passée
            $('#rclone_token').modal('hide');
            console.log("result " + data);
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        });
    });

    // on va intercepter le click sur le bouton status
    $(".bouton-install").click(function () {
        let appli = $(this).attr("data-appli");
        console.log("Appli appelée " + appli)
        // on va considérer que le texte du bouton est ok
        // a voir si on refait un appel ajax pour vérifier 
        if ($("#subdomain").val() !== "") {
            console.log('Subdomain n est pas vide');
            var subdomain = $("#subdomain").val();
            console.log('Subdomain a la valeur ' + subdomain);
            $("#validation_install_appli").attr('data-subdomain', subdomain);
        } else {
            console.log('Subdomain est VIDE !');
        }
        // on change le texte du bouton
        $("#status-" + appli).html("Installation...");
        // on lance un ajax qui va installer tout ça
        // là je ferme le modal, jusque là ca va et le modal "modalYT1" se lance
        $('#modalPoll').modal('hide');
        toastr.success("Installation de " + appli + " commencée");
        console.log('Subdomain a ENCORE la valeur ' + subdomain);
        //$(".overlay").show();
        $.ajax({
            url: "ajax/install_service.php?service=" + appli + "&subdomain=" + subdomain
        }).done(function (data) {
            // On est dans le done, tout est ok
            // la requête est passée
            console.log("result " + data);
            // on change le texte du bouton 
            toastr.success("Installation de " + appli + " terminée");
            window.location.reload();

        }).fail(function () {
            //$(".overlay").hide();
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            toastr.warning("Erreur sur ajax");
            $("#status-" + appli).html("Erreur ajax");
        });
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
            refresh_logs();
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
            refresh_logs();
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $(".start-stop-button-" + appli).show();
            $("#status-" + appli).html("Erreur ajax");
        });
    });

    function recherche(zonerecherche, zoneaffichage) {
        let searchcontent = $("#" + zonerecherche).val();
        searchcontent = searchcontent.toLowerCase();
        $("." + zoneaffichage).each(function () {

            let iddiv = $(this).attr('id');
            let nomdiv = $(this).attr('data-appli');
            if (nomdiv.includes(searchcontent)) {
                $("#" + iddiv).show();
            } else {
                $("#" + iddiv).hide();
            }
        });
    }

    // gestion de la zone de recherche
    $("#searchappli").on('input', function () {
        recherche("searchappli", "divappli");
    });

    // gestion de la zone de recherche pour les applis à installer
    $("#uninstall-search").on('input', function () {
        recherche("uninstall-search", "div-appli-uninstalled");
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
        //$("#logo-" + appli).attr("src", "https://www.scriptseedboxdocker.com/wp-content/uploads/icones/" + appli + ".png");
        $("#logo-" + appli).attr("src", "ajax/affiche_image.php?appli=" + appli);
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

    function affiche_infos_ajax(ajaxpath, elementaafficher) {
        $.ajax({
            url: ajaxpath,
        }).done(function (data) {
            $("#" + elementaafficher).html(data);
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#" + elementaafficher).html("Erreur ajax");
        });
    }

    affiche_infos_ajax("ajax/system_release.php", "server-version");
    affiche_infos_ajax("ajax/uptime.php", "uptime");
    affiche_infos_ajax("ajax/disque.php", "free-disk");


    // etat des vignettes d'appli

    test_etat();
    // on met à blanc les valeurs
    $("#subdomain").val('');
    $("#myCheck").prop("checked", false);
    // on va supprimer les vieux fichiers de logs
    $.ajax({
        url: "ajax/delete_old_logs.php",
    }).done(function (data) {
        if (data == 'ok') {
            console.log('Vieux logs effacés');
        }

    }).fail(function () {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');

    });


});
