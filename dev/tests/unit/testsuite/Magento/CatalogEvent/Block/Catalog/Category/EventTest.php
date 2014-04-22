<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogEvent\Block\Catalog\Category;

class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogEvent\Block\Catalog\Category\Event
     */
    protected $block;

    /**
     * @var \Magento\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->registryMock = $this->getMock('Magento\Registry', array(), array(), '', false);

        $this->block = $objectManager->getObject(
            'Magento\CatalogEvent\Block\Catalog\Category\Event',
            array('registry' => $this->registryMock)
        );
    }

    protected function tearDown()
    {
        $this->block = null;
    }

    public function testGetIdentities()
    {
        $categoryTags = array('catalog_category_1');
        $category = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $category->expects($this->once())->method('getIdentities')->will($this->returnValue($categoryTags));
        $this->registryMock->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_category'
        )->will(
            $this->returnValue($category)
        );
        $this->assertEquals($categoryTags, $this->block->getIdentities());
    }
}
