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

/**
 * Test for Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Urlrewrite_Cms_Page_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test prepare layout
     *
     * @dataProvider prepareLayoutDataProvider
     *
     * @param array $blockAttributes
     * @param array $expected
     */
    public function testPrepareLayout($blockAttributes, $expected)
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create(
            'Magento_Core_Model_Layout',
            array('area' => Magento_Core_Model_App_Area::AREA_ADMINHTML)
        );

        /** @var $block Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit */
        $block = $layout->createBlock(
            'Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit', '', array('data' => $blockAttributes)
        );

        $this->_checkSelector($block, $expected);
        $this->_checkLinks($block, $expected);
        $this->_checkButtons($block, $expected);
        $this->_checkForm($block, $expected);
        $this->_checkGrid($block, $expected);
    }

    /**
     * Check selector
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit $block
     * @param array $expected
     */
    private function _checkSelector($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $selectorBlock Magento_Adminhtml_Block_Urlrewrite_Selector|bool */
        $selectorBlock = $layout->getChildBlock($blockName, 'selector');

        if ($expected['selector']) {
            $this->assertInstanceOf('Magento_Adminhtml_Block_Urlrewrite_Selector', $selectorBlock,
                'Child block with entity selector is invalid');
        } else {
            $this->assertFalse($selectorBlock, 'Child block with entity selector should not present in block');
        }
    }

    /**
     * Check links
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit $block
     * @param array $expected
     */
    private function _checkLinks($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $cmsPageLinkBlock Magento_Adminhtml_Block_Urlrewrite_Link|bool */
        $cmsPageLinkBlock = $layout->getChildBlock($blockName, 'cms_page_link');

        if ($expected['cms_page_link']) {
            $this->assertInstanceOf('Magento_Adminhtml_Block_Urlrewrite_Link', $cmsPageLinkBlock,
                'Child block with CMS page link is invalid');

            $this->assertEquals('CMS page:', $cmsPageLinkBlock->getLabel(),
                'Child block with CMS page has invalid item label');

            $this->assertEquals($expected['cms_page_link']['name'], $cmsPageLinkBlock->getItemName(),
                'Child block with CMS page has invalid item name');

            $this->assertRegExp('/http:\/\/localhost\/index.php\/.*\/cms_page/', $cmsPageLinkBlock->getItemUrl(),
                'Child block with CMS page contains invalid URL');
        } else {
            $this->assertFalse($cmsPageLinkBlock, 'Child block with CMS page link should not present in block');
        }
    }

    /**
     * Check buttons
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit $block
     * @param array $expected
     */
    private function _checkButtons($block, $expected)
    {
        $buttonsHtml = $block->getButtonsHtml();

        if ($expected['back_button']) {
            if ($block->getCmsPage()->getId()) {
                $this->assertSelectCount('button.back[onclick~="\/cms_page"]', 1, $buttonsHtml,
                    'Back button is not present in CMS page URL rewrite edit block');
            } else {
                $this->assertSelectCount('button.back', 1, $buttonsHtml,
                    'Back button is not present in CMS page URL rewrite edit block');
            }
        } else {
            $this->assertSelectCount('button.back', 0, $buttonsHtml,
                'Back button should not present in CMS page URL rewrite edit block');
        }

        if ($expected['save_button']) {
            $this->assertSelectCount('button.save', 1, $buttonsHtml,
                'Save button is not present in CMS page URL rewrite edit block');
        } else {
            $this->assertSelectCount('button.save', 0, $buttonsHtml,
                'Save button should not present in CMS page URL rewrite edit block');
        }

        if ($expected['reset_button']) {
            $this->assertSelectCount('button[title="Reset"]', 1, $buttonsHtml,
                'Reset button is not present in CMS page URL rewrite edit block');
        } else {
            $this->assertSelectCount('button[title="Reset"]', 0, $buttonsHtml,
                'Reset button should not present in CMS page URL rewrite edit block');
        }

        if ($expected['delete_button']) {
            $this->assertSelectCount('button.delete', 1, $buttonsHtml,
                'Delete button is not present in CMS page URL rewrite edit block');
        } else {
            $this->assertSelectCount('button.delete', 0, $buttonsHtml,
                'Delete button should not present in CMS page URL rewrite edit block');
        }
    }

    /**
     * Check form
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit $block
     * @param array $expected
     */
    private function _checkForm($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $formBlock Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form|bool */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        if ($expected['form']) {
            $this->assertInstanceOf('Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form', $formBlock,
                'Child block with form is invalid');

            $this->assertSame($expected['form']['cms_page'], $formBlock->getCmsPage(),
                'Form block should have same CMS page attribute');

            $this->assertSame($expected['form']['url_rewrite'], $formBlock->getUrlRewrite(),
                'Form block should have same URL rewrite attribute');
        } else {
            $this->assertFalse($formBlock, 'Child block with form should not present in block');
        }
    }

    /**
     * Check grid
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit $block
     * @param array $expected
     */
    private function _checkGrid($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $gridBlock Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Grid|bool */
        $gridBlock = $layout->getChildBlock($blockName, 'cms_pages_grid');

        if ($expected['cms_pages_grid']) {
            $this->assertInstanceOf('Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Grid', $gridBlock,
                'Child block with CMS pages grid is invalid');
        } else {
            $this->assertFalse($gridBlock, 'Child block with CMS pages grid should not present in block');
        }
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function prepareLayoutDataProvider()
    {
        /** @var $urlRewrite Magento_Core_Model_Url_Rewrite */
        $urlRewrite = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Url_Rewrite');
        /** @var $cmsPage Magento_Cms_Model_Page */
        $cmsPage = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cms_Model_Page',
            array('data' => array('page_id' => 1, 'title' => 'Test CMS Page'))
        );
        /** @var $existingUrlRewrite Magento_Core_Model_Url_Rewrite */
        $existingUrlRewrite = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Url_Rewrite',
            array('data' => array('url_rewrite_id' => 1))
        );

        return array(
            // Creating URL rewrite when CMS page selected
            array(
                array(
                    'cms_page' => $cmsPage,
                    'url_rewrite' => $urlRewrite
                ),
                array(
                    'selector' => false,
                    'cms_page_link' => array(
                        'name' => $cmsPage->getTitle()
                    ),
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'form' => array(
                        'cms_page' => $cmsPage,
                        'url_rewrite' => $urlRewrite
                    ),
                    'cms_pages_grid' => false
                )
            ),
            // Creating URL rewrite when CMS page not selected
            array(
                array(
                    'url_rewrite' => $urlRewrite
                ),
                array(
                    'selector' => true,
                    'cms_page_link' => false,
                    'back_button' => true,
                    'save_button' => false,
                    'reset_button' => false,
                    'delete_button' => false,
                    'form' => false,
                    'cms_pages_grid' => true
                )
            ),
            // Editing existing URL rewrite with CMS page
            array(
                array(
                    'url_rewrite' => $existingUrlRewrite,
                    'cms_page' => $cmsPage
                ),
                array(
                    'selector' => false,
                    'cms_page_link' => array(
                        'name' => $cmsPage->getTitle(),
                    ),
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => true,
                    'delete_button' => true,
                    'form' => array(
                        'cms_page' => $cmsPage,
                        'url_rewrite' => $existingUrlRewrite
                    ),
                    'cms_pages_grid' => false
                )
            ),
        );
    }
}
