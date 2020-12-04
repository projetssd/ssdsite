/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
/* global $ */

/*global toastr */

function test_etat() {
    $(".divappli").each(function() {

        let appli = $(this).attr('data-appli');
        if ($("#status-" + appli).html() === "Installation...") {
            console.log('Appli en cours d install');

        }
        else {

            $.ajax({
                url: "ajax/etat_service.php?service=" + appli,
                dataType: "json"
            }).done(function(data) {
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
                    $("#div-" + appli).removeClass('div-uninstalled');
                    $(".start-stop-button-" + appli).show();
                    // l'appli tourne, on va modifier les boutons si besoin
                    if (running) {
                        $("#texte-bouton-restart-" + appli).html("Redémarrer");
                        $("#version-" + appli).html(version);
                    }
                    else {
                        $("#texte-bouton-restart-" + appli).html("Démarrer");
                        $("#version-" + appli).html("Service non démarré");

                    }
                    $("#nomAppli-" + appli).unwrap().wrap('<a href="https://' + public_url + '" target="_blank">');

                }
                else {
                    $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                    $("#div-" + appli).addClass('div-uninstalled');
                    $(".start-stop-button-" + appli).hide();
                    $("#nomAppli-" + appli).unwrap().wrap('<a>');
                    $("#version-" + appli).html("Application non installée");
                }
            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        }

    });

}

$(document).ready(function() {

    $(".check_install").click(function() {
        if ($('#myCheck').is(':checked')) {
            $("#text").show();
            $("#subdomain").show();
        }
        else {
            $("#text").hide();
            $("#subdomain").hide();
        }
    });

    $(".plex_install").click(function() {
        if ($('#plex').is(':checked')) {
            $("#formBlockPlex").show();
        }
        else {
            $("#formBlockPlex").hide();
        }
    });

    $(".cloudflare_install").click(function() {
        if ($('#cloud').is(':checked')) {
            $("#formBlockCloudflare").show();
        }
        else {
            $("#formBlockCloudflare").hide();
        }
    });
    $(".oauth_install").click(function() {
        if ($('#oauth').is(':checked')) {
            $("#formBlockOauth").show();
        }
        else {
            $("#formBlockOauth").hide();
        }
    });


    $(".install-modal").click(function() {
        $('#seedbox').modal('show');
    });

    $(".rclone-modal").click(function() {
        $('#rclone_token').modal('show');
    });

    $(".affichage-modal").click(function() {
        let appli = $(this).attr("data-appli");
        if ($("#status-" + appli).html() === "Installer") {
            $("#nomappliencours").html(appli);
            $("#validation_install_appli").attr('data-appli', appli);
            $('#modalPoll').modal('show');
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

    // on va créer un compte utilisateur

    $("#validation").click(function() {
        $("#formUserConfigure").validate({
            // 
            // on ajoute les règles (rules)
            // de vérification du formulaire
            //
            rules: {
                utilisateur: {
                    required: true
                },
                passe: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                domaine: {
                    required: true
                },
                idplex: {
                    required: {
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    }

                },
                passplex: {
                    required: {
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    },
                },
                idcloud: {
                    required: {
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    },
                },
                passcloud: {
                    required: {
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    },
                },
                idoauth: {
                    required: {
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    },
                },
                clientoauth: {
                    required:{
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    },
                },
                mailoauth: {
                    required: {
                        depends: function(element) {
                            return $("#plex").is(":checked");
                        }
                    },
                },

            },
            // 
            // une fois ces règles validées, on définit l'action
            // de validation de ce formulaire
            //
            submitHandler: function(form) {
                if ($("#utilisateur").val() !== "") {
                    console.log('l\'utilisateur n est pas vide');
                    var utilisateur = $("#utilisateur").val();
                    console.log('l\'utlisateur a la valeur ' + utilisateur);
                    var passe = $("#passe").val();
                    console.log('Mot de passe a la valeur success ');
                    var email = $("#email").val();
                    console.log('Email a la valeur success ');
                    var domaine = $("#domaine").val();
                    console.log('Domaine a la valeur success ');
                    var idplex = $("#idplex").val();
                    console.log('ID Plex a la valeur success ');
                    var passplex = $("#passplex").val();
                    console.log('Pass Plex a la valeur success ');
                    var idcloud = $("#idcloud").val();
                    console.log('Id cloudflare a la valeur success ');
                    var passcloud = $("#passcloud").val();
                    console.log('Pass cloudflare a la valeur success ');
                    var idoauth = $("#idoauth").val();
                    console.log('ID OAuth a la valeur success ');
                    var clientoauth = $("#clientoauth").val();
                    console.log('Client OAuth a la valeur success ');
                    var mailoauth = $("#mailoauth").val();
                    console.log('Mails OAuth a la valeur ' + mailoauth);
                    $.ajax({
                        method: "GET",
                        url: "ajax/install_seedbox.php", // on met les data en form plus visible
                        data: {
                            utilisateur: utilisateur,
                            passe: passe,
                            email: email,
                            domaine: domaine,
                            idplex: idplex,
                            passplex: passplex,
                            idcloud: idcloud,
                            passcloud: passcloud,
                            idoauth: idoauth,
                            clientoauth: clientoauth,
                            mailoauth: mailoauth,
                        },
                        // et on dit qu'on attend du json
                        dataType: "json"
                    }).done(function(data) {

                        // On est dans le done
                        // on a maintenant un tableau json qui est déjà "lu" par javascript
                        // dans la variable data
                        //
                        // on regarde si on a un bon retour
                        if (data.verif === true) {
                            console.log(data);
                            console.log('A priori tout est ok');
                            console.log(data.commande);
                            /**
                             * Ici il faut faire les actions de réussite
                             */
                            $('#seedbox').modal('hide');
                            $('#rclone').modal('show');
                            toastr.success('Installation lancée');
                        }
                        else {
                            console.log(data);
                            /**
                             * A priori un truc s'est mal passé
                             *
                             */

                            $.each(data.detail, function(key, value) {
                                console.log("key " + key + " = " + value);
                                if (value === false) {
                                    $("#" + key).addClass("error");
                                }
                            });
                            toastr.warning('Manque informations');
                            console.log("terminé");
                        }
                    }).fail(function() {
                        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                    });
                }
                else {
                    toastr.warning('Merci de remplir le nom utilisateur');
                    $("#utilisateur").addClass("error");
                    console.log('l\'utilisateur est VIDE !');
                }
            }
        });
    });

    // on va intercepter le click sur le bouton rclone
    $("#rclone_install_appli").click(function() {
            if ($("#client").val() !== "") {
                console.log('client n est pas vide');
                var client = $("#client").val();
                console.log('client a la valeur ' + client);
                var secret = $("#secret").val();
                console.log('secret a la valeur ' + secret);
            }
            else {
                console.log('client est VIDE !');
            }
            $.ajax({
                url: "ajax/install_rclone.php?client=" + client + "&secret=" + secret
            }).done(function(data) {
                // On est dans le done, tout est ok
                // la requête est passée
                // console.log("result " + data);
                $('#rclone').modal('hide');
                $('#rclone_token').modal('show');
                window.open('https://accounts.google.com/o/oauth2/auth?client_id=' + client + '&redirect_uri=urn:ietf:wg:oauth:2.0:oob&scope=https://www.googleapis.com/auth/drive&response_type=code', '_blank');
            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            });
    });

    // on va intercepter le click sur le bouton sur le modal rclone
    $("#rclone_token_valid").click(function() {
            if ($("#token").val() !== "") {
                console.log('token n est pas vide');
                var token = $("#token").val();
                console.log('token a la valeur ' + token);
                var drive = $('input[type=radio][name=drive]:checked').attr('value');
                console.log('drive a la valeur ' + drive);
                var drivename = $("#drivename").val();
                console.log('drivename a la valeur ' + drivename);
            }
            else {
                console.log('TOKEN est VIDE !');
            }
            $.ajax({
                url: "ajax/install_token.php?token=" + token + "&drive=" + drive + "&drivename=" + drivename
            }).done(function(data) {
                // On est dans le done, tout est ok
                // la requête est passée
               $('#rclone_token').modal('hide');
                console.log("result " + data);
            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            });
    });

    // on va intercepter le click sur le bouton status
    $(".bouton-install").click(function() {
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
            else {
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

                // on met à jour les infos de la div
                $("#div-" + appli).attr("data-installed", 1).removeClass('div-uninstalled');
                // on rafraichit les applis
                test_etat();
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

    // récupération des version
    $(".divappli").each(function() {

        let appli = $(this).attr('data-appli');
        // on met la bonne image
        $("#logo-" + appli).attr("src", "https://www.scriptseedboxdocker.com/wp-content/uploads/icones/" + appli + ".png");
        $.ajax({
            url: "ajax/check_version.php?service=" + appli,
        }).done(function(data) {
            $("#version-" + appli).html(data);
        }).fail(function() {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#version-".appli).html("erreur ajax");
        });
        // est-ce que le service tourne ?

    });

    /* fonction de refresh automatique  */
    window.setInterval(function() {
        test_etat();
    }, 15000); // timer en ms

    // statut du serveur
    $.ajax({
        url: "ajax/system_release.php",
    }).done(function(data) {
        $("#server-version").html(data);

    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#server-version").html("Erreur ajax");
    });
    // uptime
    $.ajax({
        url: "ajax/uptime.php",
    }).done(function(data) {
        $("#uptime").html(data);

    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#uptime").html("Erreur ajax");
    });
    // disque libre
    $.ajax({
        url: "ajax/disque.php",
    }).done(function(data) {
        $("#free-disk").html(data);

    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#free-disk").html("Erreur ajax");
    });
    // etat des vignettes d'appli

    test_etat();
    // on met à blanc les valeurs
    $("#subdomain").val('');
    $("#myCheck").prop("checked", false);
    // on va supprimer les vieux fichiers de logs
    $.ajax({
        url: "ajax/delete_old_logs.php",
    }).done(function(data) {
        if(data == 'ok')
        {
            console.log('Vieux logs effacés');
        }

    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');

    });
     $('[data-toggle="tooltip"]').tooltip();
});
