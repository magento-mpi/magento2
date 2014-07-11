<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class ResourcesPopup
 * Integration resources popup container
 */
class ResourcesPopup extends Form
{
    /**
     * Selector for "Allow" button
     *
     * @var string
     */
    protected $allowButtonSelector = './/button/span[@class="ui-button-text"][text()="Allow"]';

    /**
     * Click allow button in integration resources popup window
     *
     * @return void
     */
    public function clickAllowButton()
    {
        $this->_rootElement->find($this->allowButtonSelector, Locator::SELECTOR_XPATH)->click();
    }
}
