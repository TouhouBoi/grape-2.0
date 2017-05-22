<?php
require_once '../config.php';
$dev_server = $grp_config_server_type == 'dev';

define('VERSION', '0.8.2');

function connectSQL($server, $user, $pw, $name) {
$mysql = new mysqli($server, $user, $pw, $name);
$mysql->set_charset('utf8mb4');

if($mysql->connect_errno){
http_response_code(502); die(); }
$mysql->query('SET time_zone = "-4:00"');
date_default_timezone_set('America/New_York');
return $mysql;
}

function initAll() {
$mysql = connectSQL(CONFIG_DB_SERVER, CONFIG_DB_USER, CONFIG_DB_PASS, CONFIG_DB_NAME);
return $mysql;
}

function localeSet($custom) {
require_once '../l10n/langs.php';
if(!empty($custom)) {
$lang = $custom;
		}
elseif(!empty($_GET['locale_lang'])&&in_array($_GET['locale_lang'], ALLOWED_LANGS)) {
$lang = $_GET['locale_lang'];
		}
elseif(!empty($_COOKIE['lang'])&&in_array($_COOKIE['lang'], ALLOWED_LANGS)) {
$lang = $_COOKIE['lang'];
		}
else {
$browser_lang = explode(",",($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''))[0];
if(!empty($browser_lang)&&in_array($browser_lang, ALLOWED_LANGS)) {
$lang = $browser_lang;
	}	}
if(!isset($lang)) {
$lang = DEFAULT_LOCALE_LANG;
		}
if(empty($_COOKIE['lang']) || $_COOKIE['lang'] != $lang) {
setcookie('lang', $lang, (time() + 664800), '/');
	}
// Set locale constant
define('LOCALE', $lang);
//
$lang_enc = str_replace('-', '_', LOCALE).'.UTF-8';
   setlocale(LC_ALL, $lang_enc);
// Change later to a default
   $domain = 'default';
   bindtextdomain($domain, '../l10n/');
   textdomain($domain);
}

function setTextDomain($domain) {
   bindtextdomain($domain, '../l10n/');
   textdomain($domain);
}

/* Maybe later? Production? 
require_once 'err_display.php';
set_error_handler('grp_err', E_ERROR);
*/

if(!is_callable('humanTiming')) {
function humanTiming($time) {
if(time() - $time >= 345600) {
return date("m/d/Y g:i A",$time); }
    $time = time() - $time; // to get the time since that moment
if(strval($time) < 1) {  $time = 1; } if($time <= 59) {
return 'Less than a minute ago'; }
    $tokens = array (86400 => 'day', 3600 => 'hour', 60 => 'minute');
    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'').' ago';
    } } }

	function getMii($user, $feeling_id) {
if(!empty($feeling_id)) {
/* Please fix!

$att_userfaceJSON = json_decode($user['user_face']);
if($att_userfaceJSON) {
if($feeling_id == '0' && !empty($att_userfaceJSON->normal)) {

	}
}

*/
	if(!empty($user['mii_hash'])) {
if($feeling_id == '0') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_normal_face.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = 'Yeah!'; }
if($feeling_id == '1') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_happy_face.png';
$mii_face_feeling = 'happy'; }
$mii_face_miitoo = 'Yeah!';
if($feeling_id == '2') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_like_face.png';
$mii_face_feeling = 'like';
$mii_face_miitoo = htmlspecialchars('Yeah♥'); }
if($feeling_id == '3') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_surprised_face.png';
$mii_face_feeling = 'surprised';
$mii_face_miitoo = 'Yeah!?'; }
if($feeling_id == '4') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_frustrated_face.png';
$mii_face_feeling = 'frustrated';
$mii_face_miitoo = 'Yeah...'; }
if($feeling_id == '5') {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_puzzled_face.png';
$mii_face_feeling = 'puzzled';
$mii_face_miitoo = 'Yeah...'; }
	}
elseif(!empty($user['user_face'])) {
$mii_face_output = htmlspecialchars($user['user_face']);
$mii_face_feeling = 'normal';
$mii_face_miitoo = 'Yeah!';
} else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
$mii_face_feeling = 'normal';
$mii_face_miitoo = 'Yeah!'; }
}
else {
	if(!empty($user['mii_hash'])) {
$mii_face_output = 'https://mii-secure.cdn.nintendo.net/'.$user['mii_hash'].'_normal_face.png'; } elseif(!empty($user['user_face'])) { $mii_face_output = htmlspecialchars($user['user_face']); } else {
$mii_face_output = '/img/mii/img_unknown_MiiIcon.png';
}

}
return array(
'output' => $mii_face_output,
'feeling' => (!empty($feeling_id) ? $mii_face_feeling : null),
'miitoo' => (!empty($feeling_id) ? $mii_face_miitoo : null),
'official' => (!empty($user['official_user']) && $user['official_user'] == 1 ? true : false)
);
     }

function json500() {
global $mysql;
http_response_code(500);
header('Content-Type: application/json; charset=utf-8');
print json_encode(array(
'success' => 0, 'errors' => [array( 'message' => 'An internal error has occurred.', 'error_code' => 1600000 + $mysql->errno)], 'code' => 500));
}
function jsonSuccess() {
header('Content-Type: application/json; charset=utf-8'); print
json_encode(array('success' => 1));
}
function isNintendoUser() {
if(!empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/\bmiiverse\b/', $_SERVER['HTTP_USER_AGENT'])) {
	return true;
	} else {
	return false;
	}
}

function grpfinish($mysql) {
$mysql->close();
}
$mysql = initAll();

# Start session if not already started
session_name('grp');
if(session_status() == PHP_SESSION_NONE) {
session_set_cookie_params(72000);
ini_set('session.gc_maxlifetime', 72000);
session_start();
// Locale
localeSet(null);
// <Locale
if(!empty($_COOKIE['grp_identity']) && empty($_SESSION['pid']) && $_SERVER['REQUEST_URI'] != '/act/logout' && isset($grp_config_privkey) && isset($grp_config_pubkey)) {
if(isset($grp_config_privkey) && isset($grp_config_pubkey)) {
require_once 'crypto.php';
$identity_auth = initToken(decrypt_identity($grp_config_privkey, base64_decode($_COOKIE['grp_identity'])));
if($identity_auth) {
require_once 'account-helper.php';
setLoginVars($identity_auth, true); }
	} 	}
}
elseif(!empty($_SESSION['pid'])) {
if($mysql->query('SELECT * FROM people WHERE people.pid = "'.$_SESSION['pid'].'" AND people.user_id = "'.$_SESSION['user_id'].'"')->num_rows == 0) {{ unset($_SESSION['pid']); }}}

if(!empty($_SESSION['pid'])) {
$search_relationships_own = $mysql->query('SELECT * FROM relationships WHERE relationships.source = "'.$_SESSION['pid'].'" AND relationships.source = "'.$_SESSION['pid'].'" AND relationships.is_me2me = "1"');
if($search_relationships_own->num_rows == 0) {
$mysql->query('INSERT INTO relationships (source, target, is_me2me) VALUES ("'.$_SESSION['pid'].'", "'.$_SESSION['pid'].'", "1")'); }
}

if($grp_config_server_nsslog == true && empty($_SESSION['pid']) && $_SERVER['SCRIPT_NAME'] != '/act.php') {
header("Location: {$grp_config_default_redir_prot}{$_SERVER['HTTP_HOST']}/act/login?location=".htmlspecialchars(urlencode($_SERVER['REQUEST_URI'])), true, 302); }
