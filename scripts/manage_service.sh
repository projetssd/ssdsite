#!/bin/bash
########################################
# Gestion des services SSD
########################################

function writelog()
{
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

function credential() {
touch /opt/seedbox/rclone
}

function configure() {

  ACCOUNT=/opt/seedbox/variables/account.yml

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
  ansible-vault decrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
  docker rm -f $1
  echo 0 > /opt/seedbox/status/$1
  sed -i "/$1/d" /opt/seedbox/variables/account.yml > /dev/null 2>&1
  sed -i "/$1/d" /opt/seedbox/resume > /dev/null 2>&1
  ansible-vault encrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
}

function install() {
    
    LOGFILE=${DIRNAME}/../logtail/log
    rm -f $LOGFILE

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
    
    ## préparation installation
    if [ -e "/opt/seedbox/conf/$1.yml" ]; then
      ansible-playbook "$CONFDIR/conf/$1.yml" | tee -a $LOGFILE
    else
      ansible-playbook "$BASEDIR/includes/dockerapps/$1.yml" | tee -a $LOGFILE
      cp "$BASEDIR/includes/dockerapps/$1.yml" "$CONFDIR/conf/$1.yml" > /dev/null 2>&1
    fi

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
  configure)
    configure  $2 $3 $4 $5 $6 $7 $8 $9 ${10} ${11} ${12}
  ;;
  credential)
    credential $2
  ;;
  *)
  writelog "ACTION INDEFINIE" 'DEBUG' 
  echo "Action indéfinie"
  ;;
esac