#!/bin/bash

PROGRESS_FILE=/tmp/dependancy_fordcar_in_progress
if [ ! -z $1 ]; then
    PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Update des dépendances  *"
echo "**********************************"
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
sudo apt-get clean && sudo apt-get update
echo 50 > ${PROGRESS_FILE}
sudo pip3 install git+https://github.com/cddu33/fordpass-python.git
rm ${PROGRESS_FILE}
echo "**********************************"
echo "*  Dépendances update OK  *"
echo "**********************************"
