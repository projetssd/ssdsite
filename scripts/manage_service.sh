#!/bin/bash
########################################
# Gestion des services SSD
########################################


function configure() {
PATH=/opt/seedbox/variables/account.yml

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
cp /opt/seedbox-compose/includes/config/account.yml $PATH
echo $2 > ~/.vault_pass
echo "vault_password_file = ~/.vault_pass" >> /etc/ansible/ansible.cfg
sed -i "s/name:/name: $1/" $PATH
sed -i "s/pass:/pass: $2/" $PATH
sed -i "s/userid:/userid: $userid/" $PATH
sed -i "s/groupid:/groupid: $grpid/" $PATH
sed -i "s/group:/group: $1/" $PATH
sed -i "/htpwd:/c\   htpwd: $htpwd" $PATH
sed -i "s/mail:/mail: $3/" $PATH
sed -i "s/domain:/domain: $4/" $PATH
sed -i "s/ident:/ident: $5/" $PATH
sed -i "s/sesame:/sesame: $6/" $PATH
sed -i "s/login:/login: $7/" $PATH
sed -i "s/api:/api: $8/" $PATH
sed -i "s/client:/client: $9/" $PATH
sed -i "s/account:/account: $10/" $PATH
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
    configure $1 $2 $3 $4 $5 $6
  ;;
  *) 
  echo "Action indéfinie"
  ;;
esac