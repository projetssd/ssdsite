# A faire pour la PROD uniquement 

## nginx

### Activer le cache des éléments statiques

Editer le fichier /etc/nginx/sites-enables/default et ajouter 
```
location ~* \.(?:jpg|jpeg|gif|png|ico|cur|gz|svg|svgz|mp4|ogg|ogv|webm|htc)$ {
  expires 1M;
  access_log off;
  add_header Cache-Control "public";
}

# CSS and Javascript
location ~* \.(?:css|js)$ {
  expires 1y;
  access_log off;
  add_header Cache-Control "public";
}
```

