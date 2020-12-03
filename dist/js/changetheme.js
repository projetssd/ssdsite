/* global $ */

$(".changetheme").click(function () {
    console.log("Changement de theme");
    var theme = $(this).attr('data-theme');
    console.log("Theme choisi " + theme);
    switch (theme) {
        case "adminlte":
            $('link[href="dist/css/dashboard.min.css"]').prop('disabled', true);
            $('link[href="dist/css/darkly.min.css"]').prop('disabled', true);
            $('link[href="dist/css/material.min.css"]').prop('disabled', true);
            $("#content-global").css("background","#f4f6f9");
            break;
        case "darkly":
            $('link[href="dist/css/dashboard.min.css"]').prop('disabled', true);
            $('link[href="dist/css/material.min.css"]').prop('disabled', true);
            //$('link[href="dist/js/adminlte.min.css"]').remove();
            $("#content-global").css("background","#000000");
            break;
        case "material":
            $('link[href="dist/css/dashboard.min.css"]').prop('disabled', true);
            $('link[href="dist/css/darkly.min.css"]').prop('disabled', true);
           // $('link[href="dist/js/adminlte.min.css"]').remove();
           $("#content-global").css("background","#f4f6f9");
            break;
        case "dashboard":
            $('link[href="dist/css/darkly.min.css"]').prop('disabled', true);
            $('link[href="dist/css/material.min.css"]').prop('disabled', true);
            //$('link[href="dist/css/adminlte.min.css"]').remove();
            $("#content-global").css("background","#f4f6f9");
            break;
    }
    $('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'dist/css/' + theme + '.min.css'));
});