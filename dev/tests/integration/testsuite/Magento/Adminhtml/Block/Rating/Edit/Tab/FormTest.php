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
class Magento_Adminhtml_Block_Rating_Edit_Tab_FormTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Magento_Adminhtml_Block_Rating_Edit_Tab_Form',
            Mage::app()->getLayout()->createBlock('Magento_Adminhtml_Block_Rating_Edit_Tab_Form')
        );
    }
}
