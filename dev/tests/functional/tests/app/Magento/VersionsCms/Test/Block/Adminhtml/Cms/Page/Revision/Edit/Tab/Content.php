<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Content
 * Backend cms page revision content tab
 */
class Content extends Tab
{
    /**
     * Locator for Page Content
     *
     * @var string
     */
    protected $pageContent = "#page_content";

    /**
     * Content Editor toggle button locator
     *
     * @var string
     */
    protected $toggleButton = "#togglepage_content";

    /**
     * CMS Page Content area
     *
     * @var string
     */
    protected $contentForm = '[name="content"]';

    /**
     * Locator for Revision number
     *
     * @var string
     */
    protected $revision = "//table/tbody/tr[1]/td";

    /**
     * Locator for Version title
     *
     * @var string
     */
    protected $version = "//table/tbody/tr[4]/td";

    /**
     * Get page content
     *
     * @return string
     */
    protected function getPageContent()
    {
        $this->hideEditor();
        return $this->_rootElement->find($this->pageContent)->getText();
    }

    /**
     * Hide WYSIWYG editor
     *
     * @return void
     */
    protected function hideEditor()
    {
        $content = $this->_rootElement->find($this->contentForm);
        $toggleButton = $this->_rootElement->find($this->toggleButton);
        if (!$content->isVisible() && $toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }

    /**
     * Get Revision number
     *
     * @return string
     */
    protected function getRevision()
    {
        return $this->_rootElement->find($this->revision, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get Version title
     *
     * @return string
     */
    protected function getVersion()
    {
        return $this->_rootElement->find($this->version, Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Get data of Revision Content tab
     *
     * @return array
     */
    public function getContentData()
    {
        $data['revision'] = $this->getRevision();
        $data['version'] = $this->getVersion();
        $data['content']['content'] = $this->getPageContent();
        return $data;
    }

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $this->hideEditor();
        parent::fillFormTab($fields, $element);
        return $this;
    }
}
