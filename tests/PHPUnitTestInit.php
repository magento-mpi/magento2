<?php
if (!defined('_IS_INCLUDED')) {
    define('_IS_INCLUDED', 1);
    // start output buffer not to break headers
    ob_start();
    // setup include path and autoloader
    require realpath(dirname(__FILE__) . '/../app/Mage.php');
    set_include_path(get_include_path() . PS . dirname(__FILE__));
}
