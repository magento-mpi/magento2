<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Block;

/**
 * Test for \Magento\UrlRewrite\Block\Edit
 * @magentoAppArea adminhtml
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test prepare layout
     *
     * @dataProvider prepareLayoutDataProvider
     *
     * @param array $blockAttributes
     * @param array $expected
     *
     * @magentoAppIsolation enabled
     */
    public function testPrepareLayout($blockAttributes, $expected)
    {
        /** @var $layout \Magento\Framework\View\LayoutInterface */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );

        /** @var $block \Magento\UrlRewrite\Block\Edit */
        $block = $layout->createBlock('Magento\UrlRewrite\Block\Edit', '', array('data' => $blockAttributes));

        $this->_checkSelector($block, $expected);
        $this->_checkButtons($block, $expected);
        $this->_checkForm($block, $expected);
    }

    /**
     * Check entity selector
     *
     * @param \Magento\UrlRewrite\Block\Edit $block
     * @param array $expected
     */
    private function _checkSelector($block, $expected)
    {
        $layout = $block->getLayout();

        /** @var $selectorBlock \Magento\UrlRewrite\Block\Selector|bool */
        $selectorBlock = $layout->getChildBlock($block->getNameInLayout(), 'selector');

        if ($expected['selector']) {
            $this->assertInstanceOf(
                'Magento\UrlRewrite\Block\Selector',
                $selectorBlock,
                'Child block with entity selector is invalid'
            );
        } else {
            $this->assertFalse($selectorBlock, 'Child block with entity selector should not present in block');
        }
    }

    /**
     * Check form
     *
     * @param \Magento\UrlRewrite\Block\Edit $block
     * @param array $expected
     */
    private function _checkForm($block, $expected)
    {
        $layout = $block->getLayout();
        $blockName = $block->getNameInLayout();

        /** @var $formBlock \Magento\UrlRewrite\Block\Edit\Form|bool */
        $formBlock = $layout->getChildBlock($blockName, 'form');

        if ($expected['form']) {
            $this->assertInstanceOf(
                'Magento\UrlRewrite\Block\Edit\Form',
                $formBlock,
                'Child block with form is invalid'
            );

            $this->assertSame(
                $expected['form']['url_rewrite'],
                $formBlock->getUrlRewrite(),
                'Form block should have same URL rewrite attribute'
            );
        } else {
            $this->assertFalse($formBlock, 'Child block with form should not present in block');
        }
    }

    /**
     * Check buttons
     *
     * @param \Magento\UrlRewrite\Block\Edit $block
     * @param array $expected
     */
    private function _checkButtons($block, $expected)
    {
        $buttonsHtml = $block->getButtonsHtml();

        if ($expected['back_button']) {
            $this->assertSelectCount('button.back', 1, $buttonsHtml, 'Back button is not present in block');
        } else {
            $this->assertSelectCount('button.back', 0, $buttonsHtml, 'Back button should not present in block');
        }

        if ($expected['save_button']) {
            $this->assertSelectCount('button.save', 1, $buttonsHtml, 'Save button is not present in block');
        } else {
            $this->assertSelectCount('button.save', 0, $buttonsHtml, 'Save button should not present in block');
        }

        if ($expected['reset_button']) {
            $this->assertSelectCount('button[title="Reset"]', 1, $buttonsHtml, 'Reset button is not present in block');
        } else {
            $this->assertSelectCount(
                'button[title="Reset"]',
                0,
                $buttonsHtml,
                'Reset button should not present in block'
            );
        }

        if ($expected['delete_button']) {
            $this->assertSelectCount('button.delete', 1, $buttonsHtml, 'Delete button is not present in block');
        } else {
            $this->assertSelectCount('button.delete', 0, $buttonsHtml, 'Delete button should not present in block');
        }
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function prepareLayoutDataProvider()
    {
        /** @var $urlRewrite \Magento\UrlRewrite\Model\UrlRewrite */
        $urlRewrite = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\UrlRewrite\Model\UrlRewrite'
        );
        /** @var $existingUrlRewrite \Magento\UrlRewrite\Model\UrlRewrite */
        $existingUrlRewrite = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\UrlRewrite\Model\UrlRewrite',
            array('data' => array('url_rewrite_id' => 1))
        );

        return array(
            // Creating new URL rewrite
            array(
                array('url_rewrite' => $urlRewrite),
                array(
                    'selector' => true,
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => false,
                    'delete_button' => false,
                    'form' => array('url_rewrite' => $urlRewrite)
                )
            ),
            // Editing URL rewrite
            array(
                array('url_rewrite' => $existingUrlRewrite),
                array(
                    'selector' => true,
                    'back_button' => true,
                    'save_button' => true,
                    'reset_button' => true,
                    'delete_button' => true,
                    'form' => array('url_rewrite' => $existingUrlRewrite)
                )
            )
        );
    }
}
