#!/usr/bin/env python3
import sys, os, logging, time, json

from fordpass import Vehicle

if len(sys.argv) != 6:
	print('Il y a un problème avec les arguments: ', sys.argv[1], ' / pass: ', sys.argv[2], ' / vin: ', sys.argv[3], ' / commande: ',sys.argv[4], ' / adresse: ', sys.argv[5])
else:
	r = Vehicle(sys.argv[1], sys.argv[2], sys.argv[3]) # Username, Password, VIN # Username, Password, VIN
	print(r.status())
	try:
		with open(sys.argv[5], "w+") as json_file:
			st = r.status()
			print(st)
			json.dump(st, json_file)
		if sys.argv[4] == "lock":
			r.lock();
		elif sys.argv[4] == "unlock":
			r.unlock();
	except:
		print('Erreur de connexion')
		print(sys.argv[5])