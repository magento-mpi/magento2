<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\PageCache\Model\Indexer\Category;

/**
 * Class RefreshPluginTest
 */
class RefreshPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\Indexer\Category\RefreshPlugin
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
     *  Set up
     */
    public function setUp()
    {
        $this->subjectMock = $this->getMockForAbstractClass('Magento\Indexer\Model\ActionInterface',
            array(), '', false, true, true, array());
        $this->contextMock = $this->getMock('Magento\PageCache\Model\Indexer\Context',
            array(), array(), '', false);
        $this->plugin = new \Magento\PageCache\Model\Indexer\Category\RefreshPlugin($this->contextMock);
    }

    /**
     * test beforeExecute
     */
    public function testBeforeExecute()
    {
        $expectedIds = array(5, 6, 7);
        $this->contextMock->expects($this->once())
            ->method('registerEntities')
            ->with($this->equalTo(\Magento\Catalog\Model\Category::ENTITY),
                $this->equalTo($expectedIds))
            ->will($this->returnValue($this->contextMock));
        $actualIds = $this->plugin->beforeExecute($this->subjectMock, $expectedIds);
        $this->assertEquals(array($expectedIds), $actualIds);
    }
}
