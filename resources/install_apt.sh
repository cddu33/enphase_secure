PROGRESS_FILE=/tmp/dependancy_fordcar_in_progress
if [ ! -z $1 ]; then
    PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances  *"
echo "**********************************"
echo 5 > ${PROGRESS_FILE}
sudo apt-get update
echo 10 > ${PROGRESS_FILE}
sudo apt-get install -y python3
echo 20 > ${PROGRESS_FILE}
sudo apt-get install -y python3-pip
echo 30 > ${PROGRESS_FILE}
sudo pip3 install requests
echo 50 > ${PROGRESS_FILE}
sudo pip3 install --upgrade pip
echo 70 > ${PROGRESS_FILE}
sudo python3 -m pip install --force git+https://github.com/cddu33/fordpass-python.git
echo 100 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépendances OK  *"
echo "**********************************"
rm ${PROGRESS_FILE}