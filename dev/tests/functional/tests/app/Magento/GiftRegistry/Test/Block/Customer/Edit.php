<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Edit
 * Form for select gift registry type
 */
class Edit extends Block
{
    /**
     * Gift registry type input field
     *
     * @var string
     */
    protected $giftRegistryType = '[name="type_id"]';

    /**
     * Next button
     *
     * @var string
     */
    protected $next = ".action.next";

    /**
     * Fill gift card redeem
     *
     * @param string $value
     * @return void
     */
    public function selectGiftRegistryType($value)
    {
        $this->_rootElement->find($this->giftRegistryType, Locator::SELECTOR_CSS, 'select')->setValue($value);
        $this->_rootElement->find($this->next)->click();
    }
}
