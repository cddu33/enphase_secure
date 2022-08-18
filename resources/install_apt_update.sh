PROGRESS_FILE=/tmp/dependancy_fordcar_in_progress
if [ ! -z $1 ]; then
    PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Update des dépendances  *"
echo "**********************************"
echo $(date)
echo 50 > ${PROGRESS_FILE}
python3 -m pip install --force git+https://github.com/cddu33/fordpass-python.git
echo 100 > ${PROGRESS_FILE}
echo "**********************************"
echo "*  Dépendances update OK  *"
echo "**********************************"
rm ${PROGRESS_FILE}
