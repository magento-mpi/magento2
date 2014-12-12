<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
     * "Publish" button
     *
     * @var string
     */
    protected $publishButton = '#publish_button';

    /**
     * Click 'Publish' button
     *
     * @return void
     */
    public function publish()
    {
        $this->_rootElement->find($this->publishButton)->click();
    }

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
