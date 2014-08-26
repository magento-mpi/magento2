<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class RevisionForm
 * Block Revision Content form
 */
class RevisionForm extends FormTabs
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
