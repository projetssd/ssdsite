/* global $ */

function refresh_logs() {
    $.ajax({
        url: "ajax/get_logs.php",
        dataType: "json"
    }).done(function(data) {
        console.log(data);
        $.each(data, function(i, item) {
            console.log("boucle " + i);
            $("#detail_log_" + i).attr('data-logfile', item.nomfichier).html(item.action + " - " + item.appli)
            $("#date_log_" + i).html('Le ' + item.date + " à " + item.heure);
            $("#div_log_" + i).show();
        });
    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
    });
}

$(document).ready(function() {
   refresh_logs()



   $(".link_detail_log").click( function(event) {
       var id = $(this).attr('id');
       var logfile = $(this).attr('data-logfile');
        event.preventDefault();
        
        console.log("clic sr " + id);
        console.log('Logfile = ' + logfile);
        $.ajax({
            url: "ajax/detail_log.php?logfile=" + logfile,
        }).done(function(data) {
            //  console.log(data);
            $("#detail_log").html(data)
                
                $('#modal_logs').modal('show');
        }).fail(function() {
            console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
        });
    });
});

    /* fonction de refresh automatique  */
    window.setInterval(function() {
        refresh_logs();
    }, 15000); // timer en ms
