sudo apt-get update -y
sudo apt-get install curl sqlite3 -y

if ! getent group media >/dev/null; then
    sudo groupadd media
fi

if ! getent passwd radarr >/dev/null; then
    sudo adduser --system --no-create-home --ingroup media radarr
    sleep 3
fi

if ! getent group media | grep -qw radarr; then
    sudo usermod -a -G media radarr
    sleep 3
fi

sudo mkdir -p /var/lib/radarr
sudo chown -R radarr:media /var/lib/radarr
sudo chmod 775 /var/lib/radarr

echo ""
ARCH=$(dpkg --print-architecture)

dlbase="http://radarr.servarr.com/v1/update/{{ $branch }}/updatefile?os=linux&runtime=netcore"
case "$ARCH" in
"amd64") DLURL="${dlbase}&arch=x64" ;;
"armhf") DLURL="${dlbase}&arch=arm" ;;
"arm64") DLURL="${dlbase}&arch=arm64" ;;
*)
    echo -e "Arch is not supported!"
    exit 1
;;
esac

sudo wget --content-disposition "$DLURL"
sudo tar -xvzf Radarr*.linux*.tar.gz >/dev/null 2>&1
sudo rm -rf /opt/Radarr
sudo mv Radarr /opt/
sudo chown radarr:media -R /opt/Radarr
sudo chmod 775 /opt/Radarr

cat << EOF | sudo tee /etc/systemd/system/radarr.service > /dev/null
[Unit]
Description=Radarr Daemon
After=syslog.target network.target
[Service]
User=radarr
Group=media
UMask=0002
Type=simple

ExecStart=/opt/Radarr/Radarr -nobrowser -data=/var/lib/radarr/
TimeoutStopSec=20
KillMode=process
Restart=on-failure
[Install]
WantedBy=multi-user.target
EOF

sudo systemctl -q daemon-reload
sudo systemctl enable --now -q radarr

sudo rm Radarr*.linux*.tar.gz
