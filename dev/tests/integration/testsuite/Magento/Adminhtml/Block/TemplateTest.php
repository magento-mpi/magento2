<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(
            'Magento_Backend_Block_Template',
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
                ->createBlock('Magento_Adminhtml_Block_Template')
        );
    }
}
