#!/bin/bash
# Args: <release> <github token>

GITREPO="coscale-magento-extension"

# clone the repo
mkdir tmp-repo
cd tmp-repo
git clone git@github.com:CoScale/$GITREPO.git
cd ..

# copy the bitbucket repo to the git repo
cp -rf ./app ./tmp-repo/$GITREPO/
cp -rf ./shell ./tmp-repo/$GITREPO/
cp -f .gitignore- ./tmp-repo/$GITREPO/
cp -f build.sh ./tmp-repo/$GITREPO/
cd tmp-repo/$GITREPO

# push changes
git add -A
git commit -m "Sync from Bitbucket"
git push

# remove the git repo again
cd ../..
rm -rf tmp-repo

# build the release
build.sh $1

# launch script to create the github release
python createRelease.py $1 $GITREPO $2

# Cleanup the build
rm -f CoScale-$1.tgz
