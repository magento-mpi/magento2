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
use Magento\Cms\Test\Block\Adminhtml\Page\Edit\PageForm as ParentPageForm;

/**
 * Class PageForm
 * Backend Cms Page edit page
 */
class PageForm extends ParentPageForm
{
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
        $tab->click();

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
