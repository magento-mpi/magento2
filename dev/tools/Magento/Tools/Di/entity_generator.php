<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    DI
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../../../../app/bootstrap.php';

// default generation dir
$generationDir = BP . DS . \Magento\Code\Generator\Io::DEFAULT_DIRECTORY;

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
    $generator = new \Magento\Code\Generator();
    $entities = $generator->getGeneratedEntities();

    $allowedTypes = 'Allowed entity types are: ' . implode(', ', $entities) . '.';
    $example = 'Example: php -f entity_generator.php -- -t factory -c \Magento\Event\Observer '
        . '-g /var/mage/m2ee/generation'
        . ' - will generate file /var/mage/m2ee/generation/Magento/Event/ObserverFactory.php';

    echo $e->getMessage() . "\n";
    echo $e->getUsageMessage() . "\n";
    echo $allowedTypes . "\n";
    echo 'Default generation dir is ' . $generationDir . "\n";
    die($example);
}

\Magento\Autoload\IncludePath::addIncludePath($generationDir);

//reinit generator with correct generation path
$io = new \Magento\Code\Generator\Io(null, null, $generationDir);
$generator = new \Magento\Code\Generator(null, null, $io);

try {
    if (\Magento\Code\Generator::GENERATION_SUCCESS == $generator->generateClass($className)) {
        print("Class {$className} was successfully generated.\n");
    } else {
        print("Can't generate class {$className}. This class either not generated entity, or it already exists.\n");
    }
} catch (\Magento\MagentoException $e) {
    print("Error! {$e->getMessage()}\n");
}
