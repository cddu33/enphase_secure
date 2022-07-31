#!/usr/bin/env python3
import sys, os, logging, time, json

from fordpass import Vehicle

if len(sys.argv) != 5:
	print('Il y a un probleme avec les arguments: ', sys.argv[1], ' / pass: ', sys.argv[2], ' / vin: ', sys.argv[3])
else:
	r = Vehicle(sys.argv[1], sys.argv[2], sys.argv[3]) # Username, Password, VIN # Username, Password, VIN
	try:
		switch(sys.argv[4]){
			case "lock":
				r.lock();
				break;

			case "unlock":
				r.unlock();
				break;

			case "start":
				r.start();
				break;

			case "stop":
				r.stop();
				break;
		}
	except:
		print('Erreur de connexion')