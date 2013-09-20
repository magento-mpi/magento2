<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Block\Advanced;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    /**
     * @var \Magento\CatalogSearch\Block\Advanced\Result
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = \Mage::getSingleton('Magento\Core\Model\Layout');
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
            'Magento\Catalog\Model\Category', array('getAvailableSortByOptions'), array(), '', false
        );
        $category->expects($this->atLeastOnce())
            ->method('getAvailableSortByOptions')
            ->will($this->returnValue($sortOptions));
        $category->setId(100500); // Any id - just for layer navigation
        \Mage::getSingleton('Magento\Catalog\Model\Layer')->setCurrentCategory($category);

        $childBlock = $this->_layout->addBlock('Magento\Core\Block\Text', 'search_result_list', 'block');

        $expectedOptions = array(
            'option1' => 'Label Option 1',
            'option3' => 'Label Option 2'
        );
        $this->assertNotEquals($expectedOptions, $childBlock->getAvailableOrders());
        $this->_block->setListOrders();
        $this->assertEquals($expectedOptions, $childBlock->getAvailableOrders());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testSetListModes()
    {
        /** @var $childBlock \Magento\Core\Block\Text */
        $childBlock = $this->_layout->addBlock('Magento\Core\Block\Text', 'search_result_list', 'block');
        $this->assertEmpty($childBlock->getModes());
        $this->_block->setListModes();
        $this->assertNotEmpty($childBlock->getModes());
    }

    public function testSetListCollection()
    {
        /** @var $childBlock \Magento\Core\Block\Text */
        $childBlock = $this->_layout->addBlock('Magento\Core\Block\Text', 'search_result_list', 'block');
        $this->assertEmpty($childBlock->getCollection());
        $this->_block->setListCollection();
        $this->assertInstanceOf(
            'Magento\CatalogSearch\Model\Resource\Advanced\Collection',
            $childBlock->getCollection()
        );
    }
}
