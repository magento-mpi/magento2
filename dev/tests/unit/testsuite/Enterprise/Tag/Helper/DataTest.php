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

class Enterprise_Tag_Helper_DataTest extends Magento_Test_TestCase_ObjectManagerAbstract
{
    /**
     * @covers Enterprise_Tag_Helper_Data::addActionClassToRewardModel
     */
    public function testAddActionClassToRewardModel()
    {
        $arguments = $this->_getConstructArguments(self::MODEL_ENTITY);
        $rewardModelMock = $this->getMock('Enterprise_Reward_Model_Reward', array('_init', 'setActionModelClass'),
            $arguments);
        $rewardModelMock->staticExpects($this->once())
            ->method('setActionModelClass')
            ->will($this->returnCallback(array($this, 'validateSetActionModelClass')));

        $data = array(
            'reward_model' => $rewardModelMock
        );
        $helper = new Enterprise_Tag_Helper_Data($data);
        $helper->addActionClassToRewardModel();
    }

    /**
     * @param int $actionKey
     * @param string $actionClass
     */
    public function validateSetActionModelClass($actionKey, $actionClass)
    {
        $this->assertEquals(Enterprise_Tag_Model_Reward::REWARD_ACTION_TAG, $actionKey);
        $this->assertEquals(Enterprise_Tag_Model_Reward::REWARD_ACTION_TAG_MODEL, $actionClass);
    }
}
