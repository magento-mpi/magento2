<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Store_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $totalCount
     * @param string $configuredCount
     * @param bool $expected
     * @dataProvider isCreateRestrictedDataProvider
     */
    public function testIsCreateRestricted($totalCount, $configuredCount, $expected)
    {
        $resource = $this->getMock('Mage_Core_Model_Resource_Store', array('countAll'), array(), '', false);
        if ($totalCount) {
            $resource->expects($this->once())->method('countAll')->will($this->returnValue($totalCount));
        }
        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->once())->method('getNode')->will($this->returnValue($configuredCount));
        $model = new Mage_Core_Model_Store_Limitation($resource, $config);
        $this->assertEquals($expected, $model->isCreateRestricted());
    }

    /**
     * @return array
     */
    public function isCreateRestrictedDataProvider()
    {
        return array(
            array(0, '', false),
            array(2, 0, true),
            array(2, 1, true),
            array(2, 2, false),
            array(2, 3, false),
        );
    }

    public function getCreateRestrictionMessage()
    {
        $helper = new Mage_Core_Helper_Data;
        $this->assertNotEmpty(Mage_Core_Model_Store_Limitation::getCreateRestrictionMessage($helper));
    }
}
