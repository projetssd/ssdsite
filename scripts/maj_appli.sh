#!/bin/sh
DIRNAME=$(dirname $0)
cd ${DIRNAME}/../
git pull
rm -rf cahe/*
rm -rf dist/js/min/*
rm -rf dist/css/min/*