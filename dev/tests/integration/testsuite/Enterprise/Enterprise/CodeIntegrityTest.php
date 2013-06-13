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

class Enterprise_Enterprise_CodeIntegrityTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $this->assertEquals('enterprise/fixed',
            (string)Mage::app()->getConfig()->getNode(
                'frontend/' . Mage_Core_Model_Design_Package::XML_PATH_THEME
            )
        );
        $this->assertEquals('default/backend',
            (string)Mage::app()->getConfig()->getNode(
                'adminhtml/' . Mage_Core_Model_Design_Package::XML_PATH_THEME
            )
        );
        $this->assertEquals('default/enterprise',
            (string)Mage::app()->getConfig()->getNode(
                'install/' . Mage_Core_Model_Design_Package::XML_PATH_THEME
            )
        );
    }
}
