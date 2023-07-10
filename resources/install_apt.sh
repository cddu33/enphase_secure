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
echo 15 > ${PROGRESS_FILE}
sudo apt-get install -y python3-pip
echo 20 > ${PROGRESS_FILE}
sudo pip3 install --upgrade pip
echo 25 > ${PROGRESS_FILE}
sudo pip3 uninstall -y serial
echo 30 > ${PROGRESS_FILE}
sudo apt remove -y python3-serial
echo 35 > ${PROGRESS_FILE}
sudo apt-get install libxml2-dev libxslt-dev
echo 45 > ${PROGRESS_FILE}
sudo apt-get install -y python3-setuptools
echo 50 > ${PROGRESS_FILE}
sudo apt-get install -y python3-requests python3-pyudev
echo 60 > ${PROGRESS_FILE}
sudo pip3 install pyjwt
echo 62 > ${PROGRESS_FILE}
sudo pip3 install html5lib
echo 64 > ${PROGRESS_FILE}
sudo pip3 install asyncio
echo 66 > ${PROGRESS_FILE}
sudo pip3 install httpx
echo 68 > ${PROGRESS_FILE}
sudo pip3 install lxml
echo 72 > ${PROGRESS_FILE}
sudo pip3 install html.parser
echo 80 > ${PROGRESS_FILE}
sudo pip3 install --ignore-installed pyserial
echo 95 > ${PROGRESS_FILE}
sudo pip3 install six -U
rm ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances OK  *"
echo "**********************************"
