<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_UnitpriceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param Saas_UnitPrice_Helper_Data $helper
     * @param null $exceptionMessage
     * @param array $params
     * @return Saas_UnitPrice_Model_Unitprice
     */
    public function unitPriceMock(Saas_UnitPrice_Helper_Data $helper, $exceptionMessage = null,
        array $params = array()
    ) {
        $unitPrice = $this->getMock(
            'Saas_UnitPrice_Model_Unitprice',
            array('_throwMageException', '_getHelper'),
            array($params)
        );

        $unitPrice->expects($this->any())
            ->method('_getHelper')
            ->will($this->returnValue($helper));

        if ($exceptionMessage) {
            $unitPrice->expects($this->atLeastOnce())
                ->method('_throwMageException')
                ->with($exceptionMessage)
                ->will($this->throwException(new Magento_Core_Exception($exceptionMessage)));
        } else {
            $unitPrice->expects($this->never())
                ->method('_throwMageException');
        }

        return $unitPrice;
    }

    public function helperMock()
    {
        return $this->getMock('Saas_UnitPrice_Helper_Data', array('getConfig', '__'), array(), '', false);
    }

    /**
     * @dataProvider providerConfigRates
     */
    public function testGetConversionRateShouldReturnConversionRateByConfig($fromUnit, $toUnit, $expectedPath,
        $expectedRate
    ) {
        // prepare
        $helper = $this->helperMock();
        $helper->expects($this->once())
            ->method('getConfig')
            ->with($expectedPath)
            ->will($this->returnValue($expectedRate));

        $unitPrice = $this->unitPriceMock($helper);

        // act
        $rate = $unitPrice->getConversionRate($fromUnit, $toUnit);

        // assert
        $this->assertEquals($expectedRate, $rate);
    }

    public function providerConfigRates()
    {
        return array(
            array('l', 'ml', 'convert/L/to/ML',  1000),
            array('g', 'Kg', 'convert/G/to/KG', .001),
            array('KG', 't', 'convert/KG/to/T', 1000),
        );
    }

    /**
     * @expectedException Magento_Core_Exception
     */
    public function testGetConversionRateShouldThrowMageExceptionWithLocalizedMessageIfRateNotFound()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->once())
            ->method('getConfig')
            ->with('convert/L/to/KG');

        $helper->expects($this->at(1))
            ->method('__')
            ->with('L')
            ->will($this->returnValue('Local L'));

        $helper->expects($this->at(2))
            ->method('__')
            ->with('KG')
            ->will($this->returnValue('Local KG'));

        $helper->expects($this->at(3))
            ->method('__')
            ->with('Conversion rate not found for %s to %s', 'Local L', 'Local KG')
            ->will($this->returnValue('Local Conversion rate not found for l to kg'));

        $unitPrice = $this->unitPriceMock($helper, 'Local Conversion rate not found for l to kg');

        // act
        $unitPrice->getConversionRate('l', 'kg');
    }

    public function testGetUnitPriceShouldCalculatePriceByProductAmountUnitAndPrice()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->once())
            ->method('getConfig')
            ->with('convert/KG/to/G')
            ->will($this->returnValue(1000));

        $unitPrice = $this->unitPriceMock($helper, null, array('reference_unit' => 'G', 'reference_amount' => 1));

        // act
        $price = $unitPrice->getUnitPrice(5, 'KG', 1000);

        // assert
        $this->assertEquals(.2, $price);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Local Reference unit not set
     */
    public function testGetUnitPriceShouldThrowExceptionIfReferenceUnitIsNotSet()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->once())
            ->method('__')
            ->with('Reference unit not set')
            ->will($this->returnValue('Local Reference unit not set'));

        $unitPrice = $this->unitPriceMock($helper, 'Local Reference unit not set', array('reference_amount' => 1));

        // act
        $unitPrice->getUnitPrice(5, 'KG', 1000);
    }

    public function testGetUnitPriceShouldFetchDefaultAmountFromConfigIfReferenceAmountIsNotSet()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->at(0))
            ->method('getConfig')
            ->with('convert/KG/to/G')
            ->will($this->returnValue(1000));

        $helper->expects($this->at(1))
            ->method('getConfig')
            ->with('default_unit_price_base_amount')
            ->will($this->returnValue(8));

        $unitPrice = $this->unitPriceMock($helper, null, array('reference_unit' => 'G'));

        // act
        $price = $unitPrice->getUnitPrice(5, 'KG', 1000);

        // assert
        $this->assertEquals(1.6, $price);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Local The product unit amount must be greater than zero
     */
    public function testGetUnitPriceShouldThrowExceptionOnNonPositiveAmount()
    {
        // prepare
        $helper = $this->helperMock();

        $helper->expects($this->once())
            ->method('__')
            ->with('The product unit amount must be greater than zero')
            ->will($this->returnValue('Local The product unit amount must be greater than zero'));

        $unitPrice = $this->unitPriceMock(
            $helper,
            'Local The product unit amount must be greater than zero',
            array('reference_unit' => 'G', 'reference_amount' => 1)
        );

        // act
        $unitPrice->getUnitPrice(-5, 'KG', 1000);
    }
}
