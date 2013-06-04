<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Store_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $fixtureTotalCount
     * @param string|int $fixtureLimitation
     * @param bool $expectedResult
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($fixtureTotalCount, $fixtureLimitation, $expectedResult)
    {
        $resource = $this->getMock('Mage_Core_Model_Resource_Store', array('countAll'), array(), '', false);
        $resource->expects($this->any())->method('countAll')->will($this->returnValue($fixtureTotalCount));

        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->any())->method('getNode')
            ->with(Saas_Limitation_Model_Store_Limitation::XML_PATH_NUM_STORES)
            ->will($this->returnValue($fixtureLimitation));

        $model = new Saas_Limitation_Model_Store_Limitation($resource, $config);
        $this->assertEquals($expectedResult, $model->isCreateRestricted());
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            'no limit'       => array(0, '', false),
            'negative limit' => array(2, -1, false),
            'zero limit'     => array(2, 0, false),
            'count > limit'  => array(2, 1, true),
            'count = limit'  => array(2, 2, true),
            'count < limit'  => array(2, 3, false),
        );
    }
}
