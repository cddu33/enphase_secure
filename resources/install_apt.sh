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
try sudo apt-get clean
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

step 60 "Install Cython in venv"
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir "cython<3.0.0"

step 70 "Install Pyyaml in venv"
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir --no-build-isolation pyyaml


step 80 "Install required python3 libraries in venv"
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir pyjwt
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir serial
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir html5lib
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir pyudev
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir asyncio
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir httpx
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir lxml
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir html-parser
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir six
try sudo -u www-data $BASEDIR/venv/bin/pip3 install --no-cache-dir requests