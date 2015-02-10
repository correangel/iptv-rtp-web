<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="IPTV RTP Stream Monitor">
    <meta name="author" content="Jeremiah Millay">
    <title>IPTV RTP Stream Monitor</title>
    <!-- CSS file -->
    <link href="bootstrap-3.3.2-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="bootstrap-3.3.2-dist/css/bootstrap-theme.min.css" rel="stylesheet" />
    <link href="blog.css" rel="stylesheet" />
    <!-- Javascript -->
    <script src="bootstrap-3.2.0-dist/js/bootstrap.min.js"></script>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="index.php">IPTV RTP Stream Monitor</a>
                        </div>
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                </div>
                <!-- navbar-collapse -->
        </div>
</nav>
<div class="container">
<div class="table-responsive">
<?php
require("functions.php");

//echo "<h2><a href=\"index.php\">IPTV RTP Monitor</a></h2>";
echo "<br><br><br><br>";

// Connect to DB
$dbc = db_connect($dbhost,$dbuser,$dbpass,$database,$dbtype);

// Query the table for probes
$sql='SELECT DISTINCT(probeid) FROM log';
$query = $dbc->Execute($sql);
if($query === false) {
	trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $dbc->ErrorMsg(), E_USER_ERROR);
} else {
	$rows_returned = $query->RecordCount();
}

$query->MoveFirst();
echo "<table class=\"table table-striped table-bordered table-hover\">";
echo "<tr><th>Probe</th></tr>";
while (!$query->EOF) {
	$probeid = $query->fields['probeid'];
	echo "<tr><td><a href=\"probelog.php?probeid=".$probeid."\">".$probeid."</a></td></tr>";

	$query->MoveNext();
}
echo "</table>";


echo "<br><br>";

// Query the table for channels
$sql='SELECT DISTINCT(streamip) FROM log';
$query = $dbc->Execute($sql);
if($query === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $dbc->ErrorMsg(), E_USER_ERROR);
} else {
        $rows_returned = $query->RecordCount();
}

$query->MoveFirst();
echo "<table class=\"table table-striped table-bordered table-hover\">";
echo "<tr><th>Multicast IP</th></tr>";
while (!$query->EOF) {
        $streamip = $query->fields['streamip'];
        echo "<tr><td><a href=\"monitorstream.php?streamip=".$streamip."\">".$streamip."</a></td></tr>";

        $query->MoveNext();
}
echo "</table>";
// Close Database connection
$dbc->Close();
?>
</div>
</div>
</body>
</html>

