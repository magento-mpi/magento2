<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test block prepare layout when product selected and category mode active
     */
    public function testPrepareLayoutWhenProductSelectedAndCategoryModeActive()
    {
        $layout = Mage::app()->getLayout();

        $urlRewrite = new Mage_Core_Model_Url_Rewrite();
        $product = new Mage_Catalog_Model_Product();
        $product->addData(array(
            'entity_id' => 1,
            'name' => 'Test product',
        ));

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit', '', array(
            'product' => $product,
            'url_rewrite' => $urlRewrite,
            'is_category_mode' => true
        ));
        $blockName = $block->getNameInLayout();

        // Check entity selector
        $this->assertFalse($layout->getChildBlock($blockName, 'selector'),
            'Child block with entity selector should not present in block');

        // Check links
        /** @var $productLinkBlock Mage_Adminhtml_Block_Urlrewrite_Link */
        $productLinkBlock = $layout->getChildBlock($blockName, 'product_link');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Link', $productLinkBlock,
            'Child block with product link is invalid');

        $this->assertEquals('Product:', $productLinkBlock->getLabel(),
            'Child block with product link has invalid item label');

        $this->assertEquals($product->getName(), $productLinkBlock->getItemName(),
            'Child block with product link has invalid item name');

        $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/product/', $productLinkBlock->getItemUrl(),
            'Child block with product link contains invlalid URL');

        $this->assertFalse($layout->getChildBlock($blockName, 'category_link'),
            'Child block with category link should not present in block');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back[onclick~="\/product"]', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.save', 0, $buttonsHtml,
            'Save button should not present in block');

        // Check form
        $this->assertFalse($layout->getChildBlock($blockName, 'form'),
            'Child block with form should not present in block');

        // Check grid
        $this->assertFalse($layout->getChildBlock($blockName, 'products_grid'),
            'Child block with product grid should not present in block');

        // Check categories tree
        $categoriesTreeBlock = $layout->getChildBlock($blockName, 'categories_tree');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree', $categoriesTreeBlock,
            'Child block with categories tree is invalid');

        /** @var $skipCategoriesBlock Mage_Adminhtml_Block_Widget_Button */
        $skipCategoriesBlock = $layout->getChildBlock($blockName, 'skip_categories');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Widget_Button', $skipCategoriesBlock,
            'Child block with skip categories is invalid');
    }

    /**
     * Test block prepare layout when product selected and category mode inactive
     */
    public function testPrepareLayoutWhenProductSelectedAndCategoryModeInactive()
    {
        $layout = Mage::app()->getLayout();

        $urlRewrite = new Mage_Core_Model_Url_Rewrite();
        $product = new Mage_Catalog_Model_Product();
        $product->addData(array(
            'entity_id' => 1,
            'name' => 'Test product'
        ));

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit', '', array(
            'product' => $product,
            'url_rewrite' => $urlRewrite
        ));
        $blockName = $block->getNameInLayout();

        // Check entity selector
        $this->assertFalse($layout->getChildBlock($blockName, 'selector'),
            'Child block with entity selector should not present in block');

        // Check links
        /** @var $productLinkBlock Mage_Adminhtml_Block_Urlrewrite_Link */
        $productLinkBlock = $layout->getChildBlock($blockName, 'product_link');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Link', $productLinkBlock,
            'Child block with product link is invalid');

        $this->assertEquals('Product:', $productLinkBlock->getLabel(),
            'Child block with product link has invalid item label');

        $this->assertEquals($product->getName(), $productLinkBlock->getItemName(),
            'Child block with product link has invalid item name');

        $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/product/', $productLinkBlock->getItemUrl(),
            'Child block with product link contains invlalid URL');

        $this->assertFalse($layout->getChildBlock($blockName, 'category_link'),
            'Child block with category link should not present in block');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back[onclick~="\/product"]', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.save', 1, $buttonsHtml,
            'Save button is not present in block');

        // Check form
        /** @var $formBlock Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', $formBlock,
            'Child block with form is invalid');

        $this->assertSame($product, $formBlock->getProduct(),
            'Form block should have same product attribute');

        $this->assertSame($urlRewrite, $formBlock->getUrlRewrite(),
            'Form block should have same URL rewrite attribute');

        // Check grid
        $this->assertFalse($layout->getChildBlock($blockName, 'products_grid'),
            'Child block with product grid should not present in block');

        // Check categories tree
        $this->assertFalse($layout->getChildBlock($blockName, 'categories_tree'),
            'Child block with categories tree should not present in block');

        $this->assertFalse($layout->getChildBlock($blockName, 'skip_categories'),
            'Child block with skip categories should not present in block');
    }

    /**
     * Test block prepare layout when product and category selected
     */
    public function testPrepareLayoutWhenProductAndCategorySelected()
    {
        $layout = Mage::app()->getLayout();

        $urlRewrite = new Mage_Core_Model_Url_Rewrite();
        $product = new Mage_Catalog_Model_Product();
        $product->addData(array(
            'entity_id' => 1,
            'name' => 'Test product'
        ));

        $category = new Mage_Catalog_Model_Category();
        $category->addData(array(
            'entity_id' => 1,
            'name' => 'Test category'
        ));

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit', '', array(
            'product' => $product,
            'category' => $category,
            'url_rewrite' => $urlRewrite
        ));
        $blockName = $block->getNameInLayout();

        // Check entity selector
        $this->assertFalse($layout->getChildBlock($blockName, 'selector'),
            'Child block with entity selector should not present in block');

        // Check links
        /** @var $productLinkBlock Mage_Adminhtml_Block_Urlrewrite_Link */
        $productLinkBlock = $layout->getChildBlock($blockName, 'product_link');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Link', $productLinkBlock,
            'Child block with product link is invalid');

        $this->assertEquals('Product:', $productLinkBlock->getLabel(),
            'Child block with product link has invalid item label');

        $this->assertEquals($product->getName(), $productLinkBlock->getItemName(),
            'Child block with product link has invalid item name');

        $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/product/', $productLinkBlock->getItemUrl(),
            'Child block with product link contains invlalid URL');

        /** @var $categoryLinkBlock Mage_Adminhtml_Block_Urlrewrite_Link */
        $categoryLinkBlock = $layout->getChildBlock($blockName, 'category_link');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Link', $categoryLinkBlock,
            'Child block with category link is invalid');

        $this->assertEquals('Category:', $categoryLinkBlock->getLabel(),
            'Child block with category link has invalid item label');

        $this->assertEquals($category->getName(), $categoryLinkBlock->getItemName(),
            'Child block with category link has invalid item name');

        $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/category/', $categoryLinkBlock->getItemUrl(),
            'Child block with category link contains invlalid URL');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back[onclick~="\/category"]', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.save', 1, $buttonsHtml,
            'Save button is not present in block');

        // Check form
        /** @var $formBlock Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Catalog_Edit_Form', $formBlock,
            'Child block with form is invalid');

        $this->assertSame($product, $formBlock->getProduct(),
            'Form block should have same product attribute');

        $this->assertSame($category, $formBlock->getCategory(),
            'Form block should have same category attribute');

        $this->assertSame($urlRewrite, $formBlock->getUrlRewrite(),
            'Form block should have same URL rewrite attribute');

        // Check grid
        $this->assertFalse($layout->getChildBlock($blockName, 'products_grid'),
            'Child block with product grid should not present in block');

        // Check categories tree
        $this->assertFalse($layout->getChildBlock($blockName, 'categories_tree'),
            'Child block with categories tree should not present in block');

        $this->assertFalse($layout->getChildBlock($blockName, 'skip_categories'),
            'Child block with skip categories should not present in block');
    }

    /**
     * Test block prepare layout when product and category not selected
     */
    public function testPrepareLayoutWhenProductAndCategoryNotSelected()
    {
        $layout = Mage::app()->getLayout();

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Edit');
        $blockName = $block->getNameInLayout();

        // Check entity selector
        $selectorBlock = $layout->getChildBlock($blockName, 'selector');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Selector', $selectorBlock,
            'Child block with entity selector is invalid');

        // Check links
        $this->assertFalse($layout->getChildBlock($blockName, 'product_link'),
            'Child block with product link should not present in block');

        $this->assertFalse($layout->getChildBlock($blockName, 'category_link'),
            'Child block with category link should not present in block');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.back[onclick~="\/product"]', 0, $buttonsHtml,
            'Back button has invalid URL');

        $this->assertSelectCount('button.save', 0, $buttonsHtml,
            'Save button is should not present in block');

        // Check form
        $this->assertFalse($layout->getChildBlock($blockName, 'form'),
            'Child block with form should not present in block');

        // Check grid
        /** @var $gridBlock Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid */
        $gridBlock = $layout->getChildBlock($blockName, 'products_grid');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Catalog_Product_Grid', $gridBlock,
            'Child block with product grid is invalid');

        // Check categories tree
        $this->assertFalse($layout->getChildBlock($blockName, 'categories_tree'),
            'Child block with categories tree should not present in block');

        $this->assertFalse($layout->getChildBlock($blockName, 'skip_categories'),
            'Child block with skip categories should not present in block');
    }
}
