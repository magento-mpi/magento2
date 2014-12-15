<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected function setUp()
    {
        $this->_registry = $this->getMock(
            'Magento\Framework\Registry',
            ['registry'],
            [],
            '',
            false
        );
        $this->_request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            [],
            [],
            '',
            false,
            false
        );
        $this->_storeManager = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface',
            [],
            [],
            '',
            false,
            false
        );


        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $constructArguments = $objectManager->getConstructArguments(
            'Magento\PricePermissions\Model\Observer',
            [
                'productFactory' => $this->getMock(
                    'Magento\Catalog\Model\ProductFactory',
                    [],
                    [],
                    '',
                    false
                ),
                'coreRegistry' => $this->_registry,
                'request' => $this->_request,
                'storeManager' => $this->_storeManager,
                'data' => [
                    'can_edit_product_price' => false,
                    'can_read_product_price' => false,
                    'can_edit_product_status' => false,
                    'default_product_price_string' => 'default'
                ]
            ]
        );

        $this->_observer = $this->getMock(
            'Magento\PricePermissions\Model\Observer',
            ['_removeColumnFromGrid'],
            $constructArguments
        );
        $this->_block = $this->getMock(
            'Magento\Backend\Block\Widget\Grid',
            [
                'getNameInLayout',
                'getMassactionBlock',
                'setCanReadPrice',
                'setCanEditPrice',
                'setTabData',
                'getChildBlock',
                'getParentBlock',
                'setDefaultProductPrice',
                'getForm',
                'getGroup',
            ],
            [],
            '',
            false
        );
        $this->_varienObserver = $this->getMock('Magento\Framework\Event\Observer', ['getBlock', 'getEvent']);
        $this->_varienObserver->expects($this->any())->method('getBlock')->will($this->returnValue($this->_block));
    }

    /**
     * @param $blockName string
     * @dataProvider productGridMassactionDataProvider
     */
    public function testAdminhtmlBlockHtmlBeforeProductGridMassaction($blockName)
    {
        $massaction = $this->getMock(
            'Magento\Backend\Block\Widget\Grid\Massaction',
            ['removeItem'],
            [],
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
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
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
            ['setCanEditPrice', 'setCanReadPrice'],
            [],
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

        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            ['__wakeup', 'getTypeId', 'isObjectNew'],
            [],
            '',
            false
        );
        $product->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE));
        $product->expects($this->any())
            ->method('isObjectNew')
            ->will($this->returnValue(true));
        $this->_registry
            ->expects($this->any())
            ->method('registry')
            ->with($this->equalTo('product'))
            ->will($this->returnValue($product));
        $form = $this->getMock(
            '\Magento\Framework\Data\Form',
            ['getElement', 'setReadonly'],
            [],
            '',
            false
        );
        $form->expects($this->any())
            ->method('setReadonly')
            ->with($this->equalTo(true), $this->equalTo(true))
            ->will($this->returnSelf());
        $fieldsetGroup = $this->getMock(
            '\Magento\Framework\Data\Form\Element\Fieldset',
            ['removeField'],
            [],
            '',
            false
        );
        $fieldsetGroup->expects($this->any())->method('removeField')->will($this->returnSelf());
        $elementPayment = $this->getMock('Magento\Framework\Data\Form\Element\AbstractElement',
            ['setReadonly', 'getForm'],
            [],
            '',
            false
        );
        $elementPayment->expects($this->any())
            ->method('setReadonly')
            ->with($this->equalTo(true), $this->equalTo(true))
            ->will($this->returnSelf());
        $elementPayment->expects($this->any())->method('getForm')->will($this->returnValue($form));
        $giftcardAmounts = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            ['setValue'],
            [],
            '',
            false
        );
        $giftcardAmountsValue = [
            [
                'website_id' => 1,
                'value' => 'default',
                'website_value' => 0
            ]
        ];
        $giftcardAmounts->expects($this->any())->method('setValue')->with($this->equalTo($giftcardAmountsValue));
        $priceElement = $this->getMock(
            'Magento\Framework\Data\Form\Element\AbstractElement',
            ['setValue'],
            [],
            '',
            false
        );
        $priceElement->expects($this->any())->method('setValue')->with($this->equalTo('default'));
        $map = [
            ['group_fields1', $fieldsetGroup],
            ['price', $priceElement],
            ['giftcard_amounts', $giftcardAmounts],
        ];
        $form->expects($this->any())->method('getElement')->will($this->returnValueMap($map));
        $group = $this->getMock('\Magento\Framework\Object', ['getId'], [], '', false);
        $group->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->_block->expects($this->once())->method('getForm')->will($this->returnValue($form));
        $this->_block->expects($this->once())->method('getGroup')->will($this->returnValue($group));
        $this->_request->expects($this->once())->method('getParam')->with('store', 0)->will($this->returnValue(1));
        $store = $this->getMock(
            'Magento\Store\Model\Store',
            ['getWebsiteId', '__wakeup'],
            [],
            '',
            false
        );
        $store->expects($this->any())->method('getWebsiteId')->will($this->returnValue(1));
        $this->_storeManager->expects($this->any())->method('getStore')->with(1)->will($this->returnValue($store));
        $this->_observer->adminhtmlBlockHtmlBefore($this->_varienObserver);
    }

    public function testAdminhtmlBlockHtmlBeforeCustomerCart()
    {
        $parentBlock = $this->getMock('Magento\Backend\Block\Template', ['getNameInLayout'], [], '', false);
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
        return [['product.grid'], ['admin.product.grid']];
    }

    public function gridCategoryProductGridDataProvider()
    {
        return [
            ['catalog.product.edit.tab.related'],
            ['catalog.product.edit.tab.upsell'],
            ['catalog.product.edit.tab.crosssell'],
            ['category.product.grid']
        ];
    }

    public function checkoutAccordionDataProvider()
    {
        return [
            ['products'],
            ['wishlist'],
            ['compared'],
            ['rcompared'],
            ['rviewed'],
            ['ordered'],
            ['checkout.accordion.products'],
            ['checkout.accordion.wishlist'],
            ['checkout.accordion.compared'],
            ['checkout.accordion.rcompared'],
            ['checkout.accordion.rviewed'],
            ['checkout.accordion.ordered']
        ];
    }

    public function checkoutItemsDataProvider()
    {
        return [['checkout.items'], ['items']];
    }

    /**
     * @covers \Magento\PricePermissions\Model\Observer::viewBlockAbstractToHtmlBefore
     */
    public function testViewBlockAbstractToHtmlBefore()
    {
        $product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->setMethods(['isObjectNew', 'getIsRecurring', '__wakeup'])
            ->getMock();
        $product->expects($this->any())->method('isObjectNew')->will($this->returnValue(false));
        $product->expects($this->any())->method('getIsRecurring')->will($this->returnValue(true));

        $productFactory = $this->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $productFactory->expects($this->any())->method('create')->will($this->returnValue($product));

        $coreRegistry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->setMethods(['registry'])
            ->getMock();
        $coreRegistry->expects($this->any())->method('registry')->with('product')->will($this->returnValue($product));
        $data = ['can_read_product_price' => false, 'can_edit_product_price' => false];
        $model = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\PricePermissions\Model\Observer',
                [
                    'coreRegistry' => $coreRegistry,
                    'productFactory' => $productFactory,
                    'data' => $data,
                ]
            );
        $block = $this->getMockBuilder(
            'Magento\Framework\View\Element\AbstractBlock'
        )->disableOriginalConstructor()->setMethods(
            [
                'getNameInLayout',
                'setProductEntity',
                'setIsReadonly',
                'addConfigOptions',
                'addFieldDependence',
                'setCanEditPrice'
            ]
        )->getMock();
        $observer = $this->getMockBuilder(
            'Magento\Framework\Event\Observer'
        )->disableOriginalConstructor()->setMethods(
            ['getBlock']
        )->getMock();
        $observer->expects($this->any())->method('getBlock')->will($this->returnValue($block));

        $nameInLayout = 'adminhtml.catalog.product.edit.tab.attributes';
        $block->expects($this->any())->method('getNameInLayout')->will($this->returnValue($nameInLayout));
        $block->expects($this->once())->method('setCanEditPrice')->with(false);

        $model->viewBlockAbstractToHtmlBefore($observer);
    }

    public function testCatalogProductSaveBefore()
    {
        $helper = $this->getMockBuilder('\Magento\PricePermissions\Helper\Data')->disableOriginalConstructor()
            ->setMethods(['getCanAdminEditProductStatus'])->getMock();
        $helper->expects($this->once())->method('getCanAdminEditProductStatus')->will($this->returnValue(false));

        $product = $this->getMockBuilder('\Magento\Catalog\Model\Product')->disableOriginalConstructor()
            ->setMethods(['isObjectNew', 'setStatus'])->getMock();
        $product->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $product->expects($this->once())->method('setStatus')
            ->with(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
            ->will($this->returnSelf());

        $event = $this->getMockBuilder('\Magento\Framework\Event')->disableOriginalConstructor()
            ->setMethods(['getDataObject'])->getMock();
        $event->expects($this->once())->method('getDataObject')->will($this->returnValue($product));
        $this->_varienObserver->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        /** @var \Magento\PricePermissions\Model\Observer $model */
        $model = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\PricePermissions\Model\Observer',
                [
                    'pricePermData' => $helper
                ]
            );


        $model->catalogProductSaveBefore($this->_varienObserver);

    }
}
