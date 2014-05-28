<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Tax\Model\Config
 */
namespace Magento\Tax\Model;

use Magento\Tax\Model\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the setter/getter methods that bypass the ScopeConfigInterface object
     *
     * @param string $setterMethod
     * @param string $getterMethod
     * @param bool $value
     * @dataProvider dataProviderDirectSettersGettersMethods
     */
    public function testDirectSettersGettersMethods($setterMethod, $getterMethod, $value)
    {
        // Need a mocked object with only dummy methods.  It is just needed for construction.
        // The setter/getter methods do not use this object (for this set of tests).
        $scopeConfigMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');

        /** @var \Magento\Tax\Model\Config */
        $model = new Config($scopeConfigMock);
        $model->{$setterMethod}($value);
        $this->assertEquals($value, $model->{$getterMethod}());
    }

    /**
     * Returns a set of 'true' and 'false' parameters for each of the setter/getter method pairs
     *
     * @return array
     */
    public function dataProviderDirectSettersGettersMethods()
    {
        return $this->_buildTrueFalseArray(array(
            array(
                'setShippingPriceIncludeTax',
                'shippingPriceIncludesTax'
            ),
            array(
                'setNeedUseShippingExcludeTax',
                'getNeedUseShippingExcludeTax'
            ),
            array(
                'setPriceIncludesTax',
                'priceIncludesTax'
            )
        ));
    }

    /**
     * Returns an output array that is twice the size of the input array by adding 'true' and then 'false' to the
     * set of parameters given
     *
     * @param array $arrayIn
     * @return array
     */
    protected function _buildTrueFalseArray($arrayIn)
    {
        $arrayOut = array();

        foreach($arrayIn as $paramArray) {
            // Replicate the paramArray, append 'true', and add the new array to the output array
            $arrayT = $paramArray;
            $arrayT[] = true;
            $arrayOut[] = $arrayT;
            // Replicate the paramArray, append 'false', and add the new array to the output array
            $arrayF = $paramArray;
            $arrayF[] = false;
            $arrayOut[] = $arrayF;
        }

        return $arrayOut;
    }


    /**
     * Tests the getCalculationSequence method
     *
     * @param bool $applyTaxAfterDiscount
     * @param bool $discountTaxIncl
     * @param string $expectedValue
     * @dataProvider dataProviderGetCalculationSequence
     */
    public function testGetCalculationSequence($applyTaxAfterDiscount, $discountTaxIncl, $expectedValue)
    {
        $scopeConfigMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');
        $scopeConfigMock->expects(
            $this->at(0))->method('getValue')->will($this->returnValue($applyTaxAfterDiscount));
        $scopeConfigMock->expects(
            $this->at(1))->method('getValue')->will($this->returnValue($discountTaxIncl));

        /** @var \Magento\Tax\Model\Config */
        $model = new Config($scopeConfigMock);
        $this->assertEquals($expectedValue, $model->getCalculationSequence());
    }

    /**
     * @return array
     */
    public function dataProviderGetCalculationSequence()
    {
        return array(
            array(true,  true,  \Magento\Tax\Model\Calculation::CALC_TAX_AFTER_DISCOUNT_ON_INCL),
            array(true,  false, \Magento\Tax\Model\Calculation::CALC_TAX_AFTER_DISCOUNT_ON_EXCL),
            array(false, true,  \Magento\Tax\Model\Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_INCL),
            array(false, false, \Magento\Tax\Model\Calculation::CALC_TAX_BEFORE_DISCOUNT_ON_EXCL)
        );
    }


    /**
     * Tests the methods that rely on the ScopeConfigInterface object to provide their return values
     *
     * @param string $method
     * @param string $path
     * @param bool|int $configValue
     * @param bool $expectedValue
     * @dataProvider dataProviderScopeConfigMethods
     */
    public function testScopeConfigMethods($method, $path, $configValue, $expectedValue)
    {
        $scopeConfigMock = $this->getMockForAbstractClass('Magento\Framework\App\Config\ScopeConfigInterface');
        $scopeConfigMock->expects(
            $this->once()
            )->method(
                'getValue'
            )->with(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                null
            )->will(
                $this->returnValue($configValue)
            );

        /** @var \Magento\Tax\Model\Config */
        $model = new Config($scopeConfigMock);
        $this->assertEquals($expectedValue, $model->{$method}());
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderScopeConfigMethods()
    {
        return array(
            array(
                'priceIncludesTax',
                Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX,
                true,
                true
            ),
            array(
                'applyTaxAfterDiscount',
                Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT,
                true,
                true
            ),
            array(
                'getPriceDisplayType',
                Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE,
                true,
                true
            ),
            array(
                'discountTax',
                Config::CONFIG_XML_PATH_DISCOUNT_TAX,
                1,
                true
            ),
            array(
                'getAlgorithm',
                Config::XML_PATH_ALGORITHM,
                true,
                true
            ),
            array(
                'getShippingTaxClass',
                Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS,
                true,
                true
            ),
            array(
                'getShippingPriceDisplayType',
                Config::CONFIG_XML_PATH_DISPLAY_SHIPPING,
                true,
                true
            ),
            array(
                'shippingPriceIncludesTax',
                Config::CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX,
                true,
                true
            ),
            array(
                'displayCartPricesInclTax',
                Config::XML_PATH_DISPLAY_CART_PRICE,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displayCartPricesExclTax',
                Config::XML_PATH_DISPLAY_CART_PRICE,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displayCartPricesBoth',
                Config::XML_PATH_DISPLAY_CART_PRICE,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displayCartSubtotalInclTax',
                Config::XML_PATH_DISPLAY_CART_SUBTOTAL,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displayCartSubtotalExclTax',
                Config::XML_PATH_DISPLAY_CART_SUBTOTAL,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displayCartSubtotalBoth',
                Config::XML_PATH_DISPLAY_CART_SUBTOTAL,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displayCartShippingInclTax',
                Config::XML_PATH_DISPLAY_CART_SHIPPING,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displayCartShippingExclTax',
                Config::XML_PATH_DISPLAY_CART_SHIPPING,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displayCartShippingBoth',
                Config::XML_PATH_DISPLAY_CART_SHIPPING,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displayCartDiscountInclTax',
                Config::XML_PATH_DISPLAY_CART_DISCOUNT,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displayCartDiscountExclTax',
                Config::XML_PATH_DISPLAY_CART_DISCOUNT,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displayCartDiscountBoth',
                Config::XML_PATH_DISPLAY_CART_DISCOUNT,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displayCartTaxWithGrandTotal',
                Config::XML_PATH_DISPLAY_CART_GRANDTOTAL,
                true,
                true
            ),
            array(
                'displayCartFullSummary',
                Config::XML_PATH_DISPLAY_CART_FULL_SUMMARY,
                true,
                true
            ),
            array(
                'displayCartZeroTax',
                Config::XML_PATH_DISPLAY_CART_ZERO_TAX,
                true,
                true
            ),
            array(
                'displaySalesPricesInclTax',
                Config::XML_PATH_DISPLAY_SALES_PRICE,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displaySalesPricesExclTax',
                Config::XML_PATH_DISPLAY_SALES_PRICE,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displaySalesPricesBoth',
                Config::XML_PATH_DISPLAY_SALES_PRICE,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displaySalesSubtotalInclTax',
                Config::XML_PATH_DISPLAY_SALES_SUBTOTAL,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displaySalesSubtotalExclTax',
                Config::XML_PATH_DISPLAY_SALES_SUBTOTAL,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displaySalesSubtotalBoth',
                Config::XML_PATH_DISPLAY_SALES_SUBTOTAL,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displaySalesShippingInclTax',
                Config::XML_PATH_DISPLAY_SALES_SHIPPING,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displaySalesShippingExclTax',
                Config::XML_PATH_DISPLAY_SALES_SHIPPING,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displaySalesShippingBoth',
                Config::XML_PATH_DISPLAY_SALES_SHIPPING,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displaySalesDiscountInclTax',
                Config::XML_PATH_DISPLAY_SALES_DISCOUNT,
                Config::DISPLAY_TYPE_INCLUDING_TAX,
                true
            ),
            array(
                'displaySalestDiscountExclTax',
                Config::XML_PATH_DISPLAY_SALES_DISCOUNT,
                Config::DISPLAY_TYPE_EXCLUDING_TAX,
                true
            ),
            array(
                'displaySalesDiscountBoth',
                Config::XML_PATH_DISPLAY_SALES_DISCOUNT,
                Config::DISPLAY_TYPE_BOTH,
                true
            ),
            array(
                'displaySalesTaxWithGrandTotal',
                Config::XML_PATH_DISPLAY_SALES_GRANDTOTAL,
                true,
                true
            ),
            array(
                'displaySalesFullSummary',
                Config::XML_PATH_DISPLAY_SALES_FULL_SUMMARY,
                true,
                true
            ),
            array(
                'displaySalesZeroTax',
                Config::XML_PATH_DISPLAY_SALES_ZERO_TAX,
                true,
                true
            ),
            array(
                'crossBorderTradeEnabled',
                Config::CONFIG_XML_PATH_CROSS_BORDER_TRADE_ENABLED,
                true,
                true
            )
        );
    }
}
