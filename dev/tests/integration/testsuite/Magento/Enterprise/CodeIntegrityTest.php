<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Enterprise_CodeIntegrityTest extends PHPUnit_Framework_TestCase
{
    public function testConfiguration()
    {
        $this->assertEquals('magento_fixed_width',
            (string)Mage::app()->getConfig()->getNode(
                'frontend/' . Magento_Core_Model_View_Design::XML_PATH_THEME
            )
        );
        $this->assertEquals('magento_backend',
            (string)Mage::app()->getConfig()->getNode(
                'adminhtml/' . Magento_Core_Model_View_Design::XML_PATH_THEME
            )
        );
        $this->assertEquals('magento_enterprise',
            (string)Mage::app()->getConfig()->getNode(
                'install/' . Magento_Core_Model_View_Design::XML_PATH_THEME
            )
        );
    }
}
