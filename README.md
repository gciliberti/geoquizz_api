# geoquizz_api

CHAQUE DOSSIER EST UNE API DIFFERENTE qui peut être déployé sur un serveur différent tant que la BDD reste la même

Installation : Création d'un fichier config.ini dans le dossier conf de chaque API tel que :

driver=mysql
host=monip
database=mabdd
username=monuser
password=monmdp
charset=utf8
collation=utf8_unicode_ci
prefix=

Création d'un fichier environnement dans chaque repertoire src contenant :
JWT_SECRET="masuperclefdechiffrement"

Faire un composer install pour chaque API dans le repertoire src.
Faire pointer les VHOSTS vers index.php pour chaque API.

Il reste plus qu'à importer la structure de la BDD jointe au dépot
