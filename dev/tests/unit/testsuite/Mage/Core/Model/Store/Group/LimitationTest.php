<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Store_Group_LimitationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $totalCount
     * @param string|int $configuredCount
     * @param bool $expected
     * @dataProvider canCreateDataProvider
     */
    public function testCanCreate($totalCount, $configuredCount, $expected)
    {
        $resource = $this->getMock('Mage_Core_Model_Resource_Store_Group', array('countAll'), array(), '', false);
        if ($configuredCount !== '') {
            $resource->expects($this->once())->method('countAll')->will($this->returnValue($totalCount));
        } else {
            $resource->expects($this->never())->method('countAll');
        }
        $config = $this->getMock('Mage_Core_Model_Config', array('getNode'), array(), '', false);
        $config->expects($this->any())->method('getNode')
            ->with('limitations/store_group')
            ->will($this->returnValue($configuredCount));
        $model = new Mage_Core_Model_Store_Group_Limitation($resource, $config);
        $this->assertEquals($expected, $model->canCreate());

        // verify that resource model is invoked only when needed (see expectation "once" above)
        new Mage_Core_Model_Store_Group_Limitation($resource, $config);
    }

    /**
     * @return array
     */
    public function canCreateDataProvider()
    {
        return array(
            'no limit'       => array(0, '', true),
            'negative limit' => array(2, -1, false),
            'zero limit'     => array(2, 0, false),
            'count > limit'  => array(2, 1, false),
            'count = limit'  => array(2, 2, false),
            'count < limit'  => array(2, 3, true),
        );
    }
}
