#!/bin/sh

current_dir=$(pwd)
ftp_dir="/home10/ddboammf/tuiter.fragua.com.ar/public_html/"

sed -i '' "s@${current_dir}@${ftp_dir}@g" ./bootstrap/cache/config.php
sed -i '' "s@${current_dir}@${ftp_dir}@g" ./bootstrap/cache/packages.php
sed -i '' "s@${current_dir}@${ftp_dir}@g" ./bootstrap/cache/services.php
sed -i '' "s@${current_dir}@${ftp_dir}@g" ./bootstrap/cache/events.php
sed -i '' "s@${current_dir}@${ftp_dir}@g" ./bootstrap/cache/routes-v7.php
