/* global $ */
$(document).ready(function() {
    $.ajax({
        url: "ajax/check_git.php",
        dataType: "json"
    }).done(function(data) {
        console.log(data);
        if(data.MAJAFAIRE !== false)
        {
            toastr.warning(data.MESSAGE);
        }

    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax GIT, impossible de continuer');
    });
});
