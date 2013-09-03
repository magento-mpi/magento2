<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/../bootstrap.php';
$baseDirectory = realpath(BP) . DS;
use Magento\Tools\I18n\Code\Dictionary;

try {
    $console = new Zend_Console_Getopt(array(
        'directory|d=s' => 'Absolute path to base directory, Magento code base by default',
        'context|c=s' => 'Whether to infuse output with additional meta-information, by default "yes"',
        'output|o=s' => 'Path to output file name, by default output the results into standard output stream',
    ));
    $console->parse();
    $specificDirectory = $console->getOption('directory') ?: null;
    $outputFilename = $console->getOption('output') ?: null;
    $withContext = in_array($console->getOption('context'), array('y', 'yes', 'Y', 'Yes', 'YES'));

    if (!$specificDirectory) {
        throw new Exception("Directory parameter is required");
    }

    $options = array(
        'php' => array(
            'paths' => array($specificDirectory),
            'fileMask' => '/\.(php|phtml)$/',
        ),
        'js' => array(
            'paths' => array($specificDirectory),
            'fileMask' => '/\.(js|phtml)$/',
        ),
        'xml' => array(
            'paths' => array($specificDirectory),
            'fileMask' => '/\.xml$/',
        ),
        'outputFilename' => $outputFilename,
    );

    $generatorFactory = new Dictionary\Generator\Factory();
    $generatorFactory->create($options)->generate($withContext);

} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "Translate phrase generator failed with exception:\n" . $e->getMessage() . "\n");
    exit(1);
}
