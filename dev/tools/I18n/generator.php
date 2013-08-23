<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    I18n
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/../bootstrap.php';
$baseDir = realpath(BP);
use Magento\Tools\I18n\Code,
    Magento\Tools\I18n\Code\Dictionary,
    Magento\Tools\I18n\Code\Dictionary\Scanner;

try {
    $options = new Zend_Console_Getopt(array(
        'scan_directory|sd=s' => 'Absolute path to scan directory, Magento code base by default',
        'with_context|wc=s' => 'Whether to infuse output with additional meta-information, by default "yes"',
        'output|o=s' => 'Path to output file name, by default output the results into standard output stream',
    ));
    $options->parse();
    $scanDirectory = $options->getOption('scan_directory') ? $options->getOption('scan_directory') : null;
    $outputFilename = $options->getOption('output') ? $options->getOption('output') : null;
    $withContext = in_array($options->getOption('with_context'), array('n', 'no', 'N', 'No', 'NO')) ? false : true;

    $scanner = new Dictionary\ScannerComposite($baseDir, $scanDirectory);
    $scanner->addChild(new Scanner\PhpScanner());
    $scanner->addChild(new Scanner\XmlScanner());
    $scanner->addChild(new Scanner\JsScanner());

    $dictionaryGenerator = new Code\Dictionary($scanner);
    $dictionaryGenerator->setOutputFilename($outputFilename);
    $dictionaryGenerator->setWithContext($withContext);
    $dictionaryGenerator->generate();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "Translate phrase generator failed with exception:\n" . $e->getMessage() . "\n");
    exit(1);
}
