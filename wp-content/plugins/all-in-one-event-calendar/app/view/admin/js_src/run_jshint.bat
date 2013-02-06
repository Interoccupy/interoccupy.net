@echo off
where /q jshint || (
	echo jshint node package is not installed. You must install node, npm and then run npm install -g jshint
	goto :eof
)
jshint ./
:end