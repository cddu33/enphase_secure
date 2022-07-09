#!/usr/bin/env python3
import sys, os, logging, time, json

from fordpass import Vehicle

if len(sys.argv) != 7:
	raise Exception('Must specify Username, Password and VIN as arguments, e.g. demo.py test@test.com password123 WX231231232')
else:
	r = Vehicle(sys.argv[1], sys.argv[2], sys.argv[3]) # Username, Password, VIN # Username, Password, VIN
	try:
		with open(sys.argv[6] + sys.argv[5], "w+") as json_file:
			st = r.status()
			print(st)
			json.dump(st, json_file)
		if sys.argv[4] == "lock":
			r.lock();
		elif sys.argv[4] == "unlock":
			r.unlock();
	except:
		print('Erreur de connexion')
		print(sys.argv[6] + sys.argv[5])