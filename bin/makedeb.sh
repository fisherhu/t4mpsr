#!/bin/bash

# This script will create a .deb package

PKG="t4mpsr"
VERS="0.0"
DVERS="1"
WORK="/tmp/${PKG}-${VERS}"

DEBUTILS="debhelper devscripts"

#aptitude -y install ${DEBUTILS}

rm -fr "${WORK}"

mkdir -p  "${WORK}"/debian
cp -r conf/deb/* "${WORK}"/debian/
cd ..
tar zcf "${WORK}/../${PKG}_${VERS}.orig.tar.gz" .
# mkdir -p "${WORK}/usr/share/${PKG}"
#cd "${WORK}/usr/share/${PKG}" && tar zxf "${WORK}/../${PKG}_${VERS}.orig.tar.gz"
cd "${WORK}" && tar zxf "${WORK}/../${PKG}_${VERS}.orig.tar.gz"
cd "${WORK}/debian/" && debuild binary-indep
# > log 2>&1

#aptitude -y remove ${DEBUTILS}