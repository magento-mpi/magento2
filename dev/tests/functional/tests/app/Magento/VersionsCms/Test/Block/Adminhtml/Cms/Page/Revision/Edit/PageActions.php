<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Revision\Edit;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class PageActions
 * PageActions for the role edit page
 */
class PageActions extends FormPageActions
{
    /**
     * "Preview" button
     *
     * @var string
     */
    protected $previewButton = '[data-ui-id="revision-info-preview-button"]';

    /**
     * Click on Preview button
     *
     * @return void
     */
    public function preview()
    {
        $this->_rootElement->find($this->previewButton)->click();
    }
}
