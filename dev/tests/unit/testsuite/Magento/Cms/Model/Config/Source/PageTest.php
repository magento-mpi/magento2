<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Config\Source;

/**
 * @covers \Magento\Cms\Model\Config\Source\Page
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Config\Source\Page
     */
    protected $this;

    /**
     * @var \Magento\Cms\Model\Resource\Page\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageCollectionFactory;

    /**
     * @var \Magento\Cms\Model\Resource\Page\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageCollection;

    protected function setUp()
    {
        $this->pageCollectionFactory = $this
            ->getMockBuilder('Magento\Cms\Model\Resource\Page\CollectionFactory')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'create'
                ]
            )
            ->getMock();
        $this->pageCollection = $this
            ->getMockBuilder('Magento\Cms\Model\Resource\Page\Collection')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->this = $objectManager->getObject(
            'Magento\Cms\Model\Config\Source\Page',
            [
                'pageCollectionFactory' => $this->pageCollectionFactory
            ]
        );

        $reflection = new \ReflectionClass($this->this);
        $mathRandomProperty = $reflection->getProperty('_options');
        $mathRandomProperty->setAccessible(true);
        $mathRandomProperty->setValue($this->this, null);
    }

    /**
     * @covers \Magento\Cms\Model\Config\Source\Page::toOptionArray
     */
    public function testToOptionArray()
    {
        $resultOptions = array('val1' => 'val2');

        $this->pageCollectionFactory
            ->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->pageCollection);
        $this->pageCollection
            ->expects($this->atLeastOnce())
            ->method('load')
            ->willReturnSelf();
        $this->pageCollection
            ->expects($this->atLeastOnce())
            ->method('toOptionIdArray')
            ->willReturn($resultOptions);

        $this->assertEquals($resultOptions, $this->this->toOptionArray());
    }
}
