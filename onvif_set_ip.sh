#!/bin/bash
# pip3 install --upgrade onvif_zeep

# login, pass, port, old_ip new_ip
if (( $# == 5 )); then
  python3 onvif_set_ip.py $1 $2 $3 $4 $5 &
  sleep 10
  kill $!
fi
