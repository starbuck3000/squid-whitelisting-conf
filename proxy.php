<?php
error_reporting(E_ALL);
const NL = PHP_EOL;
const TITLE = "H Proxy";
const WAIT_SECONDS = 3;

$blacklist = array(
    '127.0.0.1',
    'ib.adnxs.com',
    'secure.adnxs.com',
    'aax-eu.amazon.fr',
    'aax-eu.amazon-adsystem.com',
    '.doubleclick.net',
    'radar.cedexis.com',
    'ubs.demdex.net',
    'connect.facebook.net',
    'chrome-devtools-frontend.appspot.com',
    'adservice.google.c',
    'www.googleadservices.com',
    'www.google-analytics.com',
    'ssl.google-analytics.com',
    'tpc.googlesyndication.com',
    'www.googletagmanager.com',
    'www.googletagservices.com',
    'connectivitycheck.platform.hicloud.com',
    'bam.nr-data.net',
    'www.summerhamster.com',
    'ads-api.twitter.com',
    'analytics.twitter.com',
    'use.typekit.net',
);

function s($aMsg)
{
    echo($aMsg.'<br/>'.PHP_EOL);
}


$pfile = '/var/log/squid/access.log';
if(!file_exists($pfile))
    die('file not found: '.$pfile);

$add = isset($_GET['add']) ? trim($_GET['add']) : '';
$schema = isset($_GET['schema']) ? trim($_GET['schema']) : '';

if(strlen($schema) > 0 and $schema !== 'https' and $schema !== 'http')
    die('invalid schema.');
else if(strlen($add) > 0 and !preg_match('/^[a-zA-Z0-9\.\-\_]+$/', $add))
    die('invalid url: '.htmlspecialchars($add));

$html = '';
$added = false;
if(strlen($add) > 0)
{
    // add entry in proxy db
    $html .= ('adding '.$add.' to proxy in mode '.$schema.NL);
    $output = null;
    if($schema === 'https')
        exec('echo '.$add.' >> /etc/squid/lists/ssl.txt', $output);
    else
        exec('echo '.$add.' >> /etc/squid/lists/whitelist.txt', $output);

    var_dump($output);
    $html .= ('done.'.NL);

    // restart proxy
    $html .= ('restarting proxy...'.NL);
    exec('sudo /bin/systemctl reload squid 2>&1', $output);
    var_dump($output);
    $html .= ('done.'.NL);
    $html .= ('<a href="?1=1">reload</a>'.NL);
    $added = true;
}
else
{

    // read latest entries from blocker urls log and render list of attempted hosts
	// warning: ensure log rotation is configured. this script excepts the log file to be negligible size
    $lines = file_get_contents($pfile);
    $lines = explode("\n", $lines);
    $lines = array_reverse($lines);

    $html .= (NL.count($lines).' lines read. (<a href="?1=1">reload</a>)'.NL.NL.NL);

    $httpHosts = array();
    $httpsHosts = array();
    $blacklisted = false; // hosts that match a blacklisted entry will not be shown on screen to avoid miss-clicks
    $shownEntriesCount = 0;
    foreach($lines as $line)
    {
        if($shownEntriesCount > 20)
            break;

        $line = trim($line);
        $pos = strpos($line, 'TCP_DENIED');
        if($pos === false)
            continue;
        $target = substr($line, $pos+14);
        $target = explode(' ', $target)[3];

        // blacklist filter
		// the list is a simple *q* match --> if you build exact match, replace this with in_array().
        foreach($blacklist as $blacklistedUrl)
        {
            if(strpos($target, $blacklistedUrl) !== false)
            {
                $blacklisted = true;
                break;
            }
        }

		// if a blacklisted item is matched -> skip to the next row
        if($blacklisted)
        {
            $blacklisted = false;
            continue;
        }

		// entry is not blacklisted -> prepare to render
        $https = true;
        if(strpos($target, ':443') !== false)
        {
           $target = str_replace(':443', '', $target);
        }
        else
        {
            $https = false;
            $target = parse_url($target, PHP_URL_HOST);
        }

		// hosts are only shown once on screen (avoid having same host shown 30 times, e.g. images)
        if(!in_array($target, $httpHosts) and !in_array($target, $httpsHosts))
        {
            if($https)
                $html .= ('HTTPS: <a href="?add='.$target.'&schema=https">'.$target.'</a>'.NL.NL);
            else
                $html .= ('HTTP: <a href="?add='.$target.'&schema=http">'.$target.'</a>'.NL.NL);

            if($https)
                $httpsHosts[] = $target;
            else
                $httpHosts[] = $target;

            $shownEntriesCount++;
        }

    }
}


?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
	// if page is showing a host addition output, page is reloaded after WAIT_SECONDS.
    if($added)
        echo('<meta http-equiv="refresh" content="'.WAIT_SECONDS.'; url=proxy.php?1=1">'.NL);
?>
<title><?php echo(TITLE); ?></title>
</head>
<body>
<?php
echo(nl2br($html));
?>
</body>
</html>
