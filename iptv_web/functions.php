<?php

require('config.php');

define("ADODB_PATH", "/usr/share/php/adodb");
require(ADODB_PATH . "/adodb.inc.php");

function db_connect($dbhost, $dbuser, $dbpass, $database, $dbtype) {
        $dbpass = rawurlencode($dbpass);
        // 1=ADODB_FETCH_NUM, 2=ADODB_FETCH_ASSOC, 3=ADODB_FETCH_BOTH
        $dsn_options='?persist=0&fetchmode=2';
        $dsn = "$dbtype://$dbuser:$dbpass@$dbhost/$database$dsn_options";
        $conn = NewADOConnection($dsn);
        return $conn;
}

?>

