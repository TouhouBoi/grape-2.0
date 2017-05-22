<?php
$grpmode = 1; require_once '../grplib-php/init.php';
require_once '../grp_act-php/lib/htm.php'; $bodyClass = 'min-height:400px';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
require_once '../grplib-php/account-helper.php';
// act create

// reCAPTCHA
	if(!empty($grp_config_recaptcha_pkey) && !isNintendoUser()) {
	$recaptcha = true;
	$recaptcha_success = check_reCAPTCHA($grp_config_recaptcha_pkey);
	} else {
	$recaptcha = false;
	}

if(isset($recaptcha_success) && $recaptcha_success == false) {
		printErr(1022452, "The reCAPTCHA check has failed.", '/act/create'); grpfinish($mysql); exit();
}
// Make better, redesign in config
if($grp_config_server_nsslog) {
		if($dev_server) {
		$nss = 1;	} else {
	    $nss = 2;	
		}
	} else { 
	$nss = 0;
	}

$check_form = actformCheck();
if(is_array($check_form)) {
printErr($check_form[0], $check_form[1], '/act/create');  grpfinish($mysql); exit();
	}
if(!empty($_POST['nn_user_id'])) {
$get_mii = getNNASmii($_POST['nn_user_id']);
if(!$get_mii) {
printErr(1022402, 'The Nintendo Network ID that has been submitted either doesn\'t exist or isn\'t on Miiverse.', '/act/create'); grpfinish($mysql); exit();
		}
	}
// Checks finished

$pidgen = PIDgen();
$query_params = ['pid'=> $pidgen, 'user_id'=> $_POST['user_id'], 'user_pass'=> passgen($_POST['password']), 
'user_email'=> $_POST['email'], 'screen_name'=> $_POST['screen_name'], 
'mii_hash'=> $get_mii['mii_image'] ?? null, 
'created_from'=> $_SERVER['REMOTE_ADDR'], 'client_info' => userInfoJSON(), 
'platform_id'=> $platform ?? 3];

if($nss == 1) {
$query_params['device_id'] = hexdec(substr($_POST['device_id'],2));
}
/* elseif($nss == 2) {
$query_params['device_id'] = (int)
} */
if(isset($get_mii)) {
$query_params['nnas_info'] = json_encode($get_mii);
}

$create_account = peopleQuery($query_params);

if(!$create_account->errno) {
print_r($create_account);
$get_act = $mysql->query('SELECT user_id, pid FROM people WHERE people.pid = "'.$create_account->insert_id.'"')->fetch_assoc();
setLoginVars($get_act, true);
header("Location: {$grp_config_default_redir_prot}{$_SERVER['HTTP_HOST']}/", true, 302);
} else {
printErr(1022128, 'A server error has occurred.', '/act/create');
}


// finished
grpfinish($mysql); exit();
}

printHeader();
print '<div class="page-header">
        <h3>'._('grp.act.account_create').'</h3>
    </div>
    <form id="act-create" method="POST" action="/act/create" class="form-horizontal">
<fieldset>





<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'._('grp.act.user.id').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="user_id" type="text" placeholder="'._('grp.act.login.id').'" class="form-control input-md" required="">
  <span class="help-block">'._('grp.act.userid_help').'</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'._('grp.act.login.passwd').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="password" type="password" placeholder="'._('grp.act.login.passwd').'" class="form-control input-md" required="">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'._('grp.act.login.passwd_confirm').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="password2" type="password" placeholder="'._('grp.act.login.passwd').'" class="form-control input-md" required="">
    
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'._('grp.act.email_addr').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="email" type="text" placeholder="'._('grp.act.email').'" class="form-control input-md" required="">
  <span class="help-block">'._('grp.act.email_help').'</span>  
  </div>
</div>


<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'._('grp.act.nnid').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="nn_user_id" type="text" placeholder="NNID" class="form-control input-md">
  <span class="help-block">'._('grp.act.nnid_help').'</span>  
  </div>
</div>
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">'._('grp.act.screenname').'</label>  
  <div class="col-md-4">
  <input id="textinput" name="screen_name" type="text" placeholder="'._('grp.act.screenmii_name').'" class="form-control input-md">
  <span class="help-block">'._('grp.act.screenname_help').'</span>  
  </div>
</div>

';
if(!empty($_GET['invite_id']) || !empty($_GET['device_id'])) {
print '
  <input name="'.(isset($_GET['device_id']) ? 'device_id' : 'invite_id').'" type="hidden" value="'.htmlspecialchars($_GET['invite_id'] ?? $_GET['device_id']).'">
';
} 
// Make better, redesign in config
elseif($grp_config_server_nsslog) {
		if($dev_server) {
		print '
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">(NSS) ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="device_id" type="text" placeholder="DeviceID" class="form-control input-md" required="">
  <span class="help-block">Device/InviteID retrieved from an administrator; required for this server</span>  
  </div>
</div>
'; 	} else {
		print '
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Invite ID</label>  
  <div class="col-md-4">
  <input id="textinput" name="invite" type="text" placeholder="Invite ID" class="form-control input-md" required="">
  <span class="help-block">Invite ID retrieved from a user or administrator; required for this server</span>  
  </div>
</div>
	';	
		}
	}
print '

<!-- Button and reCAPTCHA -->
<div class="form-group">
  
  <div class="col-md-4">
  <a href="/act/login" class="btn btn-primary btn-primary">'._('grp.act.back').'</a>';
  if(!empty($grp_config_recaptcha_pkey) && !isNintendoUser()) { print '
<!-- Hopefully this works -->
     <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <script>
       function onSubmit(token) {
         document.getElementById("act-create").submit();
       }
     </script>
<button class="g-recaptcha btn btn-primary" data-sitekey="'.$grp_config_recaptcha_pubkey.'" data-callback="onSubmit">'._('grp.act.submit').'
</button>';
  } else {
  print '<button type="submit" value="submit" class="btn btn-primary">'._('grp.act.submit').'</button>';
  }  print '
  </div>
</div>

</fieldset>
</form>';
printFooter();
