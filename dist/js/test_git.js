/* global $ */
/* global toastr */
$(document).ready(function() {
    $.ajax({
        url: "ajax/check_git.php",
        dataType: "json"
    }).done(function(data) {
        console.log(data);
        if(data.MAJAFAIRE !== false)
        {
            var message = data.MESSAGE;
            if(data.BRANCH === 'main')
            {
                message = message + '<br /><a class="majgit" href="#">Cliquez ici pour faire une mise à jour</a>';
            }
            toastr.warning(message);
        }

    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax GIT, impossible de continuer');
    });
    
    function hasClass(elem, className) {
        return elem.className.split(' ').indexOf(className) > -1;
    }
    
    
    document.addEventListener('click', function (e) {
    if (hasClass(e.target, 'majgit')) {
        console.log("mise à jour en cours");
        toastr.warning("Mise à jour de l'application en cours, merci de patienter");
        $(".overlay").show();
         $.ajax({
                url: "ajax/maj_git.php"
            }).done(function(data) {
                if(data == 'ok')
                {
                    toastr.sucess("Mise à jour terminée");
                }
                else
                {
                    toastr.error("Une erreur est survenue pendant la mise à jour");
                }
                 $(".overlay").hide();
            }).fail(function() {
                $(".overlay").hide();
                console.log('Erreur sur le chargement de l\'ajax de mise à jour, impossible de continuer');
            });
    } 
    }, false);

    
    
});
