<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Math;

class DivisionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getExactDivisionDataProvider
     */
    public function testGetExactDivision($dividend, $divisor, $expected)
    {
        $mathDivision = new \Magento\Math\Division();
        $remainder = $mathDivision->getExactDivision($dividend, $divisor);
        $this->assertEquals($expected, $remainder);
    }

    /**
     * @return array
     */
    public function getExactDivisionDataProvider()
    {
        return array(
            array(17, 3 , 2),
            array(7.7, 2 , 1.7),
            array(17.8, 3.2 , 1.8),
            array(11.7, 1.7 , 1.5),
            array(8, 2, 0)
        );
    }
}
