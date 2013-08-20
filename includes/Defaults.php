<?php
$toolserver_mycnf = parse_ini_file("/data/project/etwikt/.my.cnf");

$dbServer = 'etwiktionary.labsdb';
$dbUser = $toolserver_mycnf['user'];
$dbDatabase = 'etwiktionary_p';
$dbPassword = $toolserver_mycnf['password'];
$dbMiserMode = false;
$tsI18nDir = '/data/project/intuition/src/Intuition';
$cacheDir = '/home/kentaur/temp';
$cldrPath = false;
$subdivisionsPath = false;
