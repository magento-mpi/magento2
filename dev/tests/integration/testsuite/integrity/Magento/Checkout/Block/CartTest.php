<?php
/**
 * Integrity test for template setters in Magento_Checkout_Block_CartTest
 *
 * {license_notice}
 *
 * @category Magento
 * @package Magento_Checkout
 * @subpackage integration_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Integrity_Magento_Checkout_Block_CartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     */
    public function testCustomTemplateSetters($layoutFile)
    {
        $params = array();
        if (preg_match('/app\/design\/frontend\/(.+?)\/(.+?)\//', $layoutFile, $matches)) {
            $params = array('theme' => $matches[1]);
        }

        $xml = simplexml_load_file($layoutFile);
        $nodes = $xml->xpath('//block/action[@method="setCartTemplate" or @method="setEmptyTemplate"]') ?: array();
        /** @var $node SimpleXMLElement */
        foreach ($nodes as $node) {
            $template = (array)$node->children();
            $template = array_shift($template);
            $blockNode = $node->xpath('..');
            $blockNode = $blockNode[0];
            preg_match('/^(.+?_.+?)_/', $blockNode['class'], $matches);
            $params['module'] = $matches[1];
            $this->assertFileExists(
                Magento_Test_Helper_Bootstrap::getObjectmanager()->get('Magento_Core_Model_View_FileSystem')
                    ->getFilename($template, $params)
            );
        }
    }

    /**
     * @return array
     */
    public function layoutFilesDataProvider()
    {
        return Utility_Files::init()->getLayoutFiles(array('area' => 'frontend'));
    }
}
