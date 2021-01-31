/* global $ */

function refresh_logs() {
    $(".div_detail_log").hide();
    $.ajax({
        url: "ajax/get_logs.php",
        dataType: "json"
    }).done(function(data) {
        $.each(data, function(i, item) {
            $("#div_log_" + i).show();
            $("#a_detail_log_" + i).attr('data-logfile', item.nomfichier);
            $("#detail_log_" + i).html(item.action + " - " + item.appli);
            $("#date_log_" + i).html('Le ' + item.date + " à " + item.heure);
            $("#div_log_" + i).show();
        });
    }).fail(function() {
        console.log('Erreur sur le chargement de l\'ajax, impossible de continuer');
    });
}

$(document).ready(function() {
    refresh_logs()



    $(".link_detail_log").click(function(event) {
        var logfile = $(this).attr('data-logfile');
        event.preventDefault();

        $.ajax({
            url: "ajax/detail_log.php?logfile=" + logfile,
        }).done(function(data) {
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
