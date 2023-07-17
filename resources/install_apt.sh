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

sudo apt remove -y python3-serial
echo 5 > ${PROGRESS_FILE}
sudo apt-get install -y libxml2-dev libxslt-dev
echo 10 > ${PROGRESS_FILE}
sudo apt-get clean && sudo apt-get update
echo 15 > ${PROGRESS_FILE}
sudo apt-get install -y python3
echo 20 > ${PROGRESS_FILE}
sudo apt-get -y install python3-venv python3-dev
echo 30 > ${PROGRESS_FILE}
python3 -m venv ${BASEDIR}/venv
echo 40 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install "cython<3.0.0" && pip install --no-build-isolation pyyaml
echo 50 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install pyjwt serial
echo 55 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install html5lib pyudev
echo 65 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install asyncio
echo 70 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install httpx
echo 75 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install lxml
echo 85 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install html-parser
echo 95 > ${PROGRESS_FILE}
${BASEDIR}/venv/bin/pip3 install six requests
rm ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances OK  *"
echo "**********************************"
