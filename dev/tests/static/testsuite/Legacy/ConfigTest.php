<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Legacy_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider configFileDataProvider
     */
    public function testConfigFile($file)
    {
        $deprecations = array(
            '/config/global/fieldsets'                 => 'remove them',
            '/config/admin/fieldsets'                  => 'remove them',
            '/config/global/models/*/deprecatedNode'   => 'remove them',
            '/config/global/models/*/entities/*/table' => 'remove them',
            '/config/global/models/*/class'            => 'remove them',
            '/config/global/helpers/*/class'           => 'remove them',
            '/config/global/blocks/*/class'            => 'remove them',
            '/config/global/models/*/resourceModel'    => 'remove them',
            '/config/adminhtml/menu'                   => 'move them to adminhtml.xml',
            '/config/adminhtml/acl'                    => 'move them to adminhtml.xml',
        );
        $xml = simplexml_load_file($file);
        foreach ($deprecations as $xpath => $suggestion) {
            $this->assertEmpty(
                $xml->xpath($xpath),
                "Deprecated nodes have been found by XPath '$xpath', $suggestion."
            );
        }
    }

    public function configFileDataProvider()
    {
        $globPatterns = array(
            PATH_TO_SOURCE_CODE . '/app/etc/*.*',
            PATH_TO_SOURCE_CODE . '/app/etc/modules/*.*',
            PATH_TO_SOURCE_CODE . '/app/code/*/*/*/etc/config.xml',
        );
        $result = array();
        foreach ($globPatterns as $oneGlobPattern) {
            $files = glob($oneGlobPattern);
            foreach ($files as $file) {
                /* Use filename as a data set name to not include it to every assertion message */
                $result[$file] = array($file);
            }
        }
        return $result;
    }
}
