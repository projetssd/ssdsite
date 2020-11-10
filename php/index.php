<?php
if (isset($_POST['submit']))
{
shell_exec("rm /var/www/seedboxdocker.website/logtail/log; sudo -u root /var/www/seedboxdocker.website/scripts/update.sh 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &");
header('Location: http://178.170.54.173/index.php?success=true');
}

if (isset($_POST['radarr']))
{
shell_exec("rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/radarr.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &");
header('Location: http://178.170.54.173/index.php?success=true');
}

if (isset($_POST['sonarr']))
{
shell_exec("rm /var/www/seedboxdocker.website/logtail/log; sudo -u root ansible-playbook /opt/seedbox-compose/includes/dockerapps/sonarr.yml 2>&1 | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &");
header('Location: http://178.170.54.173/index.php?success=true');
}

if (isset($_GET['reset'])) 
{
shell_exec("rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker restart radarr > /dev/null 2>&1; echo $([ $? -eq 0 ] && echo radarr lancé avec succès || echo Erreur dans le lancement de radarr) 2>&1  | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &");
header('Location: http://178.170.54.173/index.php?success=true');
 }

if (isset($_GET['stop'])) 
{
shell_exec("rm /var/www/seedboxdocker.website/logtail/log; sudo -u root docker stop radarr > /dev/null 2>&1; echo $([ $? -eq 0 ] && echo radarr stoppé avec succès || echo Erreur radarr) 2>&1  | tee -a /var/www/seedboxdocker.website/logtail/log 2>/dev/null >/dev/null &");
header('Location: http://178.170.54.173/index.php?success=true');
}
?>