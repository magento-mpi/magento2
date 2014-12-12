<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block\Edit;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class BlockForm
 * Block form
 */
class BlockForm extends Form
{
    /**
     * Content Editor toggle button id
     *
     * @var string
     */
    protected $toggleButton = "#toggleblock_content";

    /**
     * Content Editor form
     *
     * @var string
     */
    protected $contentForm = "#page_content";

    /**
     * Custom Variable block selector
     *
     * @var string
     */
    protected $customVariableBlock = "./ancestor::body//div[div[@id='variables-chooser']]";

    /**
     * Insert Variable button selector
     *
     * @var string
     */
    protected $addVariableButton = ".add-variable";

    /**
     * Clicking in content tab 'Insert Variable' button
     *
     * @return void
     */
    public function clickInsertVariable()
    {
        $addVariableButton = $this->_rootElement->find($this->addVariableButton);
        if ($addVariableButton->isVisible()) {
            $addVariableButton->click();
        }
    }

    /**
     * Get for wysiwyg config block
     *
     * @return \Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config
     */
    public function getWysiwygConfig()
    {
        $config = $this->blockFactory->create(
            'Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config',
            ['element' => $this->_rootElement->find($this->customVariableBlock, Locator::SELECTOR_XPATH)]
        );

        return $config;
    }

    /**
     * Page Content Show/Hide Editor toggle button
     *
     * @return void
     */
    public function toggleEditor()
    {
        $content = $this->_rootElement->find($this->contentForm, Locator::SELECTOR_CSS);
        $toggleButton = $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS);
        if (!$content->isVisible() && $toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }
}
