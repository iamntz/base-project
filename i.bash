#!/bin/bash

read -p "Is WordPress? " -n 1
if [[ $REPLY =~ ^[Yy]$ ]]
then
	wget http://wordpress.org/latest.tar.gz
	tar --strip-components=1 -zxf latest.tar.gz
	rm latest.tar.gz
	

	echo "Theme name: "
	read theme_name
	cd themes/
	mkdir $theme_name
	cd $theme_name
	mkdir includes
	mkdir plugins
	
	cp -r ~/baseproject/* .
	cp -r ~/baseproject//.[a-zA-Z0-9]* .
	
	cd js/lib/
	  wget http://code.jquery.com/jquery-latest.min.js

	
	cp -r ~/baseproject/wp-base/* .
	
	touch index.php
	touch style.css
	touch header.php
	touch footer.php
	
	rm -rf .git
	rm i.bash
	rm README
	rm -rf wp-base

	
else
	cp -r ~/baseproject/* .
	cp -r ~/baseproject//.[a-zA-Z0-9]* .
	
	rm -rf .git
	rm i.bash
	rm README
	
	rm functions.php
	rm -rf wp-base
	
	cd js/lib/
	  wget http://code.jquery.com/jquery-latest.min.js
	cd ../..
fi


git init
git add .
git commit -a -m 'Initial Commit'

git branch hotfix
git branch release
git branch develop



echo 'Done! Enjoy!'
