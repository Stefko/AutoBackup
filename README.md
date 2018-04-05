# AutoBackup
Auto Backup - DB und Daten automatisch per CronJob sichern.
zB jede Nacht um 1h und anschließend eine eMail versenden, mittels:

    MAILTO=name@domain.tld
    1 0 * * * /usr/bin/lynx -dump http://domain.de/backup.php
