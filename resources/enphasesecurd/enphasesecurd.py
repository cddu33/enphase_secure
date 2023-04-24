# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# 
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.

import logging, string, sys, os, time, datetime, traceback, re, signal, asyncio, jwt, httpx, json, argparse
from os.path import join
from json.decoder import JSONDecodeError

try:
	from jeedom.jeedom import *
except ImportError:
	print("Error: importing module jeedom.jeedom")
	sys.exit(1)

testjeton = False
renew = 0
header = ''
limit = 0
JEEDOM_COM = ''
inventory = False
token = ""

def read_socket():
	global JEEDOM_SOCKET_MESSAGE
	if not JEEDOM_SOCKET_MESSAGE.empty():
		logging.debug("Message received in socket JEEDOM_SOCKET_MESSAGE")
		message = json.loads(jeedom_utils.stripped(JEEDOM_SOCKET_MESSAGE.get()))
		if message['apikey'] != _apikey:
			logging.error("Invalid apikey from socket : " + str(message))
			return
		try:
			print ('read')
		except Exception as e:
			logging.error('Send command to demon error : '+str(e))

def listen():
	global limit
	try:
		while limit < 3:
			if limit >= 1:
				logging.info("Tentative de connexion:" + str(limit))
			if  limit >= 2:
				logging.debug("Attente 120s avant reconnexion")
				time.sleep(120)
			try:
				time.sleep(int(args.delais))
			except:
				time.sleep(30)
			enphase()
	except:
		logging.exception('Erreur de connexion')
		logging.error('Erreur de connexion')
		sleep(5)
		shutdown()
	logging.exception('Erreur de connexion')
	logging.error('Erreur de connexion')
	sleep(5)
	shutdown()

# ----------------------------------------------------------------------------

def handler(signum=None, frame=None):
	logging.debug("Signal %i caught, exiting..." % int(signum))
	shutdown()

def shutdown():
	logging.debug("Shutdown")
	logging.debug("Removing PID file " + str(_pidfile))
	JEEDOM_COM.send_change_immediate('error arret')
	try:
		os.remove(_pidfile)
	except:
		pass
	try:
		jeedom_socket.close()
	except:
		pass
	try:
		jeedom_serial.close()
	except:
		pass
	logging.debug("Exit 0")
	sys.stdout.flush()
	os._exit(0)

# ----------------------------------------------------------------------------
def enphase():
	global testjeton
	global header
	global limit
	global JEEDOM_COM
	global inventory
	global renew
	global token

	renew = renew + int(args.delais)
	client = httpx.Client(verify=False)
	LOCAL_URL ="https://" + args.ip + "/" 
	#recupération Token auto
	if args.renew == "auto": 
		if testjeton == False :
			if token == "" :
				logging.info("Recuperation token")
				user = args.user
				password = args.password
				envoy_serial = args.serie
				headers = {'Content-Type': 'application/json'}
				try:
					data = {'user[email]': user, 'user[password]': password}
					response = requests.post('https://enlighten.enphaseenergy.com/login/login.json?',data=data) 
					response_data = json.loads(response.text)
					data = {'session_id': response_data['session_id'], 'serial_num': envoy_serial, 'username':user}
					response = requests.post('https://entrez.enphaseenergy.com/tokens', json=data)
					token = response.text
					logging.debug(token)
				except:
					logging.error("Erreur de connexion aux serveurs Enphase")
					JEEDOM_COM.send_change_immediate('error serveur')
					time.sleep(5)
					shutdown()

	#utilisation du token manuel
	else:
		if testjeton == False:
			logging.debug("Token Manuel, recuperation de jeedom")
			token = args.token
	
	#retest du jeton si utilisé 12h
	if  renew > (43200/int(args.delais)):
		logging.debug("Token utilisé 12h, on le dévalide")
		testjeton = False
	logging.debug("Nombre d'utilisation du token:")
	logging.debug(renew)
	# 3 tentative de validation du token si il n'a pas déjà été validé		
	while (testjeton==False & limit <= 3):
		try:
			renew = 0
			if args.renew == "manu": 
				token = jwt.decode(token, options={"verify_signature": False, "verify_aud": False}, algorithms="ES256")
			header = {"Authorization": "Bearer " + token}
			logging.info("Test Token")
			r = client.get(LOCAL_URL + "auth/check_jwt", headers=header)
			testjeton = True	
		except:
			limit = limit + 1
			testjeton = False
			logging.info("Erreur de vérification du jeton, attente de 60s pour recommmencer")
			JEEDOM_COM.send_change_immediate('error check')
			time.sleep(60)

			#renouvellement du token
			if limit>=3:
				logging.info("Renouvellement du token")
				token = ""
				JEEDOM_COM.send_change_immediate('error check bis')
	try:
		#si le token et bon on regarde si l'inventaire est présent
		if testjeton == True:
			if inventory == False:
				logging.debug("Recuperation Inventaire")
				r = client.get(LOCAL_URL + "inventory.json", headers=header)
				JEEDOM_COM.send_change_immediate(r.json())
				inventory = True
				logging.debug("Attente de 5s")
				time.sleep(5)
	except:
		limit = limit + 1
		logging.error("Erreur lors de la récupération de l'inventaire, attente de 10s pour recommmencer")
		JEEDOM_COM.send_change_immediate('error inv')
		testjeton = False
		time.sleep(10)
	try:
		if testjeton == True:	
			logging.info("Recuperation mesures")
			# logging.debug("Recuperation mesures passerelle")
			r = client.get(LOCAL_URL + "production.json?details=1", headers=header)
			
			JEEDOM_COM.send_change_immediate(r.json())
			time.sleep(1)
			logging.debug("Recuperation mesures onduleurs")
			r = client.get(LOCAL_URL + "api/v1/production/inverters", headers=header)
			JEEDOM_COM.send_change_immediate(r.json())
			limit = 0
	except:
		limit = limit + 1
		logging.error("Erreur lors de la récupération des mesures attente de 10s pour recommmencer")
		testjeton = False
		time.sleep(10)

#Demon

_log_level = "error"
_socket_port = 55060
_socket_host = 'localhost'
_device = 'auto'
_pidfile = '/tmp/demond.pid'
_apikey = ''
_callback = ''
_cycle = 0.5

parser = argparse.ArgumentParser(
    description='Daemon for Enphase Secure')
parser.add_argument("--renew", help="Auto Manu", type=str)
parser.add_argument("--device", help="Device", type=str)
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--callback", help="Callback", type=str)
parser.add_argument("--apikey", help="Apikey", type=str)
parser.add_argument("--cycle", help="Cycle to send event", type=str)
parser.add_argument("--pid", help="Pid file", type=str)
parser.add_argument("--socketport", help="Port for Enphase Server", type=str)
parser.add_argument("--user", help="User for Enphase Server", type=str)
parser.add_argument("--password", help="Password for Enphase Server", type=str)
parser.add_argument("--serie", help="Serie for Enphase Server", type=str)
parser.add_argument("--token", help="Token Enphase Server", type=str)
parser.add_argument("--ip", help="Adresse IP passrelle", type=str)
parser.add_argument("--delais", help="Delais actualisation", type=str)
args = parser.parse_args()

if args.device:
	_device = args.device
if args.loglevel:
    _log_level = args.loglevel
if args.callback:
    _callback = args.callback
if args.apikey:
    _apikey = args.apikey
if args.pid:
    _pidfile = args.pid
if args.cycle:
    _cycle = float(args.cycle)
if args.socketport:
	_socket_port = int(args.socketport)

jeedom_utils.set_log_level(_log_level)

logging.info('Start demond')

logging.info('Log level : '+str(_log_level))
logging.debug('Socket port : '+str(_socket_port))
logging.debug('Socket host : '+str(_socket_host))
logging.debug('PID file : '+str(_pidfile))
logging.debug('Apikey : '+str(_apikey))
logging.debug('Device : '+str(_device))
logging.debug('Callback : '+str(_callback))
logging.debug('Delais actualisation : '+str(args.delais))
logging.info('Adresse IP Passerelle : '+str(args.ip))
if args.renew == "auto":
	logging.debug('User : '+str(args.user))
	logging.debug('Password : '+str(args.password))
	logging.debug('Numero de serie : '+str(args.serie))
else:
	logging.debug('Token: '+str(args.token))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)	

try:
	jeedom_utils.write_pid(str(_pidfile))
	JEEDOM_COM = jeedom_com(apikey=_apikey, url=_callback, cycle=_cycle)
	if not JEEDOM_COM.test():
		logging.error('Network communication issues. Please fixe your Jeedom network configuration.')
		shutdown()
	listen()
except Exception as e:
	logging.exception('Fatal error : '+str(e))
	logging.info(traceback.format_exc())
	shutdown()
