@echo off
set PWD=%cd%
set base_project_folder=%0;

title Base Project Creator

:menu
cls
echo Base Project Creator
echo ------------------------------------------------
echo.
echo 1) Create Static Project
echo 2) Create WordPress Project
REM echo 3) Exit
echo.
echo ------------------------------------------------
echo.

set/p nbr=Choose your destiny: 

if %nbr%==1 (
set redirect=new_static
GOTO downloads
)

if %nbr%==2 (
set redirect=new_wp
GOTO downloads
)

if %nbr%==3 (
exit
)


:new_static
xcopy %template_folder%\* %PWD% /e

rm -rf wp-base
rm -rf tmp

cls
echo.
echo ------------------------------------------------
echo Done.
echo ------------------------------------------------
echo.

pause
exit
GOTO menu


:new_wp
cls
echo Base Project Creator
echo ------------------------------------------------
echo.
echo Enter Theme name
echo (for your own sanity, do NOT use spaces!)
echo Or press enter to go back
echo.
echo ------------------------------------------------
echo.

set/p themename=Enter Theme name:

GOTO wp_init


:wp_init
set theme_directory=wp-content\themes\%themename%


echo.
echo ------------------------------------------------
echo Downloading latest version of WordPress
echo ------------------------------------------------
echo.

curl -O http://wordpress.org/latest.tar.gz
tar --strip-components=1 -zxf latest.tar.gz
rm latest.tar.gz

mkdir %theme_directory%

xcopy %template_folder%\* %theme_directory%\ /e
rm -rf tmp

cd %theme_directory%

xcopy wp-base\* . /e

rm -rf wp-base
del basefile.php

mkdir plugins
cd plugins

cls
echo. installing plugins

svn checkout http://plugins.svn.wordpress.org/debug-bar/trunk/ debug-bar
cd debug-bar
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/wordpress-seo/trunk/ wordpress-seo
cd wordpress-seo
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/wp-pagenavi/trunk/ wp-pagenavi
cd wp-pagenavi
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/contact-form-7/trunk/ contact-form-7
cd contact-form-7
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/wp-db-backup/trunk/ wp-db-backup
cd wp-db-backup
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/automatic-wordpress-backup/trunk/ automatic-wordpress-backup
cd automatic-wordpress-backup
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/custom-post-type-archives/trunk/ custom-post-type-archives
cd custom-post-type-archives
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/regenerate-thumbnails/trunk/ regenerate-thumbnails
cd regenerate-thumbnails
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/wp-maintenance-mode/trunk/ wp-maintenance-mode
cd wp-maintenance-mode
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/w3-total-cache/trunk/ w3-total-cache
cd w3-total-cache
rm -rf .svn
cd ..

svn checkout http://plugins.svn.wordpress.org/wp-super-cache/trunk/ wp-super-cache
cd wp-super-cache
rm -rf .svn
cd ..

REM svn checkout http://plugins.svn.wordpress.org/jetpack/trunk/ jetpack
REM cd jetpack
REM rm -rf .svn
REM cd ..



cls
echo.
echo ------------------------------------------------
echo Done.
echo ------------------------------------------------
echo.

pause
exit
GOTO menu

:downloads

mkdir tmp
cd tmp
echo.
echo ------------------------------------------------
echo Downloading latest version of base project
echo ------------------------------------------------
echo.

curl -O https://nodeload.github.com/iamntz/base-project/zipball/master
unzip -u master
rm master

mv * baseproject
set template_folder=%PWD%\tmp\baseproject

echo.
echo ------------------------------------------------
echo Downloading latest version of jQuery
echo ------------------------------------------------
echo.

cd %template_folder%\js\lib\
curl -O http://code.jquery.com/jquery-latest.min.js 
cd %PWD%

if exist %template_folder%\i.bash rm %template_folder%\i.bash
if exist %template_folder%\i.bat rm %template_folder%\i.bat
if exist %template_folder%\readme.md rm %template_folder%\readme.md

GOTO %redirect%