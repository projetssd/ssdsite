#!/bin/bash
########################################
# Gestion des services SSD
########################################

function writelog() {
  #######################################
  # Fonction uniquement pour des logs
  # de debug
  # ces logs ne seront pas visibles par
  # l'utilisateur
  #######################################
  DIRNAME=$(dirname $0)
  DATE=$(date +"%d/%m/%Y %T")
  DATELOG=$(date +"%Y%m%d")
  if [ $# -eq 1 ]; then
    MESSAGE=$1
    TYPE='INFO'
  else
    MESSAGE=$1
    TYPE=$2
  fi
  echo "SHELL : ${DATE} - ${TYPE} - ${MESSAGE}" >>${DIRNAME}/../logs/ssdsite-${DATELOG}.log
}

function log_applicatif() {
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

function writelog_appli() {
  DATE=$(date +"%d/%m/%Y %T")
  echo "${DATE} - ${1}" >>${LOGFILE_APPLI}
}

function create_plex() {
  log_applicatif TokenPlex
  writelog_appli "récuperation token plex"
  # recuperation token plex
  curl -qu "${1}":"${2}" 'https://plex.tv/users/sign_in.xml' \
    -X POST -H 'X-Plex-Device-Name: PlexMediaServer' \
    -H 'X-Plex-Provides: server' \
    -H 'X-Plex-Version: 0.9' \
    -H 'X-Plex-Platform-Version: 0.9' \
    -H 'X-Plex-Platform: xcid' \
    -H 'X-Plex-Product: Plex Media Server' \
    -H 'X-Plex-Device: Linux' \
    -H 'X-Plex-Client-Identifier: XXXX' --compressed >/tmp/plex_sign_in
  token=$(sed -n 's/.*<authentication-token>\(.*\)<\/authentication-token>.*/\1/p' /tmp/plex_sign_in)
  manage_account_yml plex.token ${token}
  manage_account_yml plex.ident ${1}
  manage_account_yml plex.sesame ${2}

}

function tools() {
  log_applicatif ${1}
  writelog_appli "Installation ${1}"

  LOGFILE=${LOGFILE_APPLI}

  ansible-playbook "${BASEDIR}/includes/config/roles/${1}/tasks/main.yml" | tee -a ${LOGFILE}
  writelog_appli "Installation ${1} terminée"
}

function uninstall_tools() {
  log_applicatif ${1}
  writelog_appli "Désinstallation ${1}"
  LOGFILE=${LOGFILE_APPLI}
  if [ "${1}" == "authelia" ]; then

    # Mise à jour du status actif de l'appli
    echo 0 >"${CONFDIR}/status/${1}"

    sed -i "/${1}/d" "${CONFDIR}/resume" >/dev/null 2>&1
    sed -i "/${1}/d" "/home/${USER}/resume" >/dev/null 2>&1

    # supression des volumes
    docker rm -f "${1}" >/dev/null 2>&1
    rm "${CONFDIR}/conf/${1}.yml" >/dev/null 2>&1
    rm "${CONFDIR}/vars/${1}.yml" >/dev/null 2>&1
    rm -rf ${CONFDIR}/docker/${USER}/${1}

    # supressions mariadb et images associées
    docker rm -f db-${1} >/dev/null 2>&1
    docker system prune -af >/dev/null 2>&1

  else
    ansible-playbook /var/www/seedboxdocker.website/scripts/yml/${1}.yml | tee -a ${LOGFILE}
  fi

  writelog_appli "Désinstallation ${1} terminée"
}

function clflare() {
  log_applicatif Cloudflare
  writelog_appli "Installation oauth"

  LOGFILE=${LOGFILE_APPLI}

  SERVICESPERUSER=${SERVICESUSER}${USER}

  # Ajout id cloudflare dans account.yml
  manage_account_yml cloudflare.login ${1}
  manage_account_yml cloudflare.api ${2}

  ## Réinstallation traefik
  ansible-playbook "${BASEDIR}/includes/dockerapps/traefik.yml" | tee -a ${LOGFILE}

  # Listing des applications
  while read line; do echo ${line} | cut -d '.' -f1; done </home/${USER}/resume >${SERVICESPERUSER}

  # Réinitialisation des applis
  while read line; do
    if [[ -f "${CONFDIR}/conf/${line}.yml" ]]; then
      # il y a déjà un playbook "perso", on le lance
      ansible-playbook "${CONFDIR}/conf/${line}.yml" | tee -a ${LOGFILE}
    elif [[ -f "${CONFDIR}/vars/${line}.yml" ]]; then
      # il y a des variables persos, on les lance
      ansible-playbook "${BASEDIR}/includes/dockerapps/generique.yml" --extra-vars "@${CONFDIR}/vars/${line}.yml" | tee -a ${LOGFILE}
    fi
  done <${SERVICESPERUSER}

  rm ${SERVICESPERUSER}
  writelog_appli "Installation Cloudflare terminée"
}

function credential() {
  echo ${1} >/tmp/client
  echo ${2} >/tmp/secret
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
  ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 >${TMPDIR}/chaine)
  chaine=$(cat ${TMPDIR}/chaine)

  if [[ "$2" == "sharedrive" ]]; then
    writelog_appli "Création d'un shared drive"
    curl --request POST \
      "https://www.googleapis.com/drive/v3/teamdrives?requestId='$chaine" \
      --header "Authorization: Bearer ${accesstoken}" \
      --header 'Accept: application/json' \
      --header 'Content-Type: application/json' \
      --data '{"name":"'$3'","backgroundImageLink":"https://pgblitz.com/styles/io_dark/images/pgblitz4.png"}' \
      --compressed >${TMPDIR}/teamdrive

    ###récupération des variables
    cat ${TMPDIR}/teamdrive | grep "id" | awk '{ print $2 }' | cut -c2- | rev | cut -c3- | rev >${TMPDIR}/teamdrive.id
    cat ${TMPDIR}/teamdrive | grep "name" | awk '{ print $2 }' | cut -c2- | rev | cut -c2- | rev >${TMPDIR}/teamdrive.name
    name=$(sed -n ${typed}p ${TMPDIR}/teamdrive.name)
    id=$(sed -n ${typed}p ${TMPDIR}/teamdrive.id)
    echo "$name" >${TMPDIR}/pgclone.teamdrive
    echo "$id" >${TMPDIR}/pgclone.teamid
    teamid=$(cat ${TMPDIR}/pgclone.teamid)

    ## Creation rclone.conf
    writelog_appli "Création rclone.conf"
    echo "" >>${RCLONE_CONFIG_FILE}
    echo "[$name]" >>${RCLONE_CONFIG_FILE}
    echo "type = drive" >>${RCLONE_CONFIG_FILE}
    echo "client_id = $client" >>${RCLONE_CONFIG_FILE}
    echo "client_secret = $secret" >>${RCLONE_CONFIG_FILE}
    echo "scope = drive" >>${RCLONE_CONFIG_FILE}
    echo -n "token = {\"access_token\":${accesstoken}\"token_type\":\"Bearer\",\"refresh_token\":${refreshtoken}\"expiry\":\"${final}\"}" >>${RCLONE_CONFIG_FILE}
    echo "" >>${RCLONE_CONFIG_FILE}
    echo "team_drive = $teamid" >>${RCLONE_CONFIG_FILE}
    echo ""

    ## creation du crypt
    ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 >${TMPDIR}/password)
    ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 >${TMPDIR}/salt)

    PASSWORD=$(cat ${TMPDIR}/password)
    SALT=$(cat ${TMPDIR}/salt)
    ENC_PASSWORD=$(rclone obscure "$PASSWORD")
    ENC_SALT=$(rclone obscure "$SALT")
    crypt="_crypt"

    echo "" >>${RCLONE_CONFIG_FILE}
    echo "[$name$crypt]" >>${RCLONE_CONFIG_FILE}
    echo "type = crypt" >>${RCLONE_CONFIG_FILE}
    echo "remote = $name:/Medias" >>${RCLONE_CONFIG_FILE}
    echo "filename_encryption = standard" >>${RCLONE_CONFIG_FILE}
    echo "directory_name_encryption = true" >>${RCLONE_CONFIG_FILE}
    echo "password = ${ENC_PASSWORD}" >>${RCLONE_CONFIG_FILE}
    echo "password2 = ${ENC_SALT}" >>${RCLONE_CONFIG_FILE}

  else
    writelog_appli "Pas de shared drive"
    ###récupération des variables

    ## Creation rclone.conf
    writelog_appli "Création rclone.conf"
    echo "" >>${RCLONE_CONFIG_FILE}
    echo "[$3]" >>${RCLONE_CONFIG_FILE}
    echo "type = drive" >>${RCLONE_CONFIG_FILE}
    echo "client_id = $client" >>${RCLONE_CONFIG_FILE}
    echo "client_secret = $secret" >>${RCLONE_CONFIG_FILE}
    echo "scope = drive" >>${RCLONE_CONFIG_FILE}
    echo -n "token = {\"access_token\":${accesstoken}\"token_type\":\"Bearer\",\"refresh_token\":${refreshtoken}\"expiry\":\"${final}\"}" >>${RCLONE_CONFIG_FILE}
    echo "" >>${RCLONE_CONFIG_FILE}
    echo ""

    ## creation du crypt

    ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 >${TMPDIR}/password)
    ramdom=$(head /dev/urandom | tr -dc A-Za-z | head -c 8 >${TMPDIR}/salt)

    PASSWORD=$(cat ${TMPDIR}/password)
    SALT=$(cat ${TMPDIR}/salt)
    ENC_PASSWORD=$(rclone obscure "${PASSWORD}")
    ENC_SALT=$(rclone obscure "${SALT}")
    crypt="_crypt"

    echo "" >>${RCLONE_CONFIG_FILE}
    echo "[$3$crypt]" >>${RCLONE_CONFIG_FILE}
    echo "type = crypt" >>${RCLONE_CONFIG_FILE}
    echo "remote = $3:/Medias" >>${RCLONE_CONFIG_FILE}
    echo "filename_encryption = standard" >>${RCLONE_CONFIG_FILE}
    echo "directory_name_encryption = true" >>${RCLONE_CONFIG_FILE}
    echo "password = ${ENC_PASSWORD}" >>${RCLONE_CONFIG_FILE}
    echo "password2 = ${ENC_SALT}" >>${RCLONE_CONFIG_FILE}

  fi
  # incrementation du remote ds le account.yml
  manage_account_yml rclone.remote $3$crypt

  writelog_appli "Installation rclone"
  log_applicatif InstallRclone
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Installation rclone"

  LOGFILE=${LOGFILE_APPLI}

  ansible-playbook /opt/seedbox-compose/includes/config/roles/rclone/tasks/main.yml | tee -a ${LOGFILE}
  writelog_appli "Terminé"
  rm /tmp/client /tmp/secret
  rm -rf ${TMPDIR}
}

function uninstall() {
  log_applicatif ${1}
  writelog_appli "Désinstallation de ${1} en cours"

  DOMAIN=$(get_from_account_yml user.domain)

  # Mise à jour du status actif de l'appli
  echo 0 >"${CONFDIR}/status/${1}"


  sed -i "/${1}/d" "${CONFDIR}/resume" >/dev/null 2>&1
  sed -i "/${1}/d" "/home/${USER}/resume" >/dev/null 2>&1

  # supression des volumes
  writelog_appli "Suppression des volumes..."
  docker rm -f "${1}" >/dev/null 2>&1
  rm "${CONFDIR}/conf/${1}.yml" >/dev/null 2>&1
  rm "${CONFDIR}/vars/${1}.yml" >/dev/null 2>&1
  rm -rf ${CONFDIR}/docker/${USER}/${1}

  # supressions des mariadb associées
  if docker ps | grep -q db-${1}; then
    writelog_appli "Suppression de la base de données..."
    docker rm -f db-${1} >/dev/null 2>&1
  fi

  # supressions des applis complexes
  case ${1} in
  seafile)
    docker rm -f db-seafile memcached >/dev/null 2>&1
    ;;
  varken)
    docker rm -f influxdb telegraf grafana >/dev/null 2>&1
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
  plex)
    manage_account_yml plex.token " "
    manage_account_yml plex.ident " "
    manage_account_yml plex.sesame " "

    ;;
  *)
    # writelog "ACTION INDEFINIE" 'DEBUG'
    ;;
  esac
  writelog_appli "Suppression des images et volumes non utilisés"
  # supression des images non utilisées && network eventuels && crypt account.yml
  docker system prune -af >/dev/null 2>&1
  writelog_appli "Suppresion de l'application ${1} terminée"
}

function install() {
  # on appelle la fonction pour avoir le nom du log à créer
  log_applicatif ${1}
  # maintenant, on a la variable LOGFILE_APPLI utilisable
  writelog_appli "Installation de l'appli ${1}"

  LOGFILE=${LOGFILE_APPLI}


  #declaration des variables utiles
  DOMAIN=$(get_from_account_yml user.domain)

  # Mise à jour des données subdomain dans account.yml
  manage_account_yml "sub.${1}.${1}" "${2}"
  manage_account_yml "sub.${1}.auth" "${3}"



  ## Installation
  # A ce niveau, on vient de renseigner les variables nécessaires ci dessus
  # donc la fonction ne devrait pas les demander
  launch_appli "${1}" >> "${LOGFILE}"

  # mise à jour du fichier "/opt/seedbox/resume" && "/home/user/resume"
  FQDNTMP="${2}.${DOMAIN}"
  echo "${1} = ${FQDNTMP}" | tee -a "${CONFDIR}/resume" >/dev/null
  echo "${1}.${DOMAIN}" >>"/home/${USER}/resume"

  tee -a "${LOGFILE}" <<-EOF
    
       $1                             📓 https://wiki.scriptseedboxdocker.com
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
                       Installation de $1 effectuée avec succés
    
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
EOF

}

function add_authelia() {
  log_applicatif oauth
  writelog_appli "Installation oauth"

  LOGFILE=${LOGFILE_APPLI}

  echo "authelia:" >/tmp/authelia.yml
  sed -i "/authelia/a \ \ \ mail: ${1}" /tmp/authelia.yml
  sed -i "/mail/a \ \ \ smtp: ${2}" /tmp/authelia.yml
  sed -i "/smtp/a \ \ \ smtp_port: ${3}" /tmp/authelia.yml
  sed -i "/smtp_port/a \ \ \ pass_appli: ${4}" /tmp/authelia.yml
  ansible-playbook /var/www/seedboxdocker.website/scripts/yml/authelia.yml --extra-vars "@${BASEDIR}/includes/dockerapps/vars/authelia.yml" | tee -a ${LOGFILE}
  rm /tmp/authelia.yml
}

function goauth() {
  log_applicatif oauth
  writelog_appli "Installation oauth"

  LOGFILE=${LOGFILE_APPLI}


  SERVICESPERUSER=${SERVICESUSER}${USER}
  manage_account_yml oauth.client ${1}
  manage_account_yml oauth.secret ${2}
  manage_account_yml oauth.account ${3}
  OPENSSL=$(openssl rand -hex 16)
  manage_account_yml oauth.openssl $OPENSSL{}


  ## reinstallation traefik
  ansible-playbook "${BASEDIR}/includes/dockerapps/traefik.yml" | tee -a ${LOGFILE}

  # listing applis déjà installées
  while read line; do echo ${line} | cut -d'.' -f1; done </home/${USER}/resume >${SERVICESPERUSER}

  # Réinitialisations des applis
  while read line; do
    if [[ -f "${CONFDIR}/conf/${line}.yml" ]]; then
      # il y a déjà un playbook "perso", on le lance
      ansible-playbook "${CONFDIR}/conf/${line}.yml" | tee -a ${LOGFILE}
    elif [[ -f "${CONFDIR}/vars/${line}.yml" ]]; then
      # il y a des variables persos, on les lance
      ansible-playbook "${BASEDIR}/includes/dockerapps/generique.yml" --extra-vars "@${CONFDIR}/vars/${line}.yml" | tee -a ${LOGFILE}
    fi
  done <${SERVICESPERUSER}

  # supression des variables temporaires
  rm $SERVICESPERUSER
  writelog_appli "Installation Oauth terminée"
}

function stop()
{
  log_applicatif "${1}"
  writelog_appli "Arrêt de ${1} en cours"
  docker stop "${1}"
  writelog_appli "Arrêt de ${1} terminé"
}

# Variables d'environnement
DIRNAME=$(dirname $0)
export PATH="$HOME/.local/bin:$PATH"
if [[ -f "/opt/seedbox-compose/profile.sh" ]]; then
  source /opt/seedbox-compose/profile.sh
else
  export CONFDIR=/opt/seedbox
fi

writelog "Lancement du script" "DEBUG"
ACTION=${1}
writelog "Action = ${ACTION}" "DEBUG"

case $ACTION in
install)
  install ${2} ${3} ${4}
  ;;
uninstall)
  uninstall ${2}
  ;;
credential)
  credential ${2} ${3}
  ;;
createtoken)
  createtoken ${2} ${3} ${4}
  ;;
tools)
  tools ${2}
  ;;
goauth)
  goauth ${2} ${3} ${4}
  ;;
clflare)
  clflare ${2} ${3}
  ;;
create_plex)
  create_plex ${2} ${3}
  ;;
uninstall_tools)
  uninstall_tools ${2}
  ;;
add_authelia)
  add_authelia ${2} ${3} ${4} ${5}
  ;;
stop)
  stop ${2}
  ;;
*)
  writelog "ACTION INDEFINIE" 'DEBUG'
  echo "Action indéfinie"
  ;;
esac
