<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Block\Adminhtml\System\Variable;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;
use Magento\Webapi\Exception;
use Mtf\Client\Element\Locator;

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
    protected $storeViewButton = '[data-toggle="dropdown"]';

    /**
     * Store View locator
     *
     * @var string
     */
    protected $storeView = './/ul[@data-role="stores-list"]/li[a[contains(.,"%s")]]';

    /**
     * Select Store View
     *
     * @param $storeId
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
            throw new \Exception("Store View is not visible!");
        }
        $this->_rootElement->acceptAlert();
    }
}
