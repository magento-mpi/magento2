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
class Mage_Adminhtml_Block_Rating_Edit_Tab_FormTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Mage_Adminhtml_Block_Rating_Edit_Tab_Form',
            Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Rating_Edit_Tab_Form')
        );
    }
}
