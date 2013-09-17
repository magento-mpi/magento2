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

class Magento_PricePermissions_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_PricePermissions_Model_Observer
     */
    protected $_observer;

    /**
     * @var Magento_Event_Observer
     */
    protected $_varienObserver;

    /**
     * @var Magento_Adminhtml_Block_Widget_Grid
     */
    protected $_block;

    protected function setUp()
    {
        $this->_observer = $this->getMock('Magento_PricePermissions_Model_Observer',
            array('_removeColumnFromGrid', '_hidePriceElements'),
            array(
                $this->getMock('Magento_PricePermissions_Helper_Data', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false),
                array(
                    'request' => false,
                    'can_edit_product_price' => false,
                    'can_read_product_price' => false,
                    'can_edit_product_status' => false,
                    'default_product_price_string' => 'default'
                ),
            )
        );
        $this->_block = $this->getMock('Magento_Adminhtml_Block_Widget_Grid',
            array('getNameInLayout', 'getMassactionBlock', 'setCanReadPrice', 'setCanEditPrice', 'setTabData',
                'getChildBlock', 'getParentBlock', 'setDefaultProductPrice', 'getForm'),
            array(), '', false);
        $this->_varienObserver = $this->getMock('Magento_Event_Observer', array('getBlock'));
        $this->_varienObserver->expects($this->once())->method('getBlock')->will($this->returnValue($this->_block));
    }

    /**
     * @param $blockName string
     * @dataProvider productGridMassactionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeProductGridMassaction($blockName)
    {
        $massaction = $this->getMock('Magento_Backend_Block_Widget_Grid_Massaction',
            array('removeItem'), array(), '', false);
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

        $this->_observer->expects($this->exactly(2))->method('_removeColumnFromGrid')
            ->with($this->isInstanceOf('Magento_Adminhtml_Block_Widget_Grid'),
            $this->logicalOr(
                $this->equalTo('price'),
                $this->equalTo('total')
            ));
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
        $this->_block->expects($this->once())->method('setCanReadPrice')
            ->with($this->equalTo(false));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeDownloadableLinks()
    {
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.downloadable.links');
        $this->_block->expects($this->once())->method('setCanReadPrice')
            ->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')
            ->with($this->equalTo(false));
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

    public function testAdminhtmlBlockHtmlBeforeProductTabs()
    {
        $this->_setGetNameInLayoutExpects('product_tabs');

        $this->_block->expects($this->exactly(2))->method('setTabData')
            ->with($this->equalTo('configurable'),
            $this->logicalOr(
                $this->equalTo('can_edit_price'),
                $this->equalTo('can_read_price')
            ),
            $this->equalTo(false));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeProductOptions()
    {
        $this->_setGetNameInLayoutExpects('admin.product.options');

        $childBlock = $this->getMock('Magento_Backend_Block_Template',
            array('setCanEditPrice', 'setCanReadPrice'), array(), '', false);
        $childBlock->expects($this->once())->method('setCanEditPrice')->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanReadPrice')->with($this->equalTo(false));

        $this->_block->expects($this->once())->method('getChildBlock')->with($this->equalTo('options_box'))
            ->will($this->returnValue($childBlock));

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
        $this->_block->expects($this->once())->method('setCanReadPrice')
            ->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')
            ->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setDefaultProductPrice')->with($this->equalTo('default'));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeBundleOpt()
    {
        $childBlock = $this->getMock('Magento_Backend_Block_Template',
            array('setCanEditPrice', 'setCanReadPrice'), array(), '', false);
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.bundle.option');
        $childBlock->expects($this->once())->method('setCanReadPrice')
            ->with($this->equalTo(false));
        $childBlock->expects($this->once())->method('setCanEditPrice')
            ->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanReadPrice')
            ->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('setCanEditPrice')
            ->with($this->equalTo(false));
        $this->_block->expects($this->once())->method('getChildBlock')->will($this->returnValue($childBlock));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeTabAttributes()
    {
        $this->_setGetNameInLayoutExpects('adminhtml.catalog.product.edit.tab.attributes');

        $this->_observer->expects($this->once())->method('_hidePriceElements')
            ->with($this->isInstanceOf('Magento_Adminhtml_Block_Widget_Grid'));

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeSuperConfigSimple()
    {
        $formElement = $this->getMock('Magento_Data_Form_Element_Text',
            array('setValue', 'setReadOnly'), array(), '', false);
        $formElement->expects($this->once())->method('setValue')
            ->with(Magento_Catalog_Model_Product_Status::STATUS_DISABLED);
        $formElement->expects($this->once())->method('setReadOnly')
            ->with(true, true);
        $form = $this->getMock('Magento_Data_Form',
            array('getElement'), array(), '', false);
        $form->expects($this->once())->method('getElement')->with('simple_product_status')
            ->will($this->returnValue($formElement));
        $this->_setGetNameInLayoutExpects('catalog.product.edit.tab.super.config.simple');
        $this->_block->expects($this->once())->method('getForm')
            ->will($this->returnValue($form));

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerCart()
    {
        $parentBlock = $this->getMock('Magento_Backend_Block_Template',
            array('getNameInLayout'), array(), '', false);
        $parentBlock->expects($this->once())->method('getNameInLayout')
            ->will($this->returnValue('admin.customer.carts'));

        $this->_setGetNameInLayoutExpects('customer_cart_');
        $this->_block->expects($this->once())->method('getParentBlock')
            ->will($this->returnValue($parentBlock));

        $this->_observer->expects($this->exactly(2))->method('_removeColumnFromGrid')
            ->with($this->isInstanceOf('Magento_Adminhtml_Block_Widget_Grid'),
            $this->logicalOr(
                $this->equalTo('price'),
                $this->equalTo('total')
            ));

        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }


    protected function _assertPriceColumnRemove()
    {
        $this->_observer->expects($this->once())->method('_removeColumnFromGrid')
            ->with($this->isInstanceOf('Magento_Adminhtml_Block_Widget_Grid'), $this->equalTo('price'));
    }

    protected function _setGetNameInLayoutExpects($blockName)
    {
        $this->_block->expects($this->exactly(2))->method('getNameInLayout')
            ->will($this->returnValue($blockName));
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
        return array(array('products'),
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
            array('checkout.accordion.ordered'));
    }

    public function checkoutItemsDataProvider()
    {
        return array(array('checkout.items'), array('items'));
    }
}
