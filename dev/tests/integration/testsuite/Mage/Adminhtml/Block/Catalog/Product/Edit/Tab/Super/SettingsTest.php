<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_SettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @dataProvider getGetContinueUrlProvider
     */
    public function testGetContinueUrl($productId, $expectedUrl)
    {
        $product = $this->getMockBuilder('Mage_Catalog_Model_Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getId'))
            ->getMock();
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $urlModel = $this->getMockBuilder('Mage_Core_Model_Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $urlModel->expects($this->at(2))->method('getUrl')->with($this->equalTo($expectedUrl))
            ->will($this->returnValue('url'));

        Mage::register('current_product', $product);

        $layout = new Mage_Core_Model_Layout();
        $block = $layout->createBlock(
            'Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings',
            'block',
            array(
               'urlModel' => $urlModel
            )
        );
        $this->assertEquals('url', $block->getContinueUrl());
    }

    /**
     * @return array
     */
    public function getGetContinueUrlProvider()
    {
        return array(
            array(null, '*/*/new'),
            array(1, '*/*/edit'),
        );
    }
}
