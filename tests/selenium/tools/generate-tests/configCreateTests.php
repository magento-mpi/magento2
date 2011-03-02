<?php
/**
 * Constant PATH_TO_TESTS_DIR defines path to folder with files for testing.
 */
define('PATH_TO_TESTS_DIR', dirname(__FILE__));

/**
 * Constant PATH_TO_SOURCE_DATA defines path to file with list of need classes and methods.
 */
define('PATH_TO_SOURCE_DATA', PATH_TO_TESTS_DIR.'/tests.php');

/**
 * Constant PATH_TO_TEMPLATE defines path to file with template of need class for testing.
 */
define('PATH_TO_TEMPLATE', PATH_TO_TESTS_DIR.'/stub.php');

/**
 * Constant PATH_TO_TEMPLATE defines path to file with template of need class for testing.
 */
define('PATH_TO_TEMPLATE_METHOD', PATH_TO_TESTS_DIR.'/stubMethod.php');

/**
 * Constant SOURCE_DATA_KEY_PATH defines key name of path in source data array.
 */
define('SOURCE_DATA_KEY_PATH', 'path');

/**
 * Constant SOURCE_DATA_KEY_FILE defines key name of file in source data array.
 */
define('SOURCE_DATA_KEY_FILE', 'file');

/**
 * Constant SOURCE_DATA_KEY_CLASS defines key name of class in source data array.
 */
define('SOURCE_DATA_KEY_CLASS', 'class');

/**
 * Constant SOURCE_DATA_KEY_METHOD defines key name of method in source data array.
 */
define('SOURCE_DATA_KEY_METHOD', 'method');

/**
 * Constant CLASSNAME_REPLACEMENT defines name of temporary classname in test template.
 */
define('CLASSNAME_REPLACEMENT', 'CLASSNAME');

/**
 * Constant METHODNAME_REPLACEMENT defines name of temporary methodname in test template.
 */
define('METHODNAME_REPLACEMENT', 'METHODNAME()');