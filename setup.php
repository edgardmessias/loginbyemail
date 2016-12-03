<?php

// Init the hooks of the plugins -Needed
function plugin_init_loginbyemail() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['loginbyemail'] = true;

   $login = false;

   if (isset($_POST['login_name'])) {
      //For GLPI <= 0.90
      $login = $_POST['login_name'];
   } elseif (isset($_SESSION['namfield']) && isset($_POST[$_SESSION['namfield']])) {
      //For GLPI >= 9.1
      $login = $_POST[$_SESSION['namfield']];
   }

   //If not setted, skip
   if ($login === false) {
      return;
   }

   //If is not a e-mail, skip
   if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
      return;
   }

   $user = new User();

   //If exists a user with name is a e-email, skip
   if ($user->getFromDBbyName(addslashes($login))) {
      return;
   }

   //If not exists a user with email, skip
   if (!$user->getFromDBbyEmail(addslashes($login))) {
      return;
   }

   $new_login_name = $user->fields['name'];

   if (isset($_POST['login_name'])) {
      $_POST['login_name'] = $new_login_name;
   } elseif (isset($_SESSION['namfield']) && isset($_POST[$_SESSION['namfield']])) {
      $_POST[$_SESSION['namfield']] = $new_login_name;
   }
}

// Get the name and the version of the plugin - Needed
function plugin_version_loginbyemail() {
   return array(
       'name'           => 'Login By E-mail',
       'version'        => '1.0.0',
       'author'         => 'Edgard Lorraine Messias',
       'homepage'       => 'https://github.com/edgardmessias/loginbyemail',
       'minGlpiVersion' => '0.80'
   );
}

// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_loginbyemail_check_prerequisites() {
   if (version_compare(GLPI_VERSION, '0.80', 'lt')) {
      echo "This plugin requires GLPI >= 0.80";
      return false;
   } else {
      return true;
   }
}

function plugin_loginbyemail_check_config() {
   return true;
}
