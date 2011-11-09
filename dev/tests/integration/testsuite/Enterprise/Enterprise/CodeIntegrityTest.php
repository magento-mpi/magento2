<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Enterprise
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Enterprise
 */
class Enterprise_Enterprise_CodeIntegrityTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $this->assertEquals('enterprise/default/default',
            (string)Mage::app()->getConfig()->getNode('default/' . Mage_Core_Model_Design_Package::XML_PATH_THEME)
        );
        $this->assertEquals('default/default/enterprise',
            (string)Mage::app()->getConfig()->getNode('adminhtml/' . Mage_Core_Model_Design_Package::XML_PATH_THEME)
        );
        $this->assertEquals('default/enterprise/default',
            (string)Mage::app()->getConfig()->getNode('install/' . Mage_Core_Model_Design_Package::XML_PATH_THEME)
        );
    }
}
