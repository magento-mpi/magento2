<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Block\Adminhtml\System\Variable;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;
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
    protected $storeViewButton = '.store-switcher [data-toggle="dropdown"]';

    /**
     * Store View locator
     *
     * @var string
     */
    protected $storeView = './/*/a[contains(text(),"%s")]';

    /**
     * Select Store View
     *
     * @param string $storeName
     * @throws \Exception
     * @return void
     */
    public function selectStoreView($storeName)
    {
        $this->_rootElement->find($this->storeViewButton)->click();
        $selector = sprintf($this->storeView, $storeName);
        if ($this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->_rootElement->find($selector, Locator::SELECTOR_XPATH)->click();
        } else {
            throw new \Exception('Store View with name \'' . $storeName . '\' is not visible!');
        }
        $this->_rootElement->acceptAlert();
    }
}
