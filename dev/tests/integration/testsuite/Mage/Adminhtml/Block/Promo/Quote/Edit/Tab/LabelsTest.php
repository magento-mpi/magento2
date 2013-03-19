<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_LabelsTest extends Mage_Backend_Area_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels',
            Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels')
        );
    }
}
