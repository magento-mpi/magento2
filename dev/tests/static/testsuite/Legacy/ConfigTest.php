<?php
/**
 * Integrity test for configuration (config.xml)
 *
 * {license_notice}
 *
 * @category    tests
 * @package     integration
 * @subpackage  integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testConfigFiles()
    {
        $filePatterns = array(
            PATH_TO_SOURCE_CODE . '/app/etc/*.*',
            PATH_TO_SOURCE_CODE . '/app/etc/modules/*.*',
            PATH_TO_SOURCE_CODE . '/app/*/*/*/*/etc/config.xml',
        );

        $nodes = array(
            '/config/global/fieldsets',
            '/config/admin/fieldsets',
            '/config/global/models/*/deprecatedNode',
            '/config/global/models/*/entities/*/table',
            '/config/global/models/*/class',
            '/config/global/helpers/*/class',
            '/config/global/blocks/*/class',
            '/config/global/models/*/resourceModel'
        );

        $errors = array();
        foreach ($filePatterns as $pattern) {
            $files = glob($pattern);
            foreach ($files as $file) {
                $xml = simplexml_load_file($file);
                foreach ($nodes as $node) {
                    if ($xml->xpath($node)) {
                        $errors[] = 'Invalid xml file:' . $file . '(XPath: ' . $node . ')';
                    }
                }
            }
        }
        $this->assertEmpty($errors, implode("\n", $errors));
    }
}
