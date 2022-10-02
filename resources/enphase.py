#!/usr/bin/env python3

import asyncio, logging, re, time, jwt, json, sys, os
from html.parser import HTMLParser
from json.decoder import JSONDecodeError
try:
    from BeautifulSoup import BeautifulSoup
except ImportError:
    from bs4 import BeautifulSoup
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
SORTIE= sys.argv[6]
LOGIN_URL = "https://entrez.enphaseenergy.com/login"
TOKEN_URL = "https://entrez.enphaseenergy.com/entrez_tokens"

payload_login = {'username': USER, 'password': PASSWORD}
payload_token = {'Site': SITE_ID, "serialNum": SERIAL_NUMBER}
headers = {'Content-Type': 'application/json'}

client = httpx.Client(verify=False)
token = ""
try:
    r = client.post(LOGIN_URL, data=payload_login)
    r = client.post(TOKEN_URL, data=payload_token)
    parsed_html = BeautifulSoup(r.text, "lxml")
    token = parsed_html.body.find('textarea').text
    decode = jwt.decode(token, options={"verify_signature": False}, algorithms="ES256")
    header = {"Authorization": "Bearer " + token}
    r = client.get(LOCAL_URL + "auth/check_jwt", headers=header)
    r = client.get(LOCAL_URL + "production.json?details=1", headers=header)
    json.dump(r.json(), open(SORTIE, "w+"))
finally:
    client.close()