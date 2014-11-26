<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\DataSource;

/**
 * Class PageCollectionTest
 */
class PageCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Resource\PageCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaMock;

    /**
     * @var \Magento\Cms\Model\PageRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repositoryMock;

    /**
     * @var \Magento\Cms\Model\DataSource\PageCollection
     */
    protected $pageCollection;

    /**
     * Set up
     *
     * @return void
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->criteriaMock = $this->getMock(
            'Magento\Cms\Model\Resource\PageCriteria',
            [],
            [],
            '',
            false
        );
        $this->repositoryMock = $this->getMock(
            'Magento\Cms\Model\PageRepository',
            [],
            [],
            '',
            false
        );

        $this->criteriaMock->expects($this->once())
            ->method('setFirstStoreFlag')
            ->with(true);

        $this->pageCollection = $objectManager->getObject(
            'Magento\Cms\Model\DataSource\PageCollection',
            [
                'criteria' => $this->criteriaMock,
                'repository' => $this->repositoryMock
            ]
        );
    }

    /**
     * Run test addFilter method
     *
     * @param string $name
     * @param string $field
     * @param mixed $condition
     * @param string $type
     * @return void
     *
     * @dataProvider dataProviderAddFilter
     */
    public function testAddFilter($name, $field, $condition, $type)
    {
        if ($field === 'store_id') {
            $this->criteriaMock->expects($this->once())
                ->method('addStoreFilter')
                ->with($condition, false);
        } else {
            $this->criteriaMock->expects($this->once())
                ->method('addFilter')
                ->with($name, $field, $condition, $type);
        }

        $this->pageCollection->addFilter($name, $field, $condition, $type);
    }

    /**
     * Run test getResultCollection method
     *
     * @return void
     */
    public function testGetResultCollection()
    {
        $this->repositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->criteriaMock)
            ->will($this->returnValue('return-value'));

        $this->assertEquals('return-value', $this->pageCollection->getResultCollection());
    }

    /**
     * Data provider for addFilter method
     *
     * @return array
     */
    public function dataProviderAddFilter()
    {
        return [
            [
                'name' => 'test-name',
                'field' => 'store_id',
                'condition' => null,
                'type' => 'public'
            ],
            [
                'name' => 'test-name',
                'field' => 'any_field',
                'condition' => 10,
                'type' => 'private'
            ]
        ];
    }
}
