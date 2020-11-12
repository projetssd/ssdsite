/* le document.ready permet de faire en sorte que le js
ne s'éxécute que quand la page est totalement chargée
 */
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


    // on va maintenant intercepter le click sur le bouton status
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
                // le résultat de la requête est maintenant dans la variable "data"
                // dont on ne fait rien

                // on change le texte du bouton et on le remet en enable
                $("#status-" + appli).html("Désinstaller").removeClass("btn-success").addClass("btn-warning");
                // on afficher les boutons start/stop
                $(".start-stop-button-" + appli).show();
                // on affiche les logs
                // il suffit d'afficher la dic modalYT1 qui contient déjà un iframe de défilement des logs
                $('#modalYT1').modal('show');
                $("#div-" + appli).attr("data-installed", 1);
                // on retire la classe de la div
                $("#div-" + appli).removeClass('div-uninstalled');

            }).fail(function() {
                console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
                $("#status-" + appli).html("Erreur ajax");
            });

        }
        else if ($("#status-" + appli).html() === "Désinstaller") {
            $("#status-" + appli).html("Désinstallation...");
            $.ajax({
                url: "ajax/uninstall_service.php?service=" + appli
            }).done(function(data) {
                // On est dans le done, tout est ok
                // la requête est passée
                // le résultat de la requête est maintenant dans la variable "data"
                $("#status-" + appli).html("Installer").removeClass("btn-warning").addClass("btn-success");
                // on afficher les boutons start/stop
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

        $.ajax({
            url: "ajax/restart_service.php?service=" + appli
        }).done(function(data) {
          toastr.success("Redémarrage de " + appli + " en cours");
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
        }).done(function(data) {
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
                $("#"+iddiv).show();

            }
            else {
                $("#"+iddiv).hide();
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
});
