<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Block\Adminhtml\System\Variable;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;

/**
 * Class FormPageActions
 * Page Actions for Custom Variable
 */
class FormPageActions extends AbstractFormPageActions
{
    /**
     * "Save and Continue Edit" button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_edit';

    /**
     * Store View button
     *
     * @var string
     */
    protected $storeViewButton = '.store-switcher [data-toggle="dropdown"]';

    /**
     * Select Store View
     *
     * @param int $storeId
     * @throws \Exception
     * @return void
     */
    public function selectStoreView($storeId)
    {
        $this->_rootElement->find($this->storeViewButton)->click();
        $selector = "[data-value='$storeId']";
        if ($this->_rootElement->find($selector)->isVisible()) {
            $this->_rootElement->find($selector)->click();
        } else {
            throw new \Exception('Store View with name \'' . $storeId . '\'is not visible!');
        }
        $this->_rootElement->acceptAlert();
    }
}
