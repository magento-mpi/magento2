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
use Magento\Tools\I18n\Code\LanguagePack;

try {
    $console = new Zend_Console_Getopt(array(
        'target_locale|l=s' => 'Target locale for dictionary, for example "de_DE"',
        'source_file|s=s' => 'Path to source file dictionary with translations, by default read from standard input'
            . ' stream',
        'allow_duplicates|d=s' => 'Is allowed to save duplicates of translate, by default "no"',
        'mode|m=s' => 'Save mode for dictionary
        - "replace" - replace language pack by new one
        - "merge" -  merge language packages
        , by default "replace"',
    ));
    $console->parse();
    $sourceFilename = $console->getOption('source_file') ? $console->getOption('source_file') : null;
    $targetLocale = $console->getOption('target_locale') ? $console->getOption('target_locale') : null;
    $saveMode = $console->getOption('mode') ? $console->getOption('mode') : 'replace';
    $allowDuplicates = in_array($console->getOption('allow_duplicates'), array('y', 'Y', 'yes', 'Yes')) ? true : false;

    $languagePack = new LanguagePack($baseDir);
    $languagePack->setTargetLocale($targetLocale);
    $languagePack->setSourceFilename($sourceFilename);
    $languagePack->setAllowedDuplicates($allowDuplicates);
    $languagePack->setSaveMode($saveMode);
    $languagePack->splitDictionary();
    echo $languagePack->getSuccessSavedMessage();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, "Language pack failed with exception:\n" . $e->getMessage() . "\n");
    exit(1);
}
