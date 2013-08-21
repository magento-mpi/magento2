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
            'Magento_Adminhtml_Block_Page_Head',
            Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Page_Head')
        );
    }
}
