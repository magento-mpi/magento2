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

class Magento_CatalogSearch_Block_Advanced_ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_CatalogSearch_Block_Advanced_Result
     */
    protected $_block;

    protected function setUp()
    {
        $this->_layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Magento_CatalogSearch_Block_Advanced_Result', 'block');
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
            'Magento_Catalog_Model_Category', array('getAvailableSortByOptions'), array(), '', false
        );
        $category->expects($this->atLeastOnce())
            ->method('getAvailableSortByOptions')
            ->will($this->returnValue($sortOptions));
        $category->setId(100500); // Any id - just for layer navigation
        Mage::getSingleton('Magento_Catalog_Model_Layer')->setCurrentCategory($category);

        $childBlock = $this->_layout->addBlock('Magento_Core_Block_Text', 'search_result_list', 'block');

        $expectedOptions = array(
            'option1' => 'Label Option 1',
            'option3' => 'Label Option 2'
        );
        $this->assertNotEquals($expectedOptions, $childBlock->getAvailableOrders());
        $this->_block->setListOrders();
        $this->assertEquals($expectedOptions, $childBlock->getAvailableOrders());
    }

    public function testSetListModes()
    {
        /** @var $childBlock Magento_Core_Block_Text */
        $childBlock = $this->_layout->addBlock('Magento_Core_Block_Text', 'search_result_list', 'block');
        $this->assertEmpty($childBlock->getModes());
        $this->_block->setListModes();
        $this->assertNotEmpty($childBlock->getModes());
    }

    public function testSetListCollection()
    {
        /** @var $childBlock Magento_Core_Block_Text */
        $childBlock = $this->_layout->addBlock('Magento_Core_Block_Text', 'search_result_list', 'block');
        $this->assertEmpty($childBlock->getCollection());
        $this->_block->setListCollection();
        $this->assertInstanceOf(
            'Magento_CatalogSearch_Model_Resource_Advanced_Collection',
            $childBlock->getCollection()
        );
    }
}
