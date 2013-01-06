#!/bin/bash

rm -r /tmp/t4mpsrdoc/*

doxygen conf/doxygen.conf > /tmp/doxgen.log
