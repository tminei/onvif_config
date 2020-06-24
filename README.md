# onvif_config
# do this:
```
sudo -H pip3 install --upgrade onvif_zeep
sudo mkdir /var/www/.local
sudo mkdir /var/www/.cache
sudo chown www-data.www-data /var/www/.local
sudo chown www-data.www-data /var/www/.cache
sudo -H -u www-data pip3 install pyonvif[discovery]
sudo -H -u pip3 install --upgrade onvif_zeep

```
# And cp *.py /scr/scripts/cameras
