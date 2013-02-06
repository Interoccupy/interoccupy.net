#!/bin/bash

which nodemon >/dev/null

if [[ $? -eq 0 ]]
then

	nodemon -L \
	    --watch ./ \
	    --watch ./scripts \
	    --watch ./libs \
	    --watch ./external_libs \
	    --watch ./pages \
	    --watch ./themes build/r.js \
	    -o build/app.build.js
	exit 0
else

	echo 'Error: nodemon not found. Install Node.js then: npm install -g nodemon'
	exit 1
fi
