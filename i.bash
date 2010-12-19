#!/bin/bash
cp -r ~/baseproject/* .
cp -r ~/baseproject//.[a-zA-Z0-9]* .

rm -rf .git
rm i.bash
rm README

cd js/lib/
  wget http://code.jquery.com/jquery-latest.min.js
cd ../..

read -p "Git init? " -n 1
if [[ $REPLY =~ ^[Yy]$ ]]
then
	echo ''
  git init
	echo ''
  git add .
  git commit -a -m 'Initial Commit'
else
	rm .gitignore
fi

echo 'Done! Enjoy!'