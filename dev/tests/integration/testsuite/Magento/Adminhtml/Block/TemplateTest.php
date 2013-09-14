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
            'Magento\Backend\Block\Template',
            Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Template')
        );
    }
}
