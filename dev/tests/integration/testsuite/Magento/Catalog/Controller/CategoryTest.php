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
 * Test class for \Magento\Catalog\Controller\Category.
 *
 * @magentoDataFixture Magento/Catalog/_files/categories.php
 */
class Magento_Catalog_Controller_CategoryTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function assert404NotFound()
    {
        parent::assert404NotFound();
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->assertNull($objectManager->get('Magento_Core_Model_Registry')->registry('current_category'));
    }

    public function getViewActionDataProvider()
    {
        return array(
            'category without children' => array(
                '$categoryId' => 5,
                array(
                    'catalog_category_view_type_default',
                    'catalog_category_view_type_default_without_children',
                ),
                array(
                    '%acategorypath-category-1-category-1-1-category-1-1-1-html%a',
                    '%acategory-category-1-1-1%a',
                    '%a<title>Category 1.1.1 - Category 1.1 - Category 1</title>%a',
                    '%a<h1%S>%SCategory 1.1.1%S</h1>%a',
                    '%aSimple Product Two%a',
                    '%a$45.67%a',
                ),
            ),
            'anchor category' => array(
                '$categoryId' => 4,
                array(
                    'catalog_category_view_type_layered',
                ),
                array(
                    '%acategorypath-category-1-category-1-1-html%a',
                    '%acategory-category-1-1%a',
                    '%a<title>Category 1.1 - Category 1</title>%a',
                    '%a<h1%S>%SCategory 1.1%S</h1>%a',
                    '%aSimple Product%a',
                    '%a$10.00%a',
                    '%aSimple Product Two%a',
                    '%a$45.67%a',
                ),
            ),
        );
    }

    /**
     * @dataProvider getViewActionDataProvider
     */
    public function testViewAction($categoryId, array $expectedHandles, array $expectedContent)
    {
        $this->dispatch("catalog/category/view/id/$categoryId");

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var $currentCategory Magento_Catalog_Model_Category */
        $currentCategory = $objectManager->get('Magento\Core\Model\Registry')->registry('current_category');
        $this->assertInstanceOf('Magento\Catalog\Model\Category', $currentCategory);
        $this->assertEquals($categoryId, $currentCategory->getId(), 'Category in registry.');

        $lastCategoryId = Mage::getSingleton('Magento\Catalog\Model\Session')->getLastVisitedCategoryId();
        $this->assertEquals($categoryId, $lastCategoryId, 'Last visited category.');

        /* Layout updates */
        $handles = Mage::app()->getLayout()->getUpdate()->getHandles();
        foreach ($expectedHandles as $expectedHandleName) {
            $this->assertContains($expectedHandleName, $handles);
        }

        $responseBody = $this->getResponse()->getBody();

        /* Response content */
        foreach ($expectedContent as $expectedText) {
            $this->assertStringMatchesFormat($expectedText, $responseBody);
        }
    }

    public function testViewActionNoCategoryId()
    {
        $this->dispatch('catalog/category/view/');

        $this->assert404NotFound();
    }

    public function testViewActionInactiveCategory()
    {
        $this->dispatch('catalog/category/view/id/8');

        $this->assert404NotFound();
    }
}
