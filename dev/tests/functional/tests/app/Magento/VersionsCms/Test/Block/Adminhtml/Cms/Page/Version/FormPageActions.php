<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\VersionsCms\Test\Block\Adminhtml\Cms\Page\Version;

/**
 * Class FormPageActions
 * Form page actions block
 */
class FormPageActions extends \Magento\Backend\Test\Block\FormPageActions
{
    /**
     * "Save as new version" button
     *
     * @var string
     */
    protected $saveAsNewVersion = '[data-ui-id="cms-page-version-edit-new-button"]';

    /**
     * Click on "Save as new version" button
     *
     * @return void
     */
    public function saveAsNewVersion()
    {
        $this->_rootElement->find($this->saveAsNewVersion)->click();
    }
}
