<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class PageForm
 * Backend Cms Page edit page
 */
class PageForm extends FormTabs
{
    const CONTENT_TAB = 'content';

    /**
     * Content Editor toggle button id
     *
     * @var string
     */
    protected $toggleButton = "#togglepage_content";

    /**
     * Insert Variable button selector
     *
     * @var string
     */
    protected $addVariableButton = ".add-variable";

    /**
     * System Variable block selector
     *
     * @var string
     */
    protected $systemVariableBlock = "./ancestor::body//div[div[@id='variables-chooser']]";

    /**
     * Content Editor form
     *
     * @var string
     */
    protected $contentForm = "#page_content";

    /**
     * Fill the page form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        // Open "Content" tab and toggle the editor to make visible and available to interact
        $this->toggleEditor();
        return parent::fill($fixture);
    }

    /**
     * Page Content Show/Hide Editor toggle button
     *
     * @return void
     */
    protected function toggleEditor()
    {
        $this->openTab('content');
        $content = $this->_rootElement->find($this->contentForm, Locator::SELECTOR_CSS);
        $toggleButton = $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS);
        if (!$content->isVisible() && $toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }

    /**
     * Clicking in content tab 'Insert Variable' button
     *
     * @return void
     */
    public function clickInsertVariable()
    {
        $this->toggleEditor();
        $this->openTab('content');
        $contentTab = $this->getTabElement('content');
        $addVariableButton = $contentTab->_rootElement->find($this->addVariableButton);
        if ($addVariableButton->isVisible()) {
            $addVariableButton->click();
        }
    }

    /**
     * Getter for System Variable block
     *
     * @return SystemVariables
     */
    public function getSystemVariablesBlock()
    {
        /** @var \Magento\Cms\Test\Block\Adminhtml\Page\SystemVariables $systemVariablesBlock */
        $systemVariablesBlock = $this->blockFactory->create(
            __NAMESPACE__ . '\\SystemVariables',
            ['element' => $this->_rootElement->find($this->systemVariableBlock, Locator::SELECTOR_XPATH)]
        );

        return $systemVariablesBlock;
    }
}
