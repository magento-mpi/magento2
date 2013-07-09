<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(__DIR__);

$template = <<<XML
<?xml version="1.0"?>
<!--
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
<layout version="0.1.0">%s</layout>
XML;

try {
    $options = getopt('f:');
    if (empty($options['f'])) {
        throw new Exception('Usage: php -f convert.php -- -f layout_1.xml [-f layout_2.xml ... -f layout_N.xml]');
    }
    $files = (array)$options['f'];

    $analyzer = new Layout_Analyzer(new Layout_Merger(), new Xml_Formatter('    '), $template);
    $result = $analyzer->aggregateHandles($files);

    var_export($result);
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(1);
}
