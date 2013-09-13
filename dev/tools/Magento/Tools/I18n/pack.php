<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
require __DIR__ . '/bootstrap.php';

use Magento\Tools\I18n\Code\ServiceLocator;

try {
    $console = new \Zend_Console_Getopt(array(
        'source|s=s' => 'Path to source dictionary file with translations',
        'pack|p=s' => 'Path to language package',
        'locale|l=s' => 'Target locale for dictionary, for example "de_DE"',
        'mode|m=s' => 'Save mode for dictionary
        - "replace" - replace language pack by new one
        - "merge" -  merge language packages
        , by default "replace"',
        'allow_duplicates|d=s' => 'Is allowed to save duplicates of translate, by default "no"',
    ));
    $console->parse();

    $dictionaryPath = $console->getOption('source') ?: null;
    $packPath = $console->getOption('pack') ?: null;
    $locale = $console->getOption('locale') ?: null;
    $allowDuplicates = in_array($console->getOption('allow_duplicates'), array('y', 'Y', 'yes', 'Yes'));
    $saveMode = $console->getOption('mode') ?: null;

    if (!$dictionaryPath) {
        throw new \InvalidArgumentException('Dictionary source path parameter is required.');
    }
    if (!$packPath) {
        throw new \InvalidArgumentException('Pack path parameter is required.');
    }
    if (!$locale) {
        throw new \InvalidArgumentException('Locale parameter is required.');
    }

    $generator = ServiceLocator::getPackGenerator();
    $generator->generate($dictionaryPath, $packPath, $locale, $saveMode, $allowDuplicates);

    fwrite(STDOUT, sprintf("\nSuccessfully saved %s language package.\n", $locale));

} catch (\Zend_Console_Getopt_Exception $e) {
    fwrite(STDERR, $e->getUsageMessage() . "\n");
    exit(1);
} catch (\Exception $e) {
    fwrite(STDERR, 'Language pack failed: ' . $e->getMessage() . "\n");
    exit(1);
}
