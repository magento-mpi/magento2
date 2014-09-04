<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model\Validator;

use \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard;
use \Magento\Sales\Model\Quote\Item;

/**
 * Class Discount Validator
 * @package Magento\GiftCard\Model\Validator
 */
class Discount implements \Zend_Validate_Interface
{
    /**
     * @var []
     */
    protected $messages;

    /**
     * Define if we can apply discount to current item
     *
     * @param Item $item
     * @return bool
     */
    public function isValid($item)
    {
        if(Giftcard::TYPE_GIFTCARD == $item->getProductType()) {
            $this->messages[] = __('Cannot apply discount to GiftCard');
            return false;
        }
        return true;
    }

    /**
     * Returns messages on isValid() returns False
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
