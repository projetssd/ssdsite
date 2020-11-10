/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
$(document).ready(function () {
    // on va d'abord mettre les bons textes sur les bons boutons...
    $.ajax({
        url: "http://178.170.54.173/ajax/check_service.php?service=radarr"
        // appel simple, en GET
        // on peut rajouter des options si besoin pour du POST
    }).done(function (data) {
        // On est dans le done, tout est ok
        // la requête est passée
        // le résultat de la requête est maintenant dans la variable "data"
        if (data === "ok") {
            // le service tourne
            $("#status-radarr").html("Désinstaller");
            $(".start-stop-button-radarr").show();
        } else {
            // le service tourne
            $("#status-radarr").html("Installer");
            $(".start-stop-button-radarr").hide();
        }
    }).fail(function () {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        $("#status-radarr").html("Erreur ajax");
    });


    // on va maintenant intercepter le click sur le bouton status-radarr
    $("#status-radarr").click(function () {
        // on va considérer que le texte du bouton est ok
        // a voir si on refait un appel ajax pour vérifier ?
        if ($("#status-radarr").html() === "Installer") {
            $("#status-radarr").html("Installation...").prop('disabled', true);
            // on lance un ajax qui va installer tout ça
            $.ajax({
                url: "http://178.170.54.173/ajax/install_service.php?service=radarr"
            }).done(function (data) {
                // On est dans le done, tout est ok
                // la requête est passée
                // le résultat de la requête est maintenant dans la variable "data"
                if (data === "ok") {
                    // le service a été installé
                    $("#status-radarr").html("Désinstaller").prop('disabled', false);
                    $(".start-stop-button-radarr").show();
                } else {
                    // il y a eu une erreur sur l'installation
                    $("#status-radarr").html("Erreur sur installation");
                    $(".start-stop-button-radarr").hide();
                }
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-radarr").html("Erreur ajax");
            });

        } else if ($("#status-radarr").html() === "Désinstaller") {
            $("#status-radarr").html("Désinstallation...").prop('disabled', true);
            $.ajax({
                url: "http://178.170.54.173/ajax/install_service.php?service=radarr"
            }).done(function (data) {
                // On est dans le done, tout est ok
                // la requête est passée
                // le résultat de la requête est maintenant dans la variable "data"
                if (data === "ok") {
                    // le service a été désinstallé
                    $("#status-radarr").html("Installer").prop('disabled', false);
                    $(".start-stop-button-radarr").hide();
                } else {
                    // il y a eu une erreur sur l'installation
                    $("#status-radarr").html("Erreur sur installation");
                    $(".start-stop-button-radarr").hide();
                }
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-radarr").html("Erreur ajax");
            });
        } else {
            console.log('Erreur sur le texte du bouton, impossible de continuer');
        }

    });


});