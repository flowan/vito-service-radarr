sudo systemctl stop radarr
sudo systemctl disable radarr

sudo rm -rf /opt/Radarr
sudo rm -rf /var/lib/radarr
sudo rm -rf /etc/systemd/system/radarr.service

sudo systemctl -q daemon-reload
