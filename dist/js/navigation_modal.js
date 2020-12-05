/* global $ */

/**
 * Cette fonction déclare juste ce qui est obligatoire pour le formulaire
 */


$("#formUserConfigure").validate({
    debug: true,
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
            },
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
            required: {
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
    }
});


$("#install-validation").click(function() {
    console.log("Clic sur suivant");
    // on regarde sur quel step on est
    var step = $(this).attr('data-step');
    console.log("Step " + step);
    switch (step) {
        case "1":
            console.log("On est dans le step1");
            if ($("#formUserConfigure").valid()) {
                // on vérifie que le formulaire est valide (voir fonction tout en haut)
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
                            $('#seedbox').modal('hide');
                            $('#rclone').modal('show');
                            toastr.success('Installation lancée');
                        }
                        else {
                            console.log(data);
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


                $("#link-rclone-tab").removeClass("disabled");
                // on met l'étape à la valeur suivante
                $("#install-validation").attr('data-step', 2);
                // On montre la tab suivante
                $('#tab-instsall-navigation a[href="#tab-rclone"]').tab('show');
                // on désactive le lien pour la tab sur laquelle on vient de cliquer
                $("#link-home-tab").addClass("disabled");
                // on est juste dans la première étape, donc on affiche maintenant le bouton retour
                $("#retour-validation").show();
            }
            //alert( "Valid: " + $("#formUserConfigure").valid() );

            //
            // voici un exemple : lignes 146 à 309 du ssd_specific.js 
            //

            // cette commande permet d'activer le lien pour le tab suivant

            break;

        case "2":
            $("#link-gdrive-tab").removeClass("disabled");
            $('#tab-instsall-navigation a[href="#tab-gdrive"]').tab('show');
            $("#install-validation").attr('data-step', 3);
            $("#link-rclone-tab").addClass("disabled");
            break;

    }
});

$("#retour-validation").click(function() {
    console.log("Clic sur retour");
    var step = $("#install-validation").attr('data-step');
    console.log("Etape " + step);
    switch (step) {
        case "2":
            $("#link-home-tab").removeClass("disabled");
            $("#install-validation").attr('data-step', 1);
            $('#tab-instsall-navigation a[href="#tab-utilisateur"]').tab('show');
            $("#link-rclone-tab").addClass("disabled");
            $("#retour-validation").hide();
            break;
        case "3":
            $("#link-rclone-tab").removeClass("disabled");
            $('#tab-instsall-navigation a[href="#tab-rclone"]').tab('show');
            $("#install-validation").attr('data-step', 2);
            $("#link-gdrive-tab").addClass("disabled");
            break;

    }
});
