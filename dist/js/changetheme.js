/* global $ */

$(".changetheme").click(function () {
    console.log("Changement de theme");
    var theme = $(this).attr('data-theme');
    console.log("Theme choisi " + theme);
    switch (theme) {
        case "adminlte":
            $('link[href="dist/js/dashboard.min.css"]').remove();
            $('link[href="dist/js/darkly.min.css"]').remove();
            $('link[href="dist/js/material.min.css"]').remove();
            break;
        case "darkly":
            $('link[href="dist/js/dashboard.min.css"]').remove();
            $('link[href="dist/js/material.min.css"]').remove();
            $('link[href="dist/js/adminlte.min.css"]').remove();
            break;
        case "material":
            $('link[href="dist/js/dashboard.min.css"]').remove();
            $('link[href="dist/js/darkly.min.css"]').remove();
            $('link[href="dist/js/adminlte.min.css"]').remove();
            break;
        case "dashboard":
            $('link[href="dist/js/darkly.min.css"]').remove();
            $('link[href="dist/js/material.min.css"]').remove();
            $('link[href="dist/js/adminlte.min.css"]').remove();
            break;
    }
    $('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'dist/js' + theme + '.min.css'));
});