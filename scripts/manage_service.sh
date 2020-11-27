#!/bin/bash
########################################
# Gestion des services SSD
########################################


function uninstall() {
ansible-vault decrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
docker rm -f $1
echo 0 > /opt/seedbox/status/$1
sed -i "/$1/d" /opt/seedbox/variables/account.yml > /dev/null 2>&1
sed -i "/$1/d" /opt/seedbox/resume > /dev/null 2>&1
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
  *) 
  echo "Action indéfinie"
  ;;
esac