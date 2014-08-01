<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Page\Edit;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class PageForm
 * Backend Cms Page edit page
 */
class PageForm extends \Magento\Cms\Test\Block\Adminhtml\Page\Edit\PageForm
{
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
     * @return Tab
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

    /**
     * Get 'Currently Published Revision' link text
     *
     * @return string
     */
    public function getCurrentlyPublishedRevisionText()
    {
        return $this->_rootElement->find($this->currentlyPublishedRevision)->getText();
    }
}
