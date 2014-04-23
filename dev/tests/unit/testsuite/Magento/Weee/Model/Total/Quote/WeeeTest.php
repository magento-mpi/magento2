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
    protected $_weeeDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_salesRuleDataMock;

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
        $this->_object = new \Magento\Object([
            'name' => 'object_name',
            'code' => '1',
            'amount' => '2'
        ]);
        $this->_initializeMockObjects();
        $this->_prepareStaticMockExpects();
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            '\Magento\Weee\Model\Total\Quote\Weee',
            array(
                'weeeData' => $this->_weeeDataMock,
                'salesRuleData' => $this->_salesRuleDataMock,
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
            'includeInSubtotal'
        ];
        $quoteItemMethods = [
            '__wakeup',
            'getProduct',
            'setWeeeTaxAppliedAmount',
            'setBaseWeeeTaxAppliedAmount',
            'setWeeeTaxAppliedRowAmount',
            'setBaseWeeeTaxAppliedRowAmnt',
            'isChildrenCalculated',
            'getTotalQty'
        ];

        $this->_weeeDataMock = $this->getMock('\Magento\Weee\Helper\Data', $weeeDataMethods,
            $this->_prepareWeeeDataConstruct(), '');
        $this->_salesRuleDataMock = $this->getMock(
            '\Magento\SalesRule\Helper\Data', ['addItemDiscountPrices'], [], '', false
        );
        $this->_configMock = $this->getMock('\Magento\Tax\Model\Config', ['priceIncludesTax'], [], '', false);
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
            'getAllNonNominalItems'
        ], [], '', false);
    }
    /**
     * Prepare constructor data for \Magento\Weee\Helper\Data
     * return array
     */
    protected function _prepareWeeeDataConstruct() {
        $this->_contextMock = $this->getMock('\Magento\App\Helper\Context', [], [], '', false);
        $this->_storeManagerInterfaceMock = $this->getMock(
            'Magento\Store\Model\StoreManagerInterface', [], [], '', false
        );
        $this->_weeeTaxMock = $this->getMock(
            '\Magento\Weee\Model\Tax', ['__wakeup', 'getProductWeeeAttributes'], [], '', false
        );
        $this->_taxHelperMock = $this->getMock('\Magento\Tax\Helper\Data', [], [], '', false);
        $this->_registryMock = $this->getMock('\Magento\Registry', [], [], '', false);
        $this->_scopeConfigInterfaceMock = $this->getMock(
            '\Magento\App\Config\ScopeConfigInterface', ['isSetFlag', 'getValue'], [], '', false
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
        $this->_quoteItemMock->expects($this->any())->method('setWeeeTaxAppliedAmount')
            ->will($this->returnValue($this->_quoteItemMock));
        $this->_quoteItemMock->expects($this->any())->method('setBaseWeeeTaxAppliedAmount')
            ->will($this->returnValue($this->_quoteItemMock));
        $this->_quoteItemMock->expects($this->any())->method('setWeeeTaxAppliedRowAmount')
            ->will($this->returnValue($this->_quoteItemMock));
        $this->_quoteItemMock->expects($this->any())->method('setBaseWeeeTaxAppliedRowAmnt')
            ->will($this->returnValue($this->_quoteItemMock));
        $this->_quoteItemMock->expects($this->any())->method('getProduct')
            ->will($this->returnValue($this->_productModelMock));
        $this->_quoteItemMock->expects($this->any())->method('getTotalQty')
            ->will($this->returnValue(1));
        $this->_weeeDataMock->expects($this->any())->method('isEnabled')
            ->will($this->returnValue(true));
        $this->_scopeConfigInterfaceMock->expects($this->any())->method('isSetFlag')
            ->will($this->returnValue(true));
        $this->_weeeTaxMock->expects($this->any())->method('getProductWeeeAttributes')
            ->will($this->returnValue(array($this->_object)));
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
        $this->_salesRuleDataMock->expects($this->once())->method('addItemDiscountPrices');
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
        $this->_salesRuleDataMock->expects($this->never())->method('addItemDiscountPrices');
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
        $this->_quoteItemMock->expects($this->once())->method('isChildrenCalculated')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect items with price that includes tax
     *
     * @param array
     * @dataProvider amountTypeData
     */
    public function testCollectPriceIncludesTax($amountData)
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_addressMock->expects($this->once())->method('getAllNonNominalItems');
        $this->_addressMock->expects($this->once())->method('getAllNonNominalItems');
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_salesRuleDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('priceIncludesTax')
            ->will($this->returnValue(false));
        $this->_model->collect($this->_addressMock);
        foreach ($amountData as $key => $amountType) {
           $this->assertNotEmpty($this->_quoteItemMock->getData($amountType));
        }
    }

    /**
     * Collect items with price that does not include tax
     *
     * @param array
     * @dataProvider amountTypeData
     */
    public function testCollectPriceNotIncludesTax($amountData)
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_salesRuleDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('priceIncludesTax')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
        foreach ($amountData as $key => $amountType) {
            $this->assertEmpty($this->_quoteItemMock->getData($amountType));
        }
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
        $this->_salesRuleDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('priceIncludesTax')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Collect does not taxable items
     */
    public function testCollectNotTaxable()
    {
        $this->_addressMock->expects($this->any())->method('getAllNonNominalItems')
            ->will($this->returnValue(array($this->_quoteItemMock)));
        $this->_addressMock->expects($this->never())->method('unsSubtotalInclTax');
        $this->_addressMock->expects($this->never())->method('unsBaseSubtotalInclTax');
        $this->_weeeDataMock->expects($this->any())->method('isDiscounted')
            ->will($this->returnValue(true));
        $this->_salesRuleDataMock->expects($this->once())->method('addItemDiscountPrices');
        $this->_weeeDataMock->expects($this->any())->method('isTaxable')
            ->will($this->returnValue(false));
        $this->_weeeDataMock->expects($this->any())->method('includeInSubtotal')
            ->will($this->returnValue(false));
        $this->_configMock->expects($this->never())->method('priceIncludesTax')
            ->will($this->returnValue(true));
        $this->_model->collect($this->_addressMock);
    }

    /**
     * Data provider amount type list
     */
    public function amountTypeData()
    {
        return array(
            array(
                'amountType' =>
                    array(
                        'extra_taxable_amount',
                        'base_extra_taxable_amount',
                        'extra_row_taxable_amount',
                        'base_extra_row_taxable_amount'
                    )
            )
        );
    }

}