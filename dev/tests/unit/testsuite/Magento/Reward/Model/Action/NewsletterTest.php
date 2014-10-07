<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Action;

class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rewardDataMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collFactoryMock;

    /**
     * @var \Magento\Reward\Model\Action\Newsletter
     */
    protected $model;

    protected function setUp()
    {
        $this->rewardDataMock = $this->getMock('\Magento\Reward\Helper\Data', [], [], '', false);
        $this->collFactoryMock = $this->getMock(
            '\Magento\Newsletter\Model\Resource\Subscriber\CollectionFactory',
            [],
            [],
            '',
            false
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManagerHelper->getObject(
            '\Magento\Reward\Model\Action\Newsletter',
            ['rewardData' => $this->rewardDataMock, 'subscribersFactory' => $this->collFactoryMock]
        );
    }

    public function testGetPoints()
    {
        $websiteId = 100;
        $this->rewardDataMock->expects($this->once())
            ->method('getPointsConfig')
            ->with('newsletter', $websiteId)
            ->willReturn(500);
        $this->assertEquals(500, $this->model->getPoints($websiteId));
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
                'expectedResult' => 'Signed up for newsletter with email '
            ),
            array(
                'args' => array('email' => 'test@mail.com'),
                'expectedResult' => 'Signed up for newsletter with email test@mail.com'
            )
        );
    }
}
