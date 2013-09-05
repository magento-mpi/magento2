<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require_once __DIR__ . '/Code/bootstrap.php';

use Magento\Tools\I18n\Code\ServiceLocator;

try {
    $console = new \Zend_Console_Getopt(array(
        'directory|d=s' => 'Path to base directory for parsing',
        'output|o=s' => 'Path(with filename) to output file, by default output the results into standard output stream',
        'magento|m=s' => 'Indicates whether directory for parsing is Magento directory, "no" by default',
    ));
    $console->parse();

    $directory = $console->getOption('directory') ?: null;
    $outputFilename = $console->getOption('output') ?: null;
    $isMagento = in_array($console->getOption('magento'), array('y', 'yes', 'Y', 'Yes', 'YES'));

    if (!$directory) {
        throw new \InvalidArgumentException('Directory parameter is required.');
    }

    if ($isMagento) {
        $directory = rtrim($directory, '\\/');
        $filesOptions = array(
            array(
                'type' => 'php',
                'paths' => array(
                    $directory . '/app/code/',
                    $directory . '/app/design/',
                ),
                'fileMask' => '/\.(php|phtml)$/',
            ),
            array(
                'type' => 'js',
                'paths' => array(
                    $directory . '/app/code/',
                    $directory . '/app/design/',
                    $directory . '/pub/lib/mage/',
                    $directory . '/pub/lib/varien/',
                ),
                'fileMask' => '/\.(js|phtml)$/',
            ),
            array(
                'type' => 'xml',
                'paths' => array(
                    $directory . '/app/code/',
                    $directory . '/app/design/',
                ),
                'fileMask' => '/\.xml$/',
            ),
        );
    } else {
        $filesOptions = array(
            array(
                'type' => 'php',
                'paths' => array($directory),
                'fileMask' => '/\.(php|phtml)$/',
            ),
            array(
                'type' => 'js',
                'paths' => array($directory),
                'fileMask' => '/\.(js|phtml)$/',
            ),
            array(
                'type' => 'xml',
                'paths' => array($directory),
                'fileMask' => '/\.xml$/',
            ),
        );
    }


    $generator = ServiceLocator::getDictionaryGenerator();
    $generator->generate($filesOptions, $outputFilename, $isMagento);

    fwrite(STDOUT, "\nDictionary successfully processed.\n");

} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getUsageMessage() . "\n");
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, 'Translate phrase generator failed: ' . $e->getMessage() . "\n");
    exit(1);
}
