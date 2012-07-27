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
 * Test for Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit
 */
class Mage_Adminhtml_Block_Urlrewrite_Cms_Page_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test block prepare layout when CMS page selected
     */
    public function testPrepareLayoutWhenCmsPageSelected()
    {
        $layout = Mage::app()->getLayout();

        $urlRewrite = new Mage_Core_Model_Url_Rewrite();
        $cmsPage = new Mage_Cms_Model_Page();
        $cmsPage->addData(array(
            'page_id' => 1,
            'title' => 'Test CMS Page'
        ));

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit', '', array(
            'cms_page' => $cmsPage,
            'url_rewrite' => $urlRewrite
        ));
        $blockName = $block->getNameInLayout();

        // Check entity selector
        $this->assertFalse($layout->getChildBlock($blockName, 'selector'),
            'Child block with entity selector should not present in block');

        // Check links
        /** @var $cmsPageLinkBlock Mage_Adminhtml_Block_Urlrewrite_Link */
        $cmsPageLinkBlock = $layout->getChildBlock($blockName, 'cms_page_link');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Link', $cmsPageLinkBlock,
            'Child block with CMS page link is invalid');

        $this->assertEquals('CMS page:', $cmsPageLinkBlock->getLabel(),
            'Child block with CMS page has invalid item label');

        $this->assertEquals($cmsPage->getTitle(), $cmsPageLinkBlock->getItemName(),
            'Child block with CMS page has invalid item name');

        $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/cms_page/', $cmsPageLinkBlock->getItemUrl(),
            'Child block with CMS page contains invlalid URL');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back[onclick~="\/cms_page"]', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.save', 1, $buttonsHtml,
            'Save button is not present in block');

        // Check form
        /** @var $formBlock Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', $formBlock,
            'Child block with form is invalid');

        $this->assertSame($cmsPage, $formBlock->getCmsPage(),
            'Form block should have same CMS page attribute');

        $this->assertSame($urlRewrite, $formBlock->getUrlRewrite(),
            'Form block should have same URL rewrite attribute');

        // Check grid
        $this->assertFalse($layout->getChildBlock($blockName, 'cms_pages_grid'),
            'Child block with CMS pages grid should not present in block');
    }

    /**
     * Test block prepare layout when CMS page not selected
     */
    public function testPrepareLayoutWhenCmsPageNotSelected()
    {
        $layout = Mage::app()->getLayout();

        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Edit');
        $blockName = $block->getNameInLayout();

        // Check entity selector
        /** @var $selectorBlock Mage_Adminhtml_Block_Urlrewrite_Selector */
        $selectorBlock = $layout->getChildBlock($blockName, 'selector');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Selector', $selectorBlock,
            'Child block with entity selector is invalid');

        // Check links
        $this->assertFalse($layout->getChildBlock($blockName, 'cms_page_link'),
            'Child block with CMS page link should not present in block');

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button.back', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.back[onclick~="\/cms_page"]', 0, $buttonsHtml,
            'Back button has invalid URL');

        $this->assertSelectCount('button.save', 0, $buttonsHtml,
            'Save button should not present in block');

        // Check form
        $this->assertFalse($layout->getChildBlock($blockName, 'form'),
            'Child block with form should not present in block');

        // Check grid
        /** @var $gridBlock Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid */
        $gridBlock = $layout->getChildBlock($blockName, 'cms_pages_grid');
        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Cms_Page_Grid', $gridBlock,
            'Child block with CMS pages grid is invalid');
    }
}
