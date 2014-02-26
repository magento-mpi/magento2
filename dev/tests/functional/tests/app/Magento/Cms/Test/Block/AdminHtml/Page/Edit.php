<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\AdminHtml\Page;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class Edit
 * Backend Cms Page edit page
 *
 * @package Magento\Cms\Test\Block\AdminHtml\Page
 */
class Edit extends FormTabs
{
    const CONTENT_TAB = 'page_tabs_content_section';

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
     * @param Element $element
     * @return FormTabs|void
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        // Open "Content" tab and toggle the editor to make visible and available to interact
        $this->toggleEditor();
        parent::fill($fixture);
    }

    /**
     * Page Content Show/Hide Editor toggle button
     *
     * @return void
     */
    protected function toggleEditor()
    {
        parent::openTab(self::CONTENT_TAB);
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
    }
}
