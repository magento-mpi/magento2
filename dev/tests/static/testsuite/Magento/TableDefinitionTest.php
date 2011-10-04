<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Php_TableDefinitionTest extends PHPUnit_Framework_TestCase
{

    public function testTableDefinitionExistence()
    {
        $baseDir = realpath(__DIR__ . '/../../../../../');

        $log = '';

        $errorStatus = libxml_use_internal_errors(true);
        foreach (glob("{$baseDir}/app/code/*/*/*/etc/*.xml") as $filename) {
            $xml = simplexml_load_file($filename);
            if (!$xml) {
                continue;
            }
            $result = $xml->xpath('/config/global/models/*/entities/*/table');
            if (!empty($result)) {
                $log .= "File {$filename} contains tables definition.\n";
            }
        }
        libxml_use_internal_errors($errorStatus);

        $this->assertEmpty($log, $log);
    }
}
