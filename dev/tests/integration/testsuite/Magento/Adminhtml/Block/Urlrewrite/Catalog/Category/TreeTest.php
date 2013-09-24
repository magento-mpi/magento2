<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Urlrewrite\Catalog\Category;

/**
 * Test for \Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Tree
 *
 * @magentoAppArea adminhtml
 */
class TreeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Tree
     */
    private $_treeBlock;

    /**
     * Set up
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_treeBlock = \Mage::app()->getLayout()
            ->createBlock('Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Tree');
    }

    /**
     * Test for method \Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Tree::getTreeArray()
     */
    public function testGetTreeArray()
    {
        $expectedTreeArray = array(
            'id'             => 1,
            'parent_id'      => 0,
            'children_count' => 1,
            'is_active'      => false,
            'name'           => 'Root',
            'level'          => 0,
            'product_count'  => 0,
            'children'       => array(array(
                'id'             => 2,
                'parent_id'      => 1,
                'children_count' => 0,
                'is_active'      => true,
                'name'           => 'Default Category',
                'level'          => 1,
                'product_count'  => 0,
                'cls'            => 'active-category',
                'expanded'       => false
            )),
            'cls'            => 'no-active-category',
            'expanded'       => true,
        );

        $this->assertEquals($expectedTreeArray, $this->_treeBlock->getTreeArray(),
            'Tree array is invalid');
    }

    /**
     * Test prepare grid
     */
    public function testGetLoadTreeUrl()
    {
        $row = new \Magento\Object(array('id' => 1));
        $this->assertStringStartsWith('http://localhost/index.php', $this->_treeBlock->getLoadTreeUrl($row),
            'Tree load URL is invalid');
    }

    /**
     * Test for method \Magento\Adminhtml\Block\Urlrewrite\Catalog\Category\Tree::getCategoryCollection()
     */
    public function testGetCategoryCollection()
    {
        $collection = $this->_treeBlock->getCategoryCollection();
        $this->assertInstanceOf('Magento\Catalog\Model\Resource\Category\Collection', $collection);
    }
}
