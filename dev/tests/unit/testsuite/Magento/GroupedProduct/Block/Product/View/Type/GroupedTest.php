<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Block\Product\View\Type;

class GroupedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GroupedProduct\Block\Product\View\Type\Grouped
     */
    protected $groupedView;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeInstanceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuredValueMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockMock;

    /**
     * @var string
     */
    protected $defaultTemplate = 'product/view/tierprices.phtml';

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $methodsProduct = array(
            'getId',
            'setQty',
            'getTypeInstance',
            'getPreconfiguredValues',
            'getTypeId',
            '__wakeup'
        );
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', $methodsProduct, array(), '', false);
        $this->typeInstanceMock = $this->getMock(
            'Magento\GroupedProduct\Model\Product\Type\Grouped',
            array(),
            array(),
            '',
            false
        );
        $this->productMock->expects(
            $this->any()
        )->method(
            'getTypeInstance'
        )->will(
            $this->returnValue($this->typeInstanceMock)
        );
        $this->configuredValueMock = $this->getMock(
            'Magento\Framework\Object',
            array('getSuperGroup'),
            array(),
            '',
            false
        );
        // mocks for getTierPriceHtml method
        $methods = array(
            'setTemplate',
            'setProduct',
            'setListClass',
            'setShowDetailedPrice',
            'setCanDisplayQty',
            'toHtml'
        );
        $this->blockMock = $this->getMock('Magento\Catalog\Block\Product\Price', $methods, array(), '', false);
        $layout = $this->getMock('Magento\Framework\View\LayoutInterface');
        $layout->expects($this->any())->method('createBlock')->will($this->returnValue($this->blockMock));
        $this->groupedView = $helper->getObject(
            'Magento\GroupedProduct\Block\Product\View\Type\Grouped',
            array(
                'data' => array('product' => $this->productMock, 'tier_price_template' => $this->defaultTemplate),
                'priceBlockTypes' => array('product_id' => array('block' => $this->blockMock)),
                'layout' => $layout
            )
        );
    }

    public function testGetAssociatedProducts()
    {

        $this->typeInstanceMock->expects(
            $this->once()
        )->method(
            'getAssociatedProducts'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue('expected')
        );

        $this->assertEquals('expected', $this->groupedView->getAssociatedProducts());
    }

    /**
     * @param string $id
     * @dataProvider setPreconfiguredValueDataProvider
     */
    public function testSetPreconfiguredValue($id)
    {
        $configValue = array('id_one' => 2);
        $associatedProduct = array('key' => $this->productMock);
        $this->configuredValueMock->expects(
            $this->once()
        )->method(
            'getSuperGroup'
        )->will(
            $this->returnValue($configValue)
        );
        $this->productMock->expects(
            $this->once()
        )->method(
            'getPreconfiguredValues'
        )->will(
            $this->returnValue($this->configuredValueMock)
        );

        $this->typeInstanceMock->expects(
            $this->once()
        )->method(
            'getAssociatedProducts'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue($associatedProduct)
        );


        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($id));
        $this->productMock->expects($this->any())->method('setQty')->with(2);
        $this->groupedView->setPreconfiguredValue();
    }

    public function setPreconfiguredValueDataProvider()
    {
        return array('item_id_exist_in_config' => array('id_one'), 'item_id_not_exist_in_config' => array('id_two'));
    }

    public function testSetPreconfiguredValueIfSuperGroupNotExist()
    {
        $this->productMock->expects(
            $this->once()
        )->method(
            'getPreconfiguredValues'
        )->will(
            $this->returnValue($this->configuredValueMock)
        );
        $this->configuredValueMock->expects($this->once())->method('getSuperGroup')->will($this->returnValue(false));
        $this->typeInstanceMock->expects($this->never())->method('getAssociatedProducts');
        $this->groupedView->setPreconfiguredValue();
    }

    /**
     * @param null|PHPUnit_Framework_MockObject_MockObject $price
     * @dataProvider getTierPriceHtmlDataProvider
     */
    public function testGetTierPriceHtml($price)
    {
        $this->productMock->expects($this->any())->method('getTypeId')->will($this->returnValue('product_id'));
        $this->blockMock->expects(
            $this->once()
        )->method(
            'setTemplate'
        )->with(
            $this->defaultTemplate
        )->will(
            $this->returnValue($this->blockMock)
        );
        $this->blockMock->expects(
            $this->once()
        )->method(
            'setProduct'
        )->with(
            $this->productMock
        )->will(
            $this->returnValue($this->blockMock)
        );
        $this->blockMock->expects(
            $this->once()
        )->method(
            'setListClass'
        )->with(
            'tier prices grouped items'
        )->will(
            $this->returnValue($this->blockMock)
        );
        $this->blockMock->expects(
            $this->once()
        )->method(
            'setShowDetailedPrice'
        )->with(
            false
        )->will(
            $this->returnValue($this->blockMock)
        );
        $this->blockMock->expects(
            $this->once()
        )->method(
            'setCanDisplayQty'
        )->with(
            false
        )->will(
            $this->returnValue($this->blockMock)
        );
        $this->blockMock->expects($this->once())->method('toHtml')->will($this->returnValue('expected'));
        $this->assertEquals('expected', $this->groupedView->getTierPriceHtml($price));
    }

    public function getTierPriceHtmlDataProvider()
    {
        return array('if_use_default_value_for_method' => array(null), 'if_pice_exist' => array($this->productMock));
    }
}
