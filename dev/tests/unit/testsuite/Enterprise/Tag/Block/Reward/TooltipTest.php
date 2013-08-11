<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Tag_Block_Reward_TooltipTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Enterprise_Tag_Block_Reward_Tooltip::__construct
     */
    public function testConstruct()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $helperMock = $this->getMock('Enterprise_Tag_Helper_Data', array('addActionClassToRewardModel'), array(), '',
            false
        );
        $helperMock->expects($this->once())
            ->method('addActionClassToRewardModel');

        $data = array(
            'data' => array('helpers' => array('Enterprise_Tag_Helper_Data' => $helperMock))
        );
        $objectManagerHelper->getObject('Enterprise_Tag_Block_Reward_Tooltip', $data);
    }
}
