<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PricePermissions\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PricePermissions\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_varienObserver;

    /**
     * @var \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected $_block;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\PricePermissions\Model\Observer',
            array(
                'productFactory' => $this->getMock(
                    'Magento\Catalog\Model\ProductFactory',
                    array(),
                    array(),
                    '',
                    false
                ),
                'data' => array(
                    'can_edit_product_price' => false,
                    'can_read_product_price' => false,
                    'can_edit_product_status' => false,
                    'default_product_price_string' => 'default'
                )
            )
        );

        $this->_observer = $this->getMock(
            'Magento\PricePermissions\Model\Observer',
            array('_removeColumnFromGrid', '_hidePriceElements'),
            $constructArguments
        );
        $this->_block = $this->getMock(
            'Magento\Backend\Block\Widget\Grid',
            array(
                'getNameInLayout',
                'getMassactionBlock',
                'setCanReadPrice',
                'setCanEditPrice',
                'setTabData',
                'getChildBlock',
                'getParentBlock',
                'setDefaultProductPrice',
                'getForm'
            ),
            array(),
            '',
            false
        );
        $this->_varienObserver = $this->getMock('Magento\Framework\Event\Observer', array('getBlock'));
        $this->_varienObserver->expects($this->once())->method('getBlock')->will($this->returnValue($this->_block));
    }

    /**
     * @param $blockName string
     * @dataProvider productGridMassactionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeProductGridMassaction($blockName)
    {
        $massaction = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Massaction',
            array('removeItem'),
            array(),
            '',
            false
        );
        $massaction->expects($this->once())->method('removeItem')->with($this->equalTo('status'));

        $this->_setGetNameInLayoutExpects($blockName);
        $this->_block->expects($this->once())->method('getMassactionBlock')->will($this->returnValue($massaction));
        $this->_assertPriceColumnRemove();

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider gridCategoryProductGridDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeGridCategoryProductGrid($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);

        $this->_assertPriceColumnRemove();
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    /**
     * @param $blockName string
     */
    public function testAdminhtmlBlockHtmlBeforeCustomerViewCart()
    {
        $this->_setGetNameInLayoutExpects('admin.customer.view.cart');

        $this->_observer->expects(
            $this->exactly(2)
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->logicalOr($this->equalTo('price'), $this->equalTo('total'))
        );
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider checkoutAccordionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeCheckoutAccordion($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);

        $this->_assertPriceColumnRemove();
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    /**
     * @param $blockName string
     * @dataProvider checkoutItemsDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeItems($blockName)
    {
        $this->_setGetNameInLayoutExpects($blockName);
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeDownloadableLinks()
    {
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.downloadable.links');
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeSuperConfigGrid()
    {
        $this->_setGetNameInLayoutExpects('admin.product.edit.tab.super.config.grid');

        $this->_assertPriceColumnRemove();
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeTabSuperGroup()
    {
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.super.group');

        $this->_assertPriceColumnRemove();
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeProductOptions()
    {
        $this->_setGetNameInLayoutExpects('admin.product.options');

        $childBlock = $this->getMock(
            'Magento\Backend\Block\Template',
            array('setCanEditPrice', 'setCanReadPrice'),
            array(),
            '',
            false
        );
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));

        $this->_block->expects(
            $this->once()
        )->method(
            'getChildBlock'
        )->with(
            $this->equalTo('options_box')
        )->will(
            $this->returnValue($childBlock)
        );

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundleSearchGrid()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.bundle.option.search.grid');

        $this->_assertPriceColumnRemove();
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundlePrice()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.bundle.edit.tab.attributes.price');
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setDefaultProductPrice')->with($this->equalTo('default'));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundleOpt()
    {
        $childBlock = $this->getMock(
            'Magento\Backend\Block\Template',
            array('setCanEditPrice', 'setCanReadPrice'),
            array(),
            '',
            false
        );
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.bundle.option');
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('getChildBlock')->will($this->returnValue($childBlock));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeTabAttributes()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.attributes');

        $this->_observer->expects(
            $this->once()
        )->method(
            '_hidePriceElements'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid')
        );

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerCart()
    {
        $parentBlock = $this->getMock('Magento\Backend\Block\Template', array('getNameInLayout'), array(), '', false);
        $parentBlock->expects(
            $this->once()
        )->method(
            'getNameInLayout'
        )->will(
            $this->returnValue('admin.customer.carts')
        );

        $this->_setGetNameInLayoutExpects('customer_cart_');
        $this->_block->expects($this->once())->method('getParentBlock')->will($this->returnValue($parentBlock));

        $this->_observer->expects(
            $this->exactly(2)
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->logicalOr($this->equalTo('price'), $this->equalTo('total'))
        );

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    protected function _assertPriceColumnRemove()
    {
        $this->_observer->expects(
            $this->once()
        )->method(
            '_removeColumnFromGrid'
        )->with(
            $this->isInstanceOf('Magento\Backend\Block\Widget\Grid'),
            $this->equalTo('price')
        );
    }

    protected function _setGetNameInLayoutExpects($blockName)
    {
        $this->_block->expects($this->exactly(2))->method('getNameInLayout')->will($this->returnValue($blockName));
    }

    public function productGridMassactionDataProvider()
    {
        return array(array('product.grid'), array('admin.product.grid'));
    }

    public function gridCategoryProductGridDataProvider()
    {
        return array(
            array('catalog.product.edit.tab.related'),
            array('catalog.product.edit.tab.upsell'),
            array('catalog.product.edit.tab.crosssell'),
            array('category.product.grid')
        );
    }

    public function checkoutAccordionDataProvider()
    {
        return array(
            array('products'),
            array('wishlist'),
            array('compared'),
            array('rcompared'),
            array('rviewed'),
            array('ordered'),
            array('checkout.accordion.products'),
            array('checkout.accordion.wishlist'),
            array('checkout.accordion.compared'),
            array('checkout.accordion.rcompared'),
            array('checkout.accordion.rviewed'),
            array('checkout.accordion.ordered')
        );
    }

    public function checkoutItemsDataProvider()
    {
        return array(array('checkout.items'), array('items'));
    }
}
