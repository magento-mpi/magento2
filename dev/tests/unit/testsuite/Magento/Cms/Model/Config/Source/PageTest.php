<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Config\Source;

/**
 * Class PageTest
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageRepositoryMock;

    /**
     * @var \Magento\Cms\Api\PageCriteriaInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageCriteriaFactoryMock;

    /**
     * @var \Magento\Cms\Model\Config\Source\Page
     */
    protected $page;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->pageRepositoryMock = $this->getMockForAbstractClass(
            'Magento\Cms\Api\PageRepositoryInterface',
            [],
            '',
            false,
            true,
            true,
            ['getList']
        );
        $this->pageCriteriaFactoryMock = $this->getMock(
            'Magento\Cms\Api\PageCriteriaInterfaceFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->page = $objectManager->getObject(
            'Magento\Cms\Model\Config\Source\Page',
            [
                'pageRepository' => $this->pageRepositoryMock,
                'pageCriteriaFactory' => $this->pageCriteriaFactoryMock
            ]
        );
    }

    /**
     * Run test toOptionArray method
     *
     * @return void
     */
    public function testToOptionArray()
    {
        $pageCollectionMock = $this->getMockForAbstractClass(
            'Magento\Cms\Api\Data\PageCollectionInterface',
            [],
            '',
            false,
            true,
            true,
            ['toOptionIdArray']
        );
        $pageCriteriaMock = $this->getMockForAbstractClass(
            'Magento\Cms\Api\PageCriteriaInterface',
            [],
            '',
            false
        );

        $this->pageRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($pageCriteriaMock)
            ->will($this->returnValue($pageCollectionMock));

        $this->pageCriteriaFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($pageCriteriaMock));

        $pageCollectionMock->expects($this->once())
            ->method('toOptionIdArray')
            ->will($this->returnValue('return-value'));

        $this->assertEquals('return-value', $this->page->toOptionArray());
    }
}
