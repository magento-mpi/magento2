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
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_LabelsTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels',
            Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels')
        );
    }
}
