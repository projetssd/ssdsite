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
  log_applicatif $1
  writelog_appli "Installation $1"
  
  LOGFILE=${LOGFILE_APPLI}

  ansible-playbook "${BASEDIR}/includes/config/roles/${1}/tasks/main.yml" | tee -a ${LOGFILE}
  writelog_appli "Installation $1 terminée"
}

function cloudflare()  {
  log_applicatif Cloudflare
  writelog_appli "Installation oauth"
  
  LOGFILE=${LOGFILE_APPLI}

  ansible-vault decrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  SERVICESPERUSER=${SERVICESUSER}${USER}"

  # Ajout id cloudflare dans account.yml
  sed -i "/login:/c\   login: $1" "${CONFDIR}/variables/account.yml"
  sed -i "/api:/c\   api: $2" "${CONFDIR}/variables/account.yml"

  ## Réinstallation traefik
  ansible-playbook "${BASEDIR}/includes/dockerapps/traefik.yml" | tee -a ${LOGFILE}

  # Listing des applications
  while read line; do echo ${line} | cut -d '.' -f1; done < /home/${USER}/resume > ${SERVICESPERUSER}

  # Réinitialisation des applis
  while read line; do
    if [[ -f "${CONFDIR}/conf/${line}.yml" ]]; then
      # il y a déjà un playbook "perso", on le lance
      ansible-playbook "${CONFDIR}/conf/${line}.yml" | tee -a ${LOGFILE}
    elif [[ -f "${CONFDIR}/vars/${line}.yml" ]]; then
      # il y a des variables persos, on les lance
      ansible-playbook "${BASEDIR}/includes/dockerapps/generique.yml" --extra-vars "@${CONFDIR}/vars/${1}.yml" | tee -a ${LOGFILE}
    fi
  done < ${SERVICESPERUSER}

  rm ${SERVICESPERUSER}
  ansible-vault encrypt ${CONFDIR}/variables/account.yml > /dev/null 2>&1
  writelog_appli "Installation Cloudflare terminée"
}

function credential() {
  echo ${1} > /tmp/client
  echo ${2} > /tmp/secret
}

function createtoken() {
  # on appelle la fonction pour avoir le nom du log à créer
  log_applicatif CreateToken
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Création d'un token" 

  # Variables environement USER
  TMPDIR=$(mktemp -d)
  RCLONE_CONFIG_FILE="${HOME}/.config/rclone/rclone.conf"
  client=$(cat /tmp/client)
  secret=$(cat /tmp/secret)
  logfile=${TMPDIR}/log
  create_dir "${HOME}/.config/rclone"
  touch "${RCLONE_CONFIG_FILE}"
 

  curl https://rclone.org/install.sh | sudo bash
  curl --request POST --data "code=${1}&client_id=$client&client_secret=$secret&redirect_uri=urn:ietf:wg:oauth:2.0:oob&grant_type=authorization_code" https://accounts.google.com/o/oauth2/token | sudo tee $logfile 2>/dev/null >/dev/null &

  sleep 2
  accesstoken=$(cat $logfile | grep access_token | awk '{print $2}')
  refreshtoken=$(cat $logfile | grep refresh_token | awk '{print $2}')
  rcdate=$(date +'%Y-%m-%d')
  rctime=$(date +"%H:%M:%S" --date="$givenDate 60 minutes")
  rczone=$(date +"%:z")
  final=$(echo "${rcdate}T${rctime}${rczone}")
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > ${TMPDIR}/chaine)
  chaine=$(cat ${TMPDIR}/chaine)

if [[ "$2" == "sharedrive" ]]; then 
writelog_appli "Création d'un shared drive" 
  curl --request POST \
    "https://www.googleapis.com/drive/v3/teamdrives?requestId='$chaine" \
    --header "Authorization: Bearer ${accesstoken}" \
    --header 'Accept: application/json' \
    --header 'Content-Type: application/json' \
    --data '{"name":"'$3'","backgroundImageLink":"https://pgblitz.com/styles/io_dark/images/pgblitz4.png"}' \
    --compressed > ${TMPDIR}/teamdrive

  ###récupération des variables
  cat ${TMPDIR}/teamdrive | grep "id" | awk '{ print $2 }' | cut -c2- | rev | cut -c3- | rev > ${TMPDIR}/teamdrive.id
  cat ${TMPDIR}/teamdrive | grep "name" | awk '{ print $2 }' | cut -c2- | rev | cut -c2- | rev > ${TMPDIR}/teamdrive.name
  name=$(sed -n ${typed}p ${TMPDIR}/teamdrive.name)
  id=$(sed -n ${typed}p ${TMPDIR}/teamdrive.id)
  echo "$name" > ${TMPDIR}/pgclone.teamdrive
  echo "$id" > ${TMPDIR}/pgclone.teamid
  teamid=$(cat ${TMPDIR}/pgclone.teamid)

  ## Creation rclone.conf
  writelog_appli "Création rclone.conf" 
  echo "" >> ${RCLONE_CONFIG_FILE}
  echo "[$name]" >> ${RCLONE_CONFIG_FILE}
  echo "type = drive" >> ${RCLONE_CONFIG_FILE}
  echo "client_id = $client" >> ${RCLONE_CONFIG_FILE}
  echo "client_secret = $secret" >> ${RCLONE_CONFIG_FILE}
  echo "scope = drive" >> ${RCLONE_CONFIG_FILE}
  echo -n "token = {\"access_token\":${accesstoken}\"token_type\":\"Bearer\",\"refresh_token\":${refreshtoken}\"expiry\":\"${final}\"}" >> ${RCLONE_CONFIG_FILE}
  echo "" >> ${RCLONE_CONFIG_FILE}
  echo "team_drive = $teamid" >> ${RCLONE_CONFIG_FILE}
  echo ""

  ## creation du crypt
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > ${TMPDIR}/password)
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > ${TMPDIR}/salt)

  PASSWORD=`cat ${TMPDIR}/password`
  SALT=`cat ${TMPDIR}/salt`
  ENC_PASSWORD=`rclone obscure "$PASSWORD"`
  ENC_SALT=`rclone obscure "$SALT"`
  crypt="_crypt"

  echo "" >> ${RCLONE_CONFIG_FILE}
  echo "[$name$crypt]" >> ${RCLONE_CONFIG_FILE}
  echo "type = crypt" >> ${RCLONE_CONFIG_FILE}
  echo "remote = $name:/Medias" >> ${RCLONE_CONFIG_FILE}
  echo "filename_encryption = standard" >> ${RCLONE_CONFIG_FILE}
  echo "directory_name_encryption = true" >> ${RCLONE_CONFIG_FILE}
  echo "password = $ENC_PASSWORD" >> ${RCLONE_CONFIG_FILE}
  echo "password2 = $ENC_SALT" >> ${RCLONE_CONFIG_FILE}

else
   writelog_appli "Pas de shared drive" 
  ###récupération des variables

  ## Creation rclone.conf
   writelog_appli "Création rclone.conf" 
  echo "" >> ${RCLONE_CONFIG_FILE}
  echo "[$3]" >> ${RCLONE_CONFIG_FILE}
  echo "type = drive" >> ${RCLONE_CONFIG_FILE}
  echo "client_id = $client" >> ${RCLONE_CONFIG_FILE}
  echo "client_secret = $secret" >> ${RCLONE_CONFIG_FILE}
  echo "scope = drive" >> ${RCLONE_CONFIG_FILE}
  echo -n "token = {\"access_token\":${accesstoken}\"token_type\":\"Bearer\",\"refresh_token\":${refreshtoken}\"expiry\":\"${final}\"}" >> ${RCLONE_CONFIG_FILE}
  echo "" >> ${RCLONE_CONFIG_FILE}
  echo ""

 ## creation du crypt

  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > ${TMPDIR}/password)
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 > ${TMPDIR}/salt)

  PASSWORD=`cat ${TMPDIR}/password`
  SALT=`cat ${TMPDIR}/salt`
  ENC_PASSWORD=`rclone obscure "$PASSWORD"`
  ENC_SALT=`rclone obscure "$SALT"`
  crypt="_crypt"

  echo "" >> ${RCLONE_CONFIG_FILE}
  echo "[$3$crypt]" >> ${RCLONE_CONFIG_FILE}
  echo "type = crypt" >> ${RCLONE_CONFIG_FILE}
  echo "remote = $3:/Medias" >> ${RCLONE_CONFIG_FILE}
  echo "filename_encryption = standard" >> ${RCLONE_CONFIG_FILE}
  echo "directory_name_encryption = true" >> ${RCLONE_CONFIG_FILE}
  echo "password = $ENC_PASSWORD" >> ${RCLONE_CONFIG_FILE}
  echo "password2 = $ENC_SALT" >> ${RCLONE_CONFIG_FILE}

fi
  writelog_appli "Installation rclone"
  log_applicatif InstallRclone
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Installation rclone"    
  
  LOGFILE=${LOGFILE_APPLI}

  #ansible-playbook /opt/seedbox-compose/includes/config/roles/rclone/tasks/main.yml | tee -a ${LOGFILE}
  writelog_appli "Terminé" 
  rm /tmp/client /tmp/secret 
  rm -rf ${TMPDIR}
 
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
  log_applicatif ${1}

  ansible-vault decrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  DOMAIN=$(grep domain "${CONFDIR}/variables/account.yml" | cut -d : -f2 | tr -d ' ')

  # Mise à jour du status actif de l'appli
  echo 0 > "${CONFDIR}/status/${1}"

  # Mise à jour du fichier account.yml
  grep "${1}: ." "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  if [ $? -eq 0 ]; then
    sed -i "/${2}/,+2d" "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  fi
  grep "${1}:" "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  if [ $? -eq 0 ] || [ "${1}" != "plex" ] ; then
    sed -i "/${1}/,+1d" "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  fi

  sed -i "/${1}/d" "${CONFDIR}/resume" > /dev/null 2>&1
  sed -i "/${1}/d" "/home/${USER}/resume" > /dev/null 2>&1

  # supression des volumes
  docker rm -f "$1" > /dev/null 2>&1
  rm "${CONFDIR}/conf/${1}.yml" > /dev/null 2>&1
  rm "${CONFDIR}/vars/${1}.yml" > /dev/null 2>&1

  # supressions des mariadb associées
  if docker ps | grep -q db-${1}; then
  docker rm -f db-${1} > /dev/null 2>&1
  fi

  # supressions des applis complexes
  case ${1} in
     seafile)
       docker rm -f db-seafile memcached > /dev/null 2>&1
     ;;
     varken)
       docker rm -f influxdb telegraf grafana > /dev/null 2>&1
       rm -rf "${CONFDIR}/docker/${USER}/telegraf"
       rm -rf "${CONFDIR}/docker/${USER}/grafana"
       rm -rf "${CONFDIR}/docker/${USER}/influxdb"
     ;;
     jitsi)
      docker rm -f prosody jicofo jvb
      rm -rf "${CONFDIR}/docker/${USER}/.jitsi-meet-cfg"
     ;;
     nextcloud)
       docker rm -f collabora coturn office
       rm -rf "${CONFDIR}/docker/${USER}/coturn"
     ;;
     rtorrentvpn)
       rm "${CONFDIR}/conf/rutorrent-vpn.yml"
     ;;
     *)
     # writelog "ACTION INDEFINIE" 'DEBUG' 
      rm -rf "${CONFDIR}/docker/${USER}/${1}"
     ;;
  esac
 
  # supression des images non utilisées && network eventuels && crypt account.yml
  docker system prune -af > /dev/null 2>&1
  ansible-vault encrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
}

function install() {
  # on appelle la fonction pour avoir le nom du log à créer
  log_applicatif $1
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Installation de l'appli ${1}"    
  
  LOGFILE=${LOGFILE_APPLI}
    
  ansible-vault decrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  
  #declaration des variables utiles
  DOMAIN=$(grep domain "${CONFDIR}/variables/account.yml" | cut -d : -f2 | tr -d ' ')

  # Mise à jour des données subdomain et auth dans account.yml
  if [ $2 != "undefined" ]; then
    grep "${1}: ." "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
    if [ $? -eq 0 ]; then
      sed -i "/${1}: ./d" "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
    fi
    grep "${1}:" "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
    if [ $? -eq 1 ]; then
      sed -i "/sub/a \ \ \ ${1}:" "${CONFDIR}/variables/account.yml"
    fi
    sed -i "/${1}:/a \ \ \ \ \ ${1}: ${2}" "${CONFDIR}/variables/account.yml"
  fi

  # auth à intégrer
    
  ## Installation
  # On est dans le cas générique
  # on regarde s'il y a un playbook existant
  if [[ -f "${CONFDIR}/conf/${1}.yml" ]]; then
    # il y a déjà un playbook "perso", on le lance
    ansible-playbook "${CONFDIR}/conf/${1}.yml" | tee -a ${LOGFILE}
  elif [[ -f "${CONFDIR}/vars/${1}.yml" ]]; then
    # il y a des variables persos, on les lance
    ansible-playbook "${BASEDIR}/includes/dockerapps/generique.yml" --extra-vars "@${CONFDIR}/vars/${1}.yml" | tee -a ${LOGFILE}
  elif [[ -f "${BASEDIR}/includes/dockerapps/${1}.yml" ]]; then
    # pas de playbook perso ni de vars perso
    # Il y a un playbook spécifique pour cette appli, on le copie
    cp "${BASEDIR}/includes/dockerapps/${1}.yml" "${CONFDIR}/conf/${1}.yml" 
    # puis on le lance
    ansible-playbook "${CONFDIR}/conf/${1}.yml" | tee -a ${LOGFILE}
  else
    # on copie les variables pour le user
    cp "${BASEDIR}/includes/dockerapps/vars/${1}.yml" "${CONFDIR}/vars/${1}.yml" 
    # puis on lance le générique avec ce qu'on vient de copier
    ansible-playbook "${BASEDIR}/includes/dockerapps/generique.yml" --extra-vars "@${CONFDIR}/vars/${1}.yml" | tee -a ${LOGFILE}
  fi

  # mise à jour du fichier "/opt/seedbox/resume" && "/home/user/resume"
  FQDNTMP="${2}.${DOMAIN}"
  echo "${1} = ${FQDNTMP}" | tee -a "${CONFDIR}/resume"  > /dev/null
  echo "${1}.${DOMAIN}" >> "/home/${USER}/resume"

  # crypt fichier account.yml
  ansible-vault encrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
    
    					tee -a ${LOGFILE} <<-EOF
    
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
                       Installation de $1 effectuée avec succés
    
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
EOF

}

function oauth() 
{
  log_applicatif oauth
  writelog_appli "Installation oauth"
  
  LOGFILE=${LOGFILE_APPLI}

  ansible-vault decrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  SERVICESPERUSER=${SERVICESUSER}${USER}

  # Ajout id oauth dans account.yml
  sed -i "/client:/c\   client: ${1}" "${CONFDIR}/variables/account.yml"
  sed -i "/secret:/c\   secret: ${2}" "${CONFDIR}/variables/account.yml"
  sed -i "/account:/c\   account: ${3}" "${CONFDIR}/variables/account.yml"
  OPENSSL=$(openssl rand -hex 16)
  sed -i "/openssl:/c\   openssl: $OPENSSL" "${CONFDIR}/variables/account.yml"

  ## reinstallation traefik
  ansible-playbook "${BASEDIR}/includes/dockerapps/traefik.yml" | tee -a ${LOGFILE}

  # listing applis déjà installées
  while read line; do echo ${line} | cut -d'.' -f1; done < /home/${USER}/resume > ${SERVICESPERUSER}

  # Réinitialisations des applis
  while read line; do
    if [[ -f "${CONFDIR}/conf/${line}.yml" ]]; then
      # il y a déjà un playbook "perso", on le lance
      ansible-playbook "${CONFDIR}/conf/${line}.yml" | tee -a ${LOGFILE}
    elif [[ -f "${CONFDIR}/vars/${line}.yml" ]]; then
      # il y a des variables persos, on les lance
      ansible-playbook "${BASEDIR}/includes/dockerapps/generique.yml" --extra-vars "@${CONFDIR}/vars/${1}.yml" | tee -a ${LOGFILE}
    fi
  done < ${SERVICESPERUSER}

  # supression des variables temporaires
  rm $SERVICESPERUSER
  ansible-vault encrypt "${CONFDIR}/variables/account.yml" > /dev/null 2>&1
  writelog_appli "Installation Oauth terminée"
}

# Variables d'environnement
DIRNAME=$(dirname $0)
export PATH="$HOME/.local/bin:$PATH"
source /opt/seedbox-compose/profile.sh

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
  oauth)
    oauth $2 $3 $4
  ;;
  cloudflare)
    cloudflare $2 $3
  ;;
  *)
  writelog "ACTION INDEFINIE" 'DEBUG' 
  echo "Action indéfinie"
  ;;
esac