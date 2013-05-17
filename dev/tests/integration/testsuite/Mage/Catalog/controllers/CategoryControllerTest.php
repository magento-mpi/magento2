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
 * Test class for Mage_Catalog_CategoryController.
 *
 * @magentoDataFixture Mage/Catalog/_files/categories.php
 */
class Mage_Catalog_CategoryControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function assert404NotFound()
    {
        parent::assert404NotFound();
        $this->assertNull(Mage::registry('current_category'));
    }

    public function getViewActionDataProvider()
    {
        return array(
            'category without children' => array(
                '$categoryId' => 5,
                '$expectedProductCount' => 1,
                array(
                    'catalog_category_view_type_default',
                    'catalog_category_view_type_default_without_children',
                ),
                array(
                    '%scategorypath-category-1-category-1-1-category-1-1-1-html%s',
                    '%scategory-category-1-1-1%s',
                    '%s<title>Category 1.1.1 - Category 1.1 - Category 1</title>%s',
                    '%s<h1%S>%SCategory 1.1.1%S</h1>%s',
                    '%sSimple Product Two%s',
                    '%s$45.67%s',
                ),
            ),
            'anchor category' => array(
                '$categoryId' => 4,
                '$expectedProductCount' => 2,
                array(
                    'catalog_category_view_type_layered',
                ),
                array(
                    '%scategorypath-category-1-category-1-1-html%s',
                    '%scategory-category-1-1%s',
                    '%s<title>Category 1.1 - Category 1</title>%s',
                    '%s<h1%S>%SCategory 1.1%S</h1>%s',
                    '%sSimple Product%s',
                    '%s$10.00%s',
                    '%sSimple Product Two%s',
                    '%s$45.67%s',
                ),
            ),
        );
    }

    /**
     * @dataProvider getViewActionDataProvider
     */
    public function testViewAction($categoryId, $expectedProductCount, array $expectedHandles, array $expectedContent)
    {
        $this->dispatch("catalog/category/view/id/$categoryId");

        /** @var $currentCategory Mage_Catalog_Model_Category */
        $currentCategory = Mage::registry('current_category');
        $this->assertInstanceOf('Mage_Catalog_Model_Category', $currentCategory);
        $this->assertEquals($categoryId, $currentCategory->getId(), 'Category in registry.');

        $lastCategoryId = Mage::getSingleton('Mage_Catalog_Model_Session')->getLastVisitedCategoryId();
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

        $actualProductCount = substr_count($responseBody, '<h2 class="product-name">');
        $this->assertEquals($expectedProductCount, $actualProductCount, 'Number of products on the page.');
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
