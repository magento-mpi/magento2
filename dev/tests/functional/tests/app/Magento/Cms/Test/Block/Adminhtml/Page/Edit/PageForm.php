<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Edit;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Class PageForm
 * Backend Cms Page edit page
 */
class PageForm extends FormTabs
{
    /**
     * Content Editor toggle button id
     *
     * @var string
     */
    protected $toggleButton = "#togglepage_content";

    /**
     * Content Editor form
     *
     * @var string
     */
    protected $contentForm = "#page_content";

    /**
     * Select page button selector
     *
     * @var string
     */
    protected $currentlyPublishedRevision = '#page_published_revision_link';

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
     * Returns array with System Variables
     *
     * @return array
     */
    public function getSystemVariables()
    {
        $this->toggleEditor();
        $this->openTab('content');
        /** @var \Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab\Content $contentTab */
        $contentTab = $this->getTabElement('content');
        /** @var \Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config $config */
        $contentTab->clickInsertVariable();
        $config = $contentTab->getWysiwygConfig();

        return $config->getAllVariables();
    }

    /**
     * Get data of the tabs
     *
     * @param FixtureInterface|null $fixture
     * @param Element|null $element
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData(FixtureInterface $fixture = null, Element $element = null)
    {
        $data = [];

        if (null === $fixture) {
            foreach ($this->tabs as $tabName => $tab) {
                if (isset($data['under_version_control'])
                    && $data['under_version_control'] == 'Yes'
                    && $tabName == 'content'
                ) {
                    $this->clickCurrentlyPublishedRevision();
                    $this->reinitRootElement();
                }
                $this->openTab($tabName);
                $tabData = $this->getTabElement($tabName)->getDataFormTab();
                $data = array_merge($data, $tabData);
            }
        } else {
            $isHasData = ($fixture instanceof InjectableFixture) ? $fixture->hasData() : true;
            $tabsFields = $isHasData ? $this->getFieldsByTabs($fixture) : [];
            foreach ($tabsFields as $tabName => $fields) {
                $this->openTab($tabName);
                $tabData = $this->getTabElement($tabName)->getDataFormTab($fields, $this->_rootElement);
                $data = array_merge($data, $tabData);
            }
        }

        return $data;
    }

    /**
     * Clicking to Currently Published Revision link
     *
     * @return void
     */
    protected function clickCurrentlyPublishedRevision()
    {
        $this->_rootElement->find($this->currentlyPublishedRevision)->click();
    }
}
