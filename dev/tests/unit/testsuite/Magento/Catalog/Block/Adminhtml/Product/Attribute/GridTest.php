<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Attribute;

class GridTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRowUrl()
    {
        $attribute = $this->getMock('Magento\Catalog\Model\Resource\Eav\Attribute', array(), array(), '', false);
        $attribute->expects($this->once())->method('getAttributeId')->will($this->returnValue(2));

        $filesystem = $this->getMock('Magento\Framework\Filesystem', array(), array(), '', false);

        $urlBuilder = $this->getMock('Magento\Framework\UrlInterface', array(), array(), '', false);
        $urlBuilder->expects(
            $this->once()
        )->method(
            'getUrl'
        )->with(
            $this->equalTo('catalog/*/edit'),
            $this->equalTo(array('attribute_id' => 2))
        )->will(
            $this->returnValue('catalog/product_attribute/edit/id/2')
        );

        $context = $this->getMock('Magento\Backend\Block\Template\Context', array(), array(), '', false);
        $context->expects($this->once())->method('getUrlBuilder')->will($this->returnValue($urlBuilder));
        $context->expects($this->any())->method('getFilesystem')->will($this->returnValue($filesystem));

        $data = array('context' => $context);

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Catalog\Block\Adminhtml\Product\Attribute\Grid $block */
        $block = $helper->getObject('Magento\Catalog\Block\Adminhtml\Product\Attribute\Grid', $data);

        $this->assertEquals('catalog/product_attribute/edit/id/2', $block->getRowUrl($attribute));
    }
}
