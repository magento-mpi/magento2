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
 * Test for Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit
 */
class Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test block prepare layout when category selected
     */
    public function testPrepareLayoutWhenCategorySelected()
    {
        $layout = Mage::app()->getLayout();

        $urlRewrite = new Mage_Core_Model_Url_Rewrite();
        $category = new Mage_Catalog_Model_Category();
        $category->addData(array(
            'entity_id' => 1,
            'name' => 'Test category'
        ));

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit', '', array(
            'category' => $category,
            'url_rewrite' => $urlRewrite
        ));
        $blockName = $block->getNameInLayout();

        // Check entity selector
        $this->assertFalse($layout->getChildBlock($blockName, 'selector'),
            'Child block with entity selector should not present in block');

        // Check links
        /** @var $categoryBlock Mage_Adminhtml_Block_Urlrewrite_Link */
        $categoryBlock = $layout->getChildBlock($blockName, 'category_link');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Link', $categoryBlock,
            'Child block with category link is invalid');

        $this->assertEquals('Category:', $categoryBlock->getLabel(),
            'Child block with category has invalid item label');

        $this->assertEquals($category->getName(), $categoryBlock->getItemName(),
            'Child block with category has invalid item name');

        $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/category/', $categoryBlock->getItemUrl(),
            'Child block with category contains invlalid URL');

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

        $this->assertSame($category, $formBlock->getCategory(),
            'Form block should have same category attribute');

        $this->assertSame($urlRewrite, $formBlock->getUrlRewrite(),
            'Form block should have same URL rewrite attribute');

        // Check categories tree
        $this->assertFalse($layout->getChildBlock($blockName, 'categories_tree'),
            'Child block with category_tree should not present in block');
    }

    /**
     * Test block prepare layout when category not selected
     */
    public function testPrepareLayoutWhenCategoryNotSelected()
    {
        $layout = new Mage_Core_Model_Layout();

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Edit');
        $blockName = $block->getNameInLayout();

        // Check entity selector
        /** @var $selectorBlock Mage_Adminhtml_Block_Urlrewrite_Selector */
        $selectorBlock = $layout->getChildBlock($blockName, 'selector');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Selector', $selectorBlock,
            'Child block with entity selector is invalid');

        // Check links
        $this->assertFalse($layout->getChildBlock($blockName, 'category_link'),
            'Child block with category link should not present in block');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.back[onclick~="\/category"]', 0, $buttonsHtml,
            'Back button has invalid URL');

        $this->assertSelectCount('button.save', 0, $buttonsHtml,
            'Save button should not present in block');

        // Check form
        $this->assertFalse($layout->getChildBlock($blockName, 'form'),
            'Child block with form should not present in block');

        // Check categories tree
        $categoriesTreeBlock = $layout->getChildBlock($blockName, 'categories_tree');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Catalog_Category_Tree', $categoriesTreeBlock,
            'Child block with categories tree is invalid');
    }
}
