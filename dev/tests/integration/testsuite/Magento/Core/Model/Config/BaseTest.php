<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_BaseTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root><key>value</key></root>
XML;
        $config = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Config_Base', array('sourceData' => $xml));

        $this->assertInstanceOf('Magento_Core_Model_Config_Element', $config->getNode('key'));
    }
}
