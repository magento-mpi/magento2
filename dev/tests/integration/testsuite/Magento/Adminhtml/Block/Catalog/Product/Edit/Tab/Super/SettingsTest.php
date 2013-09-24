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
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super;

class SettingsTest extends \PHPUnit_Framework_TestCase
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
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(array('getId', '__wakeup'))
            ->getMock();
        $product->expects($this->any())->method('getId')->will($this->returnValue($productId));

        $urlModel = $this->getMockBuilder('Magento\Backend\Model\Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $urlModel->expects($this->any())->method('getUrl')->with($this->equalTo($expectedUrl))
            ->will($this->returnValue('url'));

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_product', $product);

        $context = \Mage::getModel('Magento\Backend\Block\Template\Context', array('urlBuilder' => $urlModel));
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        /** @var $block \Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Settings */
        $block = $layout->createBlock(
            'Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Super\Settings',
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
