#!/bin/bash
########################################
# Gestion des services SSD
########################################

function configure() {
touch /opt/seedbox/$1
}

function uninstall() {
ansible-playbook /opt/seedbox-compose/includes/dockerapps/templates/ansible/ansible.yml
ansible-vault decrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1

# recuperation variables name et domain
domain=$(cat /tmp/domain)
name=$(cat /tmp/name)

echo 0 > /opt/seedbox/status/$1
sed -i "/$1/d" /opt/seedbox/variables/account.yml > /dev/null 2>&1
sed -i "/$1/d" /opt/seedbox/resume > /dev/null 2>&1

docker rm -f "$1" > /dev/null 2>&1
rm -rf /opt/seedbox/docker/$name/$1

if [[ "$1" != "plex" ]]; then
  rm /opt/seedbox/conf/$1.yml > /dev/null 2>&1
fi

if [[ "$1" = "seafile" ]]; then
  docker rm -f db-seafile memcached > /dev/null 2>&1
fi

if docker ps | grep -q db-$1; then
  docker rm -f db-$1 > /dev/null 2>&1
fi

if [[ "$1" = "varken" ]]; then
  docker rm -f influxdb telegraf grafana > /dev/null 2>&1
  rm -rf /opt/seedbox/docker/$name/telegraf
  rm -rf /opt/seedbox/docker/$name/grafana
  rm -rf /opt/seedbox/docker/$name/influxdb
fi

if [[ "$1" = "jitsi" ]]; then
  docker rm -f prosody jicofo jvb
  rm -rf /opt/seedbox/docker/$name/.jitsi-meet-cfg
fi

if [[ "$1" = "nextcloud" ]]; then
  docker rm -f collabora coturn office
  rm -rf /opt/seedbox/docker/$name/coturn
fi

if [[ "$1" = "rtorrentvpn" ]]; then
  rm /opt/seedbox/conf/rutorrent-vpn.yml
fi

if [[ "$1" = "authelia" ]]; then
  /opt/seedbox-compose/includes/config/scripts/authelia.sh
  sed -i '/authelia/d' /home/$name/resume > /dev/null 2>&1
fi

docker system prune -af > /dev/null 2>&1
docker volume rm $(docker volume ls -qf "dangling=true") > /dev/null 2>&1

ansible-vault encrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1

}

function install() {
    LOGFILE=/var/www/lastharo/logtail/log
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

SERVICE=$1
ACTION=$2
SUBDOMAIN=$3

case $ACTION in
  install) 
    install $1 $3
  ;;
  uninstall)
    uninstall $1
  ;;
  configure)
    configure $1
  ;;
  *) 
  echo "Action indéfinie"
  ;;
esac