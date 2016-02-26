#!usr/bin/python
# requires requests library: easy_install requests

import requests, sys, subprocess

if len(sys.argv) != 4:
    print 'usage ' + sys.argv[0] + ' <version> <repo> <token>'
    sys.exit(255);

version = sys.argv[1]
repo = sys.argv[2]
token = sys.argv[3]
target = 'master'
body = 'Release for CoScale version ' + version
releaseUrl = 'https://api.github.com/repos/CoScale/' + repo  + '/releases?access_token=' + token;

print ""
print "###########################"
print "Creating release"
print "###########################"
data = {'tag_name': version, 'target_commitish': target, 'name': version, 'body': body}
response = requests.post(releaseUrl, json=data)
releaseResponse = response.json()
assetUrl = releaseResponse['upload_url'].replace('{?name,label}', '')

print ""
print "###########################"
print "Uploading assets"
print "###########################"
headers = {'Content-Type': 'application/octet-stream', 'Authorization': 'token ' + token}
binary = open('CoScale-' + version + '.tgz', 'rb')
requests.post(assetUrl + '?name=CoScale-' + version + '.tgz', headers=headers, data=binary)
