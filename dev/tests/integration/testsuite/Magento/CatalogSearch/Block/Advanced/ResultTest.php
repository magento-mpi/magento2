<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Block\Advanced;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\CatalogSearch\Block\Advanced\Result
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\View\LayoutInterface'
        );
        $this->_block = $this->_layout->createBlock('Magento\CatalogSearch\Block\Advanced\Result', 'block');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetListOrders()
    {
        $sortOptions = array(
            'option1' => 'Label Option 1',
            'position' => 'Label Position',
            'option3' => 'Label Option 2'
        );
        $category = $this->getMock(
            'Magento\Catalog\Model\Category',
            array('getAvailableSortByOptions'),
            array(),
            '',
            false
        );
        $category->expects($this->atLeastOnce())
            ->method('getAvailableSortByOptions')
            ->will($this->returnValue($sortOptions));
        $category->setId(100500); // Any id - just for layer navigation
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Layer\Search')
            ->setCurrentCategory($category);

        $childBlock = $this->_layout->addBlock('Magento\Framework\View\Element\Text', 'search_result_list', 'block');

        $expectedOptions = array('option1' => 'Label Option 1', 'option3' => 'Label Option 2');
        $this->assertNotEquals($expectedOptions, $childBlock->getAvailableOrders());
        $this->_block->setListOrders();
        $this->assertEquals($expectedOptions, $childBlock->getAvailableOrders());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetListModes()
    {
        /** @var $childBlock \Magento\Framework\View\Element\Text */
        $childBlock = $this->_layout->addBlock('Magento\Framework\View\Element\Text', 'search_result_list', 'block');
        $this->assertEmpty($childBlock->getModes());
        $this->_block->setListModes();
        $this->assertNotEmpty($childBlock->getModes());
    }

    public function testSetListCollection()
    {
        /** @var $childBlock \Magento\Framework\View\Element\Text */
        $childBlock = $this->_layout->addBlock('Magento\Framework\View\Element\Text', 'search_result_list', 'block');
        $this->assertEmpty($childBlock->getCollection());
        $this->_block->setListCollection();
        $this->assertInstanceOf(
            'Magento\CatalogSearch\Model\Resource\Advanced\Collection',
            $childBlock->getCollection()
        );
    }
}
