<?php
/**
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer\Processor;

/**
 * Class InvalidatePluginTest
 */
class InvalidatePluginTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\Indexer\Processor\InvalidatePlugin
     */
    protected $plugin;

    /**
     * @var \Magento\PageCache\Model\Indexer\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Indexer\Model\ActionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Magento\Event\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManagerMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->subjectMock = $this->getMock('Magento\Indexer\Model\Processor',
            array(), array(), '', false);
        $this->contextMock = $this->getMock('Magento\PageCache\Model\Indexer\Context',
            array(), array(), '', false);
        $this->eventManagerMock = $this->getMock('Magento\Event\Manager',
            array(), array(), '', false);
        $this->plugin = new \Magento\PageCache\Model\Indexer\Processor\InvalidatePlugin(
            $this->contextMock, $this->eventManagerMock);
    }

    /**
     * Test afterUpdateMview
     */
    public function testAfterUpdateMview()
    {
        $expectedResult = array('result' => 'this-is-fake');
        $this->eventManagerMock->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo('clean_cache_after_reindex'),
                $this->equalTo(array('object' => $this->contextMock)));
        $actualResult = $this->plugin->afterUpdateMview($this->subjectMock, $expectedResult);
        $this->assertEquals($expectedResult, $actualResult);
    }
}
