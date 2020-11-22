# Installation

## Cloner le repo
```
git clone git@github.com:Merrick28/ssdsite.git /var/www/ssdsite
```
ou bien
```
git clone https://github.com/Merrick28/ssdsite.git /var/www/ssdsite
```

Si vous changez le chemin, pensez à modifier les étapes suivantes


## Installer les dépendances

```
apt install nginx php-fpm php-mysql php-curl php-dom
```
Ca va sortir en erreur, c'est normal. Nginx essaie de démarrer sur le port 80 alors qu'il y a déjà traefik dessus. 
## modifier la conf nginx

Editer le fichier /etc/nginx/sites-enables/default

Mettre sur le port 81 :
```
server {
        listen 81 default_server;
        listen [::]:81 default_server;
```
Chager la racine
```
root /var/www/ssdsite;
```
Autoriser php en décommentant :
```
location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.3-fpm.sock;
        }
```
vérifiez la version que vous avez dans /run/php pour éventuellement modifier la ligne fastcgi_pass

Redémarrez nginx
```
systemctl restart nginx
```

## ajoutez un sous domaine
Dans votre provider dns (cloudflare ou autre), ajoutez un sous domaine en A ou CNAME vers votre serveur (par exemple ssd.monserveur.net)
crééz le fichier ssdsite.toml dans /opt/seedbox/docker/traefik/rules
```
[http.routers]
  [http.routers.pihole-rtr]
      entryPoints = ["https"]
      rule = "Host(`ssd.monserveur.net`)"
      service = "pihole-svc"
      [http.routers.pihole-rtr.tls]
        certresolver = "letsencrypt"

[http.services]
  [http.services.pihole-svc]
    [http.services.pihole-svc.loadBalancer]
      passHostHeader = true
      [[http.services.pihole-svc.loadBalancer.servers]]
        url = "http://62.210.113.232:81"
```
Changez la dernière ligne avec l'adresse publique de votre serveur

Vous devriez maintenant accéder à https://ssd.monserveur.net

## Autoriser le user a faire du sudo

tapez 
```
visudo
```
pour éditer le fichier sudoers 
(n'éditez JAMAIS directement le fichier /etc/sudoers, la commande visudo permet de voir qu'il n'y a pas d'anomalie avant de sauvegarder)
et ajoutez la ligne
```
www-data ALL=(ALL) NOPASSWD:/var/www/ssdsite/scripts/manage_service.sh
```
a la fin

## Voir le README.md pour travailler sur ce projet

