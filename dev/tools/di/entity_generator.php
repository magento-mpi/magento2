<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    DI
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('DS', DIRECTORY_SEPARATOR);
define('BP', realpath(__DIR__ . '/../../..'));

require_once BP . '/lib/Magento/Autoload.php';
require_once BP . '/app/code/core/Mage/Core/functions.php';
require_once BP . '/app/Mage.php';

$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'local';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'community';
$paths[] = BP . DS . 'app' . DS . 'code' . DS . 'core';
$paths[] = BP . DS . 'lib';
$paths[] = BP . DS . 'var' . DS . 'generation';
Magento_Autoload::getInstance()->addIncludePath($paths);
Mage::setRoot();

if (!isset($argv[1]) || in_array($argv[1], array('-?', '/?', '-help', '--help'))) {
    $message = " * Usage: php entity_generator.php <required_entity_class_name>\n"
        . " * Example: php entity_generator.php Mage_Tag_Model_TagFactory"
        . " - will generate file var/generation/Mage/Tag/Model/TagFactory.php\n";
    print($message);
    exit();
}
$className = $argv[1];

try {
    $generator = new Magento_Di_Generator();
    if ($generator->generateClass($className)) {
        print("Class {$className} was successfully generated.\n");
    } else {
        print("Can't generate class {$className}. This class either not generated entity, or it already exists.\n");
    }
} catch (Magento_Exception $e) {
    print("Error! {$e->getMessage()}\n");
}
