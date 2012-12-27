#!/bin/bash

WDR="/tmp/wd-${RANDOM}"
WDR="/tmp/wd"
WD="${WDR}/src"

mkdir -p "${WD}"

cd ..
svn up
svn ci

tar cf - . | (cd ${WD} && tar xf -)
cd "${WD}" || exit 9

find . -depth -name '.svn' -exec rm -r {} \;

rm -r "doc/Doxygen/html"
rm -r "templates_c"
git init
git add *
git remote add origin https://github.com/fisherhu/t4mpsr.git
git config --global user.name Fisher
git config --global user.email fisher@fisher.hu
git commit -m "Initial commit from svn"
git push -u origin master

rm -r "${WDR}"