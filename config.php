<?php

/// Interface language
define("LANG", "en");

define('T4MPSR_SALT', 'bubuka');
define('T4MPSR_PASS', 't4pass');

require("smarty3/Smarty.class.php");

/// Load the language specific strings
require_once(__DIR__ . '/lang/strings.' . LANG . '.php');

/// Create new Smarty object
// require_once('Smarty.class.php');
$smarty = new Smarty();

define('T4MPSR_DIR', getcwd() . '/');

// smarty configuration

class t4mpsr_smarty extends Smarty {

    function __construct() {
        parent::__construct();
        $this->setTemplateDir(T4MPSR_DIR . 'templates');
        $this->setCompileDir(T4MPSR_DIR . 'templates_c');
        $this->setConfigDir(T4MPSR_DIR . 'configs');
        $this->setCacheDir(T4MPSR_DIR . 'cache');
    }

}

// Define some constants to force the (humans) to use
// the same string several places.
define('DRPDWN', 'dropdown');
define('ROUND_FACTOR', 1);
?>
