<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Action;

class SalesruleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardFactoryMock;

    /**
     * @var \Magento\Reward\Model\Action\Salesrule
     */
    protected $model;

    protected function setUp()
    {
        $this->rewardFactoryMock = $this->getMock('\Magento\Reward\Model\Resource\RewardFactory', [], [], '', false);
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            '\Magento\Reward\Model\Action\Salesrule',
            ['rewardData' => $this->rewardFactoryMock]
        );
    }

    public function testCanAddRewardPoints()
    {
        $this->assertTrue($this->model->canAddRewardPoints());
    }

    /**
     * @param array $args
     * @param string $expectedResult
     *
     * @dataProvider getHistoryMessageDataProvider
     */
    public function testGetHistoryMessage(array $args, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->model->getHistoryMessage($args));
    }

    /**
     * @return array
     */
    public function getHistoryMessageDataProvider()
    {
        return array(
            array(
                'args' => array(),
                'expectedResult' => 'Earned promotion extra points from order #'
            ),
            array(
                'args' => array('increment_id' => 1),
                'expectedResult' => 'Earned promotion extra points from order #1'
            )
        );
    }
}
 