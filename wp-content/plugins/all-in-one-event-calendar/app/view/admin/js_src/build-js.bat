@echo off
where /q nodemon || (
	echo nodemon node package is not installed. You must install node, npm and then run npm install -g nodemon
	goto :eof
)
nodemon -L --watch ./ --watch ./scripts --watch ./libs --watch ./external_libs --watch ./pages --watch ./themes  build/r.js -o build/app.build.js 
:end
