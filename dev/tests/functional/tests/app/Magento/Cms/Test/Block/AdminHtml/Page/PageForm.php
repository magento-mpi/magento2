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
        parent::openTab(self::CONTENT_TAB);
        $toggleButton = $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS);
        if ($toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }
}
