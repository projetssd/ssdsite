/* global $ */

$(".changetheme").click(function () {
    console.log("Changement de theme");
    var theme = $(this).attr('data-theme');
    console.log("Theme choisi " + theme);
    $("content-global").css("background", "");
    $('link[href="dist/css/darkly.min.css"]').prop('disabled', true);
    $('link[href="dist/css/material.min.css"]').prop('disabled', true);
    $('link[href="dist/css/circular.min.css"]').prop('disabled', true);
    $('link[href="dist/css/knightone.min.css"]').prop('disabled', true);
    /*if(theme === "darkly")
    {
        $("#content-global").css("background","#000000");
    }
    else
    {
        $("#content-global").css("background","#f4f6f9");
    }*/


    $('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'dist/css/' + theme + '.min.css'));
});