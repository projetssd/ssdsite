<?php 
shell_exec("sudo -u root /var/www/seedboxdocker.website/scripts/container.sh");

$files = glob('/var/www/seedboxdocker.website/jsons/*.json', GLOB_BRACE);

foreach($files as $file){ ?>
    <?php 
    $json_data = file_get_contents("$file");
    $arr = json_decode($json_data, true);
    foreach($arr as $key => $value) {
    echo $key . ": " ." <td>";
    //key est le nom du container
    echo $arr[$key]["Status"] . " ";
    }
?>

<button type = "Submit" id="btSubmit" >Actif
<script>
//permet de desactiver le bouton
const button = document.getElementById('btSubmit');
button.disabled = true;
        if ($key = '<?php echo $key ?>;') {
            button.disabled = false;
            //change le label du bouton
            document.getElementById('btSubmit').innerHTML = 'Uninstall'; 
        }
</script>
</button>
<?php } ?>
