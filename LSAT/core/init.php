<?php

/*
LSAT
Copyright (C) 2015 - ITESM

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details. <http://www.gnu.org/licenses/>.
*/

/* Contains general information used on (almost) every page
   7 de Febrero del 2015
 */

ob_start();
session_start();

// Create a global configuration
$GLOBALS['config'] = array(
	'mysql' => array(
		'host' 		=> 'localhost',
		'username' 	=> 'root',
		'password' 	=> '',
		'db' 		=> 'lsat'
	),
	'remember' => array(
		'cookie_name'	=> 'hash',
		'cookie_expiry' =>  604800
	),
	'session' => array(
		'session_name'	=> 'user',
		'token_name'	=> 'token'
	),
	'roles' => array('admin', 'teacher', 'student')
);

//define( 'ABPATH', 'C:/wamp/www/lsat');
//define( 'ABPATH', '/Applications/XAMPP/xamppfiles/htdocs/LSAT');
define( 'ABPATH', '/opt/lampp/htdocs/LSAT');

// Autoload classes
function autoload($class) {
		require_once (ABPATH.'/classes/' . $class . '.php');
}
spl_autoload_register('autoload');

// Include functions
require_once (ABPATH.'/functions/sanitize.php');

require_once(ABPATH.'/controls/PHPMailer/PHPMailerAutoload.php');
