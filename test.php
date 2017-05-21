<?php

/*if (!isset($_COOKIE['testid'])) {
	setcookie('testid', 1,   time()+60*60, null, null, null, true);
}
if (!isset($_COOKIE['testpass'])) {
	setcookie('testpass', 1,   time()+60*60, null, null, null, true);
}

if (isset($_COOKIE['testid'])) {
	setcookie('testid', 2,   time()+60*60, null, null, null, true);
}
if (isset($_COOKIE['testpass'])) {
	setcookie('testpass', 2,   time()+60*60, null, null, null, true);
}

if (isset($_COOKIE['testid']) && $_COOKIE['testid'] == 2) {
	setcookie('testid', 3,   time()-60*60, null, null, null, true);
}
if (isset($_COOKIE['testpass']) && $_COOKIE['testpass'] == 2) {
	setcookie('testpass', 3,   time()-60*60, null, null, null, true);
}
*/
setcookie('pass',   'baka', time()+60*60*24*360, null, null, null, true);
setcookie('userid', 'waka',   time()+60*60*24*360, null, null, null, true);
var_dump($_COOKIE);