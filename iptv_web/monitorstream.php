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

$streamip = $_GET['streamip'];
$probeid = $_GET['probeid'];

echo "<h2><a href=\"index.php\">IPTV RTP Monitor</a></h2>";
echo "<p>Multicast Stream: <b><a href=\"monitorstream.php?streamip=".$streamip."\">".$streamip."</a></b> (Last 24 hours)</p>";


// Connect to DB
$dbc = db_connect($dbhost,$dbuser,$dbpass,$database,$dbtype);

// Query the table for channels
if (!empty($probeid)){
	$sql="SELECT * FROM log WHERE ts > FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - 86400) AND streamip=\"".$streamip."\" AND probeid=\"".$probeid."\" ORDER BY ts DESC";
} else {
	$sql="SELECT * FROM log WHERE ts > FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - 86400) AND streamip=\"".$streamip."\" ORDER BY ts DESC";
}
$query = $dbc->Execute($sql);
if($query === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $dbc->ErrorMsg(), E_USER_ERROR);
} else {
        $rows_returned = $query->RecordCount();
}

$query->MoveFirst();

if (!empty($probeid)){
	echo "<p><i>Stream filtered to results from probe: ".$probeid."</i></p>";
}

echo "<table class=\"table table-striped table-bordered table-hover\">";
echo "<tr><th>Timestamp</th><th>Probe</th><th>Total RTP Packets</th><th>Lost RTP Packets</th><th>Out of Sequence Packets</th></tr>";
while (!$query->EOF) {
	$probeid = $query->fields['probeid'];
	$ts = $query->fields['ts'];
	$totalpackets = $query->fields['totalpackets'];
	$lostpackets = $query->fields['lostpackets'];
	$oospackets = $query->fields['oospackets'];

        echo "<tr>";
	echo "<td>".$ts."</td>";
	echo "<td><a href=\"monitorstream.php?streamip=".$streamip."&probeid=".$probeid."\">".$probeid."</a></td>";
	echo "<td>".$totalpackets."</td>";
	if ($lostpackets > 0){
		echo "<td class=\"danger\"><b>".$lostpackets."</b></td>";
	} else{
		echo "<td>".$lostpackets."</td>";
	}
	if ($oospackets > 0){
		echo "<td class=\"danger\"><b>".$oospackets."</b></td>";
	} else {
		echo "<td>".$oospackets."</td>";
	}
	echo "</tr>";

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

