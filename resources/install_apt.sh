#!/bin/bash
######################### INCLUSION LIB ##########################
BASEDIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
#wget https://raw.githubusercontent.com/NebzHB/dependance.lib/master/dependance.lib -O $BASEDIR/dependance.lib &>/dev/null
PROGRESS_FILENAME=dependancy
PLUGIN=$(basename "$(realpath $BASEDIR/..)")
LANG_DEP=en
. ${BASEDIR}/dependance.lib
##################################################################
pre
step 0 "Synchronize the package index"
try apt-get clean
try sudo apt-get update

step 10 "Remove Python3 serial"
try sudo apt remove -y python3-serial

step 20 "Instal libxml2 et xslt"
try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y libxml2-dev libxslt-dev


step 30 "Instal python3"
try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y python3

step 40 "Instal python3 venv et pip"
try sudo DEBIAN_FRONTEND=noninteractive apt-get install -y python3-venv python3-pip python3-dev

step 50 "Create a python3 Virtual Environment"
try sudo -u www-data python3 -m venv $BASEDIR/venv

step 70 "Install required python3 libraries in venv"
try sudo -u www-data $BASEDIR/jmqttd/venv/bin/pip3 install --no-cache-dir -r $BASEDIR/requirements.txt

