#!/usr/bin/env python3

import asyncio
import logging
import re
import time
import jwt
import json

from html.parser import HTMLParser
from json.decoder import JSONDecodeError
try:
    from BeautifulSoup import BeautifulSoup
except ImportError:
    from bs4 import BeautifulSoup
#from envoy_utils.envoy_utils import EnvoyUtils

import httpx

class MyHTMLParser(HTMLParser):
    def handle_starttag(self, tag, attrs):
        print("Encountered a start tag:", tag)

    def handle_endtag(self, tag):
        print("Encountered an end tag :", tag)

    def handle_data(self, data):
        print("Encountered some data  :", data)

LOCAL_URL ="https://" + sys.argv[1] + "/" 
USER = sys.argv[2]
PASSWORD = sys.argv[3]
SITE_ID = sys.argv[4]
SERIAL_NUMBER = sys.argv[5]
SORTIE = sys.argv[6]

LOGIN_URL = "https://entrez.enphaseenergy.com/login"
TOKEN_URL = "https://entrez.enphaseenergy.com/entrez_tokens"

payload_login = {'username': USER, 'password': PASSWORD}

payload_token = {'Site': SITE_ID, "serialNum": SERIAL_NUMBER}

headers = {'Content-Type': 'application/json'}


client = httpx.Client(verify=False)
token = ""
try:
    r = client.post(LOGIN_URL, data=payload_login)
    print(r.status_code)
    #print(r.text)
    r = client.post(TOKEN_URL, data=payload_token)
    print(r.status_code)
    #print(r.text)
    parsed_html = BeautifulSoup(r.text, "lxml")
    token = parsed_html.body.find('textarea').text
    print(token)
    decode = jwt.decode(token, options={"verify_signature": False}, algorithms="ES256")
    print(decode["exp"])

    header = {"Authorization": "Bearer " + token}
    r = client.get(LOCAL_URL + "auth/check_jwt", headers=header)
    print(r.text)

    r = client.get(LOCAL_URL + "api/v1/production", headers=header)
    print(r.text)
    json.dump(r.json(), open(SORTIE, "w+"))
finally:
    client.close()