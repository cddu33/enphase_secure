PROGRESS_FILE=/tmp/dependancy_fordcar_in_progress
if [ ! -z $1 ]; then
    PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Installation des dépandances  *"
echo "**********************************"
echo $(date)
echo 5 > ${PROGRESS_FILE}
sudo apt-get update
echo 10 > ${PROGRESS_FILE}
sudo apt-get install -y python3 python3-requests
echo 100 > ${PROGRESS_FILE}
echo $(date)
rm ${PROGRESS_FILE}