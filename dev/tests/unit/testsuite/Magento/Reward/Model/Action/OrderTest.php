<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Action;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Action\Order
     */
    protected $model;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject('\Magento\Reward\Model\Action\Order');
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
                'expectedResult' => 'Redeemed for order #'
            ),
            array(
                'args' => array('increment_id' => 1),
                'expectedResult' => 'Redeemed for order #1'
            )
        );
    }
}
 