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
     * "Save in a new version" button
     *
     * @var string
     */
    protected $newVersionButton = '[data-ui-id="revision-info-new-version-button"]';

    /**
     * Click on Preview button
     *
     * @return void
     */
    public function preview()
    {
        $this->_rootElement->find($this->previewButton)->click();
    }

    /**
     * Save revision as new version
     *
     * @param string $versionName
     * @return void
     */
    public function saveInNewVersion($versionName)
    {
        $this->_rootElement->find($this->newVersionButton)->click();
        $this->_rootElement->setAlertText($versionName);
        $this->_rootElement->acceptAlert();
    }
}
