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
echo 50 > ${PROGRESS_FILE}
sudo python3 -m pip install --force git+https://github.com/cddu33/fordpass-python.git
echo 100 > ${PROGRESS_FILE}
echo $(date)
rm ${PROGRESS_FILE}