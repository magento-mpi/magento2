<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super;

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

        $context = $objectManager->create('Magento\Backend\Block\Template\Context', array('urlBuilder' => $urlModel));
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        /** @var $block \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Settings */
        $block = $layout->createBlock(
            'Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Super\Settings',
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
