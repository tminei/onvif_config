# onvif_config
# do this:
```
sudo mkdir /var/www/.local
sudo mkdir /var/www/.cache
sudo chown www-data.www-data /var/www/.local
sudo chown www-data.www-data /var/www/.cache
sudo -H -u www-data pip3 install pyonvif[discovery]
sudo -H -u www-data pip3 install --upgrade onvif-zeep
sudo chmod -R 777 ~/.cache/
```
# And cp *.py /scr/scripts/cameras
# And cp cam_config.php to www
