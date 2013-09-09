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
class Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_ContentTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Content',
            Mage::app()->getLayout()->createBlock('Magento_Banner_Block_Adminhtml_Banner_Edit_Tab_Content')
        );
    }
}
