<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Limitation_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $fixtureTotalCount
     * @param int $fixtureThreshold
     * @param int $inputQuantity
     * @param bool $expectedResult
     * @dataProvider isThresholdReachedDataProvider
     */
    public function testIsThresholdReached($fixtureTotalCount, $fixtureThreshold, $inputQuantity, $expectedResult)
    {
        $limitation = $this->getMock('Saas_Limitation_Model_Limitation_LimitationInterface');
        $limitation->expects($this->any())->method('getThreshold')->will($this->returnValue($fixtureThreshold));
        $limitation->expects($this->any())->method('getTotalCount')->will($this->returnValue($fixtureTotalCount));

        $model = new Saas_Limitation_Model_Limitation_Validator();
        $this->assertEquals($expectedResult, $model->isThresholdReached($limitation, $inputQuantity));
    }

    /**
     * @return array
     */
    public function isThresholdReachedDataProvider()
    {
        return array(
            'negative threshold'        => array(2, -1, 1, false),
            'zero threshold'            => array(2, 0, 1, false),
            'count + one > threshold'   => array(2, 1, 1, true),
            'count + one = threshold'   => array(2, 2, 1, true),
            'count + one < threshold'   => array(2, 3, 1, false),
            'count + qty > threshold'   => array(2, 3, 2, true),
            'count + qty = threshold'   => array(2, 4, 2, false),
            'count + qty < threshold'   => array(2, 5, 2, false),
        );
    }
}
