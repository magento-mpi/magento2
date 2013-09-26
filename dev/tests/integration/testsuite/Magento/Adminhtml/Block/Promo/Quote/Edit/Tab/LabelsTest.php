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
            'Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels',
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
                ->createBlock('Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Labels')
        );
    }
}
