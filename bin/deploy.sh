# /bin/bash

git checkout .
git pull --rebase origin master
rm application/config/database.php
ln -s /home2/dopamin1/config/database.php application/config/
