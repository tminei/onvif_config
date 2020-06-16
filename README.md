# onvif_config
# do this shit to install pyonvif to www-data user:
```
sudo mkdir /var/www/.local
sudo mkdir /var/www/.cache
sudo chown www-data.www-data /var/www/.local
sudo chown www-data.www-data /var/www/.cache
sudo -H -u www-data pip3 install pyonvif[discovery]
```
# And cp *.py /scr/scripts/cameras
