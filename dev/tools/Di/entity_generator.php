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

// default generation dir
$generationDir = BP . DS . Magento_Code_Generator_Io::DEFAULT_DIRECTORY;

try {
    $opt = new Zend_Console_Getopt(array(
        'type|t=w' => 'entity type(required)',
        'class|c=w' => 'entity class name(required)',
        'generation|g=s' => 'generation dir. Default value ' . $generationDir,
    ));
    $opt->parse();

    $entityType = $opt->getOption('t');
    if (empty($entityType)) {
        throw new Zend_Console_Getopt_Exception('type is a required parameter');
    }

    $className = $opt->getOption('c');
    if (empty($className)) {
        throw new Zend_Console_Getopt_Exception('class is a required parameter');
    }
    $substitutions = array('proxy' => '_Proxy', 'factory' => 'Factory', 'interceptor' => '_Interceptor');
    if (!in_array($entityType, array_keys($substitutions))) {
        throw new Zend_Console_Getopt_Exception('unrecognized type: ' . $entityType);
    }
    $className .= $substitutions[$entityType];

    if ($opt->getOption('g')) {
        $generationDir = $opt->getOption('g');
    }
} catch (Zend_Console_Getopt_Exception $e) {
    $generator = new Magento_Code_Generator();
    $entities = $generator->getGeneratedEntities();

    $allowedTypes = 'Allowed entity types are: ' . implode(', ', $entities) . '.';
    $example = 'Example: php -f entity_generator.php -- -t factory -c Magento_Tag_Model_Tag '
        . '-g /var/mage/m2ee/generation'
        . ' - will generate file /var/mage/m2ee/generation/Magento/Tag/Model/TagFactory.php';

    echo $e->getMessage() . "\n";
    echo $e->getUsageMessage() . "\n";
    echo $allowedTypes . "\n";
    echo 'Default generation dir is ' . $generationDir . "\n";
    die($example);
}

Magento_Autoload_IncludePath::addIncludePath($generationDir);

//reinit generator with correct generation path
$io = new Magento_Code_Generator_Io(null, null, $generationDir);
$generator = new Magento_Code_Generator(null, null, $io);

try {
    if (Magento_Code_Generator::GENERATION_SUCCESS == $generator->generateClass($className)) {
        print("Class {$className} was successfully generated.\n");
    } else {
        print("Can't generate class {$className}. This class either not generated entity, or it already exists.\n");
    }
} catch (Magento_Exception $e) {
    print("Error! {$e->getMessage()}\n");
}
