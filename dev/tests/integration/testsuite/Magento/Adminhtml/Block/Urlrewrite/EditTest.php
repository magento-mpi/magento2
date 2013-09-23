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
 * Test for Magento_Adminhtml_Block_Urlrewrite_Edit
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Urlrewrite_EditTest extends PHPUnit_Framework_TestCase
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

        /** @var $block Magento_Adminhtml_Block_Urlrewrite_Edit */
        $block = $layout->createBlock(
            'Magento_Adminhtml_Block_Urlrewrite_Edit', '', array('data' => $blockAttributes)
        );

        $this->_checkSelector($block, $expected);
        $this->_checkButtons($block, $expected);
        $this->_checkForm($block, $expected);
    }

    /**
     * Check entity selector
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Edit $block
     * @param array $expected
     */
    private function _checkSelector($block, $expected)
    {
        $layout = $block->getLayout();

        /** @var $selectorBlock Magento_Adminhtml_Block_Urlrewrite_Selector|bool */
        $selectorBlock = $layout->getChildBlock($block->getNameInLayout(), 'selector');

        if ($expected['selector']) {
            $this->assertInstanceOf('Magento_Adminhtml_Block_Urlrewrite_Selector', $selectorBlock,
                'Child block with entity selector is invalid');
        } else {
            $this->assertFalse($selectorBlock, 'Child block with entity selector should not present in block');
        }
    }

    /**
     * Check form
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Edit $block
     * @param array $expected
     */
    private function _checkForm($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $formBlock Magento_Adminhtml_Block_Urlrewrite_Edit_Form|bool */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        if ($expected['form']) {
            $this->assertInstanceOf('Magento_Adminhtml_Block_Urlrewrite_Edit_Form', $formBlock,
                'Child block with form is invalid');

            $this->assertSame($expected['form']['url_rewrite'], $formBlock->getUrlRewrite(),
                'Form block should have same URL rewrite attribute');
        } else {
            $this->assertFalse($formBlock, 'Child block with form should not present in block');
        }
    }

    /**
     * Check buttons
     *
     * @param Magento_Adminhtml_Block_Urlrewrite_Edit $block
     * @param array $expected
     */
    private function _checkButtons($block, $expected)
    {
        $buttonsHtml = $block->getButtonsHtml();

        if ($expected['back_button']) {
            $this->assertSelectCount('button.back', 1, $buttonsHtml,
                'Back button is not present in block');
        } else {
            $this->assertSelectCount('button.back', 0, $buttonsHtml,
                'Back button should not present in block');
        }

        if ($expected['save_button']) {
            $this->assertSelectCount('button.save', 1, $buttonsHtml,
                'Save button is not present in block');
        } else {
            $this->assertSelectCount('button.save', 0, $buttonsHtml,
                'Save button should not present in block');
        }

        if ($expected['reset_button']) {
            $this->assertSelectCount('button[title="Reset"]', 1, $buttonsHtml,
                'Reset button is not present in block');
        } else {
            $this->assertSelectCount('button[title="Reset"]', 0, $buttonsHtml,
                'Reset button should not present in block');
        }

        if ($expected['delete_button']) {
            $this->assertSelectCount('button.delete', 1, $buttonsHtml,
                'Delete button is not present in block');
        } else {
            $this->assertSelectCount('button.delete', 0, $buttonsHtml,
                'Delete button should not present in block');
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
        /** @var $existingUrlRewrite Magento_Core_Model_Url_Rewrite */
        $existingUrlRewrite = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Url_Rewrite',
            array('data' => array('url_rewrite_id' => 1))
        );

        return array(
            // Creating new URL rewrite
            array(
                array(
                    'url_rewrite' => $urlRewrite
                ),
                array(
                    'selector' => true,
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'form' => array(
                        'url_rewrite' => $urlRewrite
                    )
                )
            ),
            // Editing URL rewrite
            array(
                array(
                    'url_rewrite' => $existingUrlRewrite
                ),
                array(
                    'selector' => true,
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => true,
                    'delete_button' => true,
                    'form' => array(
                        'url_rewrite' => $existingUrlRewrite
                    )
                )
            )
        );
    }
}
