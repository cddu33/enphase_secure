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
from optparse import OptionParser
from os.path import join
from html.parser import HTMLParser
from json.decoder import JSONDecodeError
try:
    from BeautifulSoup import BeautifulSoup
except ImportError:
    from bs4 import BeautifulSoup

try:
	from jeedom.jeedom import *
except ImportError:
	print("Error: importing module jeedom.jeedom")
	sys.exit(1)

testjeton = False
header = ''
limit = 0
JEEDOM_COM = ''
inventory = False

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
	#jeedom_socket.open()
	try:
		while limit < 2:
			if not limit == 0:
				logging.debug("Tentative de connexion:" + str(limit))
			try:
				time.sleep(int(args.delais))
			except:
				time.sleep(10)
			enphase()
	except KeyboardInterrupt:
		shutdown()
	logging.exception('Erreur de connexion')
	logging.error('Erreur de connexion')
	shutdown()

# ----------------------------------------------------------------------------

def handler(signum=None, frame=None):
	logging.debug("Signal %i caught, exiting..." % int(signum))
	shutdown()

def shutdown():
	logging.debug("Shutdown")
	logging.debug("Removing PID file " + str(_pidfile))
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

	client = httpx.Client(verify=False)
	LOCAL_URL ="https://" + args.ip + "/" 
	if args.renew == "auto": 
		if testjeton != True:
			logging.debug("Recuperation token")
			class MyHTMLParser(HTMLParser):
				def handle_starttag(self, tag, attrs):
					print("Encountered a start tag:", tag)
				def handle_endtag(self, tag):
					print("Encountered an end tag :", tag)
				def handle_data(self, data):
					print("Encountered some data  :", data)

			USER = args.user
			PASSWORD = args.password
			SITE_ID = args.site
			SERIAL_NUMBER = args.serie

			LOGIN_URL = "https://entrez.enphaseenergy.com/login"
			TOKEN_URL = "https://entrez.enphaseenergy.com/entrez_tokens"
			payload_login = {'username': USER, 'password': PASSWORD}
			payload_token = {'Site': SITE_ID, "serialNum": SERIAL_NUMBER}
			headers = {'Content-Type': 'application/json'}

			token = ""
			try:
				r = client.post(LOGIN_URL, data=payload_login)
				r = client.post(TOKEN_URL, data=payload_token)
				parsed_html = BeautifulSoup(r.text, "lxml")
				token = parsed_html.body.find('textarea').text
				decode = jwt.decode(token, options={"verify_signature": False}, algorithms="ES256")
				header = {"Authorization": "Bearer " + token}
				logging.debug("Token: " + token)
				testjeton = True
			except:
				limit = limit + 1
				testjeton = False
				logging.error("Erreur de connexion aux serveurs Enphase")
				JEEDOM_COM.send_change_immediate('error serveur')
	else: 
		try:
			token = args.token
			decode = jwt.decode(token, options={"verify_signature": False, "verify_aud": False}, algorithms="ES256")
			header = {"Authorization": "Bearer " + token}
			testjeton = True
			
		except Exception as e:
			logging.error('Fatal error : '+str(e))
			#logging.info(traceback.format_exc())
			JEEDOM_COM.send_change_immediate('error check')
			testjeton = False
			client.close()
			time.sleep(60)	
	try:
		if testjeton == True:
			logging.debug("Test Token")
			r = client.get(LOCAL_URL + "auth/check_jwt", headers=header)
	except Exception as e:
		logging.error('Fatal error : '+str(e))
		logging.info(traceback.format_exc())
		JEEDOM_COM.send_change_immediate('error check')
		testjeton = False
		client.close()
		time.sleep(60)
	try:
		if testjeton == True:
			#logging.debug(inventory)
			if inventory == False:
				logging.debug("Recuperation Inventaire")
				r = client.get(LOCAL_URL + "inventory.json", headers=header)
				JEEDOM_COM.send_change_immediate(r.json())
				inventory = True
				logging.debug("Attente de 10s")
				time.sleep(10)
	except Exception as e:
		logging.error('Fatal error : '+str(e))
		logging.info(traceback.format_exc())
		JEEDOM_COM.send_change_immediate('error inv')
		testjeton = False
		client.close()
		time.sleep(60)
	try:
		if testjeton == True:	
			logging.debug("Recuperation mesures passerelle")
			r = client.get(LOCAL_URL + "production.json?details=1", headers=header)
			#logging.info(r.json())
			JEEDOM_COM.send_change_immediate(r.json())
			time.sleep(1)
			logging.debug("Recuperation mesures")
			r = client.get(LOCAL_URL + "api/v1/production/inverters", headers=header)
			#logging.info(r.json())
			JEEDOM_COM.send_change_immediate(r.json())

			limit = 0

	except Exception as e:
		
		limit = 1

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
parser.add_argument("--site", help="Site for Enphase Server", type=str)
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
logging.info('Socket port : '+str(_socket_port))
logging.info('Socket host : '+str(_socket_host))
logging.info('PID file : '+str(_pidfile))
logging.info('Apikey : '+str(_apikey))
logging.info('Device : '+str(_device))
logging.info('Callback : '+str(_callback))
logging.info('Delais actualisation : '+str(args.delais))
logging.info('Adresse IP Passerelle : '+str(args.ip))
logging.info('User : '+str(args.user))
logging.info('Password : '+str(args.password))
logging.info('Id Site : '+str(args.site))
logging.info('Numero de serie : '+str(args.serie))
logging.info('Token manuel (non obligatoire) : '+str(args.token))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)	

try:
	jeedom_utils.write_pid(str(_pidfile))
	#jeedom_socket = jeedom_socket(port=_socket_port,address=_socket_host)
	JEEDOM_COM = jeedom_com(apikey=_apikey, url=_callback, cycle=_cycle)
	if not JEEDOM_COM.test():
		logging.error('Network communication issues. Please fixe your Jeedom network configuration.')
		shutdown()
	listen()
except Exception as e:
	logging.exception('Fatal error : '+str(e))
	logging.info(traceback.format_exc())
	shutdown()
