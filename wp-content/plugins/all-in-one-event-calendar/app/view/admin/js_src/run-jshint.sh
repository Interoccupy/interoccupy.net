#!/bin/bash

if which -s jshint; then
	jshint ./
else
  echo 'Error: jshint not found. Install Node.js then: npm install -g jshint';
	exit 1;
fi
