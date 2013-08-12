<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_Tax_DetailsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test calculateShippingTaxInfo method
     *
     * @param boolean $isTaxAfterDiscount
     * @param boolean $isDiscountOnInclTax
     * @param string $shippingTaxClass
     * @param Magento_Object $rateRequest
     * @param array $rates
     * @param array $expectedTaxInfo
     *
     * @dataProvider testCalculateShippingTaxInfoProvider
     */
    public function testCalculateShippingTaxInfo($isTaxAfterDiscount, $isDiscountOnInclTax, $shippingTaxClass,
        $rateRequest, $rates, $expectedTaxInfo
    ) {
        $calculator = $this->_prepareTaxCalculatorMock($rates, $rateRequest);
        $config = $this->_prepareTaxConfigMock($shippingTaxClass, $isTaxAfterDiscount, $isDiscountOnInclTax);

        $details = new Saas_PrintedTemplate_Model_Tax_Details(
            array('calculator' => $calculator, 'config' => $config)
        );

        $quote = $this->_prepareQuoteMock();
        $taxInfo = $details->calculateShippingTaxInfo($quote);

        $this->assertEquals($expectedTaxInfo, $taxInfo);
    }

    /**
     * Provide data for testCalculateShippingTaxInfo method
     *
     * @return array
     */
    public function testCalculateShippingTaxInfoProvider()
    {
        return array(
            // case 1
            array(
                1, 1, 'taxclass', new Magento_Object,
                array(array(
                    'percent' => 0.255,
                    'rates' => array(
                        array('percent' => 0.1),  array('percent' => 0.4), array('percent' => 0.01)
                    )
                )),
                // expectations
                array(array(
                    'percent' => 0.1, 'real_percent' => 0.05,
                    'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                ), array(
                    'percent' => 0.4, 'real_percent' => 0.2,
                    'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                ), array(
                    'percent' => 0.01, 'real_percent' => 0.005,
                    'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                ))
            ),

            // case 2
            array(
                0, 0, 'taxclass', new Magento_Object,
                // processes
                array(
                    array(
                        'percent' => 0.6,
                        'rates' => array(
                            array('percent' => 0.1), array('percent' => 0.2)
                        )
                    ),
                    array(
                        'percent' => 0.1,
                        'rates' => array(
                            array('percent' => 0.2)
                        )
                    )
                ),
                // expectations
                array(
                    array(
                        'percent' => 0.1, 'real_percent' => 0.2,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ),
                    array(
                        'percent' => 0.2, 'real_percent' => 0.4,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ),
                    array(
                        'percent' => 0.2, 'real_percent' => 0.1,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    )
                )
            ),
        );
    }

    /**
     * Test calculateItemsTaxInfo method
     *
     * @param $isTaxAfterDiscount
     * @param $isDiscountOnInclTax
     * @param $addresses
     * @param $shippingTaxClass
     * @param $rateRequest
     * @param $rates
     * @param $expectedTaxInfo
     *
     * @dataProvider testCalculateItemsTaxInfoProvider
     */
    public function testCalculateItemsTaxInfo($isTaxAfterDiscount, $isDiscountOnInclTax, $addresses,
        $shippingTaxClass, $rateRequest, $rates, $expectedTaxInfo
    ) {
        $quoteAddresses = array();
        foreach ($addresses as $address) {
            $nonNominalItems = array();
            foreach ($address['non_nominal_items'] as $data) {
                $addressItem = $this->_prepareAddressItem(
                    $data['parent_item_id'],
                    $data['id'],
                    $data['address_product'],
                    $data['children'],
                    $data['is_children_calculated']
                );
                $nonNominalItems[] = $addressItem;
            }

            $quoteAddress = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
                ->setMethods(array('getAllNonNominalItems'))
                ->disableOriginalConstructor()
                ->getMock();

            $quoteAddress->expects($this->any())
                ->method('getAllNonNominalItems')
                ->will($this->returnValue($nonNominalItems));

            $quoteAddresses[] = $quoteAddress;
        }

        $calculator = $this->_prepareTaxCalculatorMock($rates, $rateRequest);
        $config = $this->_prepareTaxConfigMock($shippingTaxClass, $isTaxAfterDiscount, $isDiscountOnInclTax);

        $details = new Saas_PrintedTemplate_Model_Tax_Details(
            array('calculator' => $calculator, 'config' => $config)
        );

        $quote = $this->_prepareQuoteMock($quoteAddresses);
        $taxInfo = $details->calculateItemsTaxInfo($quote);

        $this->assertEquals($expectedTaxInfo, $taxInfo);
    }

    /**
     * Provide data for testCalculateItemsTaxInfo method
     *
     * @return array
     */
    public function testCalculateItemsTaxInfoProvider()
    {
        return array(
            array(
                1, 1, array(
                    array('non_nominal_items' => array(array(
                        'parent_item_id' => null, 'id' => 1, 'is_children_calculated' => true,
                        'address_product' => array('tax_class' => 'product_tax_class'),
                        'children' => array(
                            array(
                                'parent_item_id' => 1, 'id' => 2, 'is_children_calculated' => false,
                                'address_product' => array('tax_class_id' => 'product_tax_class2'),
                            ),
                            array(
                                'parent_item_id' => 1, 'id' => 3, 'is_children_calculated' => false,
                                'address_product' => array('tax_class_id' => 'product_tax_class3'),
                            )
                        )
                    )))
                ),
                'taxclass', new Magento_Object,
                array(array(
                    'percent' => 0.255,
                    'rates' => array(array('percent' => 0.1), array('percent' => 0.4), array('percent' => 0.01))
                )),
                array(
                    '2' => array(array(
                        'percent' => 0.1, 'real_percent' => 0.05,
                        'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                    ), array(
                        'percent' => 0.4, 'real_percent' => 0.2,
                        'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                    ), array(
                        'percent' => 0.01, 'real_percent' => 0.005,
                        'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                    )),
                    '3' => array(array(
                        'percent' => 0.1, 'real_percent' => 0.05,
                        'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                    ), array(
                        'percent' => 0.4, 'real_percent' => 0.2,
                        'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                    ), array(
                        'percent' => 0.01, 'real_percent' => 0.005,
                        'is_tax_after_discount' => 1, 'is_discount_on_incl_tax' => 1
                    ))
                )
            ),
            array(
                0, 0, array(
                    array('non_nominal_items' => array(array(
                        'parent_item_id' => null, 'id' => 1, 'is_children_calculated' => true,
                        'address_product' => array('tax_class' => 'product_tax_class'),
                        'children' => array(array(
                                'parent_item_id' => 1, 'id' => 2, 'is_children_calculated' => false,
                                'address_product' => array('tax_class_id' => 'product_tax_class2'),
                            ),
                            array(
                                'parent_item_id' => 1, 'id' => 3, 'is_children_calculated' => false,
                                'address_product' => array('tax_class_id' => 'product_tax_class3'),
                            )
                        )
                    )))
                ),
                'taxclass', new Magento_Object,
                array(
                    array(
                        'percent' => 0.6, 'rates' => array(array('percent' => 0.1), array('percent' => 0.2))
                    ),
                    array(
                        'percent' => 0.1, 'rates' => array(array('percent' => 0.2))
                    )
                ),
                array(
                    '2' => array(array(
                        'percent' => 0.1, 'real_percent' => 0.2,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ), array(
                        'percent' => 0.2, 'real_percent' => 0.4,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ), array(
                        'percent' => 0.2, 'real_percent' => 0.1,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    )),
                    '3' => array(array(
                        'percent' => 0.1, 'real_percent' => 0.2,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ), array(
                        'percent' => 0.2, 'real_percent' => 0.4,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ), array(
                        'percent' => 0.2, 'real_percent' => 0.1,
                        'is_tax_after_discount' => 0, 'is_discount_on_incl_tax' => 0
                    ))
                )
            ),
        );
    }

    /**
     * Prepare tax/calculation singleton mock
     *
     * @param array $rates
     * @param Magento_Object $rateRequest
     *
     * @return Mage_Tax_Model_Calculation
     */
    protected function _prepareTaxCalculatorMock($rates, $rateRequest)
    {
        $calculator = $this->getMockBuilder('Mage_Tax_Model_Calculation')
            ->setMethods(array('getRateRequest','getAppliedRates'))
            ->disableOriginalConstructor()
            ->getMock();

        $calculator->expects($this->any())
            ->method('getRateRequest')
            ->will($this->returnValue($rateRequest));

        $calculator->expects($this->any())
            ->method('getAppliedRates')
            ->will($this->returnValue($rates));

        return $calculator;
    }

    /**
     * Prepare Quote Address Item model mock
     *
     * @param integer $parentItemId
     * @param integer $addressId
     * @param Magento_Object $addressProduct
     * @param array $childrenData
     * @param bool $isChildrenCalculated
     *
     * @return Mage_Sales_Model_Quote_Address_Item
     */
    protected function _prepareAddressItem($parentItemId, $addressId, $addressProduct, $childrenData = array(),
        $isChildrenCalculated = false
    ) {
        $childrenItems = array();
        if ($childrenData) {
            foreach ($childrenData as $childData) {
                $childItem = $this->_prepareAddressItem(
                    $childData['parent_item_id'],
                    $childData['id'],
                    $childData['address_product'],
                    isset($childData['children']) ? $childData['children'] : array(),
                    $childData['is_children_calculated']
                );

                $childrenItems[] = $childItem;
            }
        }

        $addressProductItem = new Magento_Object($addressProduct);

        $addressItem = $this->getMockBuilder('Mage_Sales_Model_Quote_Address_Item')
            ->disableOriginalConstructor()
            ->setMethods(array(
                'getParentItemId', 'getId', 'getProduct', 'getHasChildren', 'isChildrenCalculated', 'getChildren')
        )->getMock();

        $addressItem->expects($this->any())
            ->method('getHasChildren')
            ->will($this->returnValue(count($childrenItems) > 0 ? true : false));

        $addressItem->expects($this->any())
            ->method('isChildrenCalculated')
            ->will($this->returnValue($isChildrenCalculated));

        $addressItem->expects($this->any())
            ->method('getParentItemId')
            ->will($this->returnValue($parentItemId));

        $addressItem->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($addressId));

        $addressItem->expects($this->any())
            ->method('getProduct')
            ->will($this->returnValue($addressProductItem));

        $addressItem->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue($childrenItems));

        return $addressItem;
    }

    /**
     * Prepare tax/config singleton mock
     *
     * @param string $shippingTaxClass
     * @param bool $isTaxAfterDiscount
     * @param bool $isDiscountOnInclTax
     *
     * @return Mage_Tax_Model_Config
     */
    protected function _prepareTaxConfigMock($shippingTaxClass, $isTaxAfterDiscount, $isDiscountOnInclTax)
    {
        $config = $this->getMockBuilder('Mage_Tax_Model_Config')
            ->setMethods(array('applyTaxAfterDiscount','discountTax','getShippingTaxClass'))
            ->getMock();

        $config->expects($this->any())
            ->method('getShippingTaxClass')
            ->will($this->returnValue($shippingTaxClass));

        $config->expects($this->any())
            ->method('applyTaxAfterDiscount')
            ->will($this->returnValue($isTaxAfterDiscount));

        $config->expects($this->any())
            ->method('discountTax')
            ->will($this->returnValue($isDiscountOnInclTax));

        return $config;
    }

    /**
     * Prepare quote mock object
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     *
     * @param null $quoteAddresses
     * @return Mage_Sales_Model_Quote
     */
    protected function _prepareQuoteMock($quoteAddresses = null)
    {
        $quote = $this->getMockBuilder('Mage_Sales_Model_Quote')
            ->setMethods(array('getCustomer', 'getShippingAddress', 'getBillingAddress', 'getCustomerTaxClassId',
                'getStore', 'getAllAddresses')
            )->disableOriginalConstructor()
            ->getMock();

        $customer = $this->getMockBuilder('Magento_Customer_Model_Customer')
            ->disableOriginalConstructor()
            ->getMock();

        $store = $this->getMockBuilder('Magento_Core_Model_Store')
            ->disableOriginalConstructor()
            ->getMock();

        $address = $this->getMockBuilder('Mage_Sales_Model_Quote_Address')
            ->disableOriginalConstructor()
            ->getMock();

        $quote->expects($this->any())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $quote->expects($this->any())
            ->method('getShippingAddress')
            ->will($this->returnValue($address));

        $quote->expects($this->any())
            ->method('getBillingAddress')
            ->will($this->returnValue($address));

        $quote->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        if ($quoteAddresses) {
            $quote->expects($this->any())
                ->method('getAllAddresses')
                ->will($this->returnValue($quoteAddresses));
        }

        return $quote;
    }
}
