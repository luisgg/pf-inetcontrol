#!/bin/sh
# Simple init script for lighhttpd

rc_start() {
	/usr/local/sbin/lighttpd -f /var/etc/inetctl-http-404.conf
	/usr/local/sbin/lighttpd -f /var/etc/inetctl-admin.conf
}

rc_stop() {
	kill `cat /var/run/inetctl-http-404.pid`
	kill `cat /var/run/inetctl-admin.pid`
}

case $1 in
	start)
		rc_start
		;;
	stop)
		rc_stop
		;;
	restart)
		rc_stop
		rc_start
		;;
esac

