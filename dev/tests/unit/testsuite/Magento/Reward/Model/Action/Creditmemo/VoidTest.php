<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Action\Creditmemo;

class VoidTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Action\Creditmemo\Void
     */
    protected $model;

    protected function setUp()
    {
        $this->model = new \Magento\Reward\Model\Action\Creditmemo\Void();
    }

    /**
     * @param array $args
     * @param string $expectedResult
     *
     * @dataProvider getHistoryMessageDataProvider
     * @covers \Magento\Reward\Model\Action\Creditmemo\Void::getHistoryMessage
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
                'expectedResult' => 'Points voided at order # refund.'
            ),
            array(
                'args' => array('increment_id' => 1),
                'expectedResult' => 'Points voided at order #1 refund.'
            )
        );
    }
}
