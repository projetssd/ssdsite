#!/bin/bash
########################################
# Gestion des services SSD
########################################

function install() {

    source /opt/seedbox-compose/includes/variables.sh
    
    ansible-playbook /opt/seedbox-compose/includes/dockerapps/templates/ansible/ansible.yml
    ansible-vault decrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
    
    domain=$(cat /tmp/domain)
    
    if [[ ! -d "$CONFDIR/conf" ]]; then
      mkdir -p $CONFDIR/conf > /dev/null 2>&1
    fi
    
    ## préparation installation
    if [ -e "/opt/seedbox/conf/$1.yml" ]; then
      ansible-playbook "$CONFDIR/conf/$1.yml"
    else
      ansible-playbook "$BASEDIR/includes/dockerapps/$1.yml"
      cp "$BASEDIR/includes/dockerapps/$1.yml" "$CONFDIR/conf/$1.yml" > /dev/null 2>&1
    fi
    
    grep $1 /opt/seedbox/variables/account.yml > /dev/null 2>&1
    if [ $? -eq 0 ]; then
      line=$(grep $1 /opt/seedbox/variables/account.yml | cut -d ':' -f2 | sed 's/ //g')
      fqdn="$1.$domain"
      echo "$1 = $fqdn" | sudo tee -a /opt/seedbox/resume  > /dev/null
    else
      fqdn="$1.$domain"
      echo "$1 = $fqdn" | sudo tee -a /opt/seedbox/resume  > /dev/null
    fi
    fqdn=""
    ansible-vault encrypt /opt/seedbox/variables/account.yml > /dev/null 2>&1
    
    					sudo tee <<-EOF
    
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
                       Installation de $1 effectuée avec succés
    
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🚀 $1                             📓 https://wiki.scriptseedboxdocker.com
EOF
        
    
}

SERVICE=$1
ACTION=$2


case $ACTION in
  install) 
    install $1
  
  ;;
  
  *) 
  echo "Action indéfinie"
  ;;
esac