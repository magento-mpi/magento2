<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Edit;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;

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
     * Current published revision link selector
     *
     * @var string
     */
    protected $currentlyPublishedRevision = '#page_published_revision_link';

    /**
     * Page Content Show/Hide Editor toggle button
     *
     * @return void
     */
    protected function toggleEditor()
    {
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
        $this->openTab('content');
        /** @var \Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab\Content $contentTab */
        $contentTab = $this->getTabElement('content');
        /** @var \Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config $config */
        $contentTab->clickInsertVariable();
        $config = $contentTab->getWysiwygConfig();

        return $config->getAllVariables();
    }

    /**
     * Click on 'Currently Published Revision' link
     *
     * @return void
     */
    protected function clickCurrentlyPublishedRevision()
    {
        $this->_rootElement->find($this->currentlyPublishedRevision)->click();
    }

    /**
     * Open tab
     *
     * @param string $tabName
     * @return $this
     */
    public function openTab($tabName)
    {
        $selector = $this->tabs[$tabName]['selector'];
        $strategy = isset($this->tabs[$tabName]['strategy'])
            ? $this->tabs[$tabName]['strategy']
            : Locator::SELECTOR_CSS;
        $tab = $this->_rootElement->find($selector, $strategy);
        if ($tabName == 'content') {
            if (!$tab->isVisible()) {
                $this->openTab('page_information');
                $this->clickCurrentlyPublishedRevision();
                $this->reinitRootElement();
            } else {
                $tab->click();
            }
            $this->toggleEditor();
        } else {
            $tab->click();
        }

        return $this;
    }
}
