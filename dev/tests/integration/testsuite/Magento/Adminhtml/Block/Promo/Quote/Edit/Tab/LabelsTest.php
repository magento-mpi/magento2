<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_LabelsTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Labels',
            Mage::app()->getLayout()->createBlock('Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Labels')
        );
    }
}
