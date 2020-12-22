#!/bin/bash
########################################
# Gestion des services SSD
########################################

function writelog()
{
  #######################################
  # Fonction uniquement pour des logs
  # de debug
  # ces logs ne seront pas visibles par
  # l'utilisateur 
  #######################################
  DIRNAME=$(dirname $0)
  DATE=$(date +"%d/%m/%Y %T")
  DATELOG=$(date +"%Y%m%d")
  if [ $# -eq 1 ]
  then
    MESSAGE=$1
    TYPE='INFO'
  else
    MESSAGE=$1
    TYPE=$2
  fi
  echo "SHELL : ${DATE} - ${TYPE} - ${MESSAGE}" >> ${DIRNAME}/../logs/ssdsite-${DATELOG}.log
}

function log_applicatif()
{
  # On va créer une variable LOGFILE_APPLI
  # de type
  # DATE-HEURE-ACTION-APPLI.log
  # ACTION => install, restart, etc...
  # APPLI => nom de l'appli ou de l'action (install rclone ?)
  # DATE: YMD (ex : 20201025 pour le 25/10/2020)
  # HEURE : HMS (ex: 162548 pour 16h25m48s)
  ####################################################
  # C'est à la fonction appelante de remplir ce log 
  ####################################################
  DIRNAME=$(dirname $0)
  DATELOG=$(date +"%Y%m%d-%H%M%S")
  LOGFILE_APPLI="${DIRNAME}/../logs/${DATELOG}-${ACTION}-${1}.log"
}

function writelog_appli()
{
   DATE=$(date +"%d/%m/%Y %T")
   echo "${DATE} - ${1}" >> ${LOGFILE_APPLI}
}

function tools() 
{
 writelog_appli "Installation $1"
 log_applicatif Install$1
  
 LOGFILE=${LOGFILE_APPLI}

 ansible-playbook /opt/seedbox-compose/includes/config/roles/$1/tasks/main.yml | tee -a $LOGFILE
 writelog_appli "Installation $1 terminée"
}

function credential() {
  mkdir -p /opt/seedbox/rclone
  echo $1 > /opt/seedbox/rclone/client
  echo $2 > /opt/seedbox/rclone/secret
}

function createtoken() {
  # on appelle la fonction pour avoir le nom du log à créer
  log_applicatif CreateToken
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Création d'un token" 
  logfile=/opt/seedbox/rclone/log

  logfile=/opt/seedbox/rclone/log
  curl https://rclone.org/install.sh | sudo bash
  client=$(cat /opt/seedbox/rclone/client)
  secret=$(cat /opt/seedbox/rclone/secret)
  curl --request POST --data "code=$1&client_id=$client&client_secret=$secret&redirect_uri=urn:ietf:wg:oauth:2.0:oob&grant_type=authorization_code" https://accounts.google.com/o/oauth2/token | sudo tee $logfile 2>/dev/null >/dev/null &

  sleep 2
  accesstoken=$(cat $logfile | grep access_token | awk '{print $2}')
  refreshtoken=$(cat $logfile | grep refresh_token | awk '{print $2}')
  rcdate=$(date +'%Y-%m-%d')
  rctime=$(date +"%H:%M:%S" --date="$givenDate 60 minutes")
  rczone=$(date +"%:z")
  final=$(echo "${rcdate}T${rctime}${rczone}")
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > /opt/seedbox/rclone/chaine)
  chaine=$(cat /opt/seedbox/rclone/chaine)

if [[ "$2" == "sharedrive" ]]; then 
writelog_appli "Création d'un shared drive" 
  curl --request POST \
    "https://www.googleapis.com/drive/v3/teamdrives?requestId='$chaine" \
    --header "Authorization: Bearer ${accesstoken}" \
    --header 'Accept: application/json' \
    --header 'Content-Type: application/json' \
    --data '{"name":"'$3'","backgroundImageLink":"https://pgblitz.com/styles/io_dark/images/pgblitz4.png"}' \
    --compressed > /opt/seedbox/rclone/teamdrive

  ###récupération des variables
  cat /opt/seedbox/rclone/teamdrive | grep "id" | awk '{ print $2 }' | cut -c2- | rev | cut -c3- | rev > /opt/seedbox/rclone/teamdrive.id
  cat /opt/seedbox/rclone/teamdrive | grep "name" | awk '{ print $2 }' | cut -c2- | rev | cut -c2- | rev > /opt/seedbox/rclone/teamdrive.name
  name=$(sed -n ${typed}p /opt/seedbox/rclone/teamdrive.name)
  id=$(sed -n ${typed}p /opt/seedbox/rclone/teamdrive.id)
  echo "$name" > /opt/seedbox/rclone/pgclone.teamdrive
  echo "$id" > /opt/seedbox/rclone/pgclone.teamid
  teamid=$(cat /opt/seedbox/rclone/pgclone.teamid)

  ## Creation rclone.conf
  writelog_appli "Création rclone.conf" 
  echo "" >> /root/.config/rclone/rclone.conf
  echo "[$name]" >> /root/.config/rclone/rclone.conf
  echo "client_id = $client" >> /root/.config/rclone/rclone.conf
  echo "client_secret = $secret" >> /root/.config/rclone/rclone.conf
  echo "type = drive" >> /root/.config/rclone/rclone.conf
  echo "scope = drive" >> /root/.config/rclone/rclone.conf
  echo -n "token = {\"access_token\":${accesstoken}\"token_type\":\"Bearer\",\"refresh_token\":${refreshtoken}\"expiry\":\"${final}\"}" >> /root/.config/rclone/rclone.conf
  echo "" >> /root/.config/rclone/rclone.conf
  echo "team_drive = $teamid" >> /root/.config/rclone/rclone.conf
  echo ""

  ## creation du crypt
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > /opt/seedbox/rclone/password)
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > /opt/seedbox/rclone/salt)

  PASSWORD=`cat /opt/seedbox/rclone/password`
  SALT=`cat /opt/seedbox/rclone/salt`
  ENC_PASSWORD=`rclone obscure "$PASSWORD"`
  ENC_SALT=`rclone obscure "$SALT"`
  crypt="_crypt"

  echo "" >> /root/.config/rclone/rclone.conf
  echo "[$name$crypt]" >> /root/.config/rclone/rclone.conf
  echo "type = crypt" >> /root/.config/rclone/rclone.conf
  echo "remote = $name:/Medias" >> /root/.config/rclone/rclone.conf
  echo "filename_encryption = standard" >> /root/.config/rclone/rclone.conf
  echo "directory_name_encryption = true" >> /root/.config/rclone/rclone.conf
  echo "password = $ENC_PASSWORD" >> /root/.config/rclone/rclone.conf
  echo "password2 = $ENC_SALT" >> /root/.config/rclone/rclone.conf

else
   writelog_appli "Pas de shared drive" 
  ###récupération des variables

  ## Creation rclone.conf
   writelog_appli "Création rclone.conf" 
  echo "" >> /root/.config/rclone/rclone.conf
  echo "[$3]" >> /root/.config/rclone/rclone.conf
  echo "client_id = $client" >> /root/.config/rclone/rclone.conf
  echo "client_secret = $secret" >> /root/.config/rclone/rclone.conf
  echo "type = drive" >> /root/.config/rclone/rclone.conf
  echo "scope = drive" >> /root/.config/rclone/rclone.conf
  echo -n "token = {\"access_token\":${accesstoken}\"token_type\":\"Bearer\",\"refresh_token\":${refreshtoken}\"expiry\":\"${final}\"}" >> /root/.config/rclone/rclone.conf
  echo "" >> /root/.config/rclone/rclone.conf
  echo ""

 ## creation du crypt

  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > /opt/seedbox/rclone/password)
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > /opt/seedbox/rclone/salt)

  PASSWORD=`cat /opt/seedbox/rclone/password`
  SALT=`cat /opt/seedbox/rclone/salt`
  ENC_PASSWORD=`rclone obscure "$PASSWORD"`
  ENC_SALT=`rclone obscure "$SALT"`
  crypt="_crypt"

  echo "" >> /root/.config/rclone/rclone.conf
  echo "[$3$crypt]" >> /root/.config/rclone/rclone.conf
  echo "type = crypt" >> /root/.config/rclone/rclone.conf
  echo "remote = $3:/Medias" >> /root/.config/rclone/rclone.conf
  echo "filename_encryption = standard" >> /root/.config/rclone/rclone.conf
  echo "directory_name_encryption = true" >> /root/.config/rclone/rclone.conf
  echo "password = $ENC_PASSWORD" >> /root/.config/rclone/rclone.conf
  echo "password2 = $ENC_SALT" >> /root/.config/rclone/rclone.conf

fi
 writelog_appli "Installation rclone"
 log_applicatif InstallRclone
 # maintenant, on a la variable LOGFILE_APPLI utilisable
 writelog_appli "Installation rclone"    
  
 LOGFILE=${LOGFILE_APPLI}

 ansible-playbook /opt/seedbox-compose/includes/config/roles/rclone/tasks/main.yml | tee -a $LOGFILE
 rm -rf /opt/seedbox/rclone > /dev/null 2>&1
 writelog_appli "Terminé" 
}

function configure() {
  log_applicatif ConfigureSeedbox
  ACCOUNT=/opt/seedbox/variables/account.yml
  writelog_appli "recuperation token plex" 
  # recuperation token plex
  curl -qu "$5":"$6" 'https://plex.tv/users/sign_in.xml' \
      -X POST -H 'X-Plex-Device-Name: PlexMediaServer' \
      -H 'X-Plex-Provides: server' \
      -H 'X-Plex-Version: 0.9' \
      -H 'X-Plex-Platform-Version: 0.9' \
      -H 'X-Plex-Platform: xcid' \
      -H 'X-Plex-Product: Plex Media Server'\
      -H 'X-Plex-Device: Linux'\
      -H 'X-Plex-Client-Identifier: XXXX' --compressed >/tmp/plex_sign_in
  token=$(sed -n 's/.*<authentication-token>\(.*\)<\/authentication-token>.*/\1/p' /tmp/plex_sign_in)

  #openssl OAuth
  openssl=$(openssl rand -hex 16)

  # creation utilisateur
  writelog_appli "Création utilisateur" 
  useradd -m $1 -s /bin/bash
  usermod -aG docker $1
  passwd $2
  chsh -s /bin/bash $2
  chown -R $1:$1 /home/$1
  chmod 755 /home/$1
  userid=$(id -u $1)
  grpid=$(id -g $1)
  htpasswd -c -b /tmp/.htpasswd $1 $2 > /dev/null 2>&1
  htpwd=$(cat /tmp/.htpasswd)

  # Mise en place du fichier account.yml
   writelog_appli "Mise en place du fichier account.yml" 
  cp /opt/seedbox-compose/includes/config/account.yml $ACCOUNT
  echo $2 > ~/.vault_pass
  echo "vault_password_file = ~/.vault_pass" >> /etc/ansible/ansible.cfg

  #incrementation des variables dans account.yml
  sed -i "s/name:/name: $1/
          s/pass:/pass: $2/
          s/userid:/userid: $userid/
          s/groupid:/groupid: $grpid/
          s/group:/group: $1/
          s/mail:/mail: $3/
          s/domain:/domain: $4/
          s/ident:/ident: $5/
          s/sesame:/sesame: $6/
          s/token:/token: $token/
          s/login:/login: $7/
          s/api:/api: $8/
          s/client:/client: $9/
          s/secret:/secret: ${10}/
          s/account:/account: ${11}/
          s/openssl:/openssl: $openssl/
          /htpwd:/c\   htpwd: $htpwd" $ACCOUNT
}

function uninstall() {
  log_applicatif $1
  ansible-playbook /opt/seedbox-compose/includes/dockerapps/templates/ansible/ansible.yml >> ${LOGFILE_APPLI}
  ansible-vault decrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
  USER=$(cat /tmp/name)

  echo 0 > /opt/seedbox/status/$1
  sed -i "/$1/d" /opt/seedbox/variables/account.yml > /dev/null 2>&1
  sed -i "/$1/d" /opt/seedbox/resume > /dev/null 2>&1
  docker rm -f "$1" > /dev/null 2>&1
  rm /opt/seedbox/conf/$1.yml > /dev/null 2>&1

  if docker ps | grep -q db-$1; then
  docker rm -f db-$1 > /dev/null 2>&1
  fi

  case $1 in
     seafile)
       docker rm -f db-seafile memcached > /dev/null 2>&1
     ;;
     varken)
       docker rm -f influxdb telegraf grafana > /dev/null 2>&1
       rm -rf /opt/seedbox/docker/$USER/telegraf
       rm -rf /opt/seedbox/docker/$USER/grafana
       rm -rf /opt/seedbox/docker/$USER/influxdb
     ;;
     jitsi)
      docker rm -f prosody jicofo jvb
      rm -rf /opt/seedbox/docker/$USER/.jitsi-meet-cfg
     ;;
     nextcloud)
       docker rm -f collabora coturn office
       rm -rf /opt/seedbox/docker/$USER/coturn
     ;;
     rtorrentvpn)
       rm /opt/seedbox/conf/rutorrent-vpn.yml
     ;;
     *)
     # writelog "ACTION INDEFINIE" 'DEBUG' 
      rm -rf /opt/seedbox/docker/$USER/$1
     ;;
  esac

  docker system prune -af > /dev/null 2>&1
  docker volume rm $(docker volume ls -qf "dangling=true") > /dev/null 2>&1
  ansible-vault encrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
}

function install() {
  # on appelle la fonction pour avoir le nom du log à créer
  log_applicatif $1
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Installation de l'appli ${1}"    
  
  LOGFILE=${LOGFILE_APPLI}
  #rm -f $LOGFILE

  source /opt/seedbox-compose/includes/variables.sh
    
  ansible-playbook /opt/seedbox-compose/includes/dockerapps/templates/ansible/ansible.yml
  ansible-vault decrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
    
  domain=$(cat /tmp/domain)
    
  if [[ ! -d "$CONFDIR/conf" ]]; then
    mkdir -p $CONFDIR/conf > /dev/null 2>&1
  fi

  grep "sub" /opt/seedbox/variables/account.yml > /dev/null 2>&1
  if [ $? -eq 1 ]; then
    sed -i '/transcodes/a sub:' /opt/seedbox/variables/account.yml 
  fi

  if [ $2 != "undefined" ]; then
    sed -i "/$1/d" /opt/seedbox/variables/account.yml > /dev/null 2>&1
    sed -i "/sub/a \ \ \ $1: $2" /opt/seedbox/variables/account.yml > /dev/null 2>&1
  fi
    
  ## Installation
  if [ -e "/opt/seedbox/conf/$1.yml" ]; then
    ansible-playbook "$CONFDIR/conf/$1.yml" | tee -a $LOGFILE
  elif [[ "$1" == "plex" ]]; then
    ansible-playbook /opt/seedbox-compose/includes/config/roles/plex/tasks/main.yml
    cp "/opt/seedbox-compose/includes/config/roles/plex/tasks/main.yml" "$CONFDIR/conf/$1.yml" > /dev/null 2>&1
  elif [[ "$1" == "mattermost" ]]; then
    /opt/seedbox-compose/includes/dockerapps/templates/mattermost/mattermost.sh
  else
    ansible-playbook "$BASEDIR/includes/dockerapps/$1.yml" | tee -a $LOGFILE
    cp "$BASEDIR/includes/dockerapps/$1.yml" "$CONFDIR/conf/$1.yml" > /dev/null 2>&1
  fi

  # mise à jour du fichier "/opt/seedbox/resume"
  grep $1 /opt/seedbox/variables/account.yml > /dev/null 2>&1
  if [ $? -eq 0 ]; then
    fqdn="$2.$domain"
    echo "$1 = $fqdn" | tee -a /opt/seedbox/resume  > /dev/null
  else
    fqdn="$1.$domain"
    echo "$1 = $fqdn" | tee -a /opt/seedbox/resume  > /dev/null
  fi
  fqdn=""
  ansible-vault encrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
    
    					tee -a $LOGFILE <<-EOF
    
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
                       Installation de $1 effectuée avec succés
    
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
EOF

}

DIRNAME=$(dirname $0)

writelog "Lancement du script" "DEBUG"
ACTION=$1
writelog "Action = ${ACTION}" "DEBUG"
case $ACTION in
  install) 
    install $2 $3
  ;;
  uninstall)
    uninstall $2
  ;;
  credential)
    credential $2 $3
  ;;
  createtoken)
    createtoken $2 $3 $4
  ;;
  tools)
    tools $2
  ;;
  *)
  writelog "ACTION INDEFINIE" 'DEBUG' 
  echo "Action indéfinie"
  ;;
esac