<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_User_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param int $limitation
     * @param int $existingCount
     * @param bool $expected
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($limitation, $existingCount, $expected)
    {
        $resource = $this->getMock('Mage_User_Model_Resource_User', array(), array(), '', false);
        $resource->expects($this->any())
            ->method('countAll')
            ->will($this->returnValue($existingCount));
        $config = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $config->expects($this->once())
            ->method('getNode')
            ->with('limitations/admin_account')
            ->will($this->returnValue($limitation));
        $model = new Saas_Limitation_Model_User_Limitation($resource, $config);
        $actual = $model->isCreateRestricted();
        $this->assertSame($expected, $actual);
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            'no limit'      => array('', 1, false),
            'zero limit'    => array(0, 1, false),
            'limit > count' => array(2, 1, false),
            'limit = count' => array(1, 1, true),
            'limit < count' => array(1, 2, true),
        );
    }
}
