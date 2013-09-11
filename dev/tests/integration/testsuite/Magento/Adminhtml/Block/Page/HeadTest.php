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
class Magento_Adminhtml_Block_Page_HeadTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\Magento\Adminhtml\Block\Page\Head',
            Mage::app()->getLayout()->createBlock('\Magento\Adminhtml\Block\Page\Head')
        );
    }
}
