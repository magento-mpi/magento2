<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Page_HeadTest extends Mage_Backend_Area_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Mage_Adminhtml_Block_Page_Head', Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Page_Head')
        );
    }
}
