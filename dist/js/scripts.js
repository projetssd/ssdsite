function myFunction() {
  if ($("#status").html() == "Installer")
    {
        $("#status").html("Desinstaller");
    }
    else
    {
        $("#status").html("Installer");
}

  // save the current label to localStorage via setItem()
  window.localStorage.setItem('btnLabel', $("#status").html());
}

// on page load, get the saved label from localStorage via getItem()

var btnLabel = window.localStorage.getItem('btnLabel');
if (btnLabel) {
  $("#status").html(btnLabel);
}

