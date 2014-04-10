<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoConfigFixture fixturestore_store general/locale/code de_DE
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriber()
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $objectManager->get('Magento\Framework\App\State')->setAreaCode(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $area = $objectManager->get('Magento\Framework\App\AreaList')
            ->getArea(\Magento\Core\Model\App\Area::AREA_FRONTEND);
        $area->load();

        /** @var $filter \Magento\Newsletter\Model\Template\Filter */
        $filter = $objectManager->get('Magento\Newsletter\Model\Template\Filter');

        $transport = $this->getMock('\Magento\Mail\TransportInterface');
        $transport->expects($this->exactly(2))->method('sendMessage')->will($this->returnSelf());

        $builder = $this->getMock(
            '\Magento\Newsletter\Model\Queue\TransportBuilder',
            array('getTransport', 'setFrom', 'addTo'),
            array(),
            '',
            false
        );
        $builder->expects($this->exactly(2))->method('getTransport')->will($this->returnValue($transport));
        $builder->expects($this->exactly(2))->method('setFrom')->will($this->returnSelf());
        $builder->expects($this->exactly(2))->method('addTo')->will($this->returnSelf());

        /** @var $queue \Magento\Newsletter\Model\Queue */
        $queue = $objectManager->create(
            'Magento\Newsletter\Model\Queue',
            array('filter' => $filter, 'transportBuilder' => $builder)
        );
        $queue->load('Subject', 'newsletter_subject');
        // fixture
        $queue->sendPerSubscriber();
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/queue.php
     * @magentoAppIsolation enabled
     */
    public function testSendPerSubscriberProblem()
    {
        $errorMsg = md5(microtime());

        \Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Core\Model\App\Area::AREA_FRONTEND);

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $transport = $this->getMock('\Magento\Mail\TransportInterface');
        $transport->expects(
            $this->any()
        )->method(
            'sendMessage'
        )->will(
            $this->throwException(new \Magento\Mail\Exception($errorMsg, 99))
        );

        $builder = $this->getMock(
            '\Magento\Newsletter\Model\Queue\TransportBuilder',
            array('getTransport', 'setFrom', 'addTo'),
            array(),
            '',
            false
        );
        $builder->expects($this->any())->method('getTransport')->will($this->returnValue($transport));
        $builder->expects($this->any())->method('setFrom')->will($this->returnSelf());
        $builder->expects($this->any())->method('addTo')->will($this->returnSelf());

        /** @var $queue \Magento\Newsletter\Model\Queue */
        $queue = $objectManager->create('Magento\Newsletter\Model\Queue', array('transportBuilder' => $builder));
        $queue->load('Subject', 'newsletter_subject');
        // fixture

        $problem = $objectManager->create('Magento\Newsletter\Model\Problem');
        $problem->load($queue->getId(), 'queue_id');
        $this->assertEmpty($problem->getId());


        $queue->sendPerSubscriber();

        $problem->load($queue->getId(), 'queue_id');
        $this->assertNotEmpty($problem->getId());
        $this->assertEquals(99, $problem->getProblemErrorCode());
        $this->assertEquals($errorMsg, $problem->getProblemErrorText());
    }
}
