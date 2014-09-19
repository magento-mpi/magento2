<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class PrepareSalesruleFormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \Magento\Reward\Model\Observer\PrepareSalesruleForm
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', ['isEnabled'], [], '', false);

        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\PrepareSalesruleForm',
            ['rewardData' => $this->rewardDataMock]
        );
    }

    public function testPrepareFormIfRewardsDisabled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(false));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testPrepareForm()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $this->rewardDataMock->expects($this->once())->method('isEnabled')->will($this->returnValue(true));

        $formMock = $this->getMock('\Magento\Framework\Data\Form', [], [], '', false);

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getForm'], [], '', false);
        $eventMock->expects($this->once())->method('getForm')->will($this->returnValue($formMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $fieldSet = $this->getMock('\Magento\Framework\Data\Form\Element\Fieldset', [], [], '', false);
        $formMock->expects($this->once())
            ->method('getElement')
            ->with('action_fieldset')
            ->will($this->returnValue($fieldSet));

        $fieldSet->expects($this->once())
            ->method('addField')
            ->with(
                'reward_points_delta',
                'text',
                array(
                    'name' => 'reward_points_delta',
                    'label' => __('Add Reward Points'),
                    'title' => __('Add Reward Points')
                ),
                'stop_rules_processing'
            )
            ->will($this->returnSelf());

        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 