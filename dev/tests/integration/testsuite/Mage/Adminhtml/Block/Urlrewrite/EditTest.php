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
 * Test for Mage_Adminhtml_Block_Urlrewrite_Edit
 */
class Mage_Adminhtml_Block_Urlrewrite_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test block prepare layout when CMS page selected
     */
    public function testPrepareLayoutWhenCmsPageSelected()
    {
        $layout = Mage::app()->getLayout();

        $urlRewrite = new Mage_Core_Model_Url_Rewrite();


        /** @var $block Mage_Adminhtml_Block_Urlrewrite_Edit */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Urlrewrite_Edit', '', array(
            'url_rewrite' => $urlRewrite
        ));
        $blockName = $block->getNameInLayout();

        // Check buttons
        $buttonsHtml = $block->getButtonsHtml();
        $this->assertSelectCount('button[title="Reset"]', 1, $buttonsHtml,
            'Back button is not present in block');

        $this->assertSelectCount('button.delete', 1, $buttonsHtml,
            'Save button is not present in block');

        // Check form
        /** @var $formBlock Mage_Adminhtml_Block_Urlrewrite_Edit_Form */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        $this->assertInstanceOf('Mage_Adminhtml_Block_Urlrewrite_Edit_Form', $formBlock,
            'Child block with form is invalid');

        $this->assertSame($urlRewrite, $formBlock->getUrlRewrite(),
            'Form block should have same URL rewrite attribute');
    }
}
