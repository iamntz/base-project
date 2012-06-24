@echo off
title SASS output Style

:menu
cls
echo SASS output Style
echo ------------------------------------------------
echo.
echo 1) :nested
echo 2) :expanded
echo 3) :compact
echo 4) :compressed
echo.
echo ------------------------------------------------
echo.

set/p nbr=Choose SASS output style 

if %nbr%==1 (
  sass --watch css/sass:css --style nested
)
if %nbr%==2 (
  sass --watch css/sass:css --style expanded
)
if %nbr%==3 (
  sass --watch css/sass:css --style compact
)
if %nbr%==4 (
  sass --watch css/sass:css --style compressed
)



