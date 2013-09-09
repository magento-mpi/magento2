<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Helper_CategoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Helper_Category
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Magento_Catalog_Helper_Category');
    }

    protected function tearDown()
    {
        if ($this->_helper) {
            $helperClass = get_class($this->_helper);
            /** @var $objectManager Magento_Test_ObjectManager */
            $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
            $objectManager->get('Magento_Core_Model_Registry')->unregister('_helper/' . $helperClass);
        }
        $this->_helper = null;
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testGetStoreCategories()
    {
        $categories = $this->_helper->getStoreCategories();
        $this->assertInstanceOf('Magento_Data_Tree_Node_Collection', $categories);
        $index = 0;
        $expectedPaths = array(array(3, '1/2/3'), array(6, '1/2/6'), array(7, '1/2/7'));
        foreach ($categories as $category) {
            $this->assertInstanceOf('Magento_Data_Tree_Node', $category);
            $this->assertEquals($expectedPaths[$index][0], $category->getId());
            $this->assertEquals($expectedPaths[$index][1], $category->getData('path'));
            $index++;
        }
    }

    public function testGetCategoryUrl()
    {
         $url = 'http://example.com/';
        $category = Mage::getModel('Magento_Catalog_Model_Category', array('data' => array('url' => $url)));
        $this->assertEquals($url, $this->_helper->getCategoryUrl($category));

        $category = new Magento_Object(array('url' => $url));
        $this->assertEquals($url, $this->_helper->getCategoryUrl($category));
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/categories.php
     */
    public function testCanShow()
    {
        // by ID of a category that is not a root
        $this->assertTrue($this->_helper->canShow(7));
    }

    public function testCanShowFalse()
    {
        /** @var $category Magento_Catalog_Model_Category */
        $category = Mage::getModel('Magento_Catalog_Model_Category');
        $this->assertFalse($this->_helper->canShow($category));
        $category->setId(1);
        $this->assertFalse($this->_helper->canShow($category));
        $category->setIsActive(true);
        $this->assertFalse($this->_helper->canShow($category));
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetCategoryUrlSuffixDefault()
    {
        $this->assertEquals('.html', $this->_helper->getCategoryUrlSuffix());
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/category_url_suffix .htm
     * @magentoAppIsolation enabled
     */
    public function testGetCategoryUrlSuffix()
    {
        $this->assertEquals('.htm', $this->_helper->getCategoryUrlSuffix());
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testGetCategoryUrlPathDefault()
    {
        $this->assertEquals('http://example.com/category',
            $this->_helper->getCategoryUrlPath('http://example.com/category.html')
        );

        $this->assertEquals('http://example.com/category/',
            $this->_helper->getCategoryUrlPath('http://example.com/category.html/', true)
        );
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/category_url_suffix .htm
     * @magentoAppIsolation enabled
     */
    public function testGetCategoryUrlPath()
    {
        $this->assertEquals('http://example.com/category.html',
            $this->_helper->getCategoryUrlPath('http://example.com/category.html')
        );
    }

    public function testCanUseCanonicalTagDefault()
    {
        $this->assertEquals(0, $this->_helper->canUseCanonicalTag());
    }

    /**
     * @magentoConfigFixture current_store catalog/seo/category_canonical_tag 1
     */
    public function testCanUseCanonicalTag()
    {
        $this->assertEquals(1, $this->_helper->canUseCanonicalTag());
    }
}
