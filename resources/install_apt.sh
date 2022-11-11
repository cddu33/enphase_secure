#!/bin/bash
PROGRESS_FILE=/tmp/dependancy_enphasesecur_in_progress
if [ ! -z $1 ]; then
    PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances  *"
echo "**********************************"
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
echo 5 > ${PROGRESS_FILE}
sudo apt-get clean && sudo apt-get update
echo 10 > ${PROGRESS_FILE}
sudo apt-get install -y python3
echo 20 > ${PROGRESS_FILE}
sudo apt-get install -y python3-pip
echo 30 > ${PROGRESS_FILE}
sudo pip3 install --upgrade pip
echo 40 > ${PROGRESS_FILE}
sudo apt-get install -y python3-setuptools
echo 60 > ${PROGRESS_FILE}
sudo pip3 install pyjwt html.parser html5lib bs4 asyncio httpx lxml
echo 60 > ${PROGRESS_FILE}
sudo pip3 install six -U
rm ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances OK  *"
echo "**********************************"
