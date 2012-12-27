#!/bin/bash

rm -r ../doc/Doxygen/*

doxygen conf/doxygen.conf > /tmp/doxgen.log
