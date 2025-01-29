#!/bin/bash

# Variables de connexion à la base de données
MYSQL_USER="root"
MYSQL_PASS=""  # Pas de mot de passe ici
MYSQL_DB="esports_db"
DATE=$(date +%F_%H-%M-%S)
BACKUP_DIR=".\backup_mysql_cloud.sh"
BACKUP_FILE="$BACKUP_DIR/sauvegarde_$DATE.sql"

# Créer le dossier de sauvegarde s'il n'existe pas
mkdir -p $BACKUP_DIR

# Sauvegarde de la base de données
mysqldump -u $MYSQL_USER -p$MYSQL_PASS $MYSQL_DB > $BACKUP_FILE

# Vérifie si la sauvegarde a été effectuée
if [ $? -eq 0 ]; then
    echo "Sauvegarde MySQL réussie et stockée dans $BACKUP_FILE"
else
    echo "Erreur lors de la sauvegarde MySQL"
fi
