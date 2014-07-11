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
 * Class TokensPopup
 * Integration tokens popup container
 */
class TokensPopup extends Form
{
    /**
     * Selector for "Done" button
     *
     * @var string
     */
    protected $doneButtonSelector = './/button/span[@class="ui-button-text"][text()="Done"]';

    /**
     * Click Done button on Integration tokens popup window
     *
     * @return void
     */
    public function clickDoneButton()
    {
        $this->_rootElement->find($this->doneButtonSelector, Locator::SELECTOR_XPATH)->click();
    }
}
