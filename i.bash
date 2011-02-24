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


read -p "Is WordPress? " -n 1
if [[ $REPLY =~ ^[Yy]$ ]]
then
	wget http://wordpress.org/latest.tar.gz
	tar --strip-components=1 -zxf latest.tar.gz
	rm latest.tar.gz
	
	echo "Theme name: "
	read theme_name
	cd wp-content/themes/
	mkdir $theme_name
	cd $theme_name
	mkdir includes
	mkdir plugins
	~/i.bash
	
	rm -rf wp-base
	
	cp -r ~/baseproject/wp-base/* .
	
	touch functions.php
	touch index.php
	touch style.css
	touch header.php
	touch footer.php
fi

echo 'Done! Enjoy!'
