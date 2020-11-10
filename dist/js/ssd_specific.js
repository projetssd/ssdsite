/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
$(document).ready(function () {
    // on va d'abord mettre les bons textes sur les bons boutons...
    $(".bouton-install").each(function() {
        let appli = $(this).attr("data-appli");
        $.ajax({
            url: "http://178.170.54.173/ajax/check_service.php?service=" + appli
            // appel simple, en GET
            // on peut rajouter des options si besoin pour du POST
        }).done(function (data) {
            // On est dans le done, tout est ok
            // la requête est passée
            // le résultat de la requête est maintenant dans la variable "data"
            if (data === "ok") {
                // le service tourne
                $("#status-" + appli).html("Désinstaller");
                $(".start-stop-button-" + appli).show();
            } else {
                // le service ne tourne pas
                $("#status-" + appli).html("Installer");
                $(".start-stop-button-" + appli).hide();
            }
        }).fail(function () {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
            $("#status-" + appli).html("Erreur ajax");
        });
    });


    // on va maintenant intercepter le click sur le bouton status-radarr
    $(".bouton-install").click(function () {
        let appli = $(this).attr("data-appli");
        console.log("Appli appelée " + appli)
        // on va considérer que le texte du bouton est ok
        // a voir si on refait un appel ajax pour vérifier ?
        if ($("#status-" + appli).html() === "Installer") {
            // on change le texte du bouton et on le met en disabled
            $("#status-" + appli).html("Installation...").prop('disabled', true);
            // on lance un ajax qui va installer tout ça
            $.ajax({
                url: "http://178.170.54.173/ajax/install_service.php?service=" + appli
            }).done(function (data) {
                // On est dans le done, tout est ok
                // la requête est passée
                // le résultat de la requête est maintenant dans la variable "data"
                // dont on ne fait rien

                // on change le texte du bouton et on le remet en enable
                $("#status-" + appli).html("Désinstaller").prop('disabled', false);
                // on afficher les boutons start/stop
                $(".start-stop-button-" + appli).show();
                // on affiche les logs
                // il suffit d'afficher la dic modalYT1 qui contient déjà un iframe de défilement des logs
                $('#modalYT1').modal('show');

            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });

        } else if ($("#status-" + appli).html() === "Désinstaller") {
            $("#status-" + appli).html("Désinstallation...").prop('disabled', true);
            $.ajax({
                url: "http://178.170.54.173/ajax/uninstall_service.php?service=" + appli
            }).done(function (data) {
                // On est dans le done, tout est ok
                // la requête est passée
                // le résultat de la requête est maintenant dans la variable "data"
                $("#status-" + appli).html("Installer").prop('disabled', false);
                // on afficher les boutons start/stop
                $(".start-stop-button-" + appli).hide();
                // on affiche les logs
                // il suffit d'afficher la dic modalYT1 qui contient déjà un iframe de défilement des logs
                $('#modalYT1').modal('show');
            }).fail(function () {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });
        } else {
            console.log('Erreur sur le texte du bouton, impossible de continuer');
        }

    });


});