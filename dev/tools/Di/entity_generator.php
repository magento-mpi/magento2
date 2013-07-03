<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    DI
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../../app/bootstrap.php';

$generator = new Magento_Code_Generator();
$generatedEntities = $generator->getGeneratedEntities();
if (!isset($argv[1]) || in_array($argv[1], array('-?', '/?', '-help', '--help'))) {
    $message = " * Usage: php entity_generator.php [" . implode('|', $generatedEntities)
        . "] <required_entity_class_name>\n"
        . " * Example: php entity_generator.php factory Mage_Tag_Model_Tag"
        . " - will generate file var/generation/Mage/Tag/Model/TagFactory.php\n";
    print($message);
    exit();
}

$entityType = $argv[1];
if (!in_array($argv[1], $generatedEntities)) {
    print "Error! Unknown entity type.\n";
    exit();
}

if (!isset($argv[2])) {
    print "Error! Please, specify class name.\n";
    exit();
}
$className = $argv[2];
switch ($entityType) {
    case 'proxy':
        $className .= '_Proxy';
        break;

    case 'factory':
        $className .= 'Factory';
        break;
    case 'interceptor':
        $className .= '_Interceptor';
        break;
}

try {
    if (Magento_Code_Generator::GENERATION_SUCCESS == $generator->generateClass($className)) {
        print("Class {$className} was successfully generated.\n");
    } else {
        print("Can't generate class {$className}. This class either not generated entity, or it already exists.\n");
    }
} catch (Magento_Exception $e) {
    print("Error! {$e->getMessage()}\n");
}
