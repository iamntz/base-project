#!/bin/bash
mkdir {css,js,resources,images,content};
cat ~/baseproject/css/screen.css > css/screen.css
cat ~/baseproject/js/script.js > js/script.js
cat ~/baseproject/.gitignore > .gitignore
cat ~/baseproject/.htaccess > .htaccess

cat ~/baseproject/basefile.php > basefile.php

read -p "Add jQuery to js folder? " -n 1
if [[ $REPLY =~ ^[Yy]$ ]]
then
	echo ''
	cd js/
  wget http://code.jquery.com/jquery-latest.min.js
  cd ..
  cat ~/baseproject/basefile.php > basefile.php
else 
cat ~/baseproject/basefile_nojQuery.php > basefile.php
fi

read -p "Git init? " -n 1
if [[ $REPLY =~ ^[Yy]$ ]]
then
	echo ''
  git init
fi

read -p "Git add & commit? " -n 1
if [[ $REPLY =~ ^[Yy]$ ]]
then
	echo ''
  git add .
  git commit -a -m 'Initial Commit'
fi

echo 'Done! Enjoy!'