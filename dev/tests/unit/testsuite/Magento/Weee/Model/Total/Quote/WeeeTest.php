<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Weee
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Weee\Model;

class WeeeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_weeeDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_mageObjMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerInterfaceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_weeeTaxMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_taxHelperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_registryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeConfigInterfaceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_quoteModelMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_addressMock;

    /**
     * @var \Magento\Weee\Model\Total\Quote\Weee
     */
    protected $_model;

    protected function setUp()
    {
        $this->_initializeMockObjects();
        $this->_prepareStaticMockExpects();
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            '\Magento\Weee\Model\Total\Quote\Weee',
            array(
                'weeeData' => $this->_weeeDataMock,
                'taxConfig' =>  $this->_configMock
            )
        );
    }

    /**
     * Initialize mock objects
     */
    protected function _initializeMockObjects(){
        $weeeDataMethods = [
            'isEnabled',
            'isDiscounted',
            'isTaxable',
            'includeInSubtotal',
            'addItemDiscountPrices',
        ];
        $quoteItemMethods = [
            '__wakeup',
            'getProduct',
            'setWeeeTaxAppliedAmount',
            'setBaseWeeeTaxAppliedAmount',
            'setWeeeTaxAppliedRowAmount',
            'setBaseWeeeTaxAppliedRowAmnt',
            'getHasChildren',
            'getChildren',
            'isChildrenCalculated',
            'getTotalQty',
            'getQuote'
        ];

        $this->_weeeDataMock = $this->getMock('\Magento\Weee\Helper\Data', $weeeDataMethods,
            $this->_prepareWeeeDataConstruct(), '');
        $this->_configMock = $this->getMock('\Magento\Tax\Model\Config', ['priceIncludesTax'], [], '', false);
        $this->_objectMock = $this->getMock('\Magento\Framework\Object', [], [], '', false);
        $this->_storeMock = $this->getMock('\Magento\Store\Model\Store', ['__wakeup', 'convertPrice'], [], '', false);
        $this->_quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item', $quoteItemMethods, [], '', false);
        $this->_productModelMock = $this->getMock('\Magento\Catalog\Model\Product', [], [], '', false);
        $this->_quoteModelMock = $this->getMock('\Magento\Sales\Model\Quote',
            ['__wakeup', 'getBillingAddress', 'getStore'], [], '', false);
        $this->_addressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [
            '__wakeup',
            'unsSubtotalInclTax',
            'unsBaseSubtotalInclTax',
            'getAllItems',
            'getQuote',
            'getAllNonNominalItems',
            'getPrice'
        ], [], '', false);
    }

    /**
     * Prepare constructor data for \Magento\Weee\Helper\Data
     * return array
     */
    protected function _prepareWeeeDataConstruct() {
        $this->_contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->_storeManagerInterfaceMock = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface', [], [], '', false
        );
        $this->_weeeTaxMock = $this->getMock(
            '\Magento\Weee\Model\Tax', ['__wakeup', 'getProductWeeeAttributes'], [], '', false
        );
        $this->_taxHelperMock = $this->getMock('\Magento\Tax\Helper\Data', [], [], '', false);
        $this->_registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->_scopeConfigInterfaceMock = $this->getMock(
            '\Magento\Framework\App\Config\ScopeConfigInterface', ['isSetFlag', 'getValue'], [], '', false
        );
        $weeeDataArgs = [
            'context'       => $this->_contextMock,
            'storeManage'   => $this->_storeManagerInterfaceMock,
            'weeeTax'        => $this->_weeeTaxMock,
            'taxData'       => $this->_taxHelperMock,
            'coreRegistry'  => $this->_registryMock,
            'scopeConfig'   => $this->_scopeConfigInterfaceMock,
        ];

        return $weeeDataArgs;
    }

    /**
     * Prepare expects for mocked objects
     */
    protected function _prepareStaticMockExpects() {
        $this->_addressMock->expects($this->any())->method('getQuote')
            ->will($this->returnValue($this->_quoteModelMock));
        $this->_addressMock->expects($this->any())->method('getAllItems')
            ->will($this->returnValue($this->_quoteModelMock));
        $this->_quoteModelMock->expects($this->any())->method('getStore')
            ->will($this->returnValue($this->_storeMock));
        $this->_quoteModelMock->expects($this->any())->method('getBillingAddress')
            ->will($this->returnValue($this->_addressMock));
        $this->_quoteModelMock->expects($this->any())->method('getPrice')
            ->will($this->returnValue(1));
        $this->_quoteItemMock->expects($this->any())->method('getProduct')
            ->will($this->returnValue($this->_productModelMock));
        $this->_quoteItemMock->expects($this->any())->method('getTotalQty')
            ->will($this->returnValue(1));
        $this->_quoteItemMock->expects($this->any())->method('getQuote')
            ->will($this->returnValue($this->_quoteModelMock));
        $this->_scopeConfigInterfaceMock->expects($this->any())->method('isSetFlag')
            ->will($this->returnValue(true));
        $this->_weeeTaxMock->expects($this->any())->method('getProductWeeeAttributes')
            ->will($this->returnValue(array($this->_objectMock)));
        $this->_storeMock->expects($this->any())->method('convertPrice')
            ->will($this->returnValue(1));
    }

    /**
     * Collect items and apply discount to weee
     */
    public function testCollectWithAddItemDiscountPrices()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items without applying discount to weee
     */
    public function testCollectWithoutAddItemDiscountPrices()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->never())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items without address item
     */
    public function testCollectWithoutAddressItem()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array()));
        $this->_addressMock->expects($this->never())->method('setAppliedTaxesReset');
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items with child
     */
    public function testCollectWithChildItem()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_quoteItemMock->expects($this->once())->method('isChildrenCalculated')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items with price that includes tax
     *
     * @param array
     */
    public function testCollectPriceIncludesTax()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_addressMock->expects($this->once())->method('getAllNonNominalItems');
        $this->_addressMock->expects($this->once())->method('getAllNonNominalItems');
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('priceIncludesTax')
            ->will($this->returnValue(false));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items with price that does not include tax
     *
     * @param array
     */
    public function testCollectPriceNotIncludesTax()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('priceIncludesTax')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect taxable items
     */
    public function testCollectTaxable()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_addressMock->expects($this->once())->method('unsSubtotalInclTax');
        $this->_addressMock->expects($this->once())->method('unsBaseSubtotalInclTax');
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('priceIncludesTax')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect does not taxable items
     */
    public function testCollectDataStoreDisabled()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_addressMock->expects($this->never())->method('unsSubtotalInclTax');
        $this->_addressMock->expects($this->never())->method('unsBaseSubtotalInclTax');
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->any())->method('includeInSubtotal')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->once(0))->method('isEnabled')
            ->will($this->returnValue(false));
        $this->_configMock->expects($this->never())->method('priceIncludesTax')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items and apply discount to weee
     */
    public function testCollectWithChildren()
    {
        $childQuoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item', [], [], '', false);

        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_quoteItemMock->expects($this->any())->method('getHasChildren')
            ->will($this->returnValue(true));
        $this->_quoteItemMock->expects($this->any())->method('isChildrenCalculated')
            ->will($this->returnValue(true));
        $this->_quoteItemMock->expects($this->any())->method('getChildren')
            ->will($this->returnValue(array($childQuoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    public function testCollectWeeeIncludeInSubtotal()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_weeeDataMock->expects($this->any())->method('includeInSubtotal')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect empty items
     */
    public function testCollectWithoutItems()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(null));
        $this->assertEquals($this->_model, $this->_model->collect($this->_addressMock));
    }

    /**
     * Fetch method test
     */
    public function testFetch()
    {
        $this->assertEquals($this->_model, $this->_model->fetch($this->_addressMock));
    }

    /**
     * Process configuration array
     */
    public function testProcessConfigArray()
    {
        $this->assertEquals($this->_configMock, $this->_model->processConfigArray($this->_configMock, $this->_storeMock));
    }

    /**
     * Get label
     */
    public function testGetLabel()
    {
        $this->assertEquals('', $this->_model->getLabel());
    }
}