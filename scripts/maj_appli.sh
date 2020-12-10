#!/bin/sh
DIRNAME=$(dirname $0)
cd ${DIRNAME}/../
echo Répertoire courant : $(pwd)
/usr/bin/git pull
echo $?
rm -rf cache/*
rm -rf dist/js/min/*
rm -rf dist/css/min/*