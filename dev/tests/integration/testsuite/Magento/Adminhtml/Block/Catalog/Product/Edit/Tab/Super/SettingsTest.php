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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_SettingsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param null|int $productId
     * @param string $expectedUrl
     *
     * @magentoAppIsolation enabled
     * @magentoAppArea adminhtml
     * @dataProvider getContinueUrlDataProvider
     */
    public function testGetContinueUrl($productId, $expectedUrl)
    {
        $product = $this->getMockBuilder('Magento_Catalog_Model_Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', '__wakeup'))
            ->getMock();
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $urlModel = $this->getMockBuilder('Magento_Backend_Model_Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $urlModel->expects($this->any())->method('getUrl')->with($this->equalTo($expectedUrl))
            ->will($this->returnValue('url'));

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_product', $product);

        $context = Mage::getModel('Magento_Backend_Block_Template_Context', array('urlBuilder' => $urlModel));
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings */
        $block = $layout->createBlock(
            'Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings',
            'block',
            array(
               'context' => $context
            )
        );
        $this->assertEquals('url', $block->getContinueUrl());
    }

    /**
     * @return array
     */
    public static function getContinueUrlDataProvider()
    {
        return array(
            array(null, '*/*/new'),
            array(1, '*/*/edit'),
        );
    }
}
