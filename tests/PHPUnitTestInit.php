<?php
if (!defined('_IS_INCLUDED')) {
    ob_start();
    require realpath(dirname(__FILE__) . '/../app/Mage.php');
    set_include_path(get_include_path() . PS . dirname(__FILE__));
    define('_IS_INCLUDED', 1);

    require_once 'PHPUnit/Framework.php';
    require_once 'PHPUnit/Framework/IncompleteTestError.php';
    require_once 'PHPUnit/Framework/TestCase.php';
    require_once 'PHPUnit/Framework/TestSuite.php';
    require_once 'PHPUnit/Runner/Version.php';
    require_once 'PHPUnit/TextUI/TestRunner.php';
    require_once 'PHPUnit/Util/Filter.php';
}
