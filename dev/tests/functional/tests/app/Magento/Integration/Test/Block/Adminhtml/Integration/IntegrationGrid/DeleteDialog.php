<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class DeleteDialog
 * Delete Dialog confirmation window
 */
class DeleteDialog extends Block
{
    /**
     * Delete button selector
     *
     * @var string
     */
    protected $deleteButton = './/button/span[@class="ui-button-text"][text()="Delete"]';

    /**
     * Click on delete button
     *
     * @return void
     */
    public function acceptDeletion()
    {
        $this->_rootElement->find($this->deleteButton, Locator::SELECTOR_XPATH)->click();
    }
}
