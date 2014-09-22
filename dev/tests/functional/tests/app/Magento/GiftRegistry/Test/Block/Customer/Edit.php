<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Edit
 * Form for select gift registry type
 */
class Edit extends Form
{
    /**
     * Gift registry type input selector
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
     * Select gift registry type
     *
     * @param string $value
     * @return void
     */
    public function selectGiftRegistryType($value)
    {
        $this->_rootElement->find($this->giftRegistryType, Locator::SELECTOR_CSS, 'select')->setValue($value);
        $this->_rootElement->find($this->next)->click();
    }

    /**
     * Check if GiftRegistry type is visible
     *
     * @param string $value
     * @return bool
     */
    public function isGiftRegistryVisible($value)
    {
        $presentOptions = $this->_rootElement->find($this->giftRegistryType)->getText();
        $presentOptions = explode("\n", $presentOptions);
        return in_array($value, $presentOptions);
    }
}
