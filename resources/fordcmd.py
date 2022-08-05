#!/usr/bin/env python3
import sys, os, logging, time, json

from fordpass import Vehicle

if len(sys.argv) <= 4:
	print('Il y a un probleme avec les arguments: ', sys.argv[1], ' / pass: ', sys.argv[2], ' / vin: ', sys.argv[3])
else:
	r = Vehicle(sys.argv[1], sys.argv[2], sys.argv[3]) # Username, Password, VIN # Username, Password, VIN
	try:
		if sys.argv[4] == "lock":
			r.lock();
		elif sys.argv[4] == "unlock":
			r.unlock();
		elif sys.argv[4] == "start":
			r.start();
		elif sys.argv[4] == "stop":
			r.stop();
		elif sys.argv[4] == "test":
			r.test();

	except:
		print('Erreur de connexion')