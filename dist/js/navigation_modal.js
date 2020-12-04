/* global $ */

$("#install-validation").click(function() {
    // on regarde sur quel step on est
    var step = $(this).attr('data-step');
    switch (step) {
        case "1":
            // c'est ici qu'on va lancer les ajax
            //
            // voici un exemple : lignes 146 à 309 du ssd_specific.js 
            //
            
            // cette commande permet d'activer le lien pour le tab suivant
            $("#link-rclone-tab").removeClass("disabled");
            // on met l'étape à la valeur suivante
            $("#install-validation").attr('data-step', 2);
            // On montre la tab suivante
            $('#tab-instsall-navigation a[href="#tab-rclone"]').tab('show');
            // on désactive le lien pour la tab sur laquelle on vient de cliquer
            $("#link-home-tab").addClass("disabled");
            // on est juste dans la première étape, donc on affiche maintenant le bouton retour
            $("#retour-validation").show();
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