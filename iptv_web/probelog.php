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

$probeid = $_GET['probeid'];
$streamip = $_GET['streamip'];
$errors = $_GET['errors'];

echo "<h2><a href=\"index.php\">IPTV RTP Monitor</a></h2>";
echo "<p>Remote Probe: <b><a href=\"probelog.php?probeid=".$probeid."\"</a>".$probeid."</a></b> (Last 24 hours)</p>";
if (!empty($streamip)){
	echo "<p><b><a href=\"probelog.php?probeid=".$probeid."&streamip=".$streamip."&errors=1\">Only show errored intervals</a></b></p>";
} else {
	echo "<p><b><a href=\"probelog.php?probeid=".$probeid."&errors=1\">Only show errored intervals</a></b></p>";
}

// Connect to DB
$dbc = db_connect($dbhost,$dbuser,$dbpass,$database,$dbtype);

// Query the table for channels

if (!empty($streamip)){
	if (!empty($errors)){
		$sql="SELECT * FROM log WHERE ts > FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - 86400) AND probeid=\"".$probeid."\" AND streamip=\"".$streamip."\" AND (lostpackets >= 1 OR oospackets >= 1) ORDER BY ts DESC";
	} else {
		$sql="SELECT * FROM log WHERE ts > FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - 86400) AND probeid=\"".$probeid."\" AND streamip=\"".$streamip."\" ORDER BY ts DESC";
	}
} else {
	if (!empty($errors)){
		$sql="SELECT * FROM log WHERE ts > FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - 86400) AND probeid=\"".$probeid."\" AND (lostpackets >= 1 OR oospackets >= 1) ORDER BY ts DESC";
	} else {
		$sql="SELECT * FROM log WHERE ts > FROM_UNIXTIME(UNIX_TIMESTAMP(NOW()) - 86400) AND probeid=\"".$probeid."\" ORDER BY ts DESC";
	}
}
$query = $dbc->Execute($sql);
if($query === false) {
        trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $dbc->ErrorMsg(), E_USER_ERROR);
} else {
        $rows_returned = $query->RecordCount();
}

$query->MoveFirst();

if (!empty($streamip)){
        echo "<p><i>Probe log filtered to results from stream: ".$streamip."</i></p>";
}

if (!empty($errors)){
        echo "<p><i>Probe log filtered to only show errored time intervals</i></p>";
}


echo "<table class=\"table table-striped table-bordered table-hover\">";
echo "<tr><th>Timestamp</th><th>Multicast Stream</th><th>Total RTP Packets</th><th>Lost RTP Packets</th><th>Out of Sequence Packets</th></tr>";
while (!$query->EOF) {
	$ts = $query->fields['ts'];
	$totalpackets = $query->fields['totalpackets'];
	$lostpackets = $query->fields['lostpackets'];
	$oospackets = $query->fields['oospackets'];
	$streamip = $query->fields['streamip'];


        echo "<tr>";
	echo "<td>".$ts."</td>";
	echo "<td><a href=\"probelog.php?probeid=".$probeid."&streamip=".$streamip."\">".$streamip."</a></td>";
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

