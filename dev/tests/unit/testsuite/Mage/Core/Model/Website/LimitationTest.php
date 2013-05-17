<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Website_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $fixtureTotalCount
     * @param string|int $fixtureLimitation
     * @param bool $expectedResult
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($fixtureTotalCount, $fixtureLimitation, $expectedResult)
    {
        $resource = $this->getMock('Mage_Core_Model_Resource_Website', array('countAll'), array(), '', false);
        $resource->expects($this->any())->method('countAll')->will($this->returnValue($fixtureTotalCount));

        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->once())->method('getNode')
            ->with(Mage_Core_Model_Website_Limitation::XML_PATH_NUM_WEBSITES)
            ->will($this->returnValue($fixtureLimitation));

        $model = new Mage_Core_Model_Website_Limitation($resource, $config);
        $this->assertEquals($expectedResult, $model->isCreateRestricted());
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            'no limit'          => array(0, '', false),
            'negative limit'    => array(2, -1, false),
            'zero limit'        => array(2, 0, false),
            'count > limit'     => array(2, 1, true),
            'count = limit'     => array(2, 2, true),
            'count < limit'     => array(2, 3, false),
        );
    }
}
