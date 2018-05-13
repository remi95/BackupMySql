# PostGreSQL

Gaby Fulchic & Rémi Mafat

## Sommaire
 
[Introduction](#introduction)    
[Prérequis](#prérequis)    
[Installations](#installations)     
[Préparation de l'environnement](#préparation-de-lenvironnement)     
[Fonctionnement de l'interface web](#fonctionnement-de-linterface-web)     

## Introduction

Dans le cadre d'un cours en école informatique, nous avons créer une interface web permettant de se connecter sur un serveur (local ou distant) et de créer des sauvegardes des bases de données qui s'y trouvent. Il est également possible de restaurer les bases de données. Enfin, il est aussi possible d'automatiser les sauvegardes avec une **cron**.      
Pour cela, le tutoriel suivant décrit pas à pas les démarches à suivre pour utiliser l'interface web.

## Prérequis

Pour mener à bien ce projet, il faut avoir une **VM Debian 9** de préférence, avec une connexion internet, et les accès _root_. Certaines commandes vous demanderont obligatoirement de les exécuter avec des droits d'administrateur. Nous avons personnelement installé _sudo_, mais libre à vous de choisir la méthode qui vous convient.     
```sh
su
apt-get install sudo
adduser yourUserName sudo
```
Il est aussi nécessaire d'avoir les accès ssh du serveur distant.

## Installations
### Installations basiques

On met à jour les dépôts et les paquets.
`sudo apt-get update`
`sudo apt-get upgrade`

On installe **Apache 2** et on active la réécriture d'url
`sudo apt-get install apache2`
`sudo a2enmod rewrite`

On se rend (en sudo) dans le fichier `/etc/apache2/apache2.conf` en _root_ et on ajoute à la fin du fichier les lignes suivantes 

```sh
<ifModule mod_rewrite.c>
RewriteEngine On
</ifModule>
```

`systemctl restart apache2`

On installe **php7** et les librairies nécessaires
```sh
sudo apt-get install php7.0
sudo apt-get install php7.0-dev
sudo apt-get install php libapache2-mod-php php-mcrypt php-mysql
sudo apt-get install php-cli
sudo apt-get install apache2-utils
sudo apt-get install php7.0-mysql php7.0-curl php7.0-json php7.0-cgi php7.0
sudo apt-get install curl libcurl3 libcurl3-dev
sudo apt-get install libssh2–1-dev libssh2–1
sudo apt-get install php-ssh2
```

Il faut aussi [télécharger](https://www.libssh2.org/) la librairie ssh2. On double-clique dessus pour l'extraire, puis on rentre dans les dossiers extraits (Il y en a deux normalement), puis on entre les commande suivantes :
```sh
./configure
make
sudo make install
```

```
wget https://github.com/Sean-Der/pecl-networking-ssh2/archive/php7.zip
unzip php7.zip
cd pecl-networking-ssh2-php7
phpize
./configure
make
sudo make install
```

On installe **MySQL**
`sudo apt-get install mysql-server mysql-client`

On installe les **librairies**
`sudo apt-get install php-mysql`

### Configuration de phpmyadmin

On télécharge un **.zip de phpmyadmin** sur le site de [phpmyadmin](https://www.phpmyadmin.net/)
On le déplace dans _/var/www/phpmyadmin/_ (dossier _phpmyadmin_ créé préalablement) puis on l'unzip.

On crée un **virtualhost**.    
Dans le fichier _/etc/hosts_ et on ajoute la ligne :
`127.0.0.1    dev.phpmyadmin.loc`
On crée le fichier _phpmyadmin.conf_ dans _/etc/apache2/sites-available/_
On le rempli avec le contenu suivant :
```sh
<VirtualHost *:80>
  ServerName dev.phpmyadmin.loc
  ServerAdmin appliweb@appliweb
  DocumentRoot /var/www/phpmyadmin/phpMyAdmin-4.7.7-all-languages
  <Directory /var/www/phpmyadmin/phpMyAdmin-4.7.7-all-languages/>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```
Bien entendu le nom du dossier est a adapter, il se peut que ce ne soit pas la même version.    
On se rend ensuite dans le dossier _/etc/apache2/site-enabled_ et on entre la commande `a2ensite phpmyadmin.conf`.
On peut vérifier que cela à bien fonctionné en entrant la commande `ls`. Il doit apparaître dans la liste.

On redémarre ensuite les services apache : `systemctl reload apache2`

Conformément au code entré dans le Virtualhost, on peut normalement accéder à phpmyadmin en tapant _dev.phpmyadmin.loc_ dans la barre de recherche du navigateur internet. En effet, le nom de l'url est donné grâce au _ServerName_, et l'emplacement est défini grâce au _DocumentRoot_ et _Directory_.

### Configuration d'un utilisateur mysql

Afin de se connecter à phpmyadmin, on crée un utilisateur mysql qui aura tous les droits, de manière à pouvoir gérer tout ce que l'on veut.
Dans un premier temps, on se rend à l'url _dev.phpmyadmin.loc/setup_ de manière à ajouter un **nouveau serveur** dans phpmyadmin, qui ne sera autre que localhost (notre VM). On ajoute un mot de passe à l'utilisateur root (_root_ par exemple), et on permet la connexion sans mot de passe.     
Ensuite, en ligne de commande, on entre dans mysql : `mysql -u root -p mysql`
Puis on créé l'utilisateur :
``` mysql
CREATE USER 'appli_web'@'localhost' IDENTIFIED BY 'erty';
GRANT ALL PRIVILEGES ON *.* TO 'appli_web'@'localhost';
FLUSH PRIVILEGES;
```
On peut maintenant se connecter à phpmyadmin avec les identifiants _appli_web_ et _erty_, et l'on possède tous les privilèges.

## Préparation de l'environnement

Afin de réaliser facilement les backups et la restauration des bases de données, nous avons réalisé une interface web (PHP).   
De manière à ce que tout s'exécute correctement, nous allons préparer l'environnement.

### Ajout du repo

Pour commencer, ajoutons le repository contenant l'interface web dans `/var/www/`.     
`git clone https://github.com/gabyfulchic/PostGreSQL.git`   
Si vous ne disposez pas de git, installez le via `apt-get install git`      
Normalement, vous trouverez dans le dossier cloné un fichier nommé _htaccess.txt_, renommez-le en _.htaccess_.

### Modifications des droits

Par défaut, le dossier se nomme _PostGreSQL_, renommez-le **backup**.   
Ensuite, attribuez les droits au dossier complet en effectuant la commande suivante : `chmod 777 -R /var/www/backup/`    
    
Les backups se créeront automatiquement dans le dossier _Documents_ de l'utilisateur, il est donc recommandé de donner les droits à ce dossier aussi : `chmod 777 -R /home/$USER/Documents/`   

Cette dernière étape est très importante : Le script PHP utilise le propriétaire du script pour correctement créer les backups. Par défaut, c'est surement _root_, vous pouvez le vérifier en faisant `ls -l` dans le dossier _/var/www/backup/_.    
Or il faut que le propriétaire soit un utilisateur (vous). Placez-vous dans le dossier **/var/www/backup/*** et entrez donc la commande suivante en remplaçant _remi_ par le nom de votre utilisateur.      
`sudo chown -R remi:remi *`

### Création du virtualhost 

De la même façon que pour phpmyadmin, ajouter au fichier _/etc/hosts_ la ligne `127.0.0.1 dev.backup.loc`.   
Dans le dossier cloné `var/www/backup/` se trouve un fichier nommé _backup.conf_. Il contient le virtualhost pour notre interface web. Mettez-le en place à l'aide des commandes suivantes.
```sh
cp /var/www/backup/backup.conf /etc/apache2/sites-available/backup.conf
ln -s /etc/apache2/sites-available/backup.conf /etc/apache2/sites-enabled/backup.conf
sudo /etc/init.d/apache2 restart
```

### Import de bases de données

Avant de pouvoir importer les bases de données, nous devons nous assurer que le fichier `/etc/php/7.0/apache2/php.ini` autorise l'import de base de données ayant une certaine taille. Il peut donc être nécessaire de modifier les valeurs des attributs suivants dans le fichier :
- memory_limit
- post_max_size
- upload_max_filesize

On mettra respectivement les valeurs 1000MB, 400MB, 400MB car la mémoire max doit être supérieure au post et upload max.     

On peut ensuite générer des données aléatoirement grâce au site [generatedata](https://www.generatedata.com/), qui peut nous fourni un .sql, mais il y a déjà un fichier dans le repo.     
Afin de faciliter l'utilisation de l'interface, certaines informations concernant le serveur distant sont sauvegardées en base de données (en local uniquement). Pour cela, utilisez la base de données fournit dans le dossier `/var/www/backup/`.    


Il faut donc importer 2 bases de données. Pour cela, allez dans à l'url _dev.phpmyadmin.loc_, connectez vous, et créez deux nouvelles bases de données :
- appli_web
- remote_servers

Puis pour chacune de ces bases, cliquez sur l'onglet **importer** et sélectionner le fichier correspondant dans le dossier `/var/www/backup/`.

## Fonctionnement de l'interface web

Conformément au code entré dans le Virtualhost, on peut normalement accéder à phpmyadmin en tapant _dev.backup.loc_ dans la barre de recherche du navigateur internet. 

Pour une première connexion, il est nécessaire d'**enregistrer une nouvelle connexion**.  
Un formulaire vous demandera toutes les informations nécessaires à la connexion **SSH** au serveur distant, ainsi que vos identifiants **mySQL** du serveur distant. Vous remarquerez aussi que l'on demande un nom de serveur, mais celui-ci sert simplement de référent, vous pouvez mettre ce que vous voulez.

Une fois votre serveur enregistré, vous pouvez accéder à l'interface. 
Trois options s'offre alors à vous :
- Réaliser une **backup** de vos bases de données
- Restaurer une ou toutes vos bases de données
- Activer ou non l'automatisation des backups

A savoir que la restauration des données vous donnera d'abord le choix de la sauvegarde en vous précisant bien sûr la date et l'heure à laquelle elle a été effectuée. Ensuite, vous pourrez alors choisir de restaurer toute les bases de données, ou alors une en particulier.

### Retrouver les sauvegardes

Lorsque vous enregistrez un nouveau serveur sur l'interface web, un dossier **backups** se créé dans `/home/$USER/Documents/` dans lequel toutes les backups de tous les serveurs se trouveront.    
Ce dossier comportera en fait plusieurs dossiers, un par serveur distant. Ces derniers seront nommés avec le nom de l'utilisateur du serveur distant renseigné, et le nom du serveur donné. _(ex : user_at_Myserver)_    
Dans chacun de ces dossiers se trouveront jusqu'à maximum 5 dossiers, correspondant cette fois à chaque sauvegarde réalisée. Il contiennent donc la date et l'heure de la sauvegarde dans leur nom. Enfin, dans chacun de ces dossiers se trouve plusieurs fichiers *.sql*, un par base de données.    
