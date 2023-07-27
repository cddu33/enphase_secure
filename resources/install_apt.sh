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
sudo apt-get clean
sudo apt-get update

echo 10 > ${PROGRESS_FILE}
sudo apt remove -y python3-serial

echo 20 > ${PROGRESS_FILE}
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y libxml2-dev libxslt-dev


echo 30 > ${PROGRESS_FILE}
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y python3

echo 40 > ${PROGRESS_FILE}
sudo DEBIAN_FRONTEND=noninteractive apt-get install -y python3-venv python3-pip python3-dev

echo 50 > ${PROGRESS_FILE}
sudo -u www-data python3 -m venv $BASEDIR/venv

echo 60 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir "cython<3.0.0"

echo 70 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir --no-build-isolation pyyaml


echo 80 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir pyjwt
echo 82 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir serial
echo 84 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir html5lib
echo 86 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir pyudev
echo 88 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir asyncio
echo 90 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir httpx
echo 92 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir lxml
echo 94 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir html-parser
echo 96 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir six
echo 98 > ${PROGRESS_FILE}
sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir requests
echo 100 > ${PROGRESS_FILE}

rm ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances OK  *"
echo "**********************************"