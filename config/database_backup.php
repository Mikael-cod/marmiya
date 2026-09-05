<?php

return [
    'disk' => 'local',
    'directory' => 'database-backups',
    'mysqldump_binary' => env('DB_BACKUP_MYSQLDUMP', 'mysqldump'),
    'mysql_binary' => env('DB_BACKUP_MYSQL', 'mysql'),
];
