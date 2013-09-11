<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Category.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class Magento_Catalog_Model_Layer_Filter_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Category
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    protected function setUp()
    {
        $this->_category = Mage::getModel('Magento\Catalog\Model\Category');
        $this->_category->load(5);
        $this->_model = Mage::getModel('Magento\Catalog\Model\Layer\Filter\Category');
        $this->_model->setData(array(
            'layer' => Mage::getModel('Magento\Catalog\Model\Layer', array(
                'data' => array('current_category' => $this->_category)
            )),
        ));
    }

    public function testGetResetValue()
    {
        $this->assertNull($this->_model->getResetValue());
    }

    public function testApplyNothing()
    {
        $this->_model->apply(
            new Magento_TestFramework_Request(),
            Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text')
        );

        $this->assertNull(Mage::registry('current_category_filter'));
    }

    public function testApply()
    {
        $request = new Magento_TestFramework_Request();
        $request->setParam('cat', 3);
        $this->_model->apply($request, Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text'));

        /** @var $category \Magento\Catalog\Model\Category */
        $category = Mage::registry('current_category_filter');
        $this->assertInstanceOf('\Magento\Catalog\Model\Category', $category);
        $this->assertEquals(3, $category->getId());

        return $this->_model;
    }

    /**
     * @depends testApply
     */
    public function testGetResetValueApplied(\Magento\Catalog\Model\Layer\Filter\Category $modelApplied)
    {
        $this->assertEquals(2, $modelApplied->getResetValue());
    }

    public function testGetName()
    {
        $this->assertEquals('Category', $this->_model->getName());
    }

    public function testGetCategory()
    {
        $this->assertSame($this->_category, $this->_model->getCategory());
    }

    /**
     * @depends testApply
     */
    public function testGetCategoryApplied(\Magento\Catalog\Model\Layer\Filter\Category $modelApplied)
    {
        $category = $modelApplied->getCategory();
        $this->assertInstanceOf('\Magento\Catalog\Model\Category', $category);
        $this->assertEquals(3, $category->getId());
    }

    /**
     * @depends testApply
     */
    public function testGetItems(\Magento\Catalog\Model\Layer\Filter\Category $modelApplied)
    {
        $items = $modelApplied->getItems();

        $this->assertInternalType('array', $items);
        $this->assertEquals(1, count($items));

        /** @var $item \Magento\Catalog\Model\Layer\Filter\Item */
        $item = $items[0];

        $this->assertInstanceOf('\Magento\Catalog\Model\Layer\Filter\Item', $item);
        $this->assertSame($modelApplied, $item->getFilter());
        $this->assertEquals('Category 1.1', $item->getLabel());
        $this->assertEquals(4, $item->getValue());
        $this->assertEquals(1, $item->getCount());
    }
}
