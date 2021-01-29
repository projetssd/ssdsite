/* global $ */
/* global taostr */

/**
 * Cette fonction déclare juste ce qui est obligatoire pour le formulaire
 */


$("#install-validation").click(function() {
    console.log("Clic sur suivant");
    // on regarde sur quel step on est
    var step = $(this).attr('data-step');
    console.log("Step " + step);
    switch (step) {

        case "1":
            $("#form_modal_rclone_etape1").validate({
                rules: {
                    client: {
                        required: true
                    },
                    secret: {
                        required: true
                    }
                }
            });
            if ($("#form_modal_rclone_etape1").valid()) {
                console.log('client n est pas vide');
                var client = $("#client").val();
                console.log('client a la valeur ' + client);
                var secret = $("#secret").val();
                console.log('secret a la valeur ' + secret);

                $.ajax({
                    url: "ajax/install_rclone.php",
                    method: "POST",
                    data: { client: client, secret: secret }
                }).done(function(data) {
                    // On est dans le done, tout est ok
                    // la requête est passée
                    // console.log("result " + data);
                    window.open('https://accounts.google.com/o/oauth2/auth?client_id=' + client + '&redirect_uri=urn:ietf:wg:oauth:2.0:oob&scope=https://www.googleapis.com/auth/drive&response_type=code', '_blank');
                }).fail(function() {
                    console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                });

                $("#link-gdrive-tab").removeClass("disabled");
                $('#tab-instsall-navigation a[href="#tab-gdrive"]').tab('show');
                $("#install-validation").attr('data-step', 2);
                $("#link-rclone-tab").addClass("disabled");
                $("#install-validation").html("Installation rclone");
            }
            else{
                toastr.warning('Merci de VERIFIER la saisie de l\'id client et\/ou de l\'id secret');
                console.log('le client ou le secret sont VIDES !')
            }


            break;

        case "2":
             $("#form_modal_rclone_etape2").validate({
                rules: {
                    drivename: {
                        required: true
                    },
                    token: {
                        required: true
                    }
                }
            });

             if ($("#form_modal_rclone_etape2").valid()) { 
                console.log('token n est pas vide');
                var token = $("#token").val();
                console.log('token a la valeur ' + token);
                var drive = $('input[type=radio][name=drive]:checked').attr('value');
                console.log('drive a la valeur ' + drive);
                var drivename = $("#drivename").val();
                console.log('drivename a la valeur ' + drivename);
                $('#seedbox').modal('hide');
                $.ajax({
                    url: "ajax/install_token.php",
                    method: "POST",
                    data: { token: token, 
                        drive: drive,
                        drivename: drivename}
                }).done(function(data) {
                    // On est dans le done, tout est ok
                    // la requête est passée
                    console.log("result " + data);
                }).fail(function() {
                    console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                });

            }
            else {
                toastr.warning('Merci de VERIFIER la saisie du token\/nom du drive');

                console.log('le token ou le nom de drive sont VIDES !')
            }

            break;
    }
});
